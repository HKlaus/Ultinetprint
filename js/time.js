// Source:		https://www.w3schools.com/howto/howto_js_countdown.asp

function timer(time) {
	var countDownDate = new Date(time).getTime();
	var x = setInterval(function() {
		  // Hole momentanes Datum
		  var now = new Date().getTime();

		  // Ermittle Zeitunterschied zwischen beiden Daten
		  var distance = countDownDate - now  + 3600 * 1000;		// Zeitzone ausgleichen

		  // Errechne die einzelnen Unterschiede
		  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		  // Zeige das Resultat an
		  document.getElementById("timer").innerHTML = "am Drucken: ";
		  if (days > 0) {
			  document.getElementById("timer").innerHTML += days + " Tage ";
		  }
		  if (hours > 0) {
			  document.getElementById("timer").innerHTML += hours + ":";
		  }
		  document.getElementById("timer").innerHTML += minutes + ":" + seconds + "";

		  // Wenn der Timer abgelaufen ist
		  if (distance < 0) {
			clearInterval(x);
			document.getElementById("timer").innerHTML = "Druck fertiggestellt.";
		  }
	}, 1000);
}