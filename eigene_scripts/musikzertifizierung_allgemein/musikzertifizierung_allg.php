<?php

session_start();

require_once("../intern/tickets.inc.php");

// require_once("../intern/bereinigen.php");

echo'<h2>Musikzertifizierung allgemeine Titel</h2>';
echo'<a href="index.php" target="_blank">zur Bewerterseite</a><br><br>';
// echo'<a href="../../../ticketsystem/admin/admin_main.php" target="_blank">zum Ticketsystem</a><br><br>';
//  print_r($_POST);echo" Post<br>";
//  print_r($_SESSION);echo" SESSION<br>";

  
// remove ID3 Tags
// ---------------
$TextEncoding = 'UTF-8';
require_once('../getID3/getid3/getid3.php');

// Initialize getID3 engine
$getID3 = new getID3;
$getID3->setOption(array('encoding'=>$TextEncoding));
  
require_once('../getID3/getid3/write.php');
// Initialize getID3 tag-writing module
$tagwriter = new getid3_writetags;

// set various options (optional)
$tagwriter->tagformats        = array('id3v1', 'id3v2.3');
$tagwriter->overwrite_tags    = true; // if true will erase existing tag data and write only passed data; if false will merge passed data with existing tag data (experimental)
$tagwriter->remove_other_tags = true; // if true removes other tag formats (e.g. ID3v1, ID3v2, APE, Lyrics3, etc) that may be present in the file and only write the specified tag format(s). If false leaves any unspecified tag formats as-is.
$tagwriter->tag_encoding      = $TextEncoding;
$tagwriter->remove_other_tags = true;

// populate data array
$TagData = array(
  'title'   => array(''),
  'artist'  => array(''),
  'album'   => array(''),
  'year'    => array(''),
  'genre'   => array(''),
  'comment' => array(''),
  'track'   => array(''),
);
$tagwriter->tag_data = $TagData;  
// -------------------  
// end remove ID3 Tags  

function umlaute($string)
{
$search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´");
$replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "");
return str_replace($search, $replace, $string);
}
  
// Tabelle und Variablen erstellen

$gewaehlt = $_POST['datei'];

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

if(!$_SESSION['anzeige'])
   $_SESSION['anzeige'] = '0';
if($_POST['anzeige'] == '0') 
  {
   $_SESSION['anzeige'] = '0';
  }
if($_POST['anzeige'] == "1" )
  {
   $_SESSION['anzeige'] = '1';
  }

echo'<table>';
echo'<tr><th width="180" align="center">offene Tickets</th><th width="180" align="center">abgeschlossene Tickets</th></tr>';
echo'<tr><td align="center">';
if($_SESSION['anzeige'] == "0")
   echo'<input type="radio" name = "anzeige" value = "0" checked>';
else
   echo'<input type="radio" name = "anzeige" value = "0" onclick="submit();">';

echo'</td><td align="center">';
if($_SESSION['anzeige'] == '1')
   echo'<input type="radio" name = "anzeige" value = "1" checked>';
else
   echo'<input type="radio" name = "anzeige" value = "1" onclick="submit();">';
echo'</td><tr>';
echo'</table>';

echo'<table border="1">';
echo'<tr><th>Auswahl</th><th>Titel</th><th>Interpret</th><th>Takte</th><th>Ticket</th><th>übertragen</th><th>Alexandra</th><th>Philipp</th><th>Hermann</th><th>erledigt</th><th>Titelname Zertifizierung</th><th>Letzte Änderung</th></tr>';

$i = 1;

$sqlab = "SELECT * FROM hesk_attachments";
    
