<?php

session_start();

if($_POST['ruecksetzen'])
  {
   session_destroy();
   session_start();
  }
$_SESSION[] = array();
unset($_SESSION[$sk]);

require_once("../admin/dboeffnen.inc.php");

/*
echo'GET: ';print_r($_GET);echo"<br>";
echo'POST: ';print_r($_POST);echo"<br>";
echo'SESSION: ';print_r($_SESSION);echo"<br>";
*/

function datenbankabfrage($db, $query, $valueQuotes, $separator, $sk, $ersatz)
   {
    global $str_body, $anzahl_auswahl, $y;
    unset($lfdnr);
    $lieder = mysqli_query($db, $query);
    $anzahl_auswahl = mysqli_affected_rows($db);

    while($temp = mysqli_fetch_array($lieder))
      {
       $_SESSION[y] = $_SESSION[y] + 1;
       $y = $y + 1;

          $wertung = $temp['wertung'];
          $bezeichnung = str_replace('&', '&teil2=', $temp['bezeichnung']);
   
       unset($stern);
          if($wertung == 1)
             $stern = '<img src="./1Stern.png">';
          if($wertung == 2)
             $stern = '<img src="./2Sterne.png">';
          if($wertung == 3)
             $stern = '<img src="./3Sterne.png">';
          if($wertung == 4)
             $stern = '<img src="./4Sterne.png">';
          if($wertung == 5)
             $stern = '<img src="./5Sterne.png">';

       $id = $temp['id'];
       $titel = $temp['titel'];
       $interpret = $temp['interpret'];
       $takte = $temp['takte'];
       $genre = $temp['genre'];
       if($temp['rocknroll'] == 1)
          $rr = '&bull;';
       else
          $rr = '';
       if($temp['boogiewoogie'] == 1)
          $bw = '&bull;';
       else
          $bw = '';  

    echo'<tr><td align="right">' . $y . '</td>';
    
    if($_POST['turnier_erstellen'] == 1 && !$_SESSION[$sk][$_SESSION[y]])
      {
       if(in_array($id, $_SESSION['doppelt']))
        {
         $ersatzlieder = mysqli_query($db, $ersatz);
         while($temp1 = mysqli_fetch_array($ersatzlieder))
           {

            $id = $temp1['id'];
            if(!in_array($id, $_SESSION['doppelt']))
               {
                $wertung = $temp1['wertung'];
                $bezeichnung = str_replace('&', '&teil2=', $temp1['bezeichnung']);

                unset($stern);
                if($wertung == 1)
                  $stern = '<img src="./1Stern.png">';
                if($wertung == 2)
                  $stern = '<img src="./2Sterne.png">';
                if($wertung == 3)
                  $stern = '<img src="./3Sterne.png">';
                if($wertung == 4)
                  $stern = '<img src="./4Sterne.png">';
                if($wertung == 5)
                  $stern = '<img src="./5Sterne.png">';
                  
                $titel = $temp1['titel'];
                $interpret = $temp1['interpret'];
                $takte = $temp1['takte'];
                $genre = $temp1['genre'];

                break;
               }
           }
        }
        
       if(in_array($id, $_SESSION['doppelt']))
          echo'<td align="center" bgcolor="#FFCC00"><input type="checkbox" name="' . $sk.$_SESSION[y] . '" value="1" checked>'; // . $id;
       else 
          echo'<td align="center"><input type="checkbox" name="' . $sk.$_SESSION[y] . '" value="1" checked>'; // . $id;
       $_SESSION[$sk][$_SESSION[y]] = $id;
       $_SESSION['doppelt'][] = $id;
//       echo $_SESSION[$sk][$_SESSION[y]] . '<bgcolor="#000000"></td>';
       echo'</td>';
      }
    else if(current($_SESSION[$sk]))
      {
      // echo'<td align="center"><input type="checkbox" name="' . $sk.$_SESSION[y] . '" value="1" checked>';
//       if(in_array($id, $_SESSION['doppelt']))  
       //if($_SESSION['doppelt'][$id] >= 2)
//         echo'<td align="center" bgcolor="#FFCC00"><input type="checkbox" name="' . $sk.$_SESSION[y] . '" value="1" checked>'; //. $id . '-1-'. $_SESSION['doppelt'][$id];
//       else
        echo'<td align="center"><input type="checkbox" name="' . $sk.$_SESSION[y] . '" value="1" checked>';
         
       //echo current($_SESSION[$sk]) . '<bgcolor="#000000"></td>';
      }

    else
      {
       echo'<td align="center"><input type="checkbox" name="' . $sk.$_SESSION[y] . '"</td>'; 
      }
      

echo'<td align="center">' . $rr . '</td><td align="center">' . $bw . '</td><td>' . $titel . '</td><td>' . $interpret . '</td><td align="center">' . $takte . '</td><td>' . $genre . '</td><td>' . $stern . '</td>';
 
    if($_POST['hoeren'] == 1)           
      echo'<td><audio controls>
  <source src="http://www.drbv.de/turniermusik/musikdb.php?pfad=' . $temp['pfad'] . '&file=' . $bezeichnung . '" type="audio/mpeg">
Your browser does not support the audio element.
</audio></td></tr>';
       else
         echo'<td>&nbsp;</td></tr>';

/*
if($y > 60)
exit('zuviele Datensätze');
*/
      }
   }

 function phpalert($msg)
   {
    echo'<script Type = "text/javascript">alert("' . $msg . '")</script>';
   }

