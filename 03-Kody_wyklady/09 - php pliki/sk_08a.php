<?php
 $max_rozmiar = 1024*1024; 
 if (is_uploaded_file($_FILES['do_wgrania']['tmp_name']))
 {
  if ($_FILES['do_wgrania']['size'] > $max_rozmiar)
   {echo 'Blad! Plik jest za duzy!';} 
  else
   { 
    echo 'Odebrano plik nazwa: '.$_FILES['do_wgrania']['name']; 
    echo '<br/>';
    if (isset($_FILES['do_wgrania']['type'])) 
    echo 'Typ: '.$_FILES['do_wgrania']['type'].'<br/>'; 
    move_uploaded_file( $_FILES['do_wgrania']['tmp_name'],
     $_SERVER['DOCUMENT_ROOT'].'/foto/'.$_FILES['do_wgrania']['name']); 
    echo 'Bład: '.$_FILES['do_wgrania']['error'].'<br/>';  
   };
 }
 else { echo 'bladd przy przesylaniu danych';}
?>
