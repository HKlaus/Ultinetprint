/**
* Erstellt einen Countdown-Timer zur Darstellung der verbleibenden Druckdauer
* 
* @author Tom Lehmann & https://www.w3schools.com/howto/howto_js_countdown.asp
*
* @modified	yes
*/	
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
function timer(time) {
	var countDownDate = new Date(time).getTime();
	var x = setInterval( async function() {
		  // Hole momentanes Datum
		  var now = new Date().getTime();

		  // Ermittle Zeitunterschied zwischen beiden Daten
		  var distance = countDownDate - now;		// Zeitzone ausgleichen

		  // Errechne die einzelnen Unterschiede
		  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		  // Füge führende Nullen hinzu
		  if (seconds < 10) { seconds = "0" + seconds }
		  if (minutes < 10) { minutes = "0" + minutes }
		  
		  // Zeige das Resultat an
		  document.getElementById("timer").innerHTML = "am Drucken: ";
		  
		  // Falls es mehrere Tage dauert:
		  if (days > 0) {
			  document.getElementById("timer").innerHTML += days + " Tag(e) ";
		  }
		  
		  // Falls es mehrere Stunden dauert:
		  if (hours > 0) {
			  document.getElementById("timer").innerHTML += hours + ":";
		  }
		  document.getElementById("timer").innerHTML += minutes + ":" + seconds + "";

		  // Wenn der Timer abgelaufen ist
		  if (distance < 0) {
			clearInterval(x);
			document.getElementById("timer").innerHTML = "Dauer: -";
		  }
	}, 1000);
}