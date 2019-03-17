<?php

session_start();

if(!$_SERVER['HTTP_REFERER'] == 'https://drbv.de/adm/adm_program/modules/drbv/musikdb_admin.php' || $_SESSION['zutritt'] != 1)
//exit('Sie haben keine Berechtigung!');

$_SESSION['zutritt'] = 1;
require_once("./dboeffnen.inc.php");

/*
echo'SERVER: ';print_r($_SERVER);echo'<br><p>';
echo'GET: ';print_r($_GET);echo"<br><p>";
echo'POST: ';print_r($_POST);echo"<br><p>";
echo'SESSION: ';print_r($_SESSION);echo"<br><p>";
*/
 
 function phpalert($msg)
   {
    echo'<script Type = "text/javascript">alert("' . $msg . '")</script>';
   }

if($_POST['von']  != '---' && $_POST['bis'] == '---')
   $_POST['bis'] = $_POST['von'];
if($_POST['von']  == '---' && $_POST['bis'] != '---')
   $_POST['bis'] = '---';

 if(($_POST['von'] !='---' && $_POST['bis'] != '---') && $_POST['von'] > $_POST['bis'])
    {
     phpalert("Takte bis darf nicht kleiner als Takte von sein!");
    }

if($_SESSION['id'] !=  $_POST['bearbeiten'] && $_POST['bearbeiten'] != '')
   $_SESSION['id'] =  $_POST['bearbeiten'];

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Musikdatenbank Admin</title>
</head>
<body>

<?php

if($_POST['eintragen'] && $_POST['bezeichnung'] !='')
{
 $sqlab = "insert turniermusik set bezeichnung = '" . $_POST['bezeichnung'] . "', pfad = '" . $_POST['pfad'] . "', titel = '" . $_POST['titel'] . "', interpret = '" . $_POST['interpret'] . "', takte = '" . $_POST['takte'] . "', rocknroll = '" . $_POST['sk_rr'] . "', boogiewoogie = '" . $_POST['sk_bw'] . "', genre = '" . $_POST['genre_1'] . "', wertung_1 = '" . $_POST['sterne_1'] . "', wertung_2 = '" . $_POST['sterne_2'] . "', wertung_3 = '" . $_POST['sterne_3'] . "', wertung_4 = '" . $_POST['sterne_4'] . "', wertung_5 = '" . $_POST['sterne_5'] . "', wertung_6 = '" . $_POST['sterne_6'] . "', wertung_7 = '" . $_POST['sterne_7'] . "', wrrc = '" . $_POST['wrrc'] . "', struktur = '" . $_POST['struktur_1'] . "', ausnahmen = '" . $_POST['ausnahmen_1'] . "'";
 
mysqli_query($db, $sqlab);
$erfolg = mysqli_affected_rows($db);
if($erfolg == 1)
   echo'Der Datenstatz wurde hinzugefügt!<br>';
// echo$sqlab. '<br>';   
}

