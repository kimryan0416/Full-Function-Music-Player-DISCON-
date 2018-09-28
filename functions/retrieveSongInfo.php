<?php
	require("config.php");
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$dataToSend = array(
		"Success" => false,
		"Error" => "No request sent"
	);

	if (isset($_POST["id"])) {
		$query = "SELECT * FROM Music WHERE music_id=".$_POST["id"];
		if (!$result = $mysqli->query($query)) {
			$dataToSend = array(
				"Success" => false,
				"Error" => "Error receiving item information from database: ".$mysqli->error()
			);
		} else {
			$row = $result->fetch_assoc();
			$dataToSend = $row;

			$lyricsToJSON = array();

			if (file_exists ( "../".$row["url"]."lyrics.json" )) {
				$json_lyrics = file_get_contents("../".$row["url"]."lyrics.json");
				$json_a = json_decode($json_lyrics, true);

				foreach($json_a as $key=>$segment) {
					$lyric_part = "";
					$start_time = "";
					$end_time = "";

					$lyric_part = html_entity_decode($segment["Lyrics"]);
					$start_time = $segment["Start"];
					$end_time = $segment["End"];
				
					$lyricsToJSON[$key] = array(
						"Start" => $start_time,
						"End" => $end_time,
						"Lyrics" => $lyric_part
					);
				}

			} else {
				$lyricsToJSON[0] = array(
					"Start" => "",
					"End" => "",
					"Lyrics" => ""
				);
			}
			$dataToSend["lyrics"] = $lyricsToJSON;
			$dataToSend["title"] = html_entity_decode($row["title"], ENT_QUOTES, "utf-8");
			$dataToSend["comments"] = html_entity_decode($row["comments"], ENT_QUOTES, "utf-8");
			$dataToSend["Success"] = true;
		}
	}

	print(json_encode($dataToSend));
	return;
?>