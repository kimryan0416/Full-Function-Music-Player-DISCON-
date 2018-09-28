<div id="upload_container">
	<form id="song_upload_form" name="song_upload_form" method="POST" enctype="multipart/form-data" action="index.php">
		<div id="upload_form_append">
			<div class="form_container">
				<div class="form_main_container">
					<span class="form_label">Song Upload:</span>
					<input class='song_file' type="file" name="song_upload" required>
				</div>
				<div class="form_inner_container">
					<span class="form_label">Title:</span>
					<input class='upload_input' id="upload_title" type="text" name='title' placeholder="Title of Song" required>
				</div>
				<div class="form_inner_container">
					<span class="form_label">Artist:</span>
					<input class='upload_input' id="upload_artist" type="text" name='artist' placeholder="Artist" required>
				</div>
				<div class="form_inner_container">
					<span class="form_label">Album:</span>
					<input class='upload_input' id="upload_album" type="text" name='album' placeholder="Album" required>
				</div>
				<div class="form_inner_container">
					<span class="form_label">Album Artist:</span>
					<input class='upload_input' id="upload_album_artist" type="text" name='album_artist' placeholder="Album Artist" required>
				</div>
				<input type="hidden" id="upload_comment" name="comment" value="">
				<input type="hidden" id="upload_year" name="year" value="">
				<input type="hidden" id="upload_composer" name="composer" value="">
				<input type="hidden" id="upload_length" name="length" value="">
				<input type="hidden" id="upload_extension" name="extension" value="">

				<span class="error" id="song_error_message"></span>
			</div>
		</div>
		<!--<span id="upload_add">Add Another Song</span>-->
		<input type="submit" name="upload_submit" value="Upload Song" id="upload_submit">
	</form>
</div>