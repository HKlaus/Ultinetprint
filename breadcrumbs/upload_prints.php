<form method="post" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
	<input name="file_to_serv" type="file" required="required"/>
	<input type="submit" value="Upload" />
</form>