<div id='navigation_bar'>
	<a href='printer.php'>
		<div id='nav_printer' class='nav_item'>
		Drucker
		</div> 
	</a>
	<a href='queue.php'>
		<div id='nav_printer' class='nav_item'>
		Aufträge
		</div> 
	</a>
	<?php if($admin == 'angemeldet') { echo "
	<a href='manage.php'>
		<div id='nav_manage' class='nav_item'>
		Verwaltung
		</div>
	</a>
	"; } ?>
</div>