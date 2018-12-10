<?php
include_once 'includes/register.inc.php';
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Registration</title>
        <script type="text/JavaScript" src="js/sha512.js"></script> 
        <script type="text/JavaScript" src="js/forms.js"></script>
        <link rel="stylesheet" href="styles/main.css" />
        <link rel="stylesheet" href="styles/input.css" />
        <link rel="stylesheet" href="styles/register.css" />
    </head>
    <body>
		<div id='register'>
			<!-- Anmeldeformular für die Ausgabe, wenn die POST-Variablen nicht gesetzt sind
			oder wenn das Anmelde-Skript einen Fehler verursacht hat. -->
			<h1>Registrierung</h1>
			<?php
			if (!empty($error_msg)) {
				echo $error_msg;
			}
			?>
			<ul>
				<li>E-Mail-Adressen müssen ein gültiges Format haben.</li>
				<li>E-Mail-Adressen müssen HFU Endung haben (@hs-furtwangen.de).</li>
				<li>Passwörter müssen mindestens sechs Zeichen lang sein.</li>
				<li>Passwörter müssen enthalten
					<ul>
						<li>mindestens einen Großbuchstaben (A..Z)</li>
						<li>mindestens einen Kleinbuchstabenr (a..z)</li>
						<li>mindestens eine Ziffer (0..9)</li>
					</ul>
				</li>
			</ul>
			<form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" 
					method="post" 
					name="registration_form">
				<input type="text" 
						placeholder="Email Adresse"
						name="email" 
						id="email" /><br>
				<input type="password"
						placeholder="Passwort"
						name="password" 
						id="password"/><br>
				<input type="password" 
						placeholder="Passwort wiederholen"
						name="confirmpwd" 
						id="confirmpwd" /><br>
				<input type="button" style="float: right; margin-top: 10px;"
					   value="Registrieren" 
					   onclick="return regformhash(this.form,
									   this.form.email,
									   this.form.password,
									   this.form.confirmpwd);" /> 
			</form>
		</div>
        <?php include 'breadcrumbs/back_to_login.php'; ?>
    </body>
</html>
