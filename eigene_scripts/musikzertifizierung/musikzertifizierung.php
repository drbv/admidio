<?php

session_start();

require_once("../intern/tickets.inc.php");
echo'<h2>Musikzertifizierung</h2>';
echo'<a href="index.php" target="_blank">zur Bewerterseite</a><br><br>';
// echo'<a href="../../../ticketsystem/admin/admin_main.php" target="_blank">zum Ticketsystem</a><br><br>';
//  print_r($_POST);echo"<br>";
//  print_r($_SESSION);echo"<br>";

  
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
echo'<tr><th>Auswahl</th><th>Name</th><th>Startbuch</th><th>Startklasse</th><th>Ticket</th><th>übertragen</th><th>Alexandra</th><th>Philipp</th><th>Hermann</th><th>erledigt</th><th>Titelname Zertifizierung</th><th bgcolor ="#D3D3D3">Titelname Admidio</th><th>Letzte Änderung</th></tr>';

$i = 1;

$sqlab = "SELECT * FROM hesk_attachments";
    
$daten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($daten))
   {
    //  echo'Temp: ';print_r($temp);echo '<br>'. '<br>';

    if(substr($temp['saved_name'],-3) == 'mp3')
      {
       $sqlab = "SELECT category, name, status, custom7, custom8, custom10, custom11, custom12, custom13, custom14 FROM hesk_tickets WHERE trackid LIKE '" . $temp['ticket_id'] . "'";

       $startklasse = mysqli_query($db, $sqlab);
       $ds = mysqli_fetch_array($startklasse);
       // print_r($ds);echo'<br>';
      
      if($ds['category'] == 3)
      {
       if($ds['custom8'] =="A")
          {
           $sk = "A";
          }
       if($ds['custom8'] =="B")
          {
           $sk = "B";
          }
       if(utf8_encode($ds['custom8']) =="Rock´n´Roll Master")
          {
           $sk = "FRRMA_";
          }
       if(utf8_encode($ds['custom8']) =="Rock´n´Roll Jugend")
          {
           $sk = "FRRJ_";
          }
       if(utf8_encode($ds['custom8']) =="Rock´n´Roll Lady")
          {
           $sk = "FRRLF_";
          }
       if(utf8_encode($ds['custom8']) =="Rock´n´Roll Girl")
          {
           $sk = "FRRGF_";
          }
       if(utf8_encode($ds['custom8']) =="Rock´n´Roll Showteam")
          {
           $sk = "FRRST_";
          }
       if(utf8_encode($ds['custom8']) =="Boogie Woogie Master")
          {
           $sk = "FBWMA_";
          }

       if(utf8_encode($ds['custom10']) == 'Titel Fußtechnik')
          {
           $ticket_titel = $ds['custom10'];
           $art = 'FT_';
          }
          
       if($ds['custom10'] == 'Titel Akrobatik')
          {
           $ticket_titel = $ds['custom10'];
           $art = 'AK_';
          }
          
       if($ds['custom10'] == 'Titel Stellprobe')
          {
           $ticket_titel = $ds['custom10'];
           $art = 'S_';
          }          

       if($ds['custom10'] == 'Titel Formation')
          {
           $ticket_titel = $ds['custom10'];
           $art = 'T_';
          }          

       if($ds['custom10'] == 'Titel Ersatzmusik')
          {
           $ticket_titel = $ds['custom10'];
           $art = 'E_';
          }                       

if($_SESSION['anzeige'] == 0)
   {
    if($gewaehlt[$i] == 1)
      {
       if($ds['status'] != 3)
       {
        $dateiname[$i][0] = $temp['saved_name'];
        $dateiname[$i][1] = date("Y") . '_' .  $sk . $art . substr($temp['saved_name'],0 ,12) . '.mp3';
        $dateiname[$i][2] = $ds['custom7'];
        $dateiname[$i][3] = $ds['custom7'] . '_' . date("Y") . '_' . $sk . $art . substr($temp['saved_name'],0 ,12);
        $dateiname[$i][4] = $sk;
        $dateiname[$i][5] = $art;
        // print_r($dateiname[$i]); echo '<br>';

// Status holen
       $sqlab = "SELECT * FROM zertifizierungen WHERE titel = '" . $dateiname[$i][1] . "'";
       $status = mysqli_query($db, $sqlab);
       $status_ds = mysqli_fetch_array($status); 
       // print_r($status_ds);echo" oder nicht in DB<br>";
 
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . utf8_encode($ds['name']) . '</td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
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
   if($ds['status'] != 3)
    {
    if($ds['status'] == 0) // neu = weiss
          $hg_color = 'bgcolor="#ffffff"';
    if($ds['status'] == 1) // Warte auf Antwort = gelb
          $hg_color = 'bgcolor="#ffff00"'; 
    if($ds['status'] == 2) // beantwortet = cyan
          $hg_color = 'bgcolor="#00ffff"'; 
    if($ds['status'] == 4) // in Bearbeitung = grün
          $hg_color = 'bgcolor="#00ff00"'; 
    if($ds['status'] == 5) // Warte auf Bearbeitung = rot
          $hg_color = 'bgcolor="#ff0000"';       

    $datei_name = date("Y") . '_' .  $sk . $art . substr($temp['saved_name'],0 ,12) . '.mp3';
    $datei_name_adm = $ds['custom7'] . '_' . date("Y") . '_' . $sk . $art . substr($temp['saved_name'],0 ,12);
// Status holen
    $sqlab = "SELECT * FROM zertifizierungen WHERE titel = '" . $datei_name . "'";
    $status = mysqli_query($db, $sqlab);
    $status_ds = mysqli_fetch_array($status); 
    // print_r($status_ds);echo" oder nicht in DB<br>";
       
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" ></td><td>' . utf8_encode($ds['name']) . '</td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td ' . $hg_color . '><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
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
  
// abegschlossen Tickets

if($_SESSION['anzeige'] == 1)
   {
    if($gewaehlt[$i] == 1)
      {
       if($ds['status'] == 3)
       {     
        $dateiname[$i][0] = $temp['saved_name'];
        $dateiname[$i][1] = date("Y") . '_' .  $sk . $art . substr($temp['saved_name'],0 ,12) . '.mp3';
        $dateiname[$i][2] = $ds['custom7'];
        $dateiname[$i][3] = $ds['custom7'] . '_' . date("Y") . '_' . $sk . $art . substr($temp['saved_name'],0 ,12);
        $dateiname[$i][4] = $sk;
        $dateiname[$i][5] = $art;
 //       print_r($dateiname[$i]); echo '<br>';

// Status holen
       $sqlab = "SELECT * FROM zertifizierungen WHERE titel = '" . $dateiname[$i][1] . "'";
       $status = mysqli_query($db, $sqlab);
       $status_ds = mysqli_fetch_array($status); 
       // print_r($status_ds);echo" oder nicht in DB<br>";
 
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . utf8_encode($ds['name']) . '</td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
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
   if($ds['status'] == 3)
    {   
    $datei_name = date("Y") . '_' .  $sk . $art . substr($temp['saved_name'],0 ,12) . '.mp3';
    $datei_name_adm = $ds['custom7'] . '_' . date("Y") . '_' . $sk . $art . substr($temp['saved_name'],0 ,12);
// Status holen
    $sqlab = "SELECT * FROM zertifizierungen WHERE titel = '" . $datei_name . "'";
    $status = mysqli_query($db, $sqlab);
    $status_ds = mysqli_fetch_array($status); 
     //   print_r($status_ds);echo" oder nicht in DB<br>";
       
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" ></td><td>' . utf8_encode($ds['name']) . '</td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td><a href="../../../ticketsystem/admin/admin_ticket.php?track=' . $temp['ticket_id'] . '" target="_blank">' . $temp['ticket_id'] . '</a></td>';
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
   
echo'<tr><td colspan="5" align="center"><input type="submit" name="zertifizieren" value="Zertifizieren"> alten Eintrag löschen <input type="checkbox" name="loeschen" value="1" ></td><td colspan="5" align="center"><input type="submit" name="akt" value="neu laden"></td><td colspan="2" align="center"><input type="submit" name="kopieren" value="Zur Turniermusik"></td><td align="center"><input type="submit" name="erledigt" value="Erledigt"></td></tr>';
echo'</table>';

echo '<p><center><span style="background-color: D9D9D9; color: 000000">Hintergrund Spalte Ticket: neu = weiss </span><span style="background-color: D9D9D9; color: ffff00"> Warte auf Antwort </span><span style="background-color: D9D9D9; color: 00ffff"> Beantwortet </span><span style="background-color: D9D9D9; color: 00ff00"> in Bearbeitung </span><span style="background-color: D9D9D9; color: ff0000"> Warte auf Bearbeitung </span></center></p>'; 
echo'<p><center>' . $anzahl_titel . ' Titel</center></p>';
echo'</form>';

// Titel zur Zertifizierung

if($_POST['zertifizieren'])
  {
   $inhalt_kopf = "Hallo,\n\nes stehen neue Musiktitel zur Zertifizierung an.\n\n";
   $inhalt_kopf .= "Bitte gehe auf http://drbv.de/adm/eigene_scripts/musikzertifizierung/index.php\nund bearbeite folgende Titel:\n\n";
   
   // Titel bestimmen
   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
       {
         // print_r(current($dateiname));echo'<br>';
         $quelle = $dateiname[key($dateiname)][0];
         $ziel = '../../adm_my_files/download/Turniermusik/Unzertifizierte-Musik/' . $dateiname[key($dateiname)][1];
         // echo 'Quelle: '.$quelle.' - Ziel: '.$ziel.'.mp3<br>';
         copy('../../../ticketsystem/attachments/' . $quelle, $ziel);
         $tagwriter->filename = $ziel;
         // write tags
         $tagwriter->WriteTags();
         $inhalt_mail =  $inhalt_mail . "- " . $dateiname[key($dateiname)][1] . "\n";
         
// alten Eintrag löschen
         if($_POST[loeschen] == 1)
            {
             $sqlab = "delete from zertifizierungen WHERE titel = '" . $dateiname[key($dateiname)][1] . "'";
//           echo$sqlab. '<br>';;
             mysqli_query($db, $sqlab);
            }

         // In Datenbank schreiben
         $sqlab = "insert zertifizierungen set titel = '" . $dateiname[key($dateiname)][1] . "', eingereicht='1', gutachter1='Alexandra', gutachter2='Philipp', gutachter3='Hermann' ";
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
   $sqlab = "update zertifizierungen set erledigt = '1' WHERE titel = '" . $dateiname[key($dateiname)][1] . "'"; 
//     echo$sqlab. '<br>';;
   mysqli_query($db, $sqlab);

   $quelle = '../../adm_my_files/download/Turniermusik/Unzertifizierte-Musik/' . $dateiname[key($dateiname)][1];
   unlink($quelle);
  }
  
// Titel in Downloadordner Turniermusik kopieren

if($_POST['kopieren'])
  {

   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
        {
         $quelle = '../../adm_my_files/download/Turniermusik/Unzertifizierte-Musik/' . $dateiname[key($dateiname)][1];
         
         if($dateiname[key($dateiname)][4] == "A")
             $hauptverzeichnis ='../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/';
             
         if($dateiname[key($dateiname)][4] == "B")
             $hauptverzeichnis ='../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/';
             
         if($dateiname[key($dateiname)][4] != "A" && $dateiname[key($dateiname)][4] != "B" )
             $hauptverzeichnis ='../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/Formationen/';

         if($dateiname[key($dateiname)][5] == "AK_")
             $unterverzeichnis ='Akrobatik/';
         if($dateiname[key($dateiname)][5] == "FT_")
             $unterverzeichnis ='Fusstechnik/';
         if($dateiname[key($dateiname)][5] != "AK_" && $dateiname[key($dateiname)][5] != "FT_")
             $unterverzeichnis ='';

             
         $ziel = $hauptverzeichnis . $unterverzeichnis . $dateiname[key($dateiname)][3] . '.mp3';
         $ziel_tlp = '../../../downloads/turniermp3/' . $dateiname[key($dateiname)][3] . '.mp3';
         // echo 'Quelle: '.$quelle.' - Ziel: '.$ziel. ' - Ziel TLP: ' . $ziel_tlp . '<br>';
         copy($quelle, $ziel);
         copy($quelle, $ziel_tlp);

         if(is_file($ziel))
             $vorhanden = 1;
         if(is_file($ziel_tlp))
             $vorhanden =  $vorhanden + 1;
         if($vorhanden == 2)
               {
                unlink($quelle);
                // In Datenbank schreiben
                $sqlab = "update zertifizierungen set erledigt = '1' WHERE titel = '" . $dateiname[key($dateiname)][1] . "'"; 
                // echo$sqlab. '<br>';;
                mysqli_query($db, $sqlab);
                unset($vorhanden);
               }
         next($dateiname);
        }
              
  }

?>
