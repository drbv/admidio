<?php

session_start();

require_once("../intern/tickets.inc.php");

require_once("../intern/bereinigen.php");

// print_r($_POST);echo"<br>";
// print_r($_GET);echo"<br>";



function makeDownload($file, $dir, $type) 
{

    header("Content-Type: $type");

    header("Content-Disposition: attachment; filename=\"$file\"");

    readfile($dir.$file);
    
}

if($_GET['file'])
{
$dir = '../../adm_my_files/download/Turniermusik/allgemeine_Lieder/';

$type = 'audio/mpeg';

if(!empty($_GET['file']) && !preg_match('=/=', $_GET['file']))
  {
    if(file_exists ($dir.$_GET['file']))
      {
        makeDownload($_GET['file'], $dir, $type);
      }      
  }
}

function auswahlliste()
  {
   echo'<select name="bewertung" >'; // onchange="submit();">';
   if($wert == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($wert == 'io')
      echo'<option value="io" selected>i.O.</option>';
   else
      echo'<option value="io">i.O.</option>';
   if($wert == 'zu schnell')
      echo'<option value="zu schnell" selected>zu schnell</option>';
   else
      echo'<option value="zu schnell">zu schnell</option>';
   if($wert == 'zu langsam')
      echo'<option value="zu langsam" selected>zu langsam</option>';
   else
      echo'<option value="zu langsam">zu langsam</option>';
   if($wert == 'Rhythmus')
      echo'<option value="Rhythmus" selected>Rhythmus</option>';
   else
      echo'<option value="Rhythmus">Rhythmus</option>';
   if($wert == 'untypisch')
      echo'<option value="Untypisch" selected>Untypisch</option>';
   else
      echo'<option value="Untypisch">Untypisch</option>';
   if($wert == 'zu kurz')
      echo'<option value="zu kurz" selected>zu kurz</option>';
   else
      echo'<option value="zu kurz">zu kurz</option>';
   if($wert == 'zu lang')
      echo'<option value="zu lang" selected>zu lang</option>';
   else
      echo'<option value="zu lang">zu lang</option>';
   if($wert == 'Pausen')
      echo'<option value="Pausen" selected>Pausen</option>';
   else
      echo'<option value="Pausen">Pausen</option>';
   if($wert == 'sonstiges')
      echo'<option value="sonstiges" selected>sonstiges</option>';
   else
      echo'<option value="sonstiges">sonstiges</option>';
   echo'</select>';
  }

echo'<h2>Musikzertifizierung Prüfung</h2>';

// print_r($_POST);echo"<br>";

  
// Tabelle und Variablen erstellen

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

if(!$_SESSION['anzeige'])
   $_SESSION['anzeige'] = 0;
if($_POST['anzeige'] == 0) 
  {
   $_SESSION['anzeige'] = 0;
  }
if($_POST['anzeige'] == 1) 
  {
   $_SESSION['anzeige'] = 1;
  }
  
echo'<table>';
echo'<tr><th width="220" align="center">offene Zertifizierungen</th><th width="220" align="center">abgeschlossene Zertifizierungen</th></tr>';
echo'<tr><td align="center">';
if($_SESSION['anzeige'] == 0)
   echo'<input type="radio" name = "anzeige" value = "0" checked>';
else
   echo'<input type="radio" name = "anzeige" value = "0" onclick="submit();">';

echo'</td><td align="center">';
if($_SESSION['anzeige'] == 1)
   echo'<input type="radio" name = "anzeige" value = "1" checked>';
else
   echo'<input type="radio" name = "anzeige" value = "1" onclick="submit();">';
echo'</td><tr>';
echo'</table>';


echo'<table border="1">';
echo'<tr><th>Auswahl</th><th>Titel</th><th colspan = "2">Alexandra ';

if($_POST['bewerter'] == 'Alexandra')
    echo'<input type="radio" name = "bewerter" value = "Alexandra" checked>';
else
    echo'<input type="radio" name = "bewerter" value = "Alexandra" onclick="submit();">';

echo'</th><th colspan = "2">Philipp ';
if($_POST['bewerter'] == "Philipp")
    echo'<input type="radio" name = "bewerter" value = "Philipp" checked>';
else
    echo'<input type="radio" name = "bewerter" value = "Philipp" onclick="submit();">';

echo'</th><th colspan = "2">Hermann ';

if($_POST['bewerter'] == "Hermann")
    echo'<input type="radio" name = "bewerter" value = "Hermann" checked>';
else
    echo'<input type="radio" name = "bewerter" value = "Hermann" onclick="submit();">';
    
echo'</th></tr>';

$sqlab = "SELECT * FROM zertifizierungen_allg ORDER BY last_update DESC";
    
$daten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($daten))
   {
//     print_r($temp);echo '<br>'. '<br>';
   if($temp['erledigt'] == $_SESSION['anzeige'])
    {
     echo'<tr><td align="center"><input type= "radio" name= "id_auswahl" value= "' . $temp['id'] . '"</td>';
     if(is_file('../../adm_my_files/download/Turniermusik/allgemeine_Lieder/' . $temp['titel'] . '.mp3'))
        echo'<td width="270"><a href="index.php?file=' . $temp['titel'] .'.mp3">' . $temp['titel'] . '</td>';
     else 
     	echo'<td width="270">' . $temp['titel'] . '</td>';
    if($temp['g1_musik'] == "io")
       echo'<td width="75" bgcolor="#33ff00" align = "center">i.O.</td>';
     else if($temp['g1_musik'] == "---")
       echo'<td width="75" align = "center">' . $temp['g1_musik'] . '</td>';
     else
       echo'<td width="75" bgcolor="#ff6666" align = "center">' . $temp['g1_musik'] . '</td>';

     echo'<td width="200">' . $temp['g1_notiz'] . '</td>';

    if($temp['g2_musik'] == "io")
       echo'<td width="75" bgcolor="#33ff00" align = "center">i.O.</td>';
     else if($temp['g2_musik'] == "---")
       echo'<td width="75" align = "center">' . $temp['g2_musik'] . '</td>';
     else
       echo'<td width="75" bgcolor="#ff6666" align = "center">' . $temp['g2_musik'] . '</td>'; 

     echo'<td width="200">' . $temp['g2_notiz'] . '</td>';

    if($temp['g3_musik'] == "io")
       echo'<td width="75" bgcolor="#33ff00" align = "center">i.O.</td>';
     else if($temp['g3_musik'] == "---")
       echo'<td width="75" align = "center">' . $temp['g3_musik'] . '</td>';
     else
       echo'<td width="75" bgcolor="#ff6666" align = "center">' . $temp['g3_musik'] . '</td>';

     echo'<td width="200">' . $temp['g3_notiz'] . '</td>';
     
    echo'</tr>' ;
    }
   }
   
