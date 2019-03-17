<?php

session_start();

require_once("./admin/dboeffnen.inc.php");

require_once("../intern/bereinigen.php");

/*
echo'GET: ';print_r($_GET);echo"<br>"; 
echo'POST: ';print_r($_POST);echo"<br>"; 
echo'SESSION: ';print_r($_SESSION);echo"<br>";
*/

if(!$_SESSION['name'])
   {
    $_SESSION['name'] = $_GET['name'];
    $_POST['nicht_bewertet'] = 1;
   }
    
if($_SESSION['name'] && $_GET['name'])
   {
    $_SESSION['name'] = $_GET['name'];
    $_POST['nicht_bewertet'] = 1;
   }
if($_POST['startklasse'] == '---' && ($_SESSION['name'] == 'Alexandra' || $_SESSION['name'] == 'Hermann' || $_SESSION['name'] == 'Philipp' || $_SESSION['name'] == 'Melanie' || $_SESSION['name'] == 'Christian' || $_SESSION['name'] == 'Jonah' || $_SESSION['name'] == 'Tanja'))
    $_POST['startklasse'] = 'rocknroll';
if($_POST['startklasse'] == '---' && ($_SESSION['name'] == 'Thomas' || $_SESSION['name'] == 'Elian' || $_SESSION['name'] == 'Michael' || $_SESSION['name'] == 'Ralf' || $_SESSION['name'] == 'Mark' || $_SESSION['name'] == 'Christoph'))
   {
    $_POST['startklasse'] = 'boogiewoogie';
   }

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
    <title>Musikdatenbank bewerten</title>
</head>
<body>

<?php

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

$teiler_1 = count($durchschnitt) - 2;

if($teiler_1 > 0.9)
   $schnitt = round((array_sum($durchschnitt) - max($durchschnitt) - min($durchschnitt))/$teiler_1);
else
   $schnitt = round(array_sum($durchschnitt)/count($durchschnitt));

 $sqlab = "UPDATE turniermusik set  genre = '" . $_POST['genre_1'] . "', wertung_1 = '" . $_POST['sterne_1'] . "', wertung_2 = '" . $_POST['sterne_2'] . "', wertung_3 = '" . $_POST['sterne_3'] . "', wertung_4 = '" . $_POST['sterne_4'] . "',  wertung_5 = '" . $_POST['sterne_5'] . "',  wertung_6 = '" . $_POST['sterne_6'] . "',  wertung_7 = '" . $_POST['sterne_7'] . "',  wertung = '" . $schnitt . "',  struktur = '" . $_POST['struktur_1'] . "'      ,  ausnahmen = '" . $_POST['ausnahmen_1'] . "' WHERE id='" . $_SESSION['id'] . "'";
// echo$sqlab. '<br>';
mysqli_query($db, $sqlab);
$erfolg = mysqli_affected_rows($db);
if($erfolg == 1)
   echo'Der Datenstatz wurde geändert!<br>';
   unset($_SESSION['id']);
}

echo'<h1><center>Turniermusik DRBV</h1>';

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<h2><center>Wertungen verwalten</h2>';

// Verwalten

echo'<center><table border="1">';
echo'<tr><th width="185" align="center">Dateiname</th><th width="185" align="center">Pfad</th><th width="185">Titel</th><th width="185">Interpret</th><th width="45">Takte</th><th width="25">RR</th><th width="25">BW</th><th width="100">Genre</th><th width="60">Struktur</th><th width="60">Ausnahmen</th><th width="60">Sterne</th></tr>'; // 1</th><th width="60">Sterne 2</th><th width="60">Sterne 3</th><th width="60">Sterne 4</th><th width="60">Sterne 5</th><th width="60">Sterne </th><th width="60">Sterne 7</th></tr>';

