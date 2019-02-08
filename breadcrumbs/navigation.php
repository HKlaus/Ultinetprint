<!-- Das Navigations-Menü zum einbetten auf Websiten -->
<?php $page_url = $_SERVER['REQUEST_URI']; $page = substr($page_url, 1, strpos($page_url, ".") - 1); ?>
<div id='navigation_bar'>
	<a href='printer.php'>
		<div <?php if ($page == "printer") echo "id='nav_printer'"; ?> class='nav_item'>
		Status
		</div> 
	</a>
	<a href='print.php'>
		<div <?php if ($page == "print") echo "id='nav_print'"; ?> class='nav_item'>
		Drucken
		</div> 
	</a>
	<a href='queue.php'>
		<div <?php if ($page == "queue") echo "id='nav_queue'"; ?> class='nav_item'>
		Queue
		</div> 
	</a>
	<?php if($admin == 'angemeldet' and $page != "users") echo "
	<a href='users.php'>
		<div class='nav_item'>
		Benutzer
		</div>
	</a>
	"; elseif ($admin == 'angemeldet' and $page == "users") echo "
	<a href='users.php'>
		<div id='nav_users' class='nav_item'>
		Benutzer
		</div>
	</a>
	";  ?>
</div>