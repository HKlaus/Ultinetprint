<?php

function send_verify($email, $password) {
	$subject = 'Signup | Verification'; 
	$message = '
	 
	Thanks for signing up!
	Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
	 
	------------------------
	Username: '.$email.'
	------------------------
	 
	Please click this link to activate your account:
	http://www.yourwebsite.com/verify.php?email='.$email.'&hash='.$password.'
	 
	'; // Our message above including the link
						 
	$headers = 'From:noreply@hs-furtwangen.de' . "\r\n"; // Set from headers
	echo mail($email, $subject, $message, $headers);
	if(mail($email, $subject, $message, $headers)) {
		echo "The email() was successfully sent.";
    } else {
        echo "The email() was NOT sent.";
    }
}