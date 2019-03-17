<?php

// Letzte gespeicherte Änderung lesen
$datum = date("_Y_m_d");

$datei = './intern/aenderungen.txt';
$gespeichert = file($datei);

for ( $x = 0; $x < count ($gespeichert); $x++)
{
 $gespeichert[$x] = trim($gespeichert[$x]);
}


// Startbuch RR  
$datei_sb_rr = './gs/startbuch-Export-RR.txt';
$sb_rr = filemtime ( $datei_sb_rr );

if($gespeichert[0] < $sb_rr)
   {
   	$gespeichert[0] = $sb_rr;
   	$geaendert[0] = 1;
   }

// Startbuch Formationen 
$datei_sb_form = './gs/startbuch-Export-formationen.txt';
$sb_form = filemtime ( $datei_sb_form );

if($gespeichert[1] < $sb_form)
   {
   	$gespeichert[1] = $sb_form;
   	$geaendert[1] = 1;
   }   

// Startbuch BW 
$datei_sb_bw = './gs/startbuch-Export-boogie.txt';
$sb_bw = filemtime ( $datei_sb_bw );

if($gespeichert[2] < $sb_bw)
   {
   	$gespeichert[2] = $sb_bw;
   	$geaendert[2] = 1;
//   	copy($datei_sb_bw, 'unbearbeitet/' . substr($datei_sb_bw,0,-4) . $datum  . '.txt');   	
   }

// Neuer Verein 
$datei_ad_ve = './gs/adressen-vereine.txt';
$ad_ve = filemtime ( $datei_ad_ve ); 

if($gespeichert[3] < $ad_ve)
   {
   	$gespeichert[3] = $ad_ve;
   	$geaendert[3] = 1;   	
   }

// Funktionäre 
$datei_ad_fu = './gs/Funktionaere-Export.txt';
$ad_fu = filemtime ( $datei_ad_fu );
// echo 'Letzte Änderung der Datei: <b>' . $datei_ad_fu . '</b> ' . gmdate ('d.m.Y H:i:s', $ad_fu) . '<br>'; 

if($gespeichert[4] < $ad_fu)
   {
   	$gespeichert[4] = $ad_fu;
   	$geaendert[4] = 1; 	
   }
   
            
if($geaendert)
   {
   	$inhalt = "Folgende Dateien wurden geaendert:\n\n";
   	
   	if($geaendert[0])
       $inhalt .= $datei_sb_rr . "\n";
    if($geaendert[1])
       $inhalt .= $datei_sb_form . "\n";     
    if($geaendert[2])
       $inhalt .= $datei_sb_bw . "\n";       
    if($geaendert[3])
       $inhalt .= $datei_ad_ve . "\n";
    if($geaendert[4])
       $inhalt .= $datei_ad_fu . "\n";     
              
   	$inhalt .= "\nBitte in Admidio aendern!\n";
   	
   	mail('webmaster@drbv.de','Datenaenderung Geschaeftsstelle',$inhalt,'From: webmaster@drbv.de');
//    mail('info@oberleonline.de','Datenaenderung Geschaeftsstelle',$inhalt,'From: webmaster@drbv.de');

// Änderungszeit speichern
     
   	 for($x = 0; $x < count($gespeichert); $x++)
         {
          $neu .= $gespeichert[$x] . "\n";
         }     
     // Dateinamen festlegen
     $filename = "aenderungen.txt";
     // Datei auf Server speichern
     $fn = "./intern/" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $neu);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>"; 
   } 
   
 // Ändrungen schreiben
 
 include("vergleich.php");  
   
?>

