<?php

$datum = date('Y-m-');
$heute = date('Y-m-d');
$gestern = date('Y-m-d',time()-86400);

$filename = 'DRBV-Akrotabelle_' . $gestern . '.txt';

$fn = $filename;
$fp = fopen($fn,'r'); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file_gestern .= $Daten;
      }

fclose($fn);


$filename = 'DRBV-Akrotabelle_' . $heute . '.txt';

$fn = $filename;
$fp = fopen($fn,'r'); 

while(($Zeile = fgets($fp, 1000)) !== FALSE)
      {
       $akro = $Zeile;
       $akt_akro = $akro;
       $akro_vorhanden = strstr($gesamt_file_gestern, $akro);

       if($akro_vorhanden === false)
         {
          $neue_akros .= $akt_akro . " wurde hinzugefuegt\n";
         }
      }
           
fclose($fn);

if($neue_akros)
  {
//   echo $neue_akros;
   // E-Mails versenden

    $absender = "XXX@XXX.XX";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

@mail("XXX@XXX.XX", "Aenderung Akrobatiken", $neue_akros, $absender_mail, $absender_path);
  }


?>