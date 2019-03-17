<?php

function makeDownload($file, $dir, $type)
     {
      header("Content-Type: $type");
      header("Content-Disposition: attachment; filename=\"$file\"");
      readfile($dir.$file);
     }

$dir = '/homepages/42/d535113983/htdocs/adm/eigene_scripts/musikdatenbank/' ;

$type = 'text/comma-separated-values';

$dateiname = $_GET['dateiname'] . '.csv';

if(!empty($dateiname) && !preg_match('=/=', $dateiname))
     {
    if(file_exists ($dir.$dateiname))
       {
        makeDownload($dateiname, $dir, $type);
        // echo"Erfolgreich<br>";
       }  
     else
         echo"Die Datei ist nicht vorhanden!<br>";  
     }
     
?>  