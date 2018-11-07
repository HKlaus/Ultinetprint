var klick = 0;
 var radiotype =0;
 function uncheck(radio){
  if (radiotype == radio){
   klick = klick + 1;
   if (klick % 2 == 0) {
    radio.checked = false;
   } else
    radio.checked = true;
  }else{
   radiotype = radio;
  }
}