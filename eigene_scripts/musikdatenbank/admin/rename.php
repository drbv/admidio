<?php

/* ************************************ */
/* Script zum Umwandeln von Dateititeln */
/* ************************************ */

function zeichen($string)
{
$search = array(" - ", "'", " ");
$replace = array("-", "`","_");
return str_replace($search, $replace, $string);
}

$verzeichnis = "../../../../downloads/" . "boogiewoogie/michael";
 
// Text, ob ein Verzeichnis angegeben wurde
if ( is_dir ( $verzeichnis ))
{
    // öffnen des Verzeichnisses
    if ( $handle = opendir($verzeichnis) )
    {
        // einlesen der Verzeichnisses
        while (($file = readdir($handle)) !== false)
        {   
            if($file !="." && $file !="..")
              {
               $neu = zeichen($file); 
               if($file != $neu)
                 {
                  echo'File: ' . $file . "<br>";
                  echo $neu . "<br><br>";
                 }

               rename($verzeichnis . '/' . $file, $verzeichnis . '/' . $neu);
               }
              
        }
    }
}

?>