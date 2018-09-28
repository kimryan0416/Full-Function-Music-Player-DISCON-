<?php
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	$dataToSend = array();
	$error = true;	//initialize the error to be true - this page should only run when files are uploaded
    $message = "No form data detected";

	// Detects if the proper files were sent via AJAX and not some malicious other way
	if(isset($_GET["data"])) {
		$message = "";

		$title = filter_input( INPUT_POST, "title", FILTER_SANITIZE_STRING );
		$artist = filter_input( INPUT_POST, "artist", FILTER_SANITIZE_STRING );
		$album_artist = filter_input( INPUT_POST, "album_artist", FILTER_SANITIZE_STRING );
		$album = filter_input( INPUT_POST, "album", FILTER_SANITIZE_STRING );
		
		$composer = filter_input( INPUT_POST, "composer", FILTER_SANITIZE_STRING );
		$year = filter_input( INPUT_POST, "year", FILTER_SANITIZE_STRING );
		$comment = filter_input( INPUT_POST, "comment", FILTER_SANITIZE_STRING );
		$lyrics = filter_input( INPUT_POST, "lyrics", FILTER_SANITIZE_STRING );
		$length = filter_input( INPUT_POST, "length", FILTER_SANITIZE_STRING );
		$song_extension = filter_input( INPUT_POST, "extension", FILTER_SANITIZE_STRING );
		// Song file is located in the media/temp/ directory - this needs to be picked up eventually
	
		// Fix spaces inside the title and artist among other strings for database storage and file creation
		$title_adjustment = html_entity_decode($title, ENT_QUOTES, "utf-8");	
			$title_adjustment = preg_replace('/[^\w\s]+/u','' ,$title_adjustment);
			$title_adjustment = str_replace(" ", "_", $title_adjustment);		// Used for folder naming and song naming
		$artist_adjustment = html_entity_decode($artist, ENT_QUOTES, "utf-8");
			$artist_adjustment = preg_replace('/[^\w\s]+/u','' ,$artist_adjustment);
			$artist_adjustment = str_replace(" ", "_", $artist_adjustment);	// Used for folder naming

		// New target directory - actual_target is the actual link, while just target is used for moving files
		$actual_target_dir = "media/music/".$artist_adjustment."/".$title_adjustment."/";
		$target_dir = "../media/music/".$artist_adjustment."/" . $title_adjustment."/";
		
		// Verify if this song already exists in the database
		$query = "SELECT * FROM Music WHERE title=\"".$title."\" AND artist=\"".$artist."\" AND album_artist=\"".$album_artist."\" AND url=\"".$actual_target_dir."\"";
		if (!$result = $mysqli->query($query)) {
			$message = "MYSQLI Error: $mysqli->error()";
			$dataToSend = array(
			    "Error" => $error, 
			   	"Message" => $message
		    );

			print(json_encode($dataToSend));
			return;

		} else {
			$rowcount=mysqli_num_rows($result);
			if ($rowcount > 0) {
				$message = "This file already exists in the database";
			} else {
				$query = "INSERT INTO Music (title, artist, album, album_artist, url, file_name, composer, year, length, comments) VALUES (\"".$title."\" , \"".$artist."\", \"".$album."\" , \"".$album_artist."\" , \"".$actual_target_dir."\" , \"".$title_adjustment.".".$song_extension."\" , \"".$composer."\" , \"".$year."\" , \"".$length."\", \"".$comment."\")";
				
				if (!$mysqli->query($query)) {
					$message = "MYSQLI Error: $mysqli->error()";
					$dataToSend = array(
					    "Error" => $error, 
					   	"Message" => $message
				    );

					print(json_encode($dataToSend));
					return;
				} else {
					$error = false;
					// At this point, the entry has been added to the database - all that is needed is to move the files to their respective places and reload the music player
				
					
					//Change the name of the music file in /temp/ to the appropriate format
					if ( !rename(
						"../media/temp/song.".$song_extension, "../media/temp/".$title_adjustment.".".$song_extension
					) ) {
						$message = "Could not rename song file... Original File Name: ../media/temp/song.".$song_extension . " || New File Name: ../media/temp/".$title_adjustment.".".$song_extension;
					}

					// Check if these directories exist or not
					// If we have to create a directory, we must also create a default icon for the new directory
					if (!file_exists("../media/music/".$artist_adjustment."/")) {
						mkdir("../media/music/".$artist_adjustment."/", 0777, true);
						copy("../assets/default_album_artist.jpg", "../media/music/".$artist_adjustment."/icon.jpg");
					}
					// since the song should be unique to this particular artist, we will automatically make the directory for the song regardless
					mkdir($target_dir, 0777, true);

					//Find the files and move them to the appropriate area
					$files = scandir("../media/temp/");
					$source = "../media/temp/";
					foreach ($files as $file) {
						rename($source.$file, $target_dir.$file);
					}
				}
				
			}
		}
	} 

	$dataToSend = array(
	    "Error" => $error, 
	   	"Message" => $message
    );

	print(json_encode($dataToSend));
	return;
?>