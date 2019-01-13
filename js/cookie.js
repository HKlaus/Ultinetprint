/**
* Funktionen um ein Cookie zu setzen um den letzten Login-Benutzernamen zu speicheren und diesen in das Login-Formular einzufüllen
* 
* @author Tom Lehmann & https://stackoverflow.com/a/24103596
*
* @modified	yes
*/	

function setCookie(name) {																// Funktion zum erstellen eines Cookies
	var date = new Date();
	date.setTime(date.getTime() + (183*24*60*60*1000));									// Setze Ablaufdatum auf in einem Semester
	expires = "; expires=" + date.toUTCString();
    document.cookie = "username=" + (name || "")  + expires + "; path=/";				// Speichere den eingegebenen Benutzernamen
}

function getCookie() {																	// Funktion zum auslesen eines Cookies
    var nameEQ = "username=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {													// Extrahiere den Benutzernamen aus dem Cookie
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
window.onload = function() {															// Nachdem das Fenster geladen wurde
	if (getCookie()) {																	// Falls es ein Cookie gibt
		document.getElementById("email").value = getCookie();							// Fülle in das Formular den gespeicherten Benutzernamen
	}
}