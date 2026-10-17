<form id="f1" action="strona.php" method="POST">
    <input type="text" name="imie" placeholder="Twoje imię">
    <button type="submit" id="zap">Zapisz dane</button>
</form>

<script>
    const p = document.getElementById('zap');
     p.addEventListener( 'click', function() 
	  {
	   if (confirm("Czy chcesz zapisać zmiany?")) { alert ("ZAPISZ")}
	   else { alert("Nie zapisuj");
	          event.preventDefault();
        }
      }
	 );
	  
  </script>