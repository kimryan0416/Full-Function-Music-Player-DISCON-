<?php
	$TextEncoding = "UTF-8";
	require_once('getid3/getid3.php');
	// Initialize getID3 engine
	$getID3 = new getID3;
	$getID3->setOption(array('encoding'=>$TextEncoding));

	$dataToSend = array();
	$error = true;	//initialize the error to be true - this page should only run when files are uploaded
    $message = "No files submitted";
    $ext = "";

    $music_types = array("mp3", "m4a");
    //$image_types = array("jpg", "JPG");

	// Detects if the proper files were sent via AJAX and not some malicious other way

    /*
	if(isset($_GET['song']) || isset($_GET["icon"])) {
		
		
		foreach($_FILES as $file) {
			$dataToSend[] = basename($file["name"]);
		}

		$upload_dir = '../media/temp/';	
		//We temporary store the icon into the "temp" folder; we'll grab it later when we process the other information in the other AJAX call

		// We clear the temp folder... when the song first is uploaded, because any other case would delete already-existing files
		if (isset($_GET["song"])) {
			//In the meanwhile, we must empty the temp directory
			$toDelete = scandir($upload_dir);
			foreach($toDelete as $fileToDelete) {
				unlink($upload_dir.$fileToDelete);
			}
		}
		
		if (isset($_GET["icon"]) && empty($_FILES)) {
			copy("../assets/default_album_artist.jpg", "../media/temp/icon.jpg");
			$error = false;
			$message = "Had to move default icon to use as the song's icon";

			$dataToSend = array(
			    "Error" => $error, 
			   	"Message" => $message
		    );

			print(json_encode($dataToSend));
			return;
		}

		foreach($_FILES as $file) {
				$ext = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
				if (isset($_GET["icon"])) {
					$target_file = $upload_dir . "icon.jpg";
				} else {
					$target_file = $upload_dir . "song." . $ext;
				}
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				if (isset($_GET["icon"])) {
					$check = getimagesize($file["tmp_name"]);
				} else {
					$check == true;
				}

			    if($check !== false) {
			    	if (isset($_GET["song"])) {
						if(!in_array($ext,$music_types) ) {
							$error = true;
						    $message = "Music file is not mp3 or m4a";
						} else {
							if (move_uploaded_file($file['tmp_name'], $target_file)) {
					    		$error = false;
					    		$message = "No Errors!";
					    		$file = $ext;
					    	} else {
					    		$error = true;
					    		$message = "Upload Error";
					    	}
						}
					} else {
						if(!in_array($ext,$image_types) ) {
							$error = true;
						    $message = "Image file is not jpg";
						} else {
							if (copy($file['tmp_name'], $target_file)) {
					    		$error = false;
					    		$message = "No Errors!";
					    	} else {
					    		$error = true;
					    		$message = "Upload Error";
					    	}
						}
					}
			    } else {
			        $error = true;
			        $message = "Check False";
			    } 
			}
			
	} */


	if (isset($_GET["song"])) {
		$upload_dir = '../media/temp/';	
		//We temporary store the icon into the "temp" folder; we'll grab it later when we process the other information in the other AJAX call

		// We clear the temp folder... when the song first is uploaded, because any other case would delete already-existing files
		if (isset($_GET["song"])) {
			//In the meanwhile, we must empty the temp directory
			$toDelete = scandir($upload_dir);
			foreach($toDelete as $fileToDelete) {
				unlink($upload_dir.$fileToDelete);
			}
		}

		foreach($_FILES as $file) {
			// Extension of file - i.e. mp3, or m4a
			$ext = pathinfo(basename($file["name"]), PATHINFO_EXTENSION);
			
			// Set upload directory to temp file in media
			$target_file = $upload_dir . "song." . $ext;
				
			// Get file type of audio file
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

			// Check if file type is an audio file
			if(!in_array($ext,$music_types) ) {
				$error = true;
			    $message = "Music file is not mp3 or m4a";
			} else {

				// We move the file temporarily to the temp file, for pickup later
				if (copy($file['tmp_name'], $target_file)) {
					// Analyze the metadata of the song file - we can use this as a base to add to the database
					$ThisFileInfo = $getID3->analyze($target_file);
					getid3_lib::CopyTagsToComments($ThisFileInfo);
					//$fileInfo = $ThisFileInfo['comments_html'];

					$length = $ThisFileInfo['playtime_string']; 

					$title = $album = $artist = $album_artist = "";
					if ( isset($ThisFileInfo['comments_html']['title']) ) {
						$title = $ThisFileInfo['comments_html']['title'][0];
					}
					if ( isset($ThisFileInfo['comments_html']['artist']) ) {
						$artist = $ThisFileInfo['comments_html']['artist'][0];
					}
					if ( isset($ThisFileInfo['comments_html']['album']) ) {
						$album = $ThisFileInfo['comments_html']['album'][0];
					}
					if ( isset($ThisFileInfo['comments_html']['album_artist']) ) {
						$album_artist = $ThisFileInfo['comments_html']['album_artist'][0];
					}

					$composer = $year = $comment = $lyrics = "";
					if ( isset($ThisFileInfo['comments_html']['composer']) ) {
						$composer = $ThisFileInfo['comments_html']['composer'][0];
					}
					if ( isset($ThisFileInfo['comments_html']['creation_date']) ) {
						$year_data = explode("-", $ThisFileInfo['comments_html']['creation_date'][0]);
						$year = $year_data[0];
					}
					if ( isset($ThisFileInfo['comments_html']['comment']) ) {
						$comment = $ThisFileInfo['comments_html']['comment'][0];
					}
					if ( isset($ThisFileInfo['comments_html']['lyrics']) ) {
						$lyrics = $ThisFileInfo['comments_html']['lyrics'][0];
					}

					//If lyrics were given, we create a "lyrics.txt" file inside /temp/ containing the lyrics
					if ($lyrics != "") {
						$myfile = fopen("../media/temp/lyrics.txt", "w");
						fwrite($myfile, html_entity_decode($lyrics, ENT_QUOTES, "utf-8"));
						fclose($myfile);
					}

					$error = false;
		    		$message = array(
		    			"Title" => $title,
		    			"Artist" => $artist,
		    			"Album" => $album,
		    			"Album_Artist" => $album_artist,
		    			"Composer" => $composer,
		    			"Year" => $year,
		    			"Comment" => $comment,
		    			"Lyrics" => $lyrics,
		    			"Length" => $length,
		    			"Extension" => $ext
		    		);
					$file = $ext;

					copy("../assets/default_album_artist.jpg", "../media/temp/icon.jpg");
		    	} else {
					$error = true;
		    		$message = "Upload Error";
		    	}
			}
		}
	}

	
	$dataToSend = array(
	    "Error" => $error, 
	   	"Message" => $message,
	 	"Song_Extension" => $ext
    );

	print(json_encode($dataToSend));
	return;



?>