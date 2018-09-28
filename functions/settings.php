<?php
	session_start();
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$dataToSend = array();

	/* --- Changing Settings --- */

	//Changing Playcount on DB
	if (isset($_GET["setPlayCount"])) {
		$query = "UPDATE Music SET play_count = play_count + 1 WHERE music_id=".$_POST["value"];
		if (!$mysqli->query($query)) {
			$dataToSend["Success"] = false;
			$dataToSend["Message"] = "Could not access database... $mysqli->error()";
		} else {
			$query = "SELECT play_count FROM MUSIC WHERE music_id=".$_POST["value"];
			if (!$result=$mysqli->query($query)) {
				$dataToSend["Success"] = false;
				$dataToSend["Message"] = "Updated database, but could not get current playcount...";
			} else {
				$row = $result->fetch_assoc();
				$dataToSend["Success"] = true;
				$dataToSend["Num"] = $row["play_count"];
			}
		}
	} 
	// Changing the state of paused audio - is the audio paused or not?
	else if ( isset($_GET["play"]) ) {
		$_SESSION["Play"] = $_POST["value"];
		$dataToSend["Success"] = true;
 	} 
 	// Changing the state of loop - is the audio supposed to loop or not?
 	else if ( isset($_GET["loop"])) {
		$_SESSION["Loop"] = $_POST["value"];
		$dataToSend["Success"] = true;
 	} 
 	// Changing the Current Category of the song list - should the song list be organized by song, artist, album, etc?
 	else if (isset($_GET["currentCategory"])) {
 		$_SESSION["Category"] = $_POST["value"];
 		$dataToSend["Success"] = true;
 	}



 	/* --- Retrieving settings from SESSION --- */

 	else if (isset($_GET["get"])) {
		// Current settings:
		// Loop - default = false
		if (isset($_SESSION["Loop"])) {
			$dataToSend["Loop"] = $_SESSION["Loop"];
		} else {
			$dataToSend["Loop"] = false;
		}

		// Should the audio play upon startup? default = 0 = pause
		if ( isset($_SESSION["Play"]) ) {
			$dataToSend["Play"] = $_SESSION["Play"];
		} else {	
			$dataToSend["Play"] = 0;
		}

		// Which tab is open (songs, sort by album, sort by artist, or sort by album artist)
		if ( isset($_SESSION["Category"]) ) {
			$dataToSend["Category"] = $_SESSION["Category"];
		} else {
			$dataToSend["Category"] = "Song";
		}

	} 


	print(json_encode($dataToSend));
	return;

?>