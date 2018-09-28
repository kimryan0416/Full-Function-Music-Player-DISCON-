<?php
	$lyrics = $_POST["lyrics"];
	$starts = $_POST["start"];
	$ends = $_POST["end"];

	$arrayToJSON = array();

	//$lyrics = filter_var( $lyrics, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );

	for ($i = 0; $i < sizeof($lyrics); $i++) {
		$lyric = htmlspecialchars($lyrics[$i]);
		$lyric = filter_var( $lyric, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );
		//$lyric = $lyrics[$i];
		if (strlen($lyric) == 0) {
			$dataToSend["Lyrics"] = "You ballsed it up";
			print(json_encode($dataToSend));
			return;
		}

		$safeStart = htmlspecialchars($starts[$i]);
		$startVerify = preg_match('~^\d{1,2}[:]\d{2}(?::\d{2})*$~', $safeStart);

		$safeEnd = htmlspecialchars($ends[$i]);
		$endVerify = preg_match('~^\d{1,2}[:]\d{2}(?::\d{2})*$~', $safeEnd);
		if ($startVerify == 1 && $endVerify == 1) {
			$safeStartArray = explode(":", $safeStart);
			$safeEndArray = explode(":", $safeEnd);

			if (sizeof($safeEndArray) > sizeof($safeStartArray)) {
				//addToLyrics($safeStart, $safeEnd, $lyrics[$i]);
				$array = array(
					"Start" => $safeStart,
					"End" => $safeEnd,
					"Lyrics" => $lyrics[$i]
				);
				$arrayToJSON[] = $array;
			} else {
				$test = false;
				for ($pos = 0; $pos < sizeof($safeEndArray); $pos++) {
					if ( (int)$safeEndArray[$pos] > (int)$safeStartArray[$pos] ) {
						$test = true;
						break;
					}
				}
				if ($test) {
					//addToLyrics($safeStart, $safeEnd, $lyrics[$i]);
					//$lyricMessage.=$safeStart."_".$lyrics[$i]."_".$safeEnd."\n";
					$array = array(
						"Start" => $safeStart,
						"End" => $safeEnd,
						"Lyrics" => $lyrics[$i]
					);
					$arrayToJSON[] = $array;
				} else {
					$dataToSend["Lyrics"] = "You ballsed it up";
					print(json_encode($dataToSend));
					return;
				}
			}
		} else {
			$dataToSend["Lyrics"] = "You ballsed it up";
			print(json_encode($dataToSend));
			return;
		}
	}


	$fp = fopen('../results.json', 'w');
	fwrite($fp, json_encode($arrayToJSON));
	fclose($fp);

	//$lyrics = file("../".$row["url"].$file);
	//$lyrics = str_replace(["\r\n", "\r", "\n"], "<br/>", $lyrics);

	//$lyricsFINAL = "";
	$lyricsFINAL = "";

	$json_lyrics = file_get_contents("../results.json");
	$json_a = json_decode($json_lyrics, true);

	foreach($json_a as $key=>$segment) {
		$lyricFINAL = "";
		$startFINAL = "";
		$endFINAL = "";

		$lyricFINAL = $segment["Lyrics"];
		$startFINAL = $segment["Start"];
		$endFINAL = $segment["End"];
		/*
		foreach($segment as $prop=>$val) {
			if ($prop == "Start") {
				$startFINAL = $val;
			} else if ($prop == "End") {
				$endFINAL = $val;
			} else {
				$lyricFINAL = str_replace(["\r\n", "\r", "\n"], "<br/>", $val);
			}
		}
		*/
		$lyricsFINAL.="<span class=\"lyric_segment\" data-start=\"".$startFINAL."\" data-end=\"".$endFINAL."\">".$lyricFINAL."</span>";
		//$lyricsFINAL[] = $segment;
	}

	$dataToSend["Lyrics"] = $lyricsFINAL;
	print(json_encode($dataToSend));
	return;
?>