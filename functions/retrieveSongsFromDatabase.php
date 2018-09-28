<?php
	session_start();
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$dataToSend = array();
	$dataToSend["Play"] = false;


	if (isset($_GET["test"])) {
		//$category = filter_input( INPUT_POST, "category", FILTER_SANITIZE_STRING );

		/* First get song list items */
		$categories = array(
			"title" => "SELECT * FROM Music ORDER BY title",
			"artist" => "SELECT * FROM Music ORDER BY artist, title",
			"album" => "SELECT * FROM Music ORDER BY album, title",
			"album_artist" => "SELECT * FROM Music ORDER BY album_artist, title",
			"playlist_title" => "SELECT * FROM Playlist LEFT JOIN playlistmusicmap INNER JOIN Music ON playlistmusicmap.music_id = Music.music_id ON playlistmusicmap.playlist_id = Playlist.playlist_id WHERE Playlist.user_id=1"
		);

		foreach($categories as $cat=>$query) {
			if ( !$result = $mysqli->query($query) ) {
				$dataToSend["Success"] = false;
				$dataToSend["Error"] = "Error receiving item information from database: ".$mysqli->error();
				print(json_encode($dataToSend));
				return;
			} else {
				while($row=$result->fetch_assoc()) {
					if ($cat == "title") {
						if ( strlen($row[$cat]) != mb_strlen($row[$cat], 'utf-8') ) { 
		        			$album = "Misc";
					    } else {
		        			$album = substr($row[$cat], 0, 1);
		    			}
					} else {
						$album = $row[$cat];
					}
					$dataToSend["Music"][$cat][$album][] = $row;
				}
			}
		}
		if (isset($_SESSION["Loop"])) {
			$dataToSend["Loop"] = $_SESSION["Loop"];
		} else {
			$dataToSend["Loop"] = false;
		}

		if (isset($_SESSION["Current_Song"])) {
			$dataToSend["Play"] = true;
			$query = "SELECT * FROM Music WHERE music_id=".$_SESSION["Current_Song"];
			$result = $mysqli->query($query);
			$row = $result->fetch_assoc();
			$dataToSend["Song"] = $row;

			$lyrics_array = array();
			$lyrics_array[] = array(
				"Start" => 0,
				"End" => 300,
				"Lyrics" => ""
			);
			$lyrics_string = "<i>No lyrics detected...</i>";
			if (file_exists ( "../".$row["url"]."lyrics.json" )) {
				$lyrics_string = "";
				$lyrics_a = file_get_contents("../".$row["url"]."lyrics.json");
				$lyrics_array = json_decode($lyrics_a, true);
				foreach($lyrics_array as $key=>$segment) {

					$lyric_part = "";
					$start_time = "";
					$end_time = "";

					$lyric_part = str_replace(["\r\n", "\r", "\n"], "<br>", html_entity_decode($segment["Lyrics"]));
					
					$start_time = $segment["Start"];
					$end_time = $segment["End"];
				
					$lyrics_string.="<span class=\"lyric_segment\" data-id=\"".$key."\" data-start=\"".$start_time."\" data-end=\"".$end_time."\">".$lyric_part."</span>";
					
				}
			}
			$dataToSend["Song"]["lyrics_string"] = $lyrics_string;
			$dataToSend["Song"]["lyrics_array"] = $lyrics_array;

			$files = scandir("../".$row["url"]);
			foreach($files as $file) {
				if ( image_type_to_mime_type(exif_imagetype("../".$row["url"].$file)) == "image/png" || image_type_to_mime_type(exif_imagetype("../".$row["url"].$file)) == "image/jpeg" ) {
        			$dataToSend["Song"]["icon"] = $row["url"].$file;
        			break;
        		}
			}
			
		}
	}

	print(json_encode($dataToSend));
	return;
?>