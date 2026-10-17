<?php
	$olimpiady = simplexml_load_file('ns.xml');
	print $olimpiady->Uczestnicy[0]->Panstwo[0]->Zawodnik[0]->Nazwisko;
?>
