/**
* Diese Datei enthält hauptsächlich die Funktionen zur Registrierung und zum Login
* und stammt zum allergrößten Teil von der in der @author-Annotation erwähnten Website
*
* @author   Tom Lehmann & https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
* @version  1.0
* 
*/

/**
* Nimmt das Passwort "password" aus dem Formular "form" und verschlüsselt dieses mit dem SHA512 Algorithmus
* bevor es dieses Formular dann an den Server sendet, um zu vermeiden dass ein Klartext-Passwort gesendet wird
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	no
*/
function formhash(form, password) {
    // Erstelle ein neues Feld für das gehashte Passwort. 
    var p = document.createElement("input");
 
    // Füge es dem Formular hinzu. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // Sorge dafür, dass kein Text-Passwort geschickt wird. 
    password.value = "";
 
    // Reiche das Formular ein. 
    form.submit();
}

/**
* Die Funktion überprüft die Eingaben aus dem Registrationsformular und sendet diese falls korrekt an den Server
* 
* @author https://de.wikihow.com/Ein-sicheres-Login-Skript-mit-PHP-und-MySQL-erstellen
*
* @modified	yes
*/
function regformhash(form, email, password, conf) {
     // Überprüfe, ob jedes Feld einen Wert hat
    if (  email.value == '' || password.value == '' || conf.value == '') {
        alert('Fehler: Du musst alle Felder ausfüllen.');
        return false;
    }
 
    // Überprüfe, dass Passwort lang genug ist (min 8 Zeichen)
    // Die Überprüfung wird unten noch einmal wiederholt, aber so kann man dem 
    // Benutzer mehr Anleitung geben
    if (password.value.length < 8) {
        alert('Fehler: Das Passwort muss mindestens 8 Zeichen lang sein.');
        form.password.focus();
        return false;
    }
 
    // Mindestens eine Ziffer, ein Kleinbuchstabe und ein Großbuchstabe
    // Mindestens acht Zeichen 
    re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/; 
    if (!re.test(password.value)) {
        alert('Fehler: Das Passwort muss mindestens eine Zahl, einen Klein- und einen Großbuchstaben enthalten.');
        return false;
    }
 
    // Überprüfe die Passwörter und bestätige, dass sie gleich sind
    if (password.value != conf.value) {
        alert('Fehler: Dein Passwort und die Bestätigung stimmen nicht überein.');
        form.password.focus();
        return false;
    }
 
    // Erstelle ein neues Feld für das gehashte Passwort.
    var p = document.createElement("input");
 
    // Füge es dem Formular hinzu. 
    form.appendChild(p);
    p.name = "p";
    p.type = "hidden";
    p.value = hex_sha512(password.value);
 
    // Sorge dafür, dass kein Text-Passwort geschickt wird. 
    password.value = "";
    conf.value = "";
 
    // Reiche das Formular ein. 
    form.submit();
    return true;
}
