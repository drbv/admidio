<?php

session_start();

/*
error_reporting (E_ALL);
echo'GET: ';print_r($_GET);echo"<br>";
echo'POST: ';print_r($_POST);echo"<br>";
echo'SESSION: ';print_r($_SESSION);echo"<br>";
*/

function makeDownload($file, $dir, $type)
     {
      header("Content-Type: $type");
      header("Content-Disposition: attachment; filename=\"$file\"");
      readfile($dir.$file);
     }

$dir = '/homepages/42/d535113983/htdocs/adm/eigene_scripts/musikdatenbank/' ;

$type = 'application/zip';

$dateiname = $_GET['datei'];

if(!empty($dateiname) && !preg_match('=/=', $dateiname))
     {
    if(file_exists ($dir.$dateiname))
       {
        makeDownload($dateiname, $dir, $type);
        // echo"Erfolgreich<br>";
         session_destroy(); 
         unlink("./downloadliste.csv");
         unlink($dateiname);
       }  
     else
         echo"Die Datei ist nicht vorhanden!<br>";  
     }
 
?>  