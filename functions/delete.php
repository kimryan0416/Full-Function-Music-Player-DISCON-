<?php
	session_start();
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$success = true;
	$message = "";

	if ( isset($_GET["song"]) ) {
		$id = filter_input( INPUT_POST, "id", FILTER_SANITIZE_STRING );
		
		$query = "SELECT url FROM Music WHERE music_id=".$id;
		if ( !$request = $mysqli->query($query) ) {
			$success = false;
			$message = "Unable to access database: ".$mysqli->error();
		} else {
			$row = $request->fetch_assoc();
			$url = $row["url"];
			$files = scandir("../".$url);
			foreach($files as $file) {
				unlink("../".$url.$file);
			}
			if( !rmdir("../".$url) ) {
				// If we were unable to delete the song's folder, we end the process immediately
				$success = false;
				$message = "Error deleting directory";
			} else {
				$query = "DELETE FROM Music WHERE music_id=".$id;
				if ( !$mysqli->query($query) ) {
					$success = false;
					$message = "Unable to delete song from database";
				} else {
					$query = "DELETE FROM playlistmusicmap WHERE music_id=".$id;
					if ( !$mysqli->query($query) ) {
						$success = false;
						$message = "Unable to delete music id from playlist";
					} else {
						if ( isset($_SESSION["Current_Song"]) && $_SESSION["Current_Song"] == $id ) {
							unset($_SESSION["Current_Song"]);
						}
						$success = true;
						$message = "Success!";
					}
				}
			}
		}
	}

	$dataToSend = array(
		"Success" => $success,
		"Message" => $message
	);
	print(json_encode($dataToSend));
	return;
?>