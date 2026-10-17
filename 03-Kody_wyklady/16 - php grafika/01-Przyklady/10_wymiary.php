<?php 
$obrazek=imagecreatefromjpeg("duze.jpg");
$szerokosc1=imagesx($obrazek);
$wysokosc1=imagesy($obrazek);
$dane = getimagesize("duze.jpg");
list($szerokosc2,$wysokosc2,$format,$znacznik_html) = $dane;
echo "
 <html>
   <body>
     imagesx() : $szerokosc1<br>
     imagesy() : $wysokosc1<br><br>
     getimagesize()<br>
     szerokosc: $szerokosc2<br>
     wysokosc: $wysokosc2<br>
     format: $format<br>
     znacznik_html: $znacznik_html<br>
   </body>
 </html>
";
?>