<?php
	require_once('getid3/getid3.php');

	// Initialize getID3 engine
	$getID3 = new getID3;
	$filename="../media/temp/song.m4a";

	// Analyze file and store returned data in $ThisFileInfo
	$ThisFileInfo = $getID3->analyze($filename);
	getid3_lib::CopyTagsToComments($ThisFileInfo);

	print(json_encode($ThisFileInfo['comments_html']));
	return;
?>