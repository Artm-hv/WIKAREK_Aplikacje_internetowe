<?php
 $sxe=simplexml_load_string('<?xml version="1.0" 
   encoding="UTF-8"?><studenci></studenci>');
 $sxe->addAttribute('Grupa','314A');
 $s=$sxe->addChild('student'); 
 $s->addChild('nazwisko','Kowalski'); 
 $s->addChild('imie','Jan');
 $p=$s->addChild('przedmiot');
 $n=$p->addChild('Nazwa','PAI');
 $n=$p->addChild('Nazwa','FIZYKA');
 $fp = fopen("plik.xml", "w");
 fputs($fp,$sxe->asXML());
?>