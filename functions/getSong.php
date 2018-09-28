<?php
	session_start();

	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$response = array();
	$response["Success"] = false;	// default set to false - only returns true when it successfully finds a song

	$songID = filter_input( INPUT_POST, "songID", FILTER_SANITIZE_STRING );

	$query = "SELECT * FROM Music WHERE music_id=".$songID;
	if (!$result = $mysqli->query($query)) {
		$response["Message"] = "Error receiving data from Database: ".$mysqli->error();
	} else {
		$row = $result->fetch_assoc();
		$response = $row;
		$files = scandir("../".$row["url"]);
		$lyrics = "<i>No Lyrics Detected...</i>";
		$lyrics_array = array();
		$lyrics_array[] = array(
			"Start" => 0,
			"End" => 300,
			"Lyrics" => ""
		);
		foreach($files as $file) {
			//if ($file == "lyrics.txt") {
			if ($file == "lyrics.json") {
				//$lyrics = file("../".$row["url"].$file);
				//$lyrics = str_replace(["\r\n", "\r", "\n"], "<br/>", $lyrics);

				$lyrics_array = array();
				$lyrics = "";

				$json_lyrics = file_get_contents("../".$row["url"].$file);
				$json_a = json_decode($json_lyrics, true);
				
				foreach($json_a as $key=>$segment) {
					$lyric = "";
					$start = "";
					$end = "";

					$lyric = str_replace(["\r\n", "\r", "\n"], "<br/>", html_entity_decode($segment["Lyrics"]));
					$start = $segment["Start"];
					$end = $segment["End"];
					
					$lyrics.="<span class=\"lyric_segment\" data-id=\"".$key."\" data-start=\"".$start."\" data-end=\"".$end."\">".$lyric."</span>";

					$lyrics_array[] = array(
						"Start" => $start,
						"End" => $end,
						"Lyrics" => $lyric
					);
				}

			} else if ( image_type_to_mime_type(exif_imagetype("../".$row["url"].$file)) == "image/jpeg" || image_type_to_mime_type(exif_imagetype("../".$row["url"].$file)) == "image/png" ) {
				$response["icon"] = $row["url"].$file;
			}
		}
		$title_adjustment = html_entity_decode($row["title"], ENT_QUOTES, "utf-8");
		$response["title"] = $title_adjustment;
		$response["lyrics_string"] = $lyrics;
		$response["lyrics_array"] = $lyrics_array;
		$response["Success"] = true;

		$_SESSION["Current_Song"] = $row["music_id"];		// Saves the current song into $_SESSION, in the case the page is reloaded by accident
		$response["Message"] = $_SESSION["Current_Song"];
	}

	print(json_encode($response));
	return;
?>