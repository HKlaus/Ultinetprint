<!-- Das Upload-Formular für Druckaufträge zum einbetten auf Websiten -->
<form method="post" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">																				
	<input name="file_to_serv" id="file_to_serv" type="file" required="required" class="inputfile" accept=".gcode,.gcode.gz"/>
	<label id="upload" for="file_to_serv"><span><img id='file' src='images/datei.png' alt='' title='Datei'> 3D-Modell auswählen (.gcode)</span></label>
	<input type="submit" value="Upload"  onclick="loading_screen();"/>
</form>
<script type="text/JavaScript" src="js/uploadbutton.js"></script> 