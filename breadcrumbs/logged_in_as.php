<div id="logged_in">
	<span>Hallo 
	<?php if (admin_check($mysqli)) echo "<b><img id='img' src='images/rang_hoch.png' alt='als Betreuer' title='als Betreuer'> " . email_check($mysqli) ."</b> ";
	  else if(email_check($mysqli)) echo "<b><img id='img' src='images/user.png' alt='als Benutzer' title='als Benutzer'> " . email_check($mysqli) ."</b> "; ?>
	du bist momentan <b><?php echo $logged; ?></b>.
	</span>
	<img src="images/drucker.png" id="title_img">
</div>