echo'<tr><td colspan="2" align="center"><input type="submit" name="absenden" value="Absenden"></td><td colspan="2" align="center">Bewertung auswählen:<br><br>';
auswahlliste();
echo'</td><td colspan="3" align="center">Bemerkung eingeben:<br><textarea name="notiz" cols="50" rows="5"></textarea></td><td align="center"><input type="submit" name="akt" value="neu laden"></td></tr>';
echo'</table>';
echo'</form>';

// In Datenbank schreiben
if($_POST['absenden'])
       {
         $_POST['notiz'] = mysqli_real_escape_string($db,$_POST['notiz']);
         $sqlab = "update zertifizierungen_allg set ";
         
         if($_POST['bewerter'] == 'Alexandra' && $_POST['bewertung'] !="---")
             $musik =  "g1_musik = '" . $_POST['bewertung'] ."'";
         if($_POST['bewerter'] == 'Alexandra' && $_POST['notiz'] !="")
             $notiz = "g1_notiz ='" . $_POST['notiz'] ."'";

         if($_POST['bewerter'] == 'Philipp' && $_POST['bewertung'] !="---")
             $musik =  "g2_musik = '" . $_POST['bewertung'] ."'";
         if($_POST['bewerter'] == 'Philipp' && $_POST['notiz'] !="")
             $notiz = "g2_notiz ='" . $_POST['notiz'] ."'";

         if($_POST['bewerter'] == 'Hermann' && $_POST['bewertung'] !="---")
             $musik =  "g3_musik = '" . $_POST['bewertung'] ."'";
         if($_POST['bewerter'] == 'Hermann' && $_POST['notiz'] !="")
             $notiz = "g3_notiz ='" . $_POST['notiz'] ."'";

         if($musik && $notiz)
            $sqlab .= $musik . ', ' . $notiz;
         else if($musik)
            $sqlab .= $musik;
         else if($notiz)
            $sqlab .= $notiz;
            
         $sqlab .=" WHERE id = '" . $_POST['id_auswahl'] ."'";
         // echo$sqlab. '<br>';;
         mysqli_query($db, $sqlab);
         
         $sqlab = "SELECT * FROM zertifizierungen_allg WHERE id = '" . $_POST['id_auswahl'] . "'";
         $auswertung = mysqli_query($db, $sqlab);
         $ergebnis= mysqli_fetch_array($auswertung);
         // print_r($ergebnis);echo"<br>";
         
         if($ergebnis['g1_musik'] == 'io')
           {
            $zaehler_io = $zaehler_io + 1;
            $sqlab = "update zertifizierungen_allg set gutachter1_ok = '1' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           }
         if($ergebnis['g1_musik'] != 'io' && $ergebnis['g1_musik'] != '---') 
           {
            $zaehler_nio = $zaehler_nio + 1;
            $sqlab = "update zertifizierungen_allg set gutachter1_ok = '2' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           } 
           
         if($ergebnis['g2_musik'] == 'io')
           {
            $zaehler_io = $zaehler_io + 1;
            $sqlab = "update zertifizierungen_allg set gutachter2_ok = '1' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           } 
         if($ergebnis['g2_musik'] != 'io' && $ergebnis['g2_musik'] != '---') 
           {
            $zaehler_nio = $zaehler_nio + 1;
            $sqlab = "update zertifizierungen_allg set gutachter2_ok = '2' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           } 
           
         if($ergebnis['g3_musik'] == 'io')
           {
            $zaehler_io = $zaehler_io + 1;
            $sqlab = "update zertifizierungen_allg set gutachter3_ok = '1' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           } 
         if($ergebnis['g3_musik'] != 'io' && $ergebnis['g3_musik'] != '---') 
           {
            $zaehler_nio = $zaehler_nio + 1;
            $sqlab = "update zertifizierungen_allg set gutachter3_ok = '2' WHERE id = '" . $_POST['id_auswahl'] . "'";
            mysqli_query($db, $sqlab);
           } 

//         next($dateiname);
       }

// Benachrichtigung per E-Mail

$zaehler = $zaehler_io + $zaehler_nio;
if($zaehler == 2)
    $inhalt_mail = $ergebnis['titel'];

  if($inhalt_mail)
      {
       music_zert_mail($inhalt_mail);
      }

  }
  ?>