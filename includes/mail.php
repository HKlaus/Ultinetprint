<?php

use PHPMailer\PHPMailer\PHPMailer;

require '/var/www/html/PHPMailer/src/PHPMailer.php';
require '/var/www/html/PHPMailer/src/SMTP.php';



function send_verify($email, $hash) {
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	$mail->Host = 'localhost';
	
	//Set who the message is to be sent from
	$mail->setFrom('noreply@hs-furtwangen.de', 'Ultimaker Networkprint');
	//Set who the message is to be sent to
	$mail->addAddress($email, '');
	//Set the subject line
	$mail->Subject = 'Ultimaker Account Aktivierung';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->Body = '
	 
	Vielen Dank fuer die Regsitrierung!
	Der Account wurde erstellt und kann nach dem Aktivieren mit folgender Email oder Benutzernamen verwendet werden:
	 
	------------------------
	
	Benutzer: '. substr($email, 0, strpos($email, "@")) .'
	E-Mail: '.$email.'
	
	------------------------
	 
	Bitte klicke auf diesen Link um den Account zu aktivieren:
	https://ultinetprint.informatik.hs-furtwangen.de/verify.php?email='.$email.'&hash='.$hash.'
	 
	';	// Our message above including the link
	
	//send the message, check for errors
	
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		header('Location: ./index.php?success=1');
	}
}

function send_notify($email, $print, $event) {
	
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	$mail->Host = 'localhost';
	
	//Set who the message is to be sent from
	$mail->setFrom('noreply@hs-furtwangen.de', 'Ultimaker Networkprint');
	//Set who the message is to be sent to
	$mail->addAddress($email, '');
	//Set the subject line
	$mail->Subject = 'Ultimaker Benachrichtigung';
	$mail->Body = '
	 
	Dein Druck '.$print.' wurde um ' . date("H:i") . ' '.$event.'.
	 
	
	------------------------------------------------------------
	https://ultinetprint.informatik.hs-furtwangen.de/
	 
	';	// Our message above including the link
	
	//send the message, check for errors
	
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	//	header('Location: ./index.php?success=1');
		echo "erfolgreich";
	}
}