<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/PHPMailer.php';

function send_verify($email, $password) {
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Set who the message is to be sent from
	$mail->setFrom('noreply@hs-furtwangen.de', 'Ultimaker Networkprint');
	//Set who the message is to be sent to
	$mail->addAddress($email, '');
	//Set the subject line
	$mail->Subject = 'Ultimaker Account Aktivierungslink';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->Body = '
	 
	Vielen Dank für die Regsitrierung!
	Der Account wurde erstellt und kann nach dem Aktivieren mit folgender Email verwendet werden:
	 
	------------------------
	Benutzername: '.$email.'
	------------------------
	 
	Bitte klicke auf diesen Link um den Account zu aktivieren:
	https://ultinetprint.informatik.hs-furtwangen.de/verify.php?email='.$email.'&hash='.$password.'
	 
	';	// Our message above including the link
	
	//send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Die Aktivierungs-Email wurde versendet!";
	}
}