<?php
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$dataToSend = array();
	$success = false;
	$message = "null";


	if (isset($_GET["create"])) {
		$title= filter_input( INPUT_POST, "title", FILTER_SANITIZE_STRING );
		$description= filter_input( INPUT_POST, "description", FILTER_SANITIZE_STRING );

		$query = "INSERT INTO Playlist (playlist_title, playlist_description, user_id) VALUES (\"".$title."\", \"".$description."\", 1)";
		if (!$mysqli->query($query)) {
			$message = "Failed to insert playlist into DB: ".$mysqli->error();
		} else {
			$success = true;
			$message = "Success!";
		}
	}

	$dataToSend = array(
		"Success" => $success,
		"Message" => $message
	);
	print(json_encode($dataToSend));
	return;

?>