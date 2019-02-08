/**
* Wird von der Druckerseite aufgerufen, aktualisiert im Zeitinterval "interval" den Status, Drucknamen und State
*
* @author   Tom Lehmann
* @version  1.0
* 
*/

var interval = 30000; 								// Zeitinterval

setTimeout(update_printer_status, interval);
setTimeout(update_print_name, interval);			// Starte die Funktionen die den AJAX Aufruf ausführt
setTimeout(update_printer_state, interval);

function update_printer_status() {					// Deklariere Funktion
  var xhttp = new XMLHttpRequest();					// Erstelle neuen XHTTP-Request
  xhttp.onreadystatechange = function() {			// Wenn der State des Requests auf "ready" ist
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("printer_status").innerHTML = this.responseText;		// Ersetze den Inhalt des Elements mit der ID "printer_status"
	 setTimeout(update_printer_status, interval);	// Starte die Funktion nach "intervall" erneut
    }
  };
  xhttp.open("GET", "https://ultinetprint.informatik.hs-furtwangen.de/includes/ajax.php?data=status", true);	// Das Skript dass die Daten zurück gibt
  xhttp.send();										// Sende den Request
}

function update_print_name() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("print_name").innerHTML = this.responseText;
	 setTimeout(update_print_name, interval);
    }
  };
  xhttp.open("GET", "https://ultinetprint.informatik.hs-furtwangen.de/includes/ajax.php?data=name", true);
  xhttp.send();
}

function update_printer_state() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
     document.getElementById("printer_state").innerHTML = this.responseText;
	 setTimeout(update_printer_state, interval);
    }
  };
  xhttp.open("GET", "https://ultinetprint.informatik.hs-furtwangen.de/includes/ajax.php?data=state", true);
  xhttp.send();
}
