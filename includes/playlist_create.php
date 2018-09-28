<div id="playlist_create_main_container">
	<span id="cancel_playlist_create">Cancel</span>

	<form id="playlist_create_form" name="playlist_create_form" method="POST" action="index.php">
		
		<!-- Playlist Title -->
		<div class="playlist_create_container">
			<span class="label">Song Title:</span>
			<input class='playlist_create_input' id="playlist_create_title" type="text" name='title' placeholder="Title of Playlist" required>
			<span class="error_message" id="playlist_create_title_error"></span>
		</div>

		<!-- Playlist Description-->
		<div class="playlist_create_container">
			<span class="label">Playlist Description</span>
			<textarea name="description" id="playlist_create_description" placeholder="Playlist Description"></textarea>
			<span class="error_message" id="playlist_create_description_error"></span>
		</div>

		<!-- Submit Button -->
		<input type="submit" name="playlist_create_submit" id="playlist_create_submit" value="Create Playlist">

		<!-- Error Message -->
		<span class="error_message" id="playlist_create_main_error"></span>
	</form>
</div>