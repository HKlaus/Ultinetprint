<form method="post" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
	<input name="file_to_serv" id="file_to_serv" type="file" required="required" class="inputfile" data-multiple-caption="{count} files selected" multiple accept=".gcode,.gcode.gz"/>
	<label id="upload" for="file_to_serv"><span>Datei auswählen (.gcode)</span></label>
	<input type="submit" value="Upload" />
</form>

<script type="text/JavaScript" src="js/uploadbutton.js"></script> 