<?php
 require "koszyk/koszyk.php";
 $zakupy = new koszyk;
 $zakupy->kup('WODA',10);
 $zakupy->kup('WODA',20);
 $zakupy->kup('MLEKO',10);
 $zakupy->kup('SER',10);
 echo "Towary dodne do koszyka <BR>";
 echo "<form action='sk_10.php' method='POST'>
        <input type='submit' value='Wyswietl liste'>
		<input type='hidden' name='za' value=$zakupy>
      </form>";
?>