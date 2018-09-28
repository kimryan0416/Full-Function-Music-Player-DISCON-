<?php
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	$dataToSend = array();
	$success = false;
    $message = "No get request detected";
    $file = "";
    $replace_song_details = 0;

    $image_types = array("jpg", "JPG", "jpeg", "JPEG", "png", "PNG");

	// Detects if the proper files were sent via AJAX and not some malicious other way

	if(isset($_GET["icon"])) {	// edits only the icon - needs to detect if we really have to change the icon or not
		if (isset($_GET["id"])) {
			$query = "SELECT url FROM Music WHERE music_id=".$_GET["id"];
			if (!$result = $mysqli->query($query)) {
				$success = false;
				$message = "Error retrieving data from database: ".$mysqli->error();
			} else {
				$row = $result->fetch_assoc();
				$url = "../".$row["url"];

				// first ensure that we clear the temp folder of any possibly remaining files
				$files = scandir("../media/temp/");
				foreach($files as $file) {
					unlink("../media/temp/".$file);
				}

				// copy files from url indicated from database to temp folder
				$files = scandir($url);
				$destination = "../media/temp/";
				$image_file;
				foreach ($files as $file) {
					if (
						image_type_to_mime_type(exif_imagetype($url.$file)) == "image/png" || 
						image_type_to_mime_type(exif_imagetype($url.$file)) == "image/jpeg"
					) {
        				$image_file = $file;
        			}
					copy($url.$file, $destination.$file);
				}

				if ($_GET["change"] == 1) {
					// if we HAVE to change the current icon (because we uploaded a new one)

					$upload_dir = '../media/temp/';	
					
					foreach($_FILES as $file) {
						$ext = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);	// grab the extension of the file we are using
						
						$target_file = $upload_dir . "icon." . $ext;
						// Setting the target path, which will simultaneously rename our new picture when we move it

						$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
						// Get the image file type - this will tell us if this is a jpg or png or whatnot

						$check = getimagesize($file["tmp_name"]);
						// This is just to check that this is a legit file that is an image

					    if($check !== false) {

					    	// Need to confirm that this is a jpg or not
							if ( !in_array($ext,$image_types) ) {
								$success = false;
						    	$message = "Image file is not jpg or png";
							} else {
								if ( !unlink("../media/temp/".$image_file) ) {
									$success = false;
									$message = "Could not delete original icon";
								} else {
									if (move_uploaded_file($file['tmp_name'], $target_file)) {
							    		$success = true;
	   									$message = "Image successfully moved!";
							    	} else {
							    		$success = false;
							    		$message = "Icon upload error - the file wasn't able to be moved to the temp file for some reason";
							    	}
								}
							}
					    } else {
					        $success = false;
					        $message = "This file is not an image; please select an image file";
					    } 
					}
				} else {
					// if we don't have to change the current icon (because we either canceled the edit or we didn't upload a new one)
					$success = true;
   					$message = "Icon Request Recieved, ID found, but we don't change the icon";
				}
			}
		} else {
			$success = false;
   			$message = "Icon Request Recieved, but no ID found";
		}
	} else if (isset($_GET["song"])) {
		// The files being moved to the temp folder for protection and the icon file being processed, we can proceed with editing the song's details

		$title = filter_input( INPUT_POST, "title", FILTER_SANITIZE_STRING );
		$artist = filter_input( INPUT_POST, "artist", FILTER_SANITIZE_STRING );
		$album_artist = filter_input( INPUT_POST, "album_artist", FILTER_SANITIZE_STRING );
		$album = filter_input( INPUT_POST, "album", FILTER_SANITIZE_STRING );
		$composer = filter_input( INPUT_POST, "composer", FILTER_SANITIZE_STRING );
		$year = filter_input( INPUT_POST, "year", FILTER_SANITIZE_STRING );
		$comments = filter_input( INPUT_POST, "comments", FILTER_SANITIZE_STRING );
		$length = filter_input( INPUT_POST, "length", FILTER_SANITIZE_STRING );

		// Lyrics - are arrays, so we have to accept as simple $_POST
		$lyrics = $_POST["lyrics"];
		$startTimes = $_POST["start"];
		$endTimes = $_POST["end"];

		// Adjustments need to be made for the TITLE and ARTIST - this is because these 2 are used to organize the media folder
		$title_adjustment = html_entity_decode($title, ENT_QUOTES, "utf-8");	
			$title_adjustment = preg_replace('/[^\w\s]+/u','' ,$title_adjustment);
			$title_adjustment = str_replace(" ", "_", $title_adjustment);
		$artist_adjustment = html_entity_decode($artist, ENT_QUOTES, "utf-8");	
			$artist_adjustment = preg_replace('/[^\w\s]+/u','' ,$artist_adjustment);
			$artist_adjustment = str_replace(" ", "_", $artist_adjustment);
		
		// New target directory - actual_target is the actual link, while just target is used for moving files
		$actual_target_dir = "media/music/".$artist_adjustment."/".$title_adjustment."/";
		$target_dir = "../media/music/".$artist_adjustment."/".$title_adjustment."/";

			$query = "SELECT * FROM Music WHERE music_id=".$_POST["id"];
			if (!$result = $mysqli->query($query)) {
				$success = false;
				$message = "Could not retrieve song data for file detail editing: ".$mysqli->error();
			} else {
				$row = $result->fetch_assoc();

				// Need to get the file extension - this is for renaming the audio file if necessary
				$song_extension = explode(".", $row["file_name"]);

				// We delete the lyrics.txt file first, then if lyrics were given then we create a new lyrics file - this occurs independently from the MySQL changing because the MySQL doesn't think about comments
				if (file_exists("../media/temp/lyrics.json")) {
					unlink("../media/temp/lyrics.json");
				}

				$lyricsToJSON = array();
				//If lyrics were given, we create a "lyrics.txt" file inside /temp/ containing the lyrics
				foreach($lyrics as $key=>$segment) {
					$lyric = "";
					$safeStart = $safeEnd = "";
					$startVerify = $endVerify = 1;
			
					/* There are TWO conditions wherein there are errors or skips: */
					// 1) If the start time, end time, and lyric segment are ALL empty, then we skip
					if ( strlen(trim($segment)) == 0 && strlen(trim($startTimes[$key])) == 0 && strlen(trim($endTimes[$key])) == 0 ) {
						continue;
					} 
					// 2) If any of the start times and end time are empty, we throw an error
					else if ( strlen(trim($startTimes[$key])) == 0 || strlen(trim($endTimes[$key])) == 0 ) {
						$success = false;
						$message = "Error in Lyrics - a start and/or end time is empty";
					}
					// If the lyric segment bypasses these two errors, then we can safely evaluate the lyric segment
					else {
						// We sanitize the lyric, and make sure that the start and end times are in proper format
						$lyric = htmlspecialchars($segment);
						$lyric = filter_var( $lyric, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );

						$safeStart = htmlspecialchars($startTimes[$key]);
						$startVerify = preg_match('~^\d{1,2}[:]\d{2}(?::\d{2})*$~', $safeStart);

						$safeEnd = htmlspecialchars($endTimes[$key]);
						$endVerify = preg_match('~^\d{1,2}[:]\d{2}(?::\d{2})*$~', $safeEnd);

						// If both the start and end times are verified, then we can proceed
						if ($startVerify == 1 && $endVerify == 1) {
							sscanf($safeStart, "%d:%d:%d", $hours, $minutes, $seconds);
							$start_time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

							sscanf($safeEnd, "%d:%d:%d", $hours, $minutes, $seconds);
							$end_time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

							//$safeStartArray = explode(":", $safeStart);
							//$safeEndArray = explode(":", $safeEnd);

							// If the timestamp for the ending time has more digits than the starting time, then it's obvious that the end time is after the start time - we automatically add this to the json array
							if ($end_time_seconds > $start_time_seconds ) {
								$array = array(
									"Start" => $start_time_seconds,
									"End" => $end_time_seconds,
									"Lyrics" => $lyric
								);
								$lyricsToJSON[] = $array;
							} 
							// In the case where the timestamps are equal in digit size, then we must provide a test
							else {
								/*
								// $test makes sure that the end time is verified to be after the start time - we set it to false as a default
								$test = false;
								for ($pos = 0; $pos < sizeof($safeEndArray); $pos++) {
									// We start from the biggest timestamp (i.e. minute) - if this value is greater than that of the start time, then we are golden
									// If in the case where this is not true, then we iterate to the next digit placement (i.e. seconds)
									// If that also doesn't work out, then $test will remain false, and we will throw an error
									if ( (int)$safeEndArray[$pos] > (int)$safeStartArray[$pos] ) {
										$test = true;
										break;
									}
								}
								if ($test) {
									$array = array(
										"Start" => $safeStart,
										"End" => $safeEnd,
										"Lyrics" => $lyrics[$i]
									);
									$lyricsToJSON[] = $array;
								} else {
									*/
									$success = false;
									$message = "One of your end times is less than the starting time - please look through your lyric times again";
								//}
							}
						} 
						// In the case where either the start time or end time are NOT in the proper format, we throw an error
						else {
							$success = false;
							$message = "One or more of your start and/or end times is not in the proper format (00:00) - please correct";
						}
					}
				}

				if (sizeof($lyricsToJSON) > 0) {
					$fp = fopen('../media/temp/lyrics.json', 'w');
					fwrite($fp, json_encode($lyricsToJSON));
					fclose($fp);
				}

				/*
				if ($lyrics != "") {
					$myfile = fopen("../media/temp/lyrics.txt", "w");
					fwrite($myfile, html_entity_decode($lyrics, ENT_QUOTES, "utf-8"));
					fclose($myfile);
				}
				*/

				// We now create the query used to change all the rows in the database
				$query = "UPDATE Music SET title=\"".$title."\" , artist=\"".$artist."\", album=\"".$album."\" , album_artist=\"".$album_artist."\" , url=\"".$actual_target_dir."\" , file_name=\"".$title_adjustment.".".$song_extension[sizeof($song_extension)-1]."\" , composer=\"".$composer."\" , year=\"".$year."\" , length=\"".$length."\" , comments=\"".$comments."\" WHERE music_id=".$_POST["id"];
				// We don't run this query just yet - we have to ensure that the files are rearranged properly

				$replace_song_details = array(
					"title" => $title,
					"artist" => $artist,
					"album_artist" => $album_artist,
					"composer" => $composer,
					"year" => $year,
					"comments" => $comments
				);
				$lyricsString = "<i>No lyrics detected...</i>";
				if ( sizeof($lyricsToJSON) > 0 ) {
					$lyricsString = "";
					$replace_song_details["lyrics_array"] = $lyricsToJSON;

					foreach($lyricsToJSON as $key=>$segment) {
						$lyric_part = "";
						$start_time = "";
						$end_time = "";

						$lyric_part = str_replace(["\r\n", "\r", "\n"], "<br/>", html_entity_decode($segment["Lyrics"]));
						$start_time = $segment["Start"];
						$end_time = $segment["End"];
					
						$lyricsString.="<span class=\"lyric_segment\" data-id=\"".$key."\" data-start=\"".$start_time."\" data-end=\"".$end_time."\">".$lyric_part."</span>";
					}
				}
				$replace_song_details["lyrics_string"] = $lyricsString;

				// If the title has changed, we MUST delete the old song folder and its contents
				if ($title != $row["title"]) {
					// Find all files in the original song folder, and then delete the song folder
					$files = scandir("../".$row["url"]);
					foreach ($files as $file) {
						unlink("../".$row["url"].$file);
					}
					if(!rmdir("../".$row["url"])) {
						// If we were unable to delete the song's folder, we end the process immediately
						$success = false;
						$message = "Error deleting directory";
						
						$dataToSend = array(
							"Success" => $success,
							"Message" => $message
						);
						print(json_encode($dataToSend));
						return;
					} else {
						// If deleting the song's folder was successful, we proceed with renaming the file
						// If renaming the song file was unsuccessful, we must end the process immediately
						if (!rename("../media/temp/".$row["file_name"], "../media/temp/".$title_adjustment.".".$song_extension[sizeof($song_extension)-1])) {
							$success = false;
							$message = "Could not rename song file...";

							$dataToSend = array(
								"Success" => $success,
								"Message" => $message
							);
							print(json_encode($dataToSend));
							return;
						}
					}
				}

				// If the artist has changed, we will delete the old artist folder unless if there's another song in there.
				if ($artist != $row["artist"]) {
					$artistCheck = scandir("../".$row["url"]."../");
					// If the artist folder has only 1 file and that file is the icon image, we delete the icon then we delete the artist folder
					if (sizeof($artistCheck) == 1 && $artistCheck[0] == "icon.jpg") {
						unlink("../".$row["url"]."../icon.jpg");
						if(!rmdir("../".$row["url"])) {
							// If we were unable to delete the artist folder, we end the process immediately
							$success = false;
							$message = "Error deleting artist directory";
							
							$dataToSend = array(
								"Success" => $success,
								"Message" => $message
							);
							print(json_encode($dataToSend));
							return;
						}
					}
				}
				// Now we have to make a new path to the new folder
				// If we have to create a directory, we must also create a default icon for the new directory
				if (!file_exists("../media/music/".$artist_adjustment."/")) {
					mkdir("../media/music/".$artist_adjustment."/", 0777, true);
					copy("../assets/default_album_artist.jpg", "../media/music/".$artist_adjustment."/icon.jpg");
				}

				// since the song should be unique to this particular artist, we will automatically make the directory for the song regardless
				mkdir($target_dir, 0777, true);

				// We have to clear the target directory of any files first
				$filesToDelete = scandir($target_dir);
				foreach($filesToDelete as $fileToDelete) {
					unlink($target_dir.$fileToDelete);
				}

				//Find the files and move them to the appropriate area
				$files = scandir("../media/temp/");
				$source = "../media/temp/";
				foreach ($files as $file) {
					rename($source.$file, $target_dir.$file);
				}

				//Now we run the query
				if (!$result = $mysqli->query($query)) {
					$success = false;
					$message = "File details could not be updated in the database - sucker, you have to fix some stuff";
				} else {
					$success = true;
					$message = "Yay, you're done!";
				}
			}
	}

	$dataToSend = array(
		"Success" => $success,
		"Message" => $message,
		"Replace" => $replace_song_details
	);

	print(json_encode($dataToSend));
	return;

?>