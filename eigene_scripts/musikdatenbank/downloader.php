<?php

session_start();

date_default_timezone_set('Europe/Berlin');

 if($_POST['hochladen'])
    header('Location: downloader.php');

// echo'Download verhindern!'. '<br>';
if($_SESSION['name_datei'])
    header('Location: download_zip.php?datei='.$_SESSION['name_datei']);

$grundverzeichnis = '../../../downloads/';

/*
error_reporting (E_ALL);
echo'GET: ';print_r($_GET);echo"<br>";
echo'POST: ';print_r($_POST);echo"<br>";
echo'SESSION: ';print_r($_SESSION);echo"<br>";
echo filemtime('downloadliste.csv'). ' - ' . date('H:m:i',filemtime('downloadliste.csv')) . '<br>';
*/

 function phpalert($msg)
   {
    echo'<script Type = "text/javascript">alert("' . $msg . '")</script>';
   }

 if(filemtime('downloadliste.csv') ==  $_SESSION['akt_datei'])
  {
   $filename = 'downloadliste.csv';
   $fn = $filename;
   $fp = fopen($fn,"r"); 
   $pruefung = fgets($fp, 13);
   if(is_file('downloadliste.csv') && $pruefung !='"Titel";"Int')
      {
       unlink("./downloadliste.csv");
       echo'<h1>Diese Datei ist nicht zulässig!</h1>';
       session_destroy();
       header('Location: downloader.php');
      }
   else
     {
      while(($Daten = fgets($fp, 1000)) !== FALSE)
         {
          $i = $i + 1;
          $zeile[$i] = $Daten;
//          echo $zeile[$i]. '<br>';
              $song = explode(';', $zeile[$i]);
//           print_r($song);echo"  Song<br>";
//echo$song[0]. '<br>';
          $klasse =  trim(str_replace ( '"', ' ', $song[0]));
// echo '*' . $klasse. '*<br>';
        if($klasse=='S-Klasse Vorrunde' || $klasse=='S-Klasse Hoffnungsrunde' || $klasse=='S-Klasse Zwischenrunde' || $klasse=='S-Klasse Endrunde' || $klasse=='J-Klasse Vorrunde' || $klasse=='J-Klasse Hoffnungsrunde' || $klasse=='J-Klasse Zwischenrunde' || $klasse=='J-Klasse Endrunde' || $klasse=='C-Klasse Vorrunde' || $klasse=='C-Klasse Hoffnungsrunde' || $klasse=='C-Klasse Zwischenrunde' || $klasse=='C-Klasse Endrunde' || $klasse=='B-Klasse Vorrunde' || $klasse=='B-Klasse Hoffnungsrunde' || $klasse=='B-Klasse Zwischenrunde' || $klasse=='B-Klasse Endrunde Akrobatik' || $klasse=='B-Klasse Endrunde Fußtechnik' || $klasse=='A-Klasse Vorrunde' || $klasse=='A-Klasse Hoffnungsrunde' || $klasse=='A-Klasse Zwischenrunde' || $klasse=='A-Klasse Endrunde Akrobatik' || $klasse=='A-Klasse Endrunde Fußtechnik' || $klasse=='BW J-Klasse Vorrunde' || $klasse=='BW J-Klasse Hoffnungsrunde' || $klasse=='BW J-Klasse Zwischenrunde' || $klasse=='BW J-Klasse Endrunde'
 || $klasse=='BW MA-Klasse Vorrunde' || $klasse=='BW MA-Klasse Hoffnungsrunde' || $klasse=='BW MA-Klasse Zwischenrunde' || $klasse=='BW MA-Klasse langsame Endrunde' || $klasse=='BW MA-Klasse schnelle Endrunde' || $klasse=='BW SA-Klasse Vorrunde' || $klasse=='BW SA-Klasse Hoffnungsrunde' || $klasse=='BW SA-Klasse Zwischenrunde' || $klasse=='BW SA-Klasse langsame Endrunde' || $klasse=='BW SA-Klasse schnelle Endrunde' || $klasse=='BW MB-Klasse Vorrunde' || $klasse=='BW MB-Klasse Hoffnungsrunde' || $klasse=='BW MB-Klasse Zwischenrunde' || $klasse=='BW MB-Klasse Endrunde'  || $klasse=='BW SB-Klasse Vorrunde' || $klasse=='BW SB-Klasse Hoffnungsrunde' || $klasse=='BW SB-Klasse Zwischenrunde' || $klasse=='BW SB-Klasse Endrunde') 
             {
              $_SESSION['link'][$i] = $klasse;
//  echo $_SESSION['link'][$i] . ' Startklasse<br>';
             }      
          else
             {
              $link = $song[5];
              $lang_titel = explode('=', $song[4]);
//  echo'Link: ' . $link. '<br>';
              $_SESSION['link'][$i] = str_replace('"', '', $link);
// echo'Link: ' . $_SESSION['link'][$i] . '<br>';  
              $_SESSION['titel'][$i] = $lang_titel[2];
// echo'Titel: ' . $i .' - ' . $_SESSION['titel'][$i] . '<br>';  
              $datein_erzeugt = 1;
            }
          }
         }
   fclose($fn);  
  }
 else
  {
   session_destroy();
   unlink("./downloadliste.csv");
  }

