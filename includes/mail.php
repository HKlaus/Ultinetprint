<?php
/**
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

use PHPMailer\PHPMailer\PHPMailer;							// PHPMailer aus dem Github Projekt https://github.com/PHPMailer/PHPMailer

require '/var/www/html/PHPMailer/src/PHPMailer.php';
require '/var/www/html/PHPMailer/src/SMTP.php';

/**
* Sendet eine Accunt-Aktivierungs Email über den SMTP Server
*
* @author Tom Lehmann & https://code.tutsplus.com/tutorials/how-to-implement-email-verification-for-new-members--net-3824
*
* modified yes
*
* @param string $email 	Die Email-Adresse des zu aktivierenden Accounts
* @param string	$hash 	Ein zufälliger Hash für den Aktivierungslink, in diesem Fall den SHA-512 Passwort hash
*
*/
function send_verify($email, $hash) {
	$mail = new PHPMailer;				// Initialisiere eine neue PHPMailer Instanz
	
	$mail->isSMTP();					// Setze Protokoll auf SMTP
	$mail->Host = 'localhost';			// Benutze lokalen SMTP Server
	
	$mail->setFrom('noreply@hs-furtwangen.de', 'Ultimaker Networkprint');		// Setze Absender auf "noreply"
	$mail->addAddress($email, '');												// Setze Empfänger
	$mail->Subject = 'Ultinetprint Account Aktivierung';							// Setze Betreff
	
	$mail->Body = '
	 
	Vielen Dank fuer die Regsitrierung!
	Der Account wurde erstellt und kann nach dem Aktivieren mit folgender Email oder Benutzernamen verwendet werden:
	 
	------------------------
	
	Benutzer: '. substr($email, 0, strpos($email, "@")) .'
	E-Mail: '.$email.'
	
	------------------------
	 
	Bitte klicke auf diesen Link um den Account zu aktivieren:
	https://ultinetprint.informatik.hs-furtwangen.de/verify.php?email='.$email.'&hash='.$hash.'
	 
	';	// Der Inhalt der Nachricht mit dem Aktivierungslink
		
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;			// Wenn die Email nicht erfolgreich versendet wurde, gib den Fehler aus
	} else {
		header('Location: ./index.php?success=1');			// Falls doch, leite auf die "Erfolg"-Seite auf der Indexseite weiter
	}
}

/**
* Sendet eine Email-Benachrichtigung über den SMTP Server
*
* @author Tom Lehmann
*
* @param string $email 	Die Email-Adresse des zu aktivierenden Accounts
* @param string	$print	Der Name des Drucks über den benachrichtigt wird
* @param string	$event	Das Ereignis über das benachrichtigt wird
*
*/
function send_notify($email, $print, $event) {
	if ($event == "fertiggestellt") {
		$notify = " wurde fertiggestellt und sollte nun entnommen werden.
	(Nicht vergessen, ggfs. den darauf folgenden Druck zu starten)";
	} elseif ($event == "druckbereit") {
		$notify = " kann nun eingerichtet und daraufhin gestartet werden.
	(Nicht vergessen, ggfs. den vorherigen Druck sauber zu entfernen)";
	} else {
		$notify = $event;
	}
	
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	$mail->Host = 'localhost';
	
	$mail->setFrom('noreply@hs-furtwangen.de', 'Ultimaker Networkprint');
	$mail->addAddress($email, '');
	$mail->Subject = 'Ultinetprint Benachrichtigung';
	$mail->Body = '
	 
	Der Druck ' . $print . $notify . '
	 
	
	------------------------------------------------------------
	https://ultinetprint.informatik.hs-furtwangen.de/
	 
	';
	
	if (!$mail->send()) {
		echo "Mailer Error: " . $mail->ErrorInfo;
	} 
}