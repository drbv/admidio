<?php

require_once("./intern/tickets.inc.php");

require_once("./intern/bereinigen.php");

echo'<h2>Musikzertifizierung</h2>';

// print_r($_POST);echo"<br>";

// print_r($_POST['datei']);

  
// remove ID3 Tags
// ---------------
$TextEncoding = 'UTF-8';
require_once('./getID3/getid3/getid3.php');

// Initialize getID3 engine
$getID3 = new getID3;
$getID3->setOption(array('encoding'=>$TextEncoding));
  
require_once('./getID3/getid3/write.php');
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

echo'<Form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<table border="1">';
echo'<tr><th>Auswahl</th><th>Startbuch</th><th>Startklasse</th><th>Ticket</th><th>übertragen</th><th>Alexandra</th><th>Philipp</th><th>Hermann</th><th>erledigt</th><th>Titelname Ticket</th></tr>';

$i = 1;
$durchlauf = 1;

$sqlab = "SELECT * FROM hesk_attachments";
    
$daten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($daten))
   {
//     print_r($temp);echo '<br>'. '<br>';

    if(substr($temp['saved_name'],-3) == 'mp3')
      {
       $sqlab = "SELECT custom7, custom8, custom10, custom11, custom12, custom13, custom14 FROM hesk_tickets WHERE trackid LIKE '" . $temp['ticket_id'] . "'";

       $startklasse = mysqli_query($db, $sqlab);
       $ds = mysqli_fetch_array($startklasse);
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
       if($ds['custom10'] !="FT_" && $ds['custom10'] !="---" && $durchlauf == 1)
          {
           $ticket_titel = $ds['custom10'];
           $art = 'FT_';
           $speicher_1 = 1;
          }
       if($ds['custom11'] !="AK_" && $ds['custom11'] !="---" && $durchlauf == 2)
          {
           $ticket_titel = $ds['custom11'];
           $art = 'AK_';
           $speicher_2 = 1;
          }
       if($ds['custom12'] !="SP_" && $ds['custom12'] !="---" && $durchlauf == 1)
          {
           $ticket_titel = $ds['custom12'];
           $art = 'S_';
           $speicher_3 = 1;
          }
       if($ds['custom13'] !="TM_" && $ds['custom13'] !="---" && $durchlauf == 2)
          {
            $ticket_titel = $ds['custom13'];
            $art = 'T_';
            $speicher_4 = 1;
           }  
         if($ds['custom14'] !="EM_" && $ds['custom14'] !="---" && $durchlauf == 3)
          {
           $ticket_titel = $ds['custom14'];
           $art = 'E_';
           $speicher_5 = 1;
          }
//  print_r($ds);echo '<br>';
// echo $durchlauf . ' - ' . $temp['real_name'] . ' Speicher: ' . $speicher_1 . ' - ' . $speicher_2 .  ' - ' . $speicher_3 . ' - ' . $speicher_4 . ' - ' . $speicher_5 . '<br>';

    if($gewaehlt[$i] == 1)
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
 
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td>' . $temp['ticket_id'] . '</td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($dateiname[$i][1]) . '</td></tr>';
       }
    else 
   {
    $datei_name = date("Y") . '_' .  $sk . $art . substr($temp['saved_name'],0 ,12) . '.mp3';
// Status holen
    $sqlab = "SELECT * FROM zertifizierungen WHERE titel = '" . $datei_name . "'";
    $status = mysqli_query($db, $sqlab);
    $status_ds = mysqli_fetch_array($status); 
       // print_r($status_ds);echo" oder nicht in DB<br>";
       
        echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" ></td><td>' . $ds['custom7'] . '</td><td>' . utf8_encode($ds['custom8']) . '</td><td>' . $temp['ticket_id'] . '</td>';
        if($status_ds['eingereicht'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter1_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['gutachter2_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>'; 
         if($status_ds['gutachter3_ok'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';
         if($status_ds['erledigt'] == 1)
           echo'<td bgcolor ="#33ff00">&nbsp</td>';
        else
           echo'<td bgcolor ="#ff0000">&nbsp</td>';           

        echo'<td>' . utf8_encode($datei_name) . '</td></tr>';
   } 
  
//     echo 'vor Durchlauf: ' . $durchlauf. '<br>';
   $durchlauf = $durchlauf + 1;
//        echo 'nach Durchlauf: ' . $durchlauf. '<br>';

  }

   if(($durchlauf == 3 && $speicher_1) || ($durchlauf == 3 && $speicher_2))
    {
     unset($speicher_1);
     unset($speicher_2);
     $durchlauf =1;
    }
   if(($durchlauf == 4 && $speicher_3) || ($durchlauf == 4 && $speicher_4 ) || ($durchlauf == 4 && $speicher_5))
    {
     unset($speicher_3);
     unset($speicher_4);
     unset($speicher_5);
     $durchlauf =1;
    }
    $i++;
   }
   
echo'<tr><td colspan="4" align="center"><input type="submit" name="zertifizieren" value="Zertifizieren"></td><td colspan="5" align="center"><input type="submit" name="akt" value="neu laden"></td><td align="center"><input type="submit" name="kopieren" value="Zur Turniermusik"></td></tr>';
echo'</table>';
echo'</form>';

// Titel zur Zertifizierung

if($_POST['zertifizieren'])
  {
   $inhalt_kopf = "Hallo,\n\nes stehen neue Musiktitel zur Zertifizierung an.\n\n";
   $inhalt_kopf .= "Bitte besuche das Aktivenportal und bearbeite folgende Titel:\n\n";
   
   // Titel bestimmen
   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
       {
         // print_r(current($dateiname));echo'<br>';
         $quelle = $dateiname[key($dateiname)][0];
         $ziel = '../adm_my_files/download/Turniermusik/Unzertifizierte-Musik/' . $dateiname[key($dateiname)][1];
         // echo 'Quelle: '.$quelle.' - Ziel: '.$ziel.'.mp3<br>';
         copy('../../ticketsystem/attachments/' . $quelle, $ziel);
         $tagwriter->filename = $ziel;
         // write tags
         $tagwriter->WriteTags();
         $inhalt_mail =  $inhalt_mail . "- " . $dateiname[key($dateiname)][1] . "\n";
         
         // In Datenbank schreiben
         $sqlab = "insert zertifizierungen set titel = '" . $dateiname[key($dateiname)][1] . "', eingereicht='1', gutachter1='Alexandra', gutachter2='Philipp', gutachter3='Hermann' ";
         // echo$sqlab. '<br>';;
         mysqli_query($db, $sqlab);

         next($dateiname);
       }

// Benachrichtigung per E-Mail

  if($inhalt_mail)
   {
    $inhalt_mail =  $inhalt_kopf . $inhalt_mail . "\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "xxx@xxx.xx";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

   @mail($absender,"Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
   @mail("xxx@xx.xx","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path); 
   @mail("xxx@xxx.xx","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
   @mail("xxx@xxx.xx","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
   }

  }
  
// Titel in Downloadordner Turniermusik kopieren

if($_POST['kopieren'])
  {

   reset($dateiname) ;
   for($x = 1; $x <= count($dateiname); $x++ )
        {
         $quelle = '../adm_my_files/download/Turniermusik/Unzertifizierte-Musik/' . $dateiname[key($dateiname)][1];
         
         if($dateiname[key($dateiname)][4] == "A")
             $hauptverzeichnis ='../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/';
             
         if($dateiname[key($dateiname)][4] == "B")
             $hauptverzeichnis ='../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/';
             
         if($dateiname[key($dateiname)][4] != "A" && $dateiname[key($dateiname)][4] != "B" )
             $hauptverzeichnis ='../adm_my_files/download/Turniermusik/Zertifizierte-Musik/Formationen/';

         if($dateiname[key($dateiname)][5] == "AK_")
             $unterverzeichnis ='Akrobatik/';
         if($dateiname[key($dateiname)][5] == "FT_")
             $unterverzeichnis ='Fusstechnik/';
         if($dateiname[key($dateiname)][5] != "AK_" && $dateiname[key($dateiname)][5] != "FT_")
             $unterverzeichnis ='';

             
         $ziel = $hauptverzeichnis . $unterverzeichnis . $dateiname[key($dateiname)][3] . '.mp3';
         $ziel_tlp = '../../downloads/turniermp3/' . $dateiname[key($dateiname)][3] . '.mp3';
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