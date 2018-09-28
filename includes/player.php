<div id="player_big_container">
	<div id="player">
		<div class="top" id="player_top">
			<span class="link" id="song_back_mobile">Back</span>
		</div>
		<img class="player_background" id="player_1_background" src="" alt="">
		<!-- The audio player - invisible -->
		<audio class="player" id="player_1" src="" preload="none" ontimeupdate="onTimeUpdate(this);">
			Your browser does not support the audio element.
		</audio>

		<div class="player_container" id="player_1_container">
			<div class="player_left player_side">
				<!-- Icon, title, artist, main scroll, details -->

				<!-- Player 1 Song Icon -->
				<img class='player_song_icon' id="player_1_song_icon" src="" alt="">

				<!-- The song's title and artist -->
				<span class='player_song_title' id="player_1_song_title">No song selected...</span>
				<span class='player_song_artist' id="player_1_song_artist"></span>

				<!-- Container for the song's time slider, current time, duration, and main controls -->
				<div class="time_container">
					<span class='curTime' id="curTime_1"></span>
					<input type="range" min="0" max="0" class="time_slider" id="time_slider_1">
					<span class='duration' id="duration_1"></span>
				</div>

				<!-- Controls including buttons and volume and repeat -->
				<div class="player_controls">
					<div class="volume_container player_button">
						<input type="range" min="0" max="100" value="100" class="slider" id="volume_1">
						<img class='volume_image' id="volume_image_1" src="" alt="">
					</div>
					<div class="player_buttons">
						<img class="back player_button" src='assets/back.png' alt=''>
						<img class="start player_button" src='assets/start.png' alt=''>
						<img class="pause player_button" src='assets/pause.png' alt=''>
						<img class="forward player_button" src='assets/forward.png' alt=''>
					</div>
					<div class='repeat_container'>
						<img class='repeat_button' id="repeat_button_1" src='assets/repeat_0.png' alt=''>
					</div>
				</div>

				<div class="player_details_container" id="player_1_details_container">
					<table class="player_details_table">
						<tr>
							<td class="detail_left">Song Title:</td>
							<td class="detail_right" id="player_1_title"></td>
						</tr>
						<tr>
							<td class="detail_left">Song Artist:</td>
							<td class="detail_right" id="player_1_artist"></td>
						</tr>
						<tr>
							<td class="detail_left">Album:</td>
							<td class="detail_right" id="player_1_album"></td>
						</tr>
						<tr>
							<td class="detail_left">Album Artist:</td>
							<td class="detail_right" id="player_1_album_artist"></td>
						</tr>
						<tr>
							<td class='detail_left'>Year:</td>
							<td class='detail_right' id="player_1_year"></td>
						</tr>
						<tr>
							<td class='detail_left'>Composer:</td>
							<td class='detail_right' id="player_1_composer"></td>
						</tr>
						<tr>
							<td class='detail_left'>Play Count:</td>
							<td class='detail_right' id="player_1_playcount"></td>
						</tr>
					</table>
					<p id="player_1_comments"></p>
					<span class="player_edit" id="player_1_edit" data-src="">Click to Edit Details</span>
				</div>
			</div>
			<div class="player_right player_side">
				<!-- Lyrics -->
				<p class="player_lyrics" id="player_1_lyrics"></p>
			</div>
		</div>
	</div>
</div>