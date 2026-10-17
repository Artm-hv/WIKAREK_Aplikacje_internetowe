<?php
 require 'vendor/autoload.php';
 use PhpOffice\PhpSpreadsheet\Spreadsheet;
 use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 use PhpOffice\PhpSpreadsheet\IOFactory;
 
 $plks = glob('dane/*.xls');
 foreach ($plks as $f) 
 {$nf = newfilename($f, '.xml');     
   echo "Konwertuje plik   ".$f." na plik ".$nf."<BR>" ;
   $parsed = parse_xls($f);           # Parsing formatu wejściowego.
   parsed_set_output($parsed, 'xml'); # Konwersja do formatu wyjściowego.  
   zapisz_xml($nf, $parsed);          # Zapisanie wynikowego formatu.    
 };

 function newfilename($filename, $ext)
 {
  return 'wyniki/'.preg_replace('/\.\w{3}$/', $ext,basename($filename));
 }

 function parse_xls($filename)
 {
   $spreadsheet = IOFactory::load($filename);
   $arkusz  = $spreadsheet->getSheetByName('artykuł');
   $arkusz1 = $spreadsheet->getSheetByName('wstęp');
  
   $w = array(); 
   $w['rok']          = f_in_xls($arkusz->getCell('b1')->getValue());
   $w['numer']        = f_in_xls($arkusz->getCell('b2')->getValue());
   $w['od_strony']    = f_in_xls($arkusz->getCell('b3')->getValue());
   $w['do_strony']    = f_in_xls($arkusz->getCell('b4')->getValue());
   $w['tytuł']        = f_in_xls($arkusz->getCell('b5')->getValue());
   $w['rubryka']      = f_in_xls($arkusz->getCell('b6')->getValue());
   $w['pod_rubryka']  = f_in_xls($arkusz->getCell('b7')->getValue());
   $w['wstep']        = f_in_xls($arkusz1->getCell('a1')->getValue());

   $autorzy = array();
   $i = 0; 
   while( null != ($arkusz->getCell([2+$i,8])) && 
          null != ($arkusz->getCell([3+$i,8])) && 
          trim($arkusz->getCell([2+$i,8])) && 
          trim($arkusz->getCell([3+$i,8]))) 
		  {		  
            $autorzy[$i]['imie']  =f_in_xls($arkusz->getCell([2+$i,8])); 
            $autorzy[$i]['nazwisko']=f_in_xls($arkusz->getCell([3+$i,8]));    
            $i++;
         };
   $w['autorzy'] = $autorzy; 
  return $w;
 }
 function f_in_xls($str)
 { 
  if (!isset($str)) $str=''; 
   $str = trim($str); 
   return $str;
 }

 
 function f_out_xml($str)
 { $str = htmlspecialchars($str) ;
   #EWENTUALNIE ZMIANA KODOWANIA ZNAKOW 
   return $str;
 }

 function parsed_set_output(&$art, $output)
 {
 if ($output == 'xml') 
  {$art['rok']     =f_out_xml($art['rok']);
   $art['numer']       =f_out_xml($art['numer']);
   $art['od_strony']    =f_out_xml($art['od_strony']);
   $art['do_strony']    =f_out_xml($art['do_strony']);
   $art['tytuł']       =f_out_xml($art['tytuł']);
   $art['rubryka']     =f_out_xml($art['rubryka']);
   $art['pod_rubryka']  =f_out_xml($art['pod_rubryka']);
   $art['wstep']       =f_out_xml($art['wstep']);  
   $ile = count($art['autorzy']); 
   for ($i= 0; $i < $ile; $i++) 
   { $art['autorzy'][$i]['imie']=f_out_xml($art['autorzy'][$i]['imie']);
     $art['autorzy'][$i]['nazwisko']=f_out_xml($art['autorzy'][$i]['nazwisko']);
   }
  }
 } 

 function zapisz_xml($filename, $article)
 {
  $art=new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><artykul></artykul>') ;
  $art->addChild('rok'       ,$article['rok']);
  $art->addChild('numer'     ,$article['numer']);
  $art->addChild('od_strony' ,$article['od_strony']); 
  $art->addChild('do_strony' ,$article['do_strony']); 
  $art->addChild('tytuł'     ,$article['tytuł']);
  $art->addChild('rubryka'   ,$article['rubryka']); 
  $art->addChild('pod_rubryka',$article['pod_rubryka']);
  $art->addChild('wstep'       ,$article['wstep']);  
  $autl=$art->addChild('autorzy'); 
  foreach ($article['autorzy'] as $autor) 
  {
    $aut=$autl->addChild('autor');
    $aut->addChild('imie',$autor['imie']);
    $aut->addChild('nazwisko',$autor['nazwisko']);
  }
  $x=$art->asXML();
  $plik=fopen($filename,'w'); 
  fwrite($plik,$x); 
  fclose($plik);
 }
?>
