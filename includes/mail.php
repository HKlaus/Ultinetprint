<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/PHPMailer.php';

function send_verify($email, $password) {
	//Create a new PHPMailer instance
	$mail = new PHPMailer;
	//Set who the message is to be sent from
	$mail->setFrom('from@example.com', 'First Last');
	//Set an alternative reply-to address
	$mail->addReplyTo('replyto@example.com', 'First Last');
	//Set who the message is to be sent to
	$mail->addAddress($email, 'John Doe');
	//Set the subject line
	$mail->Subject = 'Signup | Verification';
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->Body = '
	 
	Thanks for signing up!
	Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
	 
	------------------------
	Username: '.$email.'
	------------------------
	 
	Please click this link to activate your account:
	http://www.yourwebsite.com/verify.php?email='.$email.'&hash='.$password.'
	 
	';	// Our message above including the link
	
	//send the message, check for errors
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
		echo "Message sent!";
	}
}