$daten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($daten))
   {
    //  echo'Temp: ';print_r($temp);echo '<br>'. '<br>';
    unset($titel);
    unset($interpret);
    if(substr($temp['saved_name'],-3) == 'mp3')
      {
       $sqlab = "SELECT category, status, custom12, custom13, custom14, custom15, custom16 FROM hesk_tickets WHERE trackid LIKE '" . $temp['ticket_id'] . "'";

       $startklasse = mysqli_query($db, $sqlab);
       $ds = mysqli_fetch_array($startklasse);
// print_r($ds);echo'<br>';
      
      if($ds['category'] == 4 && substr($ds['custom16'],0,4) == 'Rock')
      {
       $tempo = $ds['custom15'];
       $titel_org = explode(' ', $ds['custom12']);
       for($x = 0; $x < count($titel_org); $x++ )
          {
          $titel .= umlaute(utf8_encode($titel_org[$x]));

        if($x < count($titel_org)-1)
           $titel .= '_';
          }
//       echo'Titel: ' . $titel;echo"<br>";
 
       $interpret_org = explode(' ', $ds['custom14']);
       for($x = 0; $x < count($interpret_org); $x++ )
          {
          $interpret .= umlaute(utf8_encode($interpret_org[$x]));
        if($x < count($interpret_org)-1)
           $interpret .= '_';
          }
//       echo'Interpret: ' . $interpret;echo"<br>";



if($_SESSION['anzeige'] == 0)
   {
    if($gewaehlt[$i] == 1)
      {
       if($ds['status'] != 3)
       {     
        $dateiname[$i][0] = $temp['saved_name'];
        $dateiname[$i][1] = $tempo . '_' . $titel . '-' . $interpret;

// Status holen
       $sqlab = "SELECT * FROM zertifizierungen_allg WHERE titel = '" . $dateiname[$i][1] . "'";
       $status = mysqli_query($db, $sqlab);
       $status_ds = mysqli_fetch_array($status); 
       // print_r($status_ds);echo" oder nicht in DB<br>";
 
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . utf8_encode($ds['custom12']) . '</td><td>' . utf8_encode($ds['custom14']) . '</td><td>' . $ds['custom15'] . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter1_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
           
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter2_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else if($status_ds['gutachter3_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
        
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($dateiname[$i][1]) . '</td><td>' . $status_ds['last_update'] . '</td></tr>';
        }
       }
    else 
   {
   if($ds['status'] != 3)
    {   
    $datei_name = $tempo . '_' . utf8_decode($titel) . '-' . utf8_decode($interpret);
// Status holen
    $sqlab = "SELECT * FROM zertifizierungen_allg WHERE titel = '" . $datei_name . "'";
    $status = mysqli_query($db, $sqlab);
    $status_ds = mysqli_fetch_array($status); 
    // print_r($status_ds);echo" oder nicht in DB<br>";
       
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" ></td><td>' . utf8_encode($ds['custom12']) . '</td><td>' . utf8_encode($ds['custom14']) . '</td><td>' . $ds['custom15'] . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter1_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
           
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter2_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter3_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
           
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($datei_name) . '</td><td>' . $status_ds['last_update'] . '</td></tr>';
        $anzahl_titel = $anzahl_titel + 1;
        }
   } 
 }
  }
  
// abegschlossen Tickets

if($_SESSION['anzeige'] == 1)
   {
    if($gewaehlt[$i] == 1)
      {
       if($ds['status'] == 3 && $ds['category'] == 4)
       {
        $dateiname[$i][0] = $temp['saved_name'];
        $dateiname[$i][1] = $tempo . '_' . $titel . '-' . $interpret;
 //       print_r($dateiname[$i]); echo '<br>';

// Status holen
       $sqlab = "SELECT * FROM zertifizierungen_allg WHERE titel = '" . $dateiname[$i][1] . "'";
       $status = mysqli_query($db, $sqlab);
       $status_ds = mysqli_fetch_array($status); 
       // print_r($status_ds);echo" oder nicht in DB<br>";
 
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . utf8_encode($ds['custom12']) . '</td><td>' . utf8_encode($ds['custom14']) . '</td><td>' . $ds['custom15'] . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter1_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
           
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter2_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else if($status_ds['gutachter3_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
        
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($dateiname[$i][1]) . '</td><td bgcolor ="#D3D3D3">' . utf8_encode($dateiname[$i][3]) . '</td><td>' . $status_ds['last_update'] . '</td></tr>';
        }
       }
    else 
   {
   if($ds['status'] == 3 && $ds['category'] == 4 && substr($ds['custom16'],0,4) == 'Rock')
    {
     $datei_name = $tempo . '_' . utf8_encode($titel) . '-' . utf8_encode($interpret);
//     echo'Datei_Name: ' . $datei_name;
// Status holen
    $sqlab = "SELECT * FROM zertifizierungen_allg WHERE titel = '" . $datei_name . "'";
    $status = mysqli_query($db, $sqlab);
    $status_ds = mysqli_fetch_array($status); 
     //   print_r($status_ds);echo" oder nicht in DB<br>";
       
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" ></td><td>' . utf8_encode($ds['custom12']) . '</td><td>' . utf8_encode($ds['custom14']) . '</td><td>' . $ds['custom15'] . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter1_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
           
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter2_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
         else if($status_ds['gutachter3_ok'] == 2)
           echo'<td bgcolor ="#ff8c00">&nbsp</td>';
         else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
           
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($datei_name) . '</td><td bgcolor ="#D3D3D3">' . utf8_encode($datei_name_adm) . '</td><td>' . $status_ds['last_update'] . '</td></tr>';
        $anzahl_titel = $anzahl_titel + 1;
        }
   } 
  }

  }

    $i++;
   }
   
echo'<tr><td colspan="5" align="center"><input type="submit" name="zertifizieren" value="Zertifizieren"> alten Eintrag löschen <input type="checkbox" name="loeschen" value="1" ></td><td colspan="5" align="center"><input type="submit" name="akt" value="neu laden"></td><td align="center"><input type="submit" name="kopieren" value="Zur Turniermusik"></td><td align="center"><input type="submit" name="erledigt" value="Erledigt"></td></tr>';
echo'</table>';

echo'<p><center>' . $anzahl_titel . ' Titel</center></p>';
echo'</form>';

// Titel zur Zertifizierung

if($_POST['zertifizieren'])
  {
   $inhalt_kopf = "Hallo,\n\nes stehen neue algemeine Musiktitel zur Zertifizierung an.\n\n";
   $inhalt_kopf .= "Bitte gehe auf http://drbv.de/adm/eigene_scripts/musikzertifizierung_allgemein/index.php\nund bearbeite folgende Titel:\n\n";
   
   // Titel bestimmen
   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
       {
         // print_r(current($dateiname));echo'<br>';
         $quelle = $dateiname[key($dateiname)][0];
         $ziel = '../../adm_my_files/download/Turniermusik/allgemeine_Lieder/' . $dateiname[key($dateiname)][1] . '.mp3';
//          echo 'Quelle: '.$quelle.' - Ziel: '.$ziel.'.mp3<br>';
         copy('../../../ticketsystem/attachments/' . $quelle, $ziel);
         $tagwriter->filename = $ziel;
         // write tags
         $tagwriter->WriteTags();
         $inhalt_mail =  $inhalt_mail . "- " . $dateiname[key($dateiname)][1] . "\n";

         
// alten Eintrag löschen
         if($_POST[loeschen] == 1)
            {
             $sqlab = "delete from zertifizierungen_allg WHERE titel = '" . $dateiname[key($dateiname)][1] . "'";
//           echo$sqlab. '<br>';;
             mysqli_query($db, $sqlab);
            }

         // In Datenbank schreiben
         $sqlab = "insert zertifizierungen_allg set titel = '" . $dateiname[key($dateiname)][1] . "', eingereicht='1', gutachter1='Alexandra', gutachter2='Philipp', gutachter3='Hermann' ";
//          echo$sqlab. '<br>';;
          mysqli_query($db, $sqlab);

         next($dateiname);
       }

// Benachrichtigung per E-Mail

  if($inhalt_mail)
   {
    music_zert_mail_zert($inhalt_kopf, $inhalt_mail);
   }
   
  }

// Als Erledigt markieren

if($_POST['erledigt'])
  {
   $sqlab = "update zertifizierungen_allg set erledigt = '1' WHERE titel = '" . $dateiname[key($dateiname)][1] . "'"; 
//     echo$sqlab. '<br>';;
   mysqli_query($db, $sqlab);

   $quelle = '../../adm_my_files/download/Turniermusik/allgemeine_Lieder/' . $dateiname[key($dateiname)][1];
   unlink($quelle);
  }
  
// Titel in Downloadordner Turniermusik kopieren

if($_POST['kopieren'])
  {
   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
        {
         $quelle = '../../adm_my_files/download/Turniermusik/allgemeine_Lieder/' . $dateiname[key($dateiname)][1] . '.mp3';

         $ziel = '../../../downloads/neue_lieder/' . $dateiname[key($dateiname)][1] . '.mp3';
//          echo 'Quelle: '.$quelle.' - Ziel: '.$ziel. '<br>';
         copy($quelle, $ziel);

            
         if(is_file($ziel))
             $vorhanden = 1;
         if($vorhanden == 1)
               {
                unlink($quelle);
                // In Datenbank schreiben
                $sqlab = "update zertifizierungen_allg set erledigt = '1' WHERE titel = '" . $dateiname[key($dateiname)][1] . "'"; 
                // echo$sqlab. '<br>';;
                mysqli_query($db, $sqlab);
                unset($vorhanden);
               }
         next($dateiname);
        }
  }
  ?>