if($datein_erzeugt == 1)
   {
    $zip = new ZipArchive;
    $name_datei ='musik_' .date("Hisdmy") .'.zip';
    $_SESSION['name_datei'] = $name_datei;
    if ($zip->open($name_datei, ZipArchive::CREATE) === TRUE)
       {
        $i = 1; 
        while($i < count($_SESSION['link']) - 1)
           {
           $i ++;
//           echo $_SESSION['link'][$i] . '-' . $i . '<br>';
           
           if($_SESSION['link'][$i] =='S-Klasse Vorrunde' || $_SESSION['link'][$i] =='S-Klasse Hoffnungsrunde' || $_SESSION['link'][$i] =='S-Klasse Zwischenrunde' || $_SESSION['link'][$i] =='S-Klasse Endrunde' || $_SESSION['link'][$i] =='J-Klasse Vorrunde' || $_SESSION['link'][$i] =='J-Klasse Hoffnungsrunde' || $_SESSION['link'][$i] =='J-Klasse Zwischenrunde' || $_SESSION['link'][$i] =='J-Klasse Endrunde' || $_SESSION['link'][$i] =='C-Klasse Vorrunde' || $_SESSION['link'][$i] =='C-Klasse Hoffnungsrunde' || $_SESSION['link'][$i] =='C-Klasse Zwischenrunde' || $_SESSION['link'][$i] =='C-Klasse Endrunde' || $_SESSION['link'][$i] =='B-Klasse Vorrunde' || $_SESSION['link'][$i] =='B-Klasse Hoffnungsrunde' || $_SESSION['link'][$i] =='B-Klasse Zwischenrunde' || $_SESSION['link'][$i] =='B-Klasse Endrunde Akrobatik' || $_SESSION['link'][$i] =='B-Klasse Endrunde Fußtechnik' || $_SESSION['link'][$i] =='A-Klasse Vorrunde' || $_SESSION['link'][$i] =='A-Klasse Hoffnungsrunde' || $_SESSION['link'][$i] =='A-Klasse Zwischenrunde' || $_SESSION['link'][$i] =='A-Klasse Endrunde Akrobatik' || $_SESSION['link'][$i] =='A-Klasse Endrunde Fußtechnik'  || $_SESSION['link'][$i]=='BW J-Klasse Vorrunde' || $_SESSION['link'][$i]=='BW J-Klasse Hoffnungsrunde' || $_SESSION['link'][$i]=='BW J-Klasse Zwischenrunde' || $_SESSION['link'][$i]=='BW J-Klasse Endrunde' || $_SESSION['link'][$i]=='BW MA-Klasse Vorrunde' || $_SESSION['link'][$i]=='BW MA-Klasse Hoffnungsrunde' || $_SESSION['link'][$i]=='BW MA-Klasse Zwischenrunde' || $_SESSION['link'][$i]=='BW MA-Klasse langsame Endrunde' || $_SESSION['link'][$i]=='BW MA-Klasse schnelle Endrunde' || $_SESSION['link'][$i]=='BW SA-Klasse Vorrunde' || $_SESSION['link'][$i]=='BW SA-Klasse Hoffnungsrunde' || $_SESSION['link'][$i]=='BW SA-Klasse Zwischenrunde' || $_SESSION['link'][$i]=='BW SA-Klasse langsame Endrunde' || $_SESSION['link'][$i]=='BW SA-Klasse schnelle Endrunde' || $_SESSION['link'][$i]=='BW MB-Klasse Vorrunde' || $_SESSION['link'][$i]=='BW MB-Klasse Hoffnungsrunde' || $_SESSION['link'][$i]=='BW MB-Klasse Zwischenrunde' || $_SESSION['link'][$i]=='BW MB-Klasse Endrunde'  || $_SESSION['link'][$i]=='BW SB-Klasse Vorrunde' || $_SESSION['link'][$i]=='BW SB-Klasse Hoffnungsrunde' || $_SESSION['link'][$i]=='BW SB-Klasse Zwischenrunde' || $_SESSION['link'][$i]=='BW SB-Klasse Endrunde')
            {
              $verzeichnis = $_SESSION['link'][$i];
 //             echo'Verzeichnis: ' . $verzeichnis . '<br>';
              $zip->addEmptyDir($verzeichnis);
            }
            else
            {  
             $hinzu = $grundverzeichnis . ltrim($_SESSION['link'][$i]);
             $hinzu = rtrim($hinzu);
// Fehlersuchroutine
/*
            if(is_file($hinzu))
                echo"Datei ist vorhanden". '<br>';
             else      
                       echo"Datei ist nicht vorhanden". '<br>';
*/
             $name = $_SESSION['titel'][$i];
             $name = substr($name,0, strlen($name) - 1);
//             echo '*' . $hinzu . '*' . '<br>';
              if(is_file($hinzu))
                 $zip->addFile($hinzu, $verzeichnis.'/'.$name);
            }
/*
            if($i + 1 == count($_SESSION['link']))
              {
               session_destroy();
               session_start();
               unlink("./downloadliste.csv");
              }
*/
            }
        $zip->close();
        header('Location: download_zip.php?datei='.$_SESSION['name_datei']);
        echo'Die Datei wurde erfolgreich erzeugt!'. '<br>';
       }
    else
       {
        echo 'Fehler';
       }
   }

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Turniermusik download</title>
</head>
<body>

<h1>DRBV Turniermusik download</h1>
<form action="downloader.php" method="post" enctype="multipart/form-data">

<input type="file" name="datei">
<p><input type="submit" name="hochladen" value="Datei Hochladen"/></p>

</form>

<?php

if($_POST["hochladen"])
{
   if(!$_FILES['datei']['tmp_name'])
       phpalert('Es wurde keine Datei ausgewählt!');
   else
    {
     if (is_file("./downloadliste.csv"))
        unlink("./downloadliste.csv");
        move_uploaded_file($_FILES['datei']['tmp_name'], "./downloadliste.csv");
    }
  }


if(file_exists("downloadliste.csv") && !$_SESSION['akt_datei'])
  {
   if(filemtime('downloadliste.csv') <= time() && filemtime('downloadliste.csv') >= time() - 2)
   $_SESSION['akt_datei'] = filemtime('downloadliste.csv');
   echo'File: ' . filemtime('downloadliste.csv') . ' Time: ' . time();
  }

?>

</body>
</html>