if($_POST['aendern'])
{
unset($durchschnitt);
$durchschnitt = array();  
unset($teiler);

 if($_POST['sterne_1'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_1'];
   }
 if($_POST['sterne_2'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_2'];
   }
 if($_POST['sterne_3'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_3'];
   }
 if($_POST['sterne_4'] >= 1)
    {
    $durchschnitt[] = $_POST['sterne_4'];
   }
 if($_POST['sterne_5'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_5'];
   }
 if($_POST['sterne_6'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_6'];
   }
    
 if($_POST['sterne_7'] >= 1)
   {
    $durchschnitt[] = $_POST['sterne_7'];
   }

if($teiler_1 > 0.9)
   $schnitt = round((array_sum($durchschnitt) - max($durchschnitt) - min($durchschnitt))/$teiler_1);
else
   $schnitt = round(array_sum($durchschnitt)/count($durchschnitt));

 $sqlab = "UPDATE turniermusik set bezeichnung = '" . $_POST['bezeichnung'] . "', pfad = '" . $_POST['pfad'] . "', titel = '" . $_POST['titel'] . "', interpret = '" . $_POST['interpret'] . "', takte = '" . $_POST['takte'] . "', rocknroll = '" . $_POST['sk_rr'] . "', boogiewoogie = '" . $_POST['sk_bw'] . "', genre = '" . $_POST['genre_1'] . "', wertung_1 = '" . $_POST['sterne_1'] . "', wertung_2 = '" . $_POST['sterne_2'] . "', wertung_3 = '" . $_POST['sterne_3'] . "', wertung_4 = '" . $_POST['sterne_4'] . "',  wertung_5 = '" . $_POST['sterne_5'] . "',  wertung_6 = '" . $_POST['sterne_6'] . "',  wertung_7 = '" . $_POST['sterne_7'] . "',  wertung = '" . $schnitt . "',  wrrc = '" . $_POST['wrrc'] . "', struktur = '" . $_POST['struktur_1'] . "', ausnahmen = '" . $_POST['ausnahmen_1'] . "' WHERE id='" . $_SESSION['id'] . "'";
// echo$sqlab. '<br>';
mysqli_query($db, $sqlab);
$erfolg = mysqli_affected_rows($db);
if($erfolg == 1)
   echo'Der Datenstatz wurde geändert!<br>';
   unset($_SESSION['id']);
}

if($_POST['loeschen'])
{
 $sqlab = "DELETE FROM turniermusik WHERE id='" . $_SESSION['id'] . "'";
//  echo$sqlab. '<br>';
 mysqli_query($db, $sqlab);
$erfolg = mysqli_affected_rows($db);
if($erfolg == 1)
   echo'Der Datenstatz wurde gelöscht!<br>';
}

echo'<center><table border="0">';
echo'<tr><td width="200"><a href="einlesen.php">Dateien einlesen</a></td><td width ="200"><a href="bewertungen_uebertragen.php">Bewertungen übertragen</a></td></tr>';
echo'</table><br>';

echo'<h1><center>Admin Turniermusik DRBV</h1>';

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<h2><center>Datenbank verwalten</h2>';

// Verwalten

echo'<center><table border="1">';
echo'<tr><th width="185" align="center">Dateiname</th><th width="185" align="center">Pfad</th><th width="185">Titel</th><th width="185">Interpret</th><th width="45">Takte</th><th width="25">RR</th><th width="25">BW</th><th width="55">WRRC</th><th width="100">Genre</th><th width="60">Sterne 1</th><th width="60">Sterne 2</th><th width="60">Sterne 3</th><th width="60">Sterne 4</th><th width="60">Sterne 5</th><th width="60">Sterne 6</th><th width="60">Sterne 7</th><th width="60">Struktur</th><th width="60">Ausnahmen</th></tr>';

if($_POST['bearbeiten'])
  {
   $sqlab = 'SELECT * From turniermusik WHERE id=' . $_POST['bearbeiten'] ;
   $datensatz = mysqli_query($db, $sqlab);
   $daten = mysqli_fetch_array($datensatz);
   // print_r($daten);echo"<br>";
  }


   echo'<tr><td align="center"><input type=text name="bezeichnung" value="' . $daten['bezeichnung'] . '"  size="25" maxlength="80"></td><td align="center"><input type=text name="pfad" value="' . $daten['pfad'] . '" size="25" maxlength="40"></td><td><input type=text name="titel" value="' .$daten['titel'] . '" size="25" maxlength="50"></td><td><input type=text name="interpret" value="'. $daten['interpret'] . '"  size="25" maxlength="50"></td><td align="center"><input type=text name="takte" value="' . $daten['takte'] . '"  size="2" maxlength="2"></td><td align="center">';
   if($daten['rocknroll'] == 1)
      echo'<input type="checkbox" name="sk_rr" value="1" checked>';
   else
      echo'<input type="checkbox" name="sk_rr" value="1">';
   echo'</td><td align="center">';
   if($daten['boogiewoogie'] == 1)
   echo'<input type="checkbox" name="sk_bw" value="1" checked>';
   else
      echo'<input type="checkbox" name="sk_bw" value="1">';
   echo'</td><td align="center">';
if($daten['wrrc'] == 1)
      echo'<input type="checkbox" name="wrrc" value="1" checked>';
   else
      echo'<input type="checkbox" name="wrrc" value="1">';
   echo'</td><td>';
   
   
echo'<select name="genre_1">';
   if($daten['genre'] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($daten['genre'] == 'blues')
      echo'<option value="blues" selected>Blues</option>';
   else
      echo'<option value="blues">Blues</option>';
   if($daten['genre'] == 'swing')
      echo'<option value="swing" selected>Swing</option>';
   else
      echo'<option value="swing">Swing</option>';
   if($daten['genre'] == 'rock')
      echo'<option value="rock" selected>Rock</option>';
   else
      echo'<option value="rock">Rock</option>';
   if($daten['genre'] == 'piano')
      echo'<option value="piano" selected>Piano Boogie</option>';
   else
      echo'<option value="piano">Piano Boogie</option>';
   echo'</select>';   

    echo'</td><td align="center">';
    echo'<select name="sterne_1" >';
   if($_POST["sterne_1"] == '---' || $daten['wertung_1'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_1'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';

    echo'</td><td align="center">';
    echo'<select name="sterne_2" >';
   if($daten['wertung_2'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_2'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';

    echo'</td><td align="center">';
    
    echo'<select name="sterne_3" >';
   if($daten['wertung_3'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_3'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';

    echo'</td><td align="center">';    
    echo'<select name="sterne_4" >';
   if($daten['wertung_4'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_4'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';
    echo'</td><td align="center">';
    echo'<select name="sterne_5" >';
   if($daten['wertung_5'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_5'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';
    echo'</td><td align="center">';
    echo'<select name="sterne_6" >';
   if($daten['wertung_6'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_6'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select>';
    echo'</td><td align="center">';
    echo'<select name="sterne_7" >';
   if($_POST["sterne_7"] == '---' || $daten['wertung_7'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($daten['wertung_7'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
   }
   echo'</select></td>';
   
   echo'</td><td>';
   
echo'<select name="struktur_1">';
   if($daten['struktur'] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($daten['struktur'] == '4x8')
      echo'<option value="4x8" selected>4x8</option>';
   else
      echo'<option value="4x8">4x8</option>';
   if($daten['struktur'] == '6x8')
      echo'<option value="6x8" selected>6x8</option>';
   else
      echo'<option value="6x8">6x8</option>';
   if($daten['struktur'] == 'gemischt')
      echo'<option value="gemischt" selected>gemischt</option>';
   else
      echo'<option value="gemischt">gemischt</option>';
   if($daten['struktur'] == 'andere')
      echo'<option value="andere" selected>andere</option>';
   else
      echo'<option value="andere">andere</option>';
   echo'</select>';

   echo'</td><td>';
   
echo'<center><select name="ausnahmen_1">';
   if($daten['ausnahmen'] == 0)
      echo'<option value="0" selected>0</option>';
   else
      echo'<option value="0">0</option>';
   if($daten['ausnahmen'] == 1)
      echo'<option value="1" selected>1</option>';
   else
      echo'<option value="1">1</option>';
   if($daten['ausnahmen'] == 2)
      echo'<option value="2" selected>2</option>';
   else
      echo'<option value="2">2</option>';
   if($daten['ausnahmen'] == 3)
      echo'<option value="3" selected>3</option>';
   else
      echo'<option value="3">3</option>';
   if($daten['ausnahmen'] == 4)
      echo'<option value="4" selected>4</option>';
   else
      echo'<option value="4">4</option>';
   if($daten['ausnahmen'] == 5)
      echo'<option value="5" selected>5</option>';
   else
      echo'<option value="5">5</option>';
   if($daten['ausnahmen'] == 6)
      echo'<option value="6" selected>6</option>';
   else
      echo'<option value="6">6</option>';
   if($daten['ausnahmen'] == 7)
      echo'<option value="7" selected>7</option>';
   else
      echo'<option value="7">7</option>';
   if($daten['ausnahmen'] == 8)
      echo'<option value="8" selected>8</option>';
   else
      echo'<option value="8">8</option>';
   if($daten['ausnahmen'] == 9)
      echo'<option value="9" selected>9</option>';
   else
      echo'<option value="9">9</option>';
   echo'</select>';
   
   
   echo'</td></tr>';

echo'<tr><td colspan="2" align="center"><input type="submit" name="eintragen" value="Eintragen"></td><td colspan="2" align="center"><input type="submit" name="aendern" value="Ändern"></td><td colspan="7" align="center"><input type="submit" name="auswahl" value="Auswählen"></td><td colspan="7" align="center"><input type="submit" name="loeschen" value="Löschen"></td></tr>';
echo'</table>';
echo'<p>';

// Ausgaben

echo'<h2><center>Ausgaben erzeugen</h2>';

echo'<center><table border="1">';

// Startklassen

echo'<tr><th width="210" align="center">Anzahl</th><th width="120" align="center">Startklasse</th><th width="180">Takte</th><th width="150">Genre</th><th width="100">Sterne</th><th>Struktur</th><th>Ausnahmen</th></tr>';



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
   echo' Lieder </td><td>';


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

   for($x = 27; $x < 61; $x++ )
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

   for($x = 27; $x < 61; $x++ )
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
   echo'</td><td>';

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

echo'</td><td>';

echo'<select name="struktur" onchange="submit();">';
   if($_POST['struktur'] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($_POST['struktur'] == '4x8')
      echo'<option value="4x8" selected>4x8</option>';
   else
      echo'<option value="4x8">4x8</option>';
   if($_POST['struktur'] == '6x8')
      echo'<option value="6x8" selected>6x8</option>';
   else
      echo'<option value="6x8">6x8</option>';
   if($_POST['struktur'] == 'gemischt')
      echo'<option value="gemischt" selected>gemischt</option>';
   else
      echo'<option value="gemischt">gemischt</option>';
   if($_POST['struktur'] == 'andere')
      echo'<option value="andere" selected>andere</option>';
   else
      echo'<option value="andere">andere</option>';
   echo'</select>';

   echo'</td><td>';
   
echo'<center><select name="ausnahmen" onchange="submit();">';
   if($_POST['ausnahmen'] == '---')
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   if($_POST['ausnahmen'] == 1)
      echo'<option value="1" selected>1</option>';
   else
      echo'<option value="1">1</option>';
   if($_POST['ausnahmen'] == 2)
      echo'<option value="2" selected>2</option>';
   else
      echo'<option value="2">2</option>';
   if($_POST['ausnahmen'] == 3)
      echo'<option value="3" selected>3</option>';
   else
      echo'<option value="3">3</option>';
   if($_POST['ausnahmen'] == 4)
      echo'<option value="4" selected>4</option>';
   else
      echo'<option value="4">4</option>';
   if($_POST['ausnahmen'] == 5)
      echo'<option value="5" selected>5</option>';
   else
      echo'<option value="5">5</option>';
   if($_POST['ausnahmen'] == 6)
      echo'<option value="6" selected>6</option>';
   else
      echo'<option value="6">6</option>';
   if($_POST['ausnahmen'] == 7)
      echo'<option value="7" selected>7</option>';
   else
      echo'<option value="7">7</option>';
   if($_POST['ausnahmen'] == 8)
      echo'<option value="8" selected>8</option>';
   else
      echo'<option value="8">8</option>';
   if($_POST['ausnahmen'] == 9)
      echo'<option value="9" selected>9</option>';
   else
      echo'<option value="9">9</option>';
   echo'</select>';
   
   echo'</td></tr>';

echo'<tr><td>Dateiname: <input type=text name="dateiname" value="' . $_POST['dateiname'] . '"  size="15" maxlength="20"></td><td colspan = "3" align="center"><input type="submit" name="anzeigen" value="Anzeigen"></td><td colspan="3" align="center"><input type="submit" name="download" value="Für Musikdownloader herunterladen"></td></tr>';
echo'</table>';
echo'</form>';
echo'<p />';

// Bildschirmausgabe

// Kopfzeile für Datei

$separator   = ";";
$valueQuotes = '"';

$str_csv = $str_csv. $valueQuotes. 'Titel'. $valueQuotes.  
         $separator. $valueQuotes. 'Interpret'. $valueQuotes.  
         $separator. $valueQuotes. 'Takte'. $valueQuotes.  
         $separator. $valueQuotes. 'Genre'. $valueQuotes.  
         $separator. $valueQuotes. 'Link'. $valueQuotes.  
$str_csv = $str_csv. "\r\n";

echo'<h4><center>Zeige alle Titel die  ';
   if($_POST['bewertet'] == 1)
      echo'<input type="checkbox" name="bewertet" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="bewertet" value="1" onchange="submit();">';
echo' bewertet '; 
   if($_POST['nicht_bewertet'] == 1)
      echo'<input type="checkbox" name="nicht_bewertet" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="nicht_bewertet" value="1" onchange="submit();">';
echo' nicht bewertet wurden '; 
   if($_POST['hoeren'] == 1)
      echo'<input type="checkbox" name="hoeren" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="hoeren" value="1" onchange="submit();">';
echo' Musik anhören </center></h4>'; 

// Suchfeld

echo'<center><b>Beliebige Suche: </b><input type=text name="suche" size="25" maxlength="80">';
echo' <input type="submit" name="suchen" value="Suchen"></center>';


echo'<center><table border="1">';
echo'<tr><th rowspan="2" align="center">Nr.</th><th width="50" rowspan="2" align="center">Auswahl</th><th width="20" rowspan="2" align="center">RR</th><th width="20" rowspan="2" align="center">BW</th><th width="300" rowspan="2">Titel</th><th width="300" rowspan="2">Interpret</th><th width="50" rowspan="2">Takte</th><th width="100" rowspan="2">Genre</th><th colspan="7">Punkte</th><th width="82" rowspan="2">Sterne</th><th width="50" rowspan="2">Struktur</th><th rowspan="2">Ausn.<th width="150" rowspan="2">Anhören</th><th rowspan="2">Update ';

if($_POST['sortieren'] == 1)
      echo'<input type="checkbox" name="sortieren" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="sortieren" value="1" onchange="submit();">';

echo'</th></tr>';
echo'<tr><th>A<br>T</th><th>H<br>E</th><th>P<br>M</th><th>M<br>R</th><th>C<br>M</th><th>J<br>C</th><th>T</th><tr>';

$sqlab = 'SELECT * From turniermusik ';

if($_POST['startklasse'] =='rocknroll')
    $sqlab .= ' WHERE rocknroll = 1 ';
    
if($_POST['startklasse'] =='boogiewoogie')
   $sqlab .= ' WHERE boogiewoogie = 1 ';
   
if($_POST['startklasse'] != '---' && $_POST['von'] != '---' && $_POST['bis'] != '---')
   $sqlab .= ' AND  takte >= ' . $_POST['von'] . ' AND takte <= ' . $_POST['bis'];
elseif($_POST['von'] != '---' && $_POST['bis'] != '---')
   $sqlab .= ' WHERE takte >= ' . $_POST['von'] . ' AND takte <= ' . $_POST['bis'];  

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---') && $_POST['genre'] != '---')
   $sqlab .= ' AND  genre LIKE "' . $_POST['genre'] . '" ';
elseif($_POST['genre'] != '---')
   $sqlab .= ' WHERE genre LIKE "' . $_POST['genre'] . '" ';  

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---') && $_POST['wertung'] != '---')
   $sqlab .= ' AND  wertung = "' . $_POST['wertung'] . '" ';
elseif($_POST['wertung'] != '---')
   $sqlab .= ' WHERE wertung = "' . $_POST['wertung'] . '" ';

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---' || $_POST['wertung'] != '---') && $_POST['struktur'] != '---')
   $sqlab .= ' AND  struktur = "' . $_POST['struktur'] . '" ';
elseif($_POST['struktur'] != '---')
   $sqlab .= ' WHERE struktur = "' . $_POST['struktur'] . '" ';

if(($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---' || $_POST['wertung'] != '---' || $_POST['struktur'] != '---') && $_POST['ausnahmen'] != '---')
   $sqlab .= ' AND ausnahmen = "' . $_POST['ausnahmen'] . '" ';
elseif($_POST['ausnahmen'] != '---')
   $sqlab .= ' WHERE ausnahmen = "' . $_POST['ausnahmen'] . '" ';

   

if($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---' || $_POST['wertung'] != '---')
  {
   if($_POST['bewertet'] == 1)
      $sqlab .= ' AND wertung > 0';
   if($_POST['nicht_bewertet'] == 1)
      $sqlab .= ' AND wertung < 1';
  }
else  
  {
   if($_POST['bewertet'] == 1)
      $sqlab .= ' WHERE wertung > 0';
   if($_POST['nicht_bewertet'] == 1)
      $sqlab .= ' WHERE wertung < 1';
  }

   if($_POST['sortieren'] == 1)
      $sqlab .= ' ORDER BY last_update DESC ';

if($_POST['anzahl'] !='---')
    $sqlab .= ' LIMIT ' . $_POST['anzahl'];
    
if($_POST['suche'] !='')
    $sqlab = "SELECT * FROM turniermusik WHERE titel LIKE '%" . $_POST["suche"] . "%' OR interpret LIKE '%" . $_POST["suche"] . "%'";

if($sqlab == 'SELECT * From turniermusik ')
   $sqlab = 'SELECT * From turniermusik LIMIT 10';

 echo $sqlab . '<br>';
$lieder = mysqli_query($db, $sqlab);
$anzahl_auswahl = mysqli_affected_rows($db);

while($temp = mysqli_fetch_array($lieder))
  {
   // print_r($temp). '<br>';
//   echo'<tr><td>' . utf8_encode($temp['titel']) . '</td><td>' . utf8_encode($temp['interpret']) . '</td><td>' . $temp['takte'] . '</td><td>' . utf8_encode($temp['genre']) . '</td></tr>';

if($temp['rocknroll'] == 1)
   $rr = '&bull;';
else
   $rr = '';
 if($temp['boogiewoogie'] == 1)
   $bw = '&bull;';
else
   $bw = '';  
unset($durchschnitt);
$durchschnitt = array();  
unset($teiler);
if($temp['wertung_1'] >= 1)
  {
   $teiler = 1;
   $durchschnitt[] = $temp['wertung_1'];
  }
if($temp['wertung_2'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_2'];
  }
if($temp['wertung_3'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_3'];
  }
if($temp['wertung_4'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_4'];
  }
if($temp['wertung_5'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_5'];
  }
if($temp['wertung_6'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_6'];
  }
if($temp['wertung_7'] >= 1)
  {
   $teiler = $teiler + 1;
   $durchschnitt[] = $temp['wertung_7'];
  }
   
   $wertung = $temp['wertung'];
   $wertung_org = ($temp['wertung_1'] + $temp['wertung_2'] + $temp['wertung_3'] + $temp['wertung_4'] + $temp['wertung_5'] + $temp['wertung_6'] + $temp['wertung_7'])/$teiler;
   $wertung_org = number_format($wertung_org, 4,',', '.');
   $bezeichnung = str_replace('&', '&teil2=', $temp['bezeichnung']);
   $teiler_2 = count($durchschnitt) - 2;
   $schnitt_2 = (array_sum($durchschnitt) - max($durchschnitt) - min($durchschnitt))/$teiler_2;

unset($stern);
   if($wertung == 1)
      $stern = '<img src="../1Stern.png">';
   if($wertung == 2)
      $stern = '<img src="../2Sterne.png">';
   if($wertung == 3)
      $stern = '<img src="../3Sterne.png">';
   if($wertung == 4)
      $stern = '<img src="../4Sterne.png">';
   if($wertung == 5)
      $stern = '<img src="../5Sterne.png">';

       $lfdnr = $lfdnr + 1;
   echo'<tr>
          <td align="center">' . $lfdnr . '</td>
          <td align="center"><input type="radio" name = "bearbeiten" value = "' . $temp['id'] . '" ></td>
          <td align="center">' . $rr . '</td>
          <td align="center">' . $bw . '</td>
          <td>' . $temp['titel'] . '</td>
          <td>' . $temp['interpret'] . '</td>
          <td align="center">' . $temp['takte'] . '</td>
          <td>' . $temp['genre'] . '</td>
          <td align="center">' . $temp['wertung_1'] . '</td>
          <td>' . $temp['wertung_2'] . '</td> 
          <td align="center">' . $temp['wertung_3'] . '</td> 
          <td align="center">' . $temp['wertung_4'] . '</td> 
          <td align="center">' . $temp['wertung_5'] . '</td> 
          <td align="center">' . $temp['wertung_6'] . '</td> 
          <td align="center">' . $temp['wertung_7'] . '</td> 
          <td align="center">' . $stern . '<br>' . $wertung_org . '<br>' . $schnitt_2 .'</td>';
          
echo'<td><center>' . $temp['struktur'] . '</td><td><center>' . $temp['ausnahmen'] . '</td>';
 
if($_POST['hoeren'] == 1)         
   echo'<td><audio controls preload="none">>
  <source src="http://www.drbv.de/turniermusik/musikdb.php?pfad=' . $temp['pfad'] . '&file=' . $bezeichnung . '" type="audio/mpeg">
Your browser does not support the audio element.
</audio>
          </td>';
else
   echo'<td>&nbsp;</td>';
          
   echo'<td>' . $temp['last_update'] . '</tr>';
   
$str_body .= $valueQuotes . $temp['titel'] . $valueQuotes . $separator . $valueQuotes . $temp['interpret'] . $valueQuotes . $separator . $valueQuotes . $temp['takte'] . $valueQuotes . $separator . $valueQuotes . $temp['genre'] . $valueQuotes . $separator . $valueQuotes . "https://drbv.de/turniermusik/musikdb.php?pfad=" . $temp['pfad'] . '&file=' . $bezeichnung . $valueQuotes. "\r\n";
  }
    $inhalt = $str_csv . $str_body; 
  
// Dateinamen festlegen
     $filename = $_POST['dateiname'] . ".csv";
     // Datei auf Server speichern
     $fn = "./" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $inhalt);
     fclose($fp);
     // if(is_file($fn))
     // echo"Die Datei $filename wurde gespeichert!<br><br>"; 
     
   
   
   echo'</table><br>';

echo'Gesamt: ' . $anzahl_auswahl . ' Titel<br>';

?>


</body>
</html>