if($_POST['bearbeiten'])
  {
   $sqlab = 'SELECT * From turniermusik WHERE id=' . $_POST['bearbeiten'] ;
   $datensatz = mysqli_query($db, $sqlab);
   $daten = mysqli_fetch_array($datensatz);
//    print_r($daten);echo"<br>";
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

if($_SESSION['name'] == 'Alexandra' || $_SESSION['name'] == 'Thomas')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_1" >';
   if($_POST["sterne_1"] == '---' || $daten['wertung_1'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_1"] == $x || $daten['wertung_1'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select>';
   echo'</td></tr>'; //<td align="center">' . $daten['wertung_2'] . '</td><td align="center">' . $daten['wertung_3'] . '</td><td align="center">' . $daten['wertung_4'] . '</td><td align="center">' . $daten['wertung_5'] . '</td><td align="center">' . $daten['wertung_6'] . '</td><td align="center">' . $daten['wertung_7'] . '</td></tr>';
   echo'<input type=hidden name="sterne_2" value="' . $daten['wertung_2'] . '">';
   echo'<input type=hidden name="sterne_3" value="' . $daten['wertung_3']. '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4'] . '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5']. '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
  }
  
  
if($_SESSION['name'] == 'Hermann' || $_SESSION['name'] == 'Elian')
  {
   echo'<td align="center">';
   echo'<select name="sterne_2" >';
   if($_POST["sterne_2"] == '---' || $daten['wertung_2'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_2"] == $x || $daten['wertung_2'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_3" value="' . ($daten['wertung_3']) . '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4'] . '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5']. '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
  }
  
  
if($_SESSION['name'] == 'Philipp' || $_SESSION['name'] == 'Michael')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_3" >';
   if($_POST["sterne_3"] == '---' || $daten['wertung_3'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_3"] == $x || $daten['wertung_3'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_2" value="' . ($daten['wertung_2']) . '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4'] . '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5']. '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
  }
  
  
 // Melanie
 if($_SESSION['name'] == 'Melanie' || $_SESSION['name'] == 'Ralf')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_4" >';
   if($_POST["sterne_4"] == '---' || $daten['wertung_4'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_4"] == $x || $daten['wertung_4'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_2" value="' . ($daten['wertung_2']) . '">';
   echo'<input type=hidden name="sterne_3" value="' . ($daten['wertung_3']) . '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5']. '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
   }
 // Christian
  if($_SESSION['name'] == 'Christian' || $_SESSION['name'] == 'Mark')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_5" >';
   if($_POST["sterne_5"] == '---' || $daten['wertung_5'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_5"] == $x || $daten['wertung_5'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_2" value="' . ($daten['wertung_2']) . '">';
   echo'<input type=hidden name="sterne_3" value="' . ($daten['wertung_3']) . '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4']. '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
   }
 
 // Jonah
  if($_SESSION['name'] == 'Jonah' || $_SESSION['name'] == 'Christoph')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_6" >';
   if($_POST["sterne_6"] == '---' || $daten['wertung_6'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_6"] == $x || $daten['wertung_6'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_2" value="' . ($daten['wertung_2']) . '">';
   echo'<input type=hidden name="sterne_3" value="' . ($daten['wertung_3']) . '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4']. '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5'] . '">';
   echo'<input type=hidden name="sterne_7" value="' . $daten['wertung_7']. '">';
   } 
 
// Tanja
  if($_SESSION['name'] == 'Tanja')
  {
   echo'</td><td align="center">';
   echo'<select name="sterne_7" >';
   if($_POST["sterne_7"] == '---' || $daten['wertung_7'] == $x)
      echo'<option value="---" selected>---</option>';
   else
      echo'<option value="---">---</option>';
   for($x = 1; $x < 6; $x++ )
   {
    if($_POST["sterne_7"] == $x || $daten['wertung_7'] == $x)
      echo'<option value="' . $x . '" selected>' . $x . '</option>';
   else
      echo'<option value="' . $x . '">' . $x . '</option>';
     echo $x . '<br>';
   }
   echo'</select></td></tr>';
   echo'<input type=hidden name="sterne_1" value="' . ($daten['wertung_1']) . '">';
   echo'<input type=hidden name="sterne_2" value="' . ($daten['wertung_2']) . '">';
   echo'<input type=hidden name="sterne_3" value="' . ($daten['wertung_3']) . '">';
   echo'<input type=hidden name="sterne_4" value="' . $daten['wertung_4']. '">';
   echo'<input type=hidden name="sterne_5" value="' . $daten['wertung_5'] . '">';
   echo'<input type=hidden name="sterne_6" value="' . $daten['wertung_6']. '">';
   } 

echo'<tr><td colspan="3" align="center"><input type="submit" name="auswahl" value="Auswählen"></td><td colspan="8" align="center"><input type="submit" name="aendern" value="Speichern"></td></tr>';

   echo'</table>';
echo'<p>';

// Ausgaben

echo'<h2><center>Ausgaben erzeugen</h2>';
echo'<h4><center>Zeige alle Titel die ich ';
   if($_POST['bewertet'] == 1)
      echo'<input type="checkbox" name="bewertet" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="bewertet" value="1" onchange="submit();">';
echo' bewertet '; 
   if($_POST['nicht_bewertet'] == 1)
      echo'<input type="checkbox" name="nicht_bewertet" value="1" checked onchange="submit();">';
   else
      echo'<input type="checkbox" name="nicht_bewertet" value="1" onchange="submit();">';
echo' nicht bewertet habe</center></h4>'; 

// Suchfeld

echo'<center><b>Beliebige Suche: </b><input type=text name="suche" value="' . $_POST['suche'] . '"  size="25" maxlength="80">';
echo' <input type="submit" name="suchen" value="Suchen"></center>';

echo'<center><table border="1">';

// Startklassen

echo'<tr><th width="200" align="center">Anzahl</th><th width="120" align="center">Startklasse</th><th width="180">Takte</th><th width="150">Genre</th><th width="100">Sterne</th></tr>';
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
     if($_POST["anzahl"] == 5000)
      echo'<option value="5000" selected>alle</option>';
   else
      echo'<option value="5000">alle</option>';
     
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

   for($x = 27; $x < 55; $x++ )
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

   for($x = 27; $x < 55; $x++ )
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

echo'</td></tr>';

echo'<tr><td>Dateiname: <input type=text name="dateiname" value="' . $_POST['dateiname'] . '"  size="15" maxlength="20"></td><td align="center"><input type="submit" name="anzeigen" value="Anzeigen"></td><td colspan="3" align="center"><input type="submit" name="download" value="Für Musikdownloader herunterladen"></td></tr>';
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


echo'<center><table border="1">';
echo'<tr><th width="50" align="center">Auswahl</th><th width="20" align="center">RR</th><th width="20" align="center">BW</th><th width="300">Titel</th><th width="300">Interpret</th><th width="50">Takte</th><th width="100">Genre</th><th width="150">Anhören</th></tr>';

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
   $sqlab .= ' AND wertung = "' . $_POST['wertung'] . '" ';
elseif($_POST['wertung'] != '---')
   $sqlab .= ' WHERE wertung = "' . $_POST['wertung'] . '" ';

if($_POST['startklasse'] != '---' || $_POST['von'] != '---' || $_POST['bis'] != '---' || $_POST['genre'] != '---' || $_POST['wertung'] != '---')
{
if($_SESSION['name'] == 'Alexandra'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_1 > 0';
if($_SESSION['name'] == 'Alexandra'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_1 < 1';
if($_SESSION['name'] == 'Thomas'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_1 > 0';
if($_SESSION['name'] == 'Thomas'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_1 < 1';
if($_SESSION['name'] == 'Hermann'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_2 > 0';
if($_SESSION['name'] == 'Hermann'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_2 < 1';
if($_SESSION['name'] == 'Elian'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_2 > 0';
if($_SESSION['name'] == 'Elian'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_2 < 1';
if($_SESSION['name'] == 'Philipp'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_3 > 0';
if($_SESSION['name'] == 'Philipp'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_3 < 1';
if($_SESSION['name'] == 'Michael'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_3 > 0';
if($_SESSION['name'] == 'Michael'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_3 < 1';
if($_SESSION['name'] == 'Melanie'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_4 > 0';
if($_SESSION['name'] == 'Melanie'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_4 < 1';
if($_SESSION['name'] == 'Ralf'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_4 > 0';
if($_SESSION['name'] == 'Ralf'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_4 < 1';
if($_SESSION['name'] == 'Christian'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_5 > 0';
if($_SESSION['name'] == 'Christian'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_5 < 1';
if($_SESSION['name'] == 'Mark'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_5 > 0';
if($_SESSION['name'] == 'Mark'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_5 < 1';
if($_SESSION['name'] == 'Jonah'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_6 > 0';
if($_SESSION['name'] == 'Jonah'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_6 < 1';
if($_SESSION['name'] == 'Christoph'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_6 > 0';
if($_SESSION['name'] == 'Christoph'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_6 < 1';   
   if($_SESSION['name'] == 'Tanja'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_7 > 0';
if($_SESSION['name'] == 'Tanja'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_7 < 1';
}
else
{
if($_SESSION['name'] == 'Alexandra'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_1 > 0';
if($_SESSION['name'] == 'Alexandra'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_1 < 1';
if($_SESSION['name'] == 'Thomas'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_1 > 0';
if($_SESSION['name'] == 'Thomas'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_1 < 1';
if($_SESSION['name'] == 'Hermann'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_2 > 0';
if($_SESSION['name'] == 'Hermann'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_2 < 1';
if($_SESSION['name'] == 'Elian'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_2 > 0';
if($_SESSION['name'] == 'Elian'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_2 < 1';
if($_SESSION['name'] == 'Philipp'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_3 > 0';
if($_SESSION['name'] == 'Philipp'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_3 < 1';
if($_SESSION['name'] == 'Michael'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_3 > 0';
if($_SESSION['name'] == 'Michael'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_3 < 1';
if($_SESSION['name'] == 'Melanie'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_4 > 0';
if($_SESSION['name'] == 'Melanie'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_4 < 1';
if($_SESSION['name'] == 'Ralf'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_4 > 0';
if($_SESSION['name'] == 'Ralf'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_4 < 1';
if($_SESSION['name'] == 'Christian'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_5 > 0';
if($_SESSION['name'] == 'Christian'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_5 < 1';
if($_SESSION['name'] == 'Mark'  && $_POST['bewertet'] == 1)
   $sqlab .= ' AND wertung_5 > 0';
if($_SESSION['name'] == 'Mark'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' AND wertung_5 < 1';
if($_SESSION['name'] == 'Jonah'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_6 > 0';
if($_SESSION['name'] == 'Jonah'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_6 < 1';
if($_SESSION['name'] == 'Christoph'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_6 > 0';
if($_SESSION['name'] == 'Christoph'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_6 < 1';
if($_SESSION['name'] == 'Tanja'  && $_POST['bewertet'] == 1)
   $sqlab .= ' WHERE wertung_7 > 0';
if($_SESSION['name'] == 'Tanja'  && $_POST['nicht_bewertet'] == 1)
   $sqlab .= ' WHERE wertung_7 < 1';
}

//****************************
// 25 Testtitel
//$sqlab .= ' AND id < 51 ';
//****************************

if($_POST['anzahl'] !='---')
   $sqlab .= ' LIMIT ' . $_POST['anzahl'];
//    $sqlab .= ' ORDER BY RAND() LIMIT ' . $_POST['anzahl'];

if($_POST['suche'] !='')
    $sqlab = "SELECT * FROM turniermusik WHERE titel LIKE '%" . $_POST["suche"] . "%' OR interpret LIKE '%" . $_POST["suche"] . "%'";

if($sqlab == 'SELECT * From turniermusik ')
   $sqlab = 'SELECT * From turniermusik ; //LIMIT 25';

// echo $sqlab . '<br>';

$lieder = mysqli_query($db, $sqlab);
$anzahl_auswahl = mysqli_affected_rows($db);

while($temp = mysqli_fetch_array($lieder))
  {
   // print_r($temp). '<br>';
   $bezeichnung = str_replace('&', '&teil2=', $temp['bezeichnung']);
   if($temp['rocknroll'] == 1)
      $rr = '&bull;';
   else
      $rr = '';
   if($temp['boogiewoogie'] == 1)
      $bw = '&bull;';
   else
      $bw = '';  

   echo'<tr><td align="center"><input type="radio" name = "bearbeiten" value = "' . $temp['id'] . '" ></td><td align="center">' . $rr . '</td><td align="center">' . $bw . '</td><td>' . $temp['titel'] . '</td><td>' . $temp['interpret'] . '</td><td align="center">' . $temp['takte'] . '</td><td>' . $temp['genre'] . '</td><td><audio controls preload="none">
<source src="http://www.drbv.de/turniermusik/musikdb.php?pfad=' . $temp['pfad'] . '&file=' . $bezeichnung . '" type="audio/mpeg">
Your browser does not support the audio element.
</audio></td></tr>';
   
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