// Download
if($_POST['download'])
   {
    if(!$_POST['dateiname'])
      {
       phpalert("Bitte einen Dateinamen angeben!");
       echo'<h1><center><a href="javascript:history.back()"> Zurück </a></center></h1>';
      }
     else if($_SESSION['erstellt'] != 2)
      {
       phpalert("Bitte zuerst das Turnier erstellen!");
       echo'<h1><center><a href="javascript:history.back()"> Zurück </a></center></h1>';
      }
   else
      {
       // Kopfzeile für Datei
       $separator   = ";";
       $valueQuotes = '"';

       $str_csv = $str_csv. $valueQuotes. 'Titel'. $valueQuotes.	
         $separator. $valueQuotes. 'Interpret'. $valueQuotes.	
         $separator. $valueQuotes. 'Takte'. $valueQuotes.	
         $separator. $valueQuotes. 'Genre'. $valueQuotes.	
         $separator. $valueQuotes. 'Link'. $valueQuotes.	
         $separator. $valueQuotes. 'Dateiname'. $valueQuotes.	
       $str_csv = $str_csv. "\r\n";  
// Runden

    if($_POST['s_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'S-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["s_klasse_v"]);
       for($x = 0;$x < count($_SESSION["s_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["s_klasse_v"]);
          }
      }
    if($_POST['s_klasse'] >= 7 && $_POST['hoff_s'])
      {
       $str_body .=  $valueQuotes . 'S-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["s_klasse_h"]);
       for($x = 0;$x < count($_SESSION["s_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["s_klasse_h"]);
          }
      }
    if($_POST['s_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'S-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["s_klasse_z"]);
       for($x = 0;$x < count($_SESSION["s_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["s_klasse_z"]);
          }
      }
    if($_POST['s_klasse'])
      {
       $str_body .=  $valueQuotes . 'S-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["s_klasse_e"]);
       for($x = 0;$x < count($_SESSION["s_klasse_e"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_e"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["s_klasse_e"]);
          }
      }

    if($_POST['j_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'J-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["j_klasse_v"]);
       for($x = 0;$x < count($_SESSION["j_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["j_klasse_v"]);
          }
      }
    if($_POST['j_klasse'] >= 7 && $_POST['hoff_j'])
      {
       $str_body .=  $valueQuotes . 'J-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["j_klasse_h"]);
       for($x = 0;$x < count($_SESSION["j_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["j_klasse_h"]);
          }
      }
    if($_POST['j_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'J-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["j_klasse_z"]);
       for($x = 0;$x < count($_SESSION["j_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["j_klasse_z"]);
          }
      }
    if($_POST['j_klasse'])
      {
       $str_body .=  $valueQuotes . 'J-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["j_klasse_e"]);
       for($x = 0;$x < count($_SESSION["j_klasse_e"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_e"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["j_klasse_e"]);
          }
      }

    if($_POST['c_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'C-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["c_klasse_v"]);
       for($x = 0;$x < count($_SESSION["c_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["c_klasse_v"]);
          }
      }
    if($_POST['c_klasse'] >= 7 && $_POST['hoff_c'])
      {
       $str_body .=  $valueQuotes . 'C-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["c_klasse_h"]);
       for($x = 0;$x < count($_SESSION["c_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["c_klasse_h"]);
          }
      }
    if($_POST['c_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'C-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["c_klasse_z"]);
       for($x = 0;$x < count($_SESSION["c_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["c_klasse_z"]);
          }
      }
    if($_POST['c_klasse'])
      {
       $str_body .=  $valueQuotes . 'C-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["c_klasse_e"]);
       for($x = 0;$x < count($_SESSION["c_klasse_e"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_e"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["c_klasse_e"]);
          }
      }

    if($_POST['b_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'B-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["b_klasse_v"]);
       for($x = 0;$x < count($_SESSION["b_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["b_klasse_v"]);
          }
      }
    if($_POST['b_klasse'] >= 7 && $_POST['hoff_b'])
      {
       $str_body .=  $valueQuotes . 'B-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["b_klasse_h"]);
       for($x = 0;$x < count($_SESSION["b_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["b_klasse_h"]);
          }
      }
    if($_POST['b_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'B-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["b_klasse_z"]);
       for($x = 0;$x < count($_SESSION["b_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["b_klasse_z"]);
          }
      }
    if($_POST['b_klasse'])
      {
       $str_body .=  $valueQuotes . 'B-Klasse Endrunde Akrobatik' . $valueQuotes ."\r\n";
       reset ($_SESSION["b_klasse_ea"]);
       for($x = 0;$x < count($_SESSION["b_klasse_ea"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_ea"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["b_klasse_ea"]);
          }
      }
    if($_POST['b_klasse'])
      {
       $str_body .=  $valueQuotes . 'B-Klasse Endrunde Fußtechnik' . $valueQuotes ."\r\n";
       reset ($_SESSION["b_klasse_ef"]);
       for($x = 0;$x < count($_SESSION["b_klasse_ef"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_ef"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["b_klasse_ef"]);
          }
      }
      
    if($_POST['a_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'A-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["a_klasse_v"]);
       for($x = 0;$x < count($_SESSION["a_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["a_klasse_v"]);
          }
      }
    if($_POST['a_klasse'] >= 7 && $_POST['hoff_a'])
      {
       $str_body .=  $valueQuotes . 'A-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["a_klasse_h"]);
       for($x = 0;$x < count($_SESSION["a_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["a_klasse_h"]);
          }
      }
    if($_POST['a_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'A-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["a_klasse_z"]);
       for($x = 0;$x < count($_SESSION["a_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["a_klasse_z"]);
          }
      }
    if($_POST['a_klasse'])
      {
       $str_body .=  $valueQuotes . 'A-Klasse Endrunde Akrobatik' . $valueQuotes ."\r\n";
       reset ($_SESSION["a_klasse_ea"]);
       for($x = 0;$x < count($_SESSION["a_klasse_ea"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_ea"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["a_klasse_ea"]);
          }
      }
    if($_POST['a_klasse'])
      {
       $str_body .=  $valueQuotes . 'A-Klasse Endrunde Fußtechnik' . $valueQuotes ."\r\n";
       reset ($_SESSION["a_klasse_ef"]);
       for($x = 0;$x < count($_SESSION["a_klasse_ef"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_ef"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["a_klasse_ef"]);
          }
      }
      
      
      
      
    if($_POST['bw_j_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'BW J-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_j_klasse_v"]);
       for($x = 0;$x < count($_SESSION["bw_j_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["bw_j_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_j_klasse_v"]);
          }
      }
    if($_POST['bw_j_klasse'] >= 7 && $_POST['hoff_bj'])
      {
       $str_body .=  $valueQuotes . 'BW J-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_j_klasse_h"]);
       for($x = 0;$x < count($_SESSION["bw_j_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["bw_j_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_j_klasse_h"]);
          }
      }
    if($_POST['bw_j_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'BW J-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_j_klasse_z"]);
       for($x = 0;$x < count($_SESSION["bw_j_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["bw_j_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_j_klasse_z"]);
          }
      }
    if($_POST['bw_j_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW J-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_j_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_j_klasse_se"]);$x++)
          {
           $db_id = current($_SESSION["bw_j_klasse_se"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_j_klasse_se"]);
          }
      }
    if($_POST['bw_ma_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'BW MA-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_ma_klasse_v"]);
       for($x = 0;$x < count($_SESSION["bw_ma_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["bw_ma_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_ma_klasse_v"]);
          }
      }
    if($_POST['bw_ma_klasse'] >= 7 && $_POST['hoff_ma'])
      {
       $str_body .=  $valueQuotes . 'BW MA-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_ma_klasse_h"]);
       for($x = 0;$x < count($_SESSION["bw_ma_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["bw_ma_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_ma_klasse_h"]);
          }
      }
    if($_POST['bw_ma_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'BW MA-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_ma_klasse_z"]);
       for($x = 0;$x < count($_SESSION["bw_ma_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["bw_ma_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_ma_klasse_z"]);
          }
      }
          if($_POST['bw_ma_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW MA-Klasse langsame Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_ma_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_ma_klasse_le"]);$x++)
          {
           $db_id = current($_SESSION["bw_ma_klasse_le"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_ma_klasse_le"]);
          }
      }
    if($_POST['bw_ma_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW MA-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_ma_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_ma_klasse_se"]);$x++)
          {
           $db_id = current($_SESSION["bw_ma_klasse_se"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_ma_klasse_se"]);
          }
      }
    if($_POST['bw_sa_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'BW SA-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sa_klasse_v"]);
       for($x = 0;$x < count($_SESSION["bw_sa_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["bw_sa_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sa_klasse_v"]);
          }
      }
    if($_POST['bw_sa_klasse'] >= 7 && $_POST['hoff_ms'])
      {
       $str_body .=  $valueQuotes . 'BW SA-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sa_klasse_h"]);
       for($x = 0;$x < count($_SESSION["bw_sa_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["bw_sa_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sa_klasse_h"]);
          }
      }
    if($_POST['bw_sa_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'BW SA-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sa_klasse_z"]);
       for($x = 0;$x < count($_SESSION["bw_sa_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["bw_sa_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sa_klasse_z"]);
          }
      }
          if($_POST['bw_sa_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW SA-Klasse langsame Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sa_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_sa_klasse_le"]);$x++)
          {
           $db_id = current($_SESSION["bw_sa_klasse_le"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sa_klasse_le"]);
          }
      }
    if($_POST['bw_sa_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW SA-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sa_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_sa_klasse_se"]);$x++)
          {
           $db_id = current($_SESSION["bw_sa_klasse_se"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sa_klasse_se"]);
          }
      }
    if($_POST['bw_mb_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'BW MB-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_mb_klasse_v"]);
       for($x = 0;$x < count($_SESSION["bw_mb_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["bw_mb_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_mb_klasse_v"]);
          }
      }
    if($_POST['bw_mb_klasse'] >= 7 && $_POST['hoff_mb'])
      {
       $str_body .=  $valueQuotes . 'BW MB-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_mb_klasse_h"]);
       for($x = 0;$x < count($_SESSION["bw_mb_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["bw_mb_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_mb_klasse_h"]);
          }
      }
    if($_POST['bw_mb_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'BW MB-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_mb_klasse_z"]);
       for($x = 0;$x < count($_SESSION["bw_mb_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["bw_mb_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_mb_klasse_z"]);
          }
      }
    if($_POST['bw_mb_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW MB-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_mb_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_mb_klasse_se"]);$x++)
          {
           $db_id = current($_SESSION["bw_mb_klasse_se"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_mb_klasse_se"]);
          }
      }
    if($_POST['bw_sb_klasse'] >= 7)
      {
       $str_body .=  $valueQuotes . 'BW SB-Klasse Vorrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sb_klasse_v"]);
       for($x = 0;$x < count($_SESSION["bw_sb_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["bw_sb_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sb_klasse_v"]);
          }
      }
    if($_POST['bw_sb_klasse'] >= 7 && $_POST['hoff_sb'])
      {
       $str_body .=  $valueQuotes . 'BW SB-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sb_klasse_h"]);
       for($x = 0;$x < count($_SESSION["bw_sb_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["bw_sb_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sb_klasse_h"]);
          }
      }
    if($_POST['bw_sb_klasse'] > 20)
      {
       $str_body .=  $valueQuotes . 'BW SB-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sb_klasse_z"]);
       for($x = 0;$x < count($_SESSION["bw_sb_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["bw_sb_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sb_klasse_z"]);
          }
      }
    if($_POST['bw_sb_klasse'])
      {
       $str_body .=  $valueQuotes . 'BW SB-Klasse Endrunde' . $valueQuotes ."\r\n";
       reset ($_SESSION["bw_sb_klasse_e"]);
       for($x = 0;$x < count($_SESSION["bw_sb_klasse_se"]);$x++)
          {
           $db_id = current($_SESSION["bw_sb_klasse_se"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           $datensatz = mysqli_query($db, $sqlab);
           $temp = mysqli_fetch_array($datensatz);
           $str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $temp['bezeichnung'] . $valueQuotes . $separator . $valueQuotes . $temp['pfad'] . $temp['bezeichnung'] . $valueQuotes ."\r\n";
           $inhalt = $str_csv . $str_body;
           next($_SESSION["bw_sb_klasse_se"]);
          }
      }


   // Dateinamen festlegen
     $filename = $_POST['dateiname'] . ".csv";
     // Datei auf Server speichern
     $fn = "./" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $inhalt);
     fclose($fp);
      if(is_file($fn))
      //   echo"Die Datei $filename wurde gespeichert!<br><br>"; 
       header("Location: ./download.php?dateiname=" . $_POST['dateiname']);
      }
   }
else
   {

if($_POST['von']  != '---' && $_POST['bis'] == '---')
   $_POST['bis'] = $_POST['von'];
if($_POST['von']  == '---' && $_POST['bis'] != '---')
   $_POST['bis'] = '---';

/*   
if($_POST['anzeigen'] && !$_POST['dateiname'])
    {
     phpalert("Bitte einen Dateinamen angeben!");
     echo'<h1><center><a href="javascript:history.back()"> Zurück </a></center></h1>';
     exit();
    }
*/

 if(($_POST['von'] !='---' && $_POST['bis'] != '---') && $_POST['von'] > $_POST['bis'])
    {
     phpalert("Takte bis darf nicht kleiner als Takte von sein!");
    }

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Musikdatenbank</title>
</head>
<body>

<?php

echo'<h1><center>Turniermusik DRBV</h1>';

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<center><table border="1">';

// Startklassen

echo'<tr><th width="200" align="center">Anzahl</th><th width="200" align="center">Startklasse</th><th width="180">Takte</th><th width="150">Genre</th><th width="150">Sterne</th></tr>';
echo'<tr><td align="center">';

// Anzahl Lieder

   echo'<select name="anzahl" onchange="submit();">';
   if($_POST["anzahl"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';

   for($x = 1; $x < 26; $x++ )
   {
    if($_POST["anzahl"] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }

   echo'</select>';
   echo' Lieder </td><td align="center">';


   echo'<select name="startklasse" onchange="submit();">';
   if($_POST["startklasse"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($_POST["startklasse"] == 'rocknroll')
      echo'<option value="rocknroll" selected>Rock´n´Roll</option>';
   else
      echo'<option value="rocknroll">Rock´n´Roll</option>';
   if($_POST["startklasse"] == 'boogiewoogie')
      echo'<option value="boogiewoogie" selected>Boogie Woogie</option>';
   else
      echo'<option value="boogiewoogie">Boogie Woogie</option>';

   echo'</select>';
   
// Takte

   echo'</td><td align="center">von ';

   echo'<select name="von" onchange="submit();">';
   if($_POST["von"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';

   for($x = 27; $x < 54; $x++ )
   {
    if($_POST["von"] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }

   echo'</select>';
   echo' bis ';

   echo'<select name="bis" onchange="submit();">';
   if($_POST["bis"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';

   for($x = 27; $x < 54; $x++ )
   {
    if($_POST["bis"] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }      
   echo'</select>';

// Genre

   echo'</td><td align="center">';
   
   echo'<select name="genre"onchange="submit();">';
   
   if($_POST["genre"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';

// Genres einlesen

   $sqlab = 'SELECT genre FROM turniermusik ORDER BY genre';
   $genres_auswahl = mysqli_query($db, $sqlab);

   while($temp = mysqli_fetch_row($genres_auswahl))
     {

      if($temp[0] != $akt)
         {
          if($_POST["genre"] == $temp[0])
              echo'<option value="' . $temp[0] . '" selected>' . $temp[0] . '</option>';
          else
              echo'<option value="' . $temp[0] . '">' . $temp[0] . '</option>';
              
          $akt = $temp[0];
         }
     }
   echo'</select>';
   echo'</td><td align="center">';

// Sterne

   echo'<select name="wertung" onchange="submit();">';
   if($_POST["wertung"] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';

   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["wertung"] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }

   echo'</select>';
   echo' Sterne';

echo'</td></tr>';

// Turnier erstellen

echo'<tr><td>';
echo' Turniermusik erstellen '; 
   if($_POST['turnier_erstellen'] == 1)
      echo'<input type="checkbox" name="turnier_erstellen" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="turnier_erstellen" value="1" onchange="submit();">';
echo'</td>';

echo'<td>Anzahl Teilnehmer RR:</td><td>&nbsp;S <input type=text name="s_klasse" value="' . $_POST['s_klasse'] . '"  size="2" maxlength="2"> J <input type=text name="j_klasse" value="' . $_POST['j_klasse'] . '"  size="2" maxlength="2"> C <input type=text name="c_klasse" value="' . $_POST['c_klasse'] . '"  size="2" maxlength="2"> B <input type=text name="b_klasse" value="' . $_POST['b_klasse'] . '"  size="2" maxlength="2"> A <input type=text name="a_klasse" value="' . $_POST['a_klasse'] . '"  size="2" maxlength="2"></td><td colspan ="2">&nbsp;BW: J <input type=text name="bw_j_klasse" value="' . $_POST['bw_j_klasse'] . '"  size="2" maxlength="2"> MA <input type=text name="bw_ma_klasse" value="' . $_POST['bw_ma_klasse'] . '"  size="2" maxlength="2"> SA <input type=text name="bw_sa_klasse" value="' . $_POST['bw_sa_klasse'] . '"  size="2" maxlength="2"> MB <input type=text name="bw_mb_klasse" value="' . $_POST['bw_mb_klasse'] . '"  size="2" maxlength="2"> SB <input type=text name="bw_sb_klasse" value="' . $_POST['bw_sb_klasse'] . '"  size="2" maxlength="2"></td>';
echo'</tr>';

echo'<tr><td>Nur WRRC-Titel BW: ';
   if($_POST['wrrc'] == 1)
      echo'<input type="checkbox" name="wrrc" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="wrrc" value="1" onchange="submit();">';
echo'</td></td><td>Hoffnungsrunde durchführen:</td><td>';

echo'&nbsp;S ';
   if($_POST['hoff_s'] == 1)
      echo'<input type="checkbox" name="hoff_s" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_s" value="1">';
echo'&nbsp;&nbsp;&nbsp;J ';
   if($_POST['hoff_j'] == 1)
      echo'<input type="checkbox" name="hoff_j" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_j" value="1">';
echo'&nbsp;&nbsp;&nbsp;C ';
   if($_POST['hoff_c'] == 1)
      echo'<input type="checkbox" name="hoff_c" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_c" value="1">';
echo'&nbsp;&nbsp;&nbsp;B ';
   if($_POST['hoff_b'] == 1)
      echo'<input type="checkbox" name="hoff_b" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_b" value="1">';
echo'&nbsp;&nbsp;&nbsp;A ';
   if($_POST['hoff_a'] == 1)
      echo'<input type="checkbox" name="hoff_a" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_a" value="1">';
echo'</td>';

echo'<td colspan="2">&nbsp;BW: J&nbsp;';
   if($_POST['hoff_bj'] == 1)
      echo'<input type="checkbox" name="hoff_bj" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_bj" value="1">';
echo'&nbsp;&nbsp;&nbsp;MA ';
   if($_POST['hoff_ma'] == 1)
      echo'<input type="checkbox" name="hoff_ma" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_ma" value="1">';
echo'&nbsp;&nbsp;&nbsp;SA ';
   if($_POST['hoff_ms'] == 1)
      echo'<input type="checkbox" name="hoff_ms" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_ms" value="1">';
echo'&nbsp;&nbsp;&nbsp;MB ';
   if($_POST['hoff_mb'] == 1)
      echo'<input type="checkbox" name="hoff_mb" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_mb" value="1">';
echo'&nbsp;&nbsp;&nbsp;SB ';
   if($_POST['hoff_sb'] == 1)
      echo'<input type="checkbox" name="hoff_sb" value="1" checked>';
   else
      echo'<input type="checkbox" name="hoff_sb" value="1">';

// Endrunden-Genre
echo'<br>Endrunden Genre<br>';

   echo'MA: <select name="ma_genre">';
   $sqlab = 'SELECT genre FROM turniermusik ORDER BY genre';
   $genres_auswahl = mysqli_query($db, $sqlab);

   while($temp = mysqli_fetch_row($genres_auswahl))
     {

      if($temp[0] != $akt)
         {          if($_POST["ma_genre"] == $temp[0])
              echo'<option value="' . $temp[0] . '" selected>' . $temp[0] . '</option>';
          else
              echo'<option value="' . $temp[0] . '">' . $temp[0] . '</option>';
          $akt = $temp[0];
         }
     }
   echo'</select>';
   
   echo' SA: <select name="sa_genre">';
   $sqlab = 'SELECT genre FROM turniermusik ORDER BY genre';
   $genres_auswahl = mysqli_query($db, $sqlab);

   while($temp = mysqli_fetch_row($genres_auswahl))
     {

      if($temp[0] != $akt)
         {          if($_POST["sa_genre"] == $temp[0])
              echo'<option value="' . $temp[0] . '" selected>' . $temp[0] . '</option>';
          else
              echo'<option value="' . $temp[0] . '">' . $temp[0] . '</option>';
          $akt = $temp[0];
         }
     }
   echo'</select>';
echo'</td>';

echo'<tr><td>Dateiname: <input type=text name="dateiname" value="' . $_POST['dateiname'] . '"  size="15" maxlength="20"></td><td align="center"><input type="submit" name="anzeigen" value="Anzeigen"></td><td align="center"><input type="submit" name="download" value="Für Musikdownloader herunterladen"></td><td align="center">';
   if($_POST['hoeren'] == 1)
      echo'<input type="checkbox" name="hoeren" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="hoeren" value="1" onchange="submit();">';
echo' Musik anhören'; 


echo'</td><td align="center"><input type="submit" name="ruecksetzen" value="Zurücksetzten"></td></tr>';
echo'</table>';

echo'<p />';

// Bildschirmausgabe

echo'<center><table border="1">';
echo'<tr><th width="20" align="center">Nr.</th><th width="40" align="center">Wahl</th><th width="20" align="center">RR</th><th width="20" align="center">BW</th><th width="300">Titel</th><th width="300">Interpret</th><th width="50">Takte</th><th width="100">Genre</th><th width="82">Sterne</th><th width="150">Anhören</th></tr>';

$sqlab = 'SELECT * From turniermusik ';

if($_POST['startklasse'] =='rocknroll')
    $sqlab .= ' WHERE rocknroll = 1 ';
    
if($_POST['startklasse'] =='boogiewoogie' && $_POST['wrrc'] != 1)
   $sqlab .= ' WHERE boogiewoogie = 1 ';
if($_POST['startklasse'] =='boogiewoogie' && $_POST['wrrc'] == 1)
   $sqlab .= ' WHERE boogiewoogie = 1 AND wrrc = 1';   
   
if($_POST['startklasse'] != '---' && $_POST['von'] != '---' && $_POST['bis'] != '---')
   $sqlab .= ' AND  takte >= ' . $_POST['von'] . ' AND takte <= ' . $_POST['bis'];
elseif($_POST['von'] != '---' && $_POST['bis'] != '---')
   $sqlab .= ' WHERE takte >= ' . $_POST['von'] . ' AND takte <= ' . $_POST['bis'];  

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---') && $_POST['genre'] != '---')
   $sqlab .= ' AND  genre LIKE "' . $_POST['genre'] . '" ';
elseif($_POST['genre'] != '---')
   $sqlab .= ' WHERE genre LIKE "' . $_POST['genre'] . '" ';  

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---') && $_POST['wertung'] != '---')
   $sqlab .= ' AND  wertung LIKE "' . $_POST['wertung'] . '" ';
elseif($_POST['wertung'] != '---')
   $sqlab .= ' WHERE wertung LIKE "' . $_POST['wertung'] . '" ';  
   
if($_POST['anzahl'] !='---')
    $sqlab .= ' ORDER BY RAND() LIMIT ' . $_POST['anzahl'];

if($sqlab == 'SELECT * From turniermusik ' )
   $sqlab = 'SELECT * From turniermusik LIMIT 50';
   
if(!$_POST['turnier_erstellen']) 
   datenbankabfrage($db, $sqlab, $valueQuotes, $separator);
else
 {
// DB-Abfrage bei Turnier erstellen

if($_POST['turnier_erstellen'] && !$_SESSION['erstellt'])
   {
    $_SESSION['erstellt'] = 1;
    echo'<h3>Erstellroutine!</h3>';
// S-Klasse    
    if($_POST['s_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">S-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'S-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['s_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_v', $ersatz);
      }
    if($_POST['s_klasse'] >= 7 && $_POST['hoff_s'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">S-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'S-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['s_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_h', $ersatz);
      }
    if($_POST['s_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">S-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'S-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_z', $ersatz);
      }      
    if($_POST['s_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">S-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'S-Klasse Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 7';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_e', $ersatz);
      }
// J_Klasse
    if($_POST['j_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">J-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'J-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['j_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_v', $ersatz);
      }
    if($_POST['j_klasse'] >= 7 && $_POST['hoff_j'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">J-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'J-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['j_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_h', $ersatz);
      }
    if($_POST['j_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">J-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'J-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_z', $ersatz);
      }        
    if($_POST['j_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">J-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'J-Klasse Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="47" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 7';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="47" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_e', $ersatz);
      }
// C_Klasse
    if($_POST['c_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">C-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'C-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['c_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="48" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="48" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_v', $ersatz);
      }
    if($_POST['c_klasse'] >= 7 && $_POST['hoff_c'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">C-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'C-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['c_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="48" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="48" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_h', $ersatz);
      }
    if($_POST['c_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">C-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'C-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="48" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="48" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_z', $ersatz);
      } 
    if($_POST['c_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">C-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'C-Klasse Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="48" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 7';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="48" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_e', $ersatz);
      }
// B_Klasse
    if($_POST['b_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">B-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'B-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['b_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_v', $ersatz);
      }
    if($_POST['b_klasse'] >= 7 && $_POST['hoff_b'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">B-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'B-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['b_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_h', $ersatz);
      }
    if($_POST['b_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">B-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'B-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_z', $ersatz);
      }      
    if($_POST['b_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">B-Klasse Endrunde Akrobatik</th></tr>';
       $str_body .=  $valueQuotes . 'B-Klasse Endrunde Akro' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 3';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_ea', $ersatz);
      }
    if($_POST['b_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">B-Klasse Endrunde Fußtechnik</th></tr>';
       $str_body .=  $valueQuotes . 'B-Klasse Endrunde FT' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="50" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 5';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="50" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_ef', $ersatz);
      }
// A_Klasse
    if($_POST['a_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">A-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'A-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['a_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung >="2" AND wertung <="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_v', $ersatz);
      }
    if($_POST['a_klasse'] >= 7 && $_POST['hoff_a'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">A-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'A-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['a_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung ="3" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_h', $ersatz);
      }
    if($_POST['a_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">A-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'A-Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung ="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_z', $ersatz);
      }      
    if($_POST['a_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">A-Klasse Endrunde Akrobatik</th></tr>';
       $str_body .=  $valueQuotes . 'A-Klasse Endrunde Akro' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="49" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 3';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="49" AND rocknroll ="1" AND wertung ="5" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_ea', $ersatz);
      }
    if($_POST['a_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="8">A-Klasse Endrunde Fußtechnik</th></tr>';
       $str_body .=  $valueQuotes . 'A-Klasse Endrunde FT' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE  takte ="51" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 5';
       $ersatz = 'SELECT * From turniermusik WHERE takte ="51" AND rocknroll ="1" AND wertung >="4" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_ef', $ersatz);
      }

/****************************************************************/
// Boogie Woogie

//BW J_Klasse
    if($_POST['bw_j_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW J-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW J-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_j_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_j_klasse_v', $ersatz);
      }
    if($_POST['bw_j_klasse'] >= 7 && $_POST['hoff_bj'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW J-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW J-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_j_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_j_klasse_h', $ersatz);
      }
    if($_POST['bw_j_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW J-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW J--Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_j_klasse_z', $ersatz);
      }      
    if($_POST['bw_j_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW J-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW J-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_j_klasse_se', $ersatz);
      }

//BW MA_Klasse
    if($_POST['bw_ma_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MA-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_ma_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_ma_klasse_v', $ersatz);
      }
    if($_POST['bw_ma_klasse'] >= 7 && $_POST['hoff_ma'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MA-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_ma_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_ma_klasse_h', $ersatz);
      }
    if($_POST['bw_ma_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MA-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA--Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_ma_klasse_z', $ersatz);
      }      
    if($_POST['bw_ma_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MA-Klasse langsame Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA-Klasse langsame Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="27" AND takte <="30" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="27" AND takte <="30" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_ma_klasse_le', $ersatz);
      }
    if($_POST['bw_ma_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MA-Klasse schnelle Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="50" AND takte <="54" AND boogiewoogie ="1" AND genre LIKE "' . $_POST['ma_genre'] . '" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="50" AND takte <="54" AND boogiewoogie ="1" AND genre LIKE "' . $_POST['ma_genre'] . '" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_ma_klasse_se', $ersatz);
      }

//BW SA_Klasse
    if($_POST['bw_sa_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SA-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SA-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_sa_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sa_klasse_v', $ersatz);
      }
    if($_POST['bw_sa_klasse'] >= 7 && $_POST['hoff_ms'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SA-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SA-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_sa_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sa_klasse_h', $ersatz);
      }
    if($_POST['bw_sa_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SA-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA--Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="46" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sa_klasse_z', $ersatz);
      }      
    if($_POST['bw_sa_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SA-Klasse langsame Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SA-Klasse langsame Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="27" AND takte <="30" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="27" AND takte <="30" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sa_klasse_le', $ersatz);
      }
    if($_POST['bw_sa_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SA-Klasse schnelle Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SA-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="48" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "' . $_POST['sa_genre'] . '" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="48" AND takte <="50" AND boogiewoogie ="1" AND genre LIKE "' . $_POST['sa_genre'] . '" ORDER BY RAND() LIMIT 50';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sa_klasse_se', $ersatz);
      }

//BW MB_Klasse
    if($_POST['bw_mb_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MB-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MB-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_mb_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_mb_klasse_v', $ersatz);
      }
    if($_POST['bw_mb_klasse'] >= 7 && $_POST['hoff_ma'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MB-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MB-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_mb_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_mb_klasse_h', $ersatz);
      }
    if($_POST['bw_mb_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MB-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA--Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_mb_klasse_z', $ersatz);
      }      
    if($_POST['bw_mb_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW MB-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MB-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  4;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_mb_klasse_se', $ersatz);
      }

//BW SB_Klasse
    if($_POST['bw_sb_klasse'] >= 7)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SB-Klasse Vorrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SB-Klasse Vorrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_sb_klasse'] +0.5) / 2) + 3;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sb_klasse_v', $ersatz);
      }
    if($_POST['bw_sb_klasse'] >= 7 && $_POST['hoff_ms'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SB-Klasse Hoffnungsrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SB-Klasse Hoffnungsrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  round(($_POST['bw_sb_klasse'] +0.5) / 2);
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sb_klasse_h', $ersatz);
      }
    if($_POST['bw_sb_klasse'] > 20)
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SB-Klasse Zwischenrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW MA--Klasse Zwischenrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  10;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sb_klasse_z', $ersatz);
      }      
    if($_POST['bw_sb_klasse'])
      {
       unset($y);
       unset($_SESSION[y]);
       echo'<tr><th colspan ="10">BW SB-Klasse Endrunde</th></tr>';
       $str_body .=  $valueQuotes . 'BW SB-Klasse schnelle Endrunde' . $valueQuotes ."\r\n";
       $anzahl_s =  4;
       $sqlab = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT ' . $anzahl_s;
       $ersatz = 'SELECT * From turniermusik WHERE takte >="44" AND takte <="48" AND boogiewoogie ="1" AND genre LIKE "%%" ORDER BY RAND() LIMIT 48';
       datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'bw_sb_klasse_se', $ersatz);
      }

 }
     }

if($_SESSION['erstellt'] == 2)
 {
// DB-Abfrage bei Turnier schon erstellt
    echo'<h3>Erstellroutine!</h3>';
// S-Klasse    
    if($_POST['s_klasse'] >= 7)
      {
       echo'<tr><th colspan ="8">S-Klasse Vorrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["s_klasse_v"]);
       for($x = 0;$x < count($_SESSION["s_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_v');
           next($_SESSION["s_klasse_v"]);
          }
       unset($y);
      }  
    if($_POST['s_klasse'] >= 7 && $_POST['hoff_s'])
      {
       echo'<tr><th colspan ="8">S-Klasse Hoffnungsrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["s_klasse_h"]);
       for($x = 0;$x < count($_SESSION["s_klasse_h"]);$x++)
         {
          $db_id = current($_SESSION["s_klasse_h"]);
          $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
          datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_h');
          next($_SESSION["s_klasse_h"]);
         }
       unset($y);
      }
    if($_POST['s_klasse'] > 20)
      {
       echo'<tr><th colspan ="8">S-Klasse Zwischenrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["s_klasse_z"]);
       for($x = 0;$x < count($_SESSION["s_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["s_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_z');
           next($_SESSION["s_klasse_z"]);
          }
       unset($y);
      }      
    if($_POST['s_klasse'])
      {
       echo'<tr><th colspan ="8">S-Klasse Endrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["s_klasse_e"]);
       for($x = 0;$x < count($_SESSION["s_klasse_e"]);$x++)
         {
          $db_id = current($_SESSION["s_klasse_e"]);
          $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
          datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 's_klasse_e');
          next($_SESSION["s_klasse_e"]);
         }
       unset($y);
      }
// J_Klasse
    if($_POST['j_klasse'] >= 7)
      {
       echo'<tr><th colspan ="8">J-Klasse Vorrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["j_klasse_v"]);
       for($x = 0;$x < count($_SESSION["j_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_v');
           next($_SESSION["j_klasse_v"]);
          }
       unset($y);
      }
    if($_POST['j_klasse'] >= 7 && $_POST['hoff_j'])
      {
       echo'<tr><th colspan ="8">J-Klasse Hoffnungsrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["j_klasse_h"]);
       for($x = 0;$x < count($_SESSION["j_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_h');
           next($_SESSION["j_klasse_h"]);
          }
       unset($y);
      }
    if($_POST['j_klasse'] > 20)
      {
       echo'<tr><th colspan ="8">J-Klasse Zwischenrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["j_klasse_z"]);
       for($x = 0;$x < count($_SESSION["j_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_z');
           next($_SESSION["j_klasse_z"]);
          }
       unset($y);
      }      
    if($_POST['j_klasse'])
      {
       echo'<tr><th colspan ="8">J-Klasse Endrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["j_klasse_e"]);
       for($x = 0;$x < count($_SESSION["j_klasse_e"]);$x++)
          {
           $db_id = current($_SESSION["j_klasse_e"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'j_klasse_e');
           next($_SESSION["j_klasse_e"]);
          }
       unset($y);
      }
// C-Klasse    
    if($_POST['c_klasse'] >= 7)
      {
       echo'<tr><th colspan ="8">C-Klasse Vorrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["c_klasse_v"]);
       for($x = 0;$x < count($_SESSION["c_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_v');
           next($_SESSION["c_klasse_v"]);
          }
       unset($y);
      }
    if($_POST['c_klasse'] >= 7 && $_POST['hoff_c'])
      {
       echo'<tr><th colspan ="8">C-Klasse Hoffnungsrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["c_klasse_h"]);
       for($x = 0;$x < count($_SESSION["c_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_h');
           next($_SESSION["c_klasse_h"]);
          }
       unset($y);
      }
    if($_POST['c_klasse'] > 20)
      {
       echo'<tr><th colspan ="8">C-Zwischenrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["c_klasse_z"]);
       for($x = 0;$x < count($_SESSION["c_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_z');
           next($_SESSION["c_klasse_z"]);
          }
       unset($y);
      }
    if($_POST['c_klasse'])
      {
       echo'<tr><th colspan ="8">C-Klasse Endrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["c_klasse_e"]);
       for($x = 0;$x < count($_SESSION["c_klasse_e"]);$x++)
          {
           $db_id = current($_SESSION["c_klasse_e"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'c_klasse_e');
           next($_SESSION["c_klasse_e"]);
          }
       unset($y);
      }
// B-Klasse    
    if($_POST['b_klasse'] >= 7)
      {
      echo'<tr><th colspan ="8">B-Klasse Vorrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["b_klasse_v"]);
       for($x = 0;$x < count($_SESSION["b_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_v');
           next($_SESSION["b_klasse_v"]);
          }
       unset($y);
      }
    if($_POST['b_klasse'] >= 7 && $_POST['hoff_b'])
      {
       echo'<tr><th colspan ="8">B-Klasse Hoffnungsrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["b_klasse_h"]);
       for($x = 0;$x < count($_SESSION["b_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_h');
           next($_SESSION["b_klasse_h"]);
          }
       unset($y);
      } 
    if($_POST['b_klasse'] > 20)
      {
       echo'<tr><th colspan ="8">B-Klasse Zwischenrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["b_klasse_z"]);
       for($x = 0;$x < count($_SESSION["b_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_z');
           next($_SESSION["b_klasse_z"]);
          }
       unset($y);
      }      
    if($_POST['b_klasse'])
      {
       echo'<tr><th colspan ="8">B-Klasse Endrunde Akrobatik</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["b_klasse_ea"]);
       for($x = 0;$x < count($_SESSION["b_klasse_ea"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_ea"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_ea');
           next($_SESSION["b_klasse_ea"]);
          }
       unset($y);
      }
    if($_POST['b_klasse'])
      {
       echo'<tr><th colspan ="8">B-Klasse Endrunde Fußtechnik</th></tr>';
       unset($_SESSION[y]); 
       reset ($_SESSION["b_klasse_ef"]);
       for($x = 0;$x < count($_SESSION["b_klasse_ef"]);$x++)
          {
           $db_id = current($_SESSION["b_klasse_ef"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'b_klasse_ef');
           next($_SESSION["b_klasse_ef"]);
          }
       unset($y);
      }
// A-Klasse    
    if($_POST['a_klasse'] >= 7)
      {
      echo'<tr><th colspan ="8">A-Klasse Vorrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["a_klasse_v"]);
       for($x = 0;$x < count($_SESSION["a_klasse_v"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_v"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_v');
           next($_SESSION["a_klasse_v"]);
          }
       unset($y);
      }
    if($_POST['a_klasse'] >= 7 && $_POST['hoff_a'])
      {
       echo'<tr><th colspan ="8">A-Klasse Hoffnungsrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["a_klasse_h"]);
       for($x = 0;$x < count($_SESSION["a_klasse_h"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_h"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_h');
           next($_SESSION["a_klasse_h"]);
          }
       unset($y);
      } 
    if($_POST['a_klasse'] > 20)
      {
       echo'<tr><th colspan ="8">A-Klasse Zwischenrunde</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["a_klasse_z"]);
       for($x = 0;$x < count($_SESSION["a_klasse_z"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_z"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_z');
           next($_SESSION["a_klasse_z"]);
          }
       unset($y);
      }      
    if($_POST['a_klasse'])
      {
       echo'<tr><th colspan ="8">A-Klasse Endrunde Akrobatik</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["a_klasse_ea"]);
       for($x = 0;$x < count($_SESSION["a_klasse_ea"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_ea"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_ea');
           next($_SESSION["a_klasse_ea"]);
          }
       unset($y);
      }
    if($_POST['a_klasse'])
      {
       echo'<tr><th colspan ="8">A-Klasse Endrunde Fußtechnik</th></tr>';
       unset($_SESSION[y]);
       reset ($_SESSION["a_klasse_ef"]);
       for($x = 0;$x < count($_SESSION["a_klasse_ef"]);$x++)
          {
           $db_id = current($_SESSION["a_klasse_ef"]);
           $sqlab = 'SELECT * From turniermusik WHERE id="' . $db_id .'"';
           datenbankabfrage($db, $sqlab, $valueQuotes, $separator, 'a_klasse_ef');
           next($_SESSION["a_klasse_ef"]);
          }
       unset($y);
      }


    $anzahl_bw_j =  round(($_POST['bw_j_klasse'] +0.5) / 2) + 3;
    $anzahl_bw_ma =  round(($_POST['bw_ma_klasse'] +0.5) / 2) + 3;
    $anzahl_bw_sa =  round(($_POST['bw_sa_klasse'] +0.5) / 2) + 3;
    $anzahl_bw_mb =  round(($_POST['bw_mb_klasse'] +0.5) / 2) + 3;
    $anzahl_bw_sb =  round(($_POST['bw_sb_klasse'] +0.5) / 2) + 3;
     }     
     

     
     echo'</table><br>';
          
 }
   
echo'Gesamt: ' . $anzahl_auswahl . ' Titel<br>';

if($_SESSION['erstellt'] == 1)
   $_SESSION['erstellt'] = 2;

echo'</form>';

echo'Doppelte auflisten'. '<br>';
$doppelte = array_count_values($_SESSION['doppelt']);
while ( list ( $key, $val ) = each ( $doppelte ) )
{
$i = $i + 1;
if($val > 1)
    echo $i .  ' -> ' . $key . ' kommt ' . $val . ' mal vor.<br>';
$gesamt_titel = $gesamt_titel + $val;
}
echo'Gesamttitel: ' . $gesamt_titel . '<br>';

?>


</body>
</html>
