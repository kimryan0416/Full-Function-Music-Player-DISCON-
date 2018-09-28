<div id="song_edit">
	<form id="song_edit_form" name="song_edit_form" method="POST" enctype="multipart/form-data" action="index.php">
		<div id='song_edit_container'>
			<div id="song_edit_nav">
				<span class="song_edit_nav_button song_edit_nav_opened" data-id="details">Main Details</span>
				<span class="song_edit_nav_button" data-id="artwork">Artwork</span>
				<span class="song_edit_nav_button" data-id="lyrics">Lyrics</span>
			</div>

			<div class="song_edit_main song_edit_opened" id='song_edit_text_container'>
				<!-- Song Title -->
				<div class='form_main_container'>
					<span class="form_label">Song Title:</span>
					<input class='upload_input' id="edit_title" type="text" name='title' placeholder="Title of Song" required>
				</div>
				<!-- Song Artist -->
				<div class='form_inner_container'>
					<span class="form_label">Artist:</span>
					<input class='upload_input' id="edit_artist" type="text" name="artist" placeholder="Song Artist" required>
				</div>
				<!-- Song Album -->
				<div class="form_inner_container">
					<span class="form_label">Album:</span>
					<input class='upload_input' id="edit_album" type="text" name="album" placeholder="Album" required>
				</div>
				<!-- Song Album Artist -->
				<div class='form_inner_container'>
					<span class="form_label">Album Artist:</span>
					<input class='upload_input' id="edit_album_artist" type="text" name="album_artist" placeholder="Album Artist" required>
				</div>
				<!-- Song Composer -->
				<div class="form_inner_container">
					<span class="form_label">Composer:</span>
					<input class='upload_input' id="edit_composer" type="text" name="composer" placeholder="Composer">
				</div>
				<!-- Song Year -->
				<div class='form_inner_container'>
					<span class="form_label">Year of Release:</span>
					<input class='upload_input' id="edit_year" type="number" name="year" placeholder="Year of Release">
				</div>
				<!-- Song Length -->
				<div class='form_inner_container'>
					<span class="form_label">Audio Length:</span>
					<input class='upload_input' id="edit_length" type="text" name="length" placeholder="Length">
				</div>
				<!-- Song Comments -->
				<div class='form_main_container'>
					<span class="form_label">Comment:</span>
					<textarea name="comments" id="edit_comments" placeholder="Comments"></textarea>
				</div>
			</div>

			<div class="song_edit_main" id='song_edit_icon_main_container'>
				<div id='song_edit_icon_container'>
					<img id="edit_image" src='assets/default_album_artist.jpg' alt=''>
				</div>
				<input type="file" name="icon_upload" id="icon_edit">
				<span class="upload_text error" id="edit_icon_error"></span>
			</div>

			<div class="song_edit_main" id="song_edit_lyrics_container">
				<!-- Song Lyrics -->
				<span class="song_edit_text">Lyrics</span>
				<div id="append_song_lyrics"></div>
				<!--<span class="song_edit_button" id="add_lyric_segment">Add New Segment</span>-->
			</div>
			
			<span id="edit_message"></span>
		</div>
		<input type="submit" name="song_edit_submit" value="Edit Song Details" id="song_edit_submit">
	</form>
</div>