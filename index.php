<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Music Player Demo</title>
		<link rel="stylesheet" type="text/css" href="styles/all.css">
		<script src="scripts/jquery-3.1.1.min.js"></script>
		<script src="scripts/audio-metadata.min.js"></script>
		<script src="scripts/main.js"></script>
	</head>
	<body>
		<div id="main_container">
			<div class="top" id="main_top">
				<span class="link" id="upload_link" data-src="Upload">Upload</span>
				<!-- Top player located on top of the screen for easy access -->
				<div id="top_player">
					<img id="player_top_song_icon" src="" alt="">
					<div id="player_top_contents">
						<div id="player_top_text">
							<span id="player_top_song_artist">---</span>
							<span id="player_top_song_title">---</span>
						</div>
						<div id="player_top_time_container">
							<span class='player_top_time_text' id="curTime_top">--:--</span>
							<input type="range" min="0" max="0" id="time_slider_top">
							<span class='player_top_time_text' class='duration' id="duration_top">--:--</span>
						</div>
					</div>
					<div id="player_top_extras">
						<div id="player_top_volume_container">
							<input type="range" min="0" max="100" value="100" class="slider" id="volume_top">
							<img class='volume_image' id="volume_image_top" src="" alt="">

							<img class='repeat_button volume_icon_other' id="repeat_button_top" src='assets/repeat_0.png' alt=''>
						</div>
						<div id="player_top_controls">
							<img class="back" src='assets/back.png' alt=''>
							<img class="start" src='assets/start.png' alt=''>
							<img class="pause" src='assets/pause.png' alt=''>
							<img class="forward" src='assets/forward.png' alt=''>
						</div>
					</div>
				</div>
				<span class="link" id="song_forward_mobile">Player</span>
			</div>
			<div id="submain">
				<!-- Song_List: Main div that contains the songs - empty because Jquery fills it out -->
				<div id="song_list">
					<div class="song_list_cat" id="song_list_title" data-id="Song"></div>
					<div class="song_list_cat" id="song_list_artist" data-id="Artist"></div>
					<div class="song_list_cat" id="song_list_album" data-id="Album"></div>
					<div class="song_list_cat" id="song_list_album_artist" data-id="Album_Artist"></div>
					<div class="song_list_cat" id="song_list_playlist_title" data-id="Playlist"></div>
				</div>
				<!-- The container that contains the form for song upload -->
				<?php include("includes/upload_container.php"); ?>
				<!-- The container that contains the form for song editing -->
				<?php include("includes/song_edit.php"); ?>
				<!-- The container that contains the form for playlist creation -->
				<?php include("includes/playlist_create.php"); ?>
			</div>
			<div id="bottom">
				<span class="link" id="top_songs" data-src="Song">Songs</span>
				<span class="link" id="top_artists" data-src="Artist">Artists</span>
				<span class="link" id="top_albums" data-src="Album">Albums</span>
				<span class="link" id="top_album_artists" data-src="Album_Artist">Album Artists</span>
				<!--<span class="link" id="top_playlists" data-src="Playlist">Playlists</span>-->
			</div>
		</div>

		<?php include("includes/player.php"); ?>
		
	</body>
</html>