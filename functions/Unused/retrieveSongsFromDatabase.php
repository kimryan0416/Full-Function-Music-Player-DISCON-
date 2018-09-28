<?php
	session_start();
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$dataToSend = array();
	$dataToSend["Play"] = false;

	$category = filter_input( INPUT_POST, "category", FILTER_SANITIZE_STRING );

	if ($category == "Playlist") {
		$query = "SELECT * FROM Playlist LEFT JOIN playlistmusicmap INNER JOIN Music ON playlistmusicmap.music_id = Music.music_id ON playlistmusicmap.playlist_id = Playlist.playlist_id WHERE Playlist.user_id=1";
	} else {
		$query = "SELECT * FROM Music";
		if ($category == "Song") {
			$query.=" ORDER BY title";
		} else if ($category == "Artist") {
			$query.=" ORDER BY artist, title";
		} else if ($category == "Album") {
			$query.=" ORDER BY album, title";
		} else if ($category == "Album_Artist") {
			$query.=" ORDER BY album_artist, title";
		} else {
			$query.=" ORDER BY title";
		}
	}
	
	if (!$result = $mysqli->query($query)) {
		$dataToSend["Message"] = "Error receiving item information from database: ".$mysqli->error();
	} else {
		while($row=$result->fetch_assoc()) {
			if ($category == "Playlist") {
				// If the songs are grouped by playlist, then we must organize them by playlist title
				$album = $row["playlist_title"];
			} else if ($category == "Song") {
				// If songs are grouped by song alone, then each parent container is a letter, and all songs in each parent container are ordered alphabetically
				if ( strlen($row["title"]) != mb_strlen($row["title"], 'utf-8') ) { 
        			$album = "Misc";
			    } else {
        			$album = substr($row["title"], 0, 1);
    			}
			} else if ($category == "Artist") {
				// If songs are grouped and organized by artist, then each parent container is an artist, with all artists being alphabetically organized
				// song order in this case doesn't matter
				$album = $row["artist"];
			} else if ($category == "Album") {
				// If songs are organized by Album, then each parent container is an album - song order isn't considered
				$album = $row["album"];
			} else if ($category == "Album_Artist") {
				// If songs are organized by Album Artist, then each parent container is an album artist - song order isn't considered
				$album = $row["album_artist"];
			} else {
    			if ( strlen($row["title"]) != mb_strlen($row["title"], 'utf-8') ) { 
        			$album = "Misc";
			    } else {
        			$album = substr($row["title"], 0, 1);
    			}
			}
			$dataToSend["Music"][$album][] = $row;
			
			if (isset($_SESSION["Loop"])) {
				$dataToSend["Loop"] = $_SESSION["Loop"];
			} else {
				$dataToSend["Loop"] = false;
			}
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