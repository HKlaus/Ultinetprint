<div id='navigation_bar'>
	<a href='printer.php'>
		<div id='nav_printer' class='nav_item'>
		Status
		</div> 
	</a>
	<a href='print.php'>
		<div id='nav_printer' class='nav_item'>
		Drucken
		</div> 
	</a>
	<a href='queue.php'>
		<div id='nav_printer' class='nav_item'>
		Queue
		</div> 
	</a>
	<?php if($admin == 'angemeldet') { echo "
	<a href='manage.php'>
		<div id='nav_manage' class='nav_item'>
		Benutzer
		</div>
	</a>
	"; } ?>
</div>