<?php

array_walk ( $_POST, 'eingabebereinigen' );
array_walk ( $_GET, 'isint' );
array_walk ( $_REQUEST, 'eingabebereinigen' );
 
require("./intern/dboeffnen.inc.php");

// Startbuchnummer eingeben
echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';  

echo'<center><table>';
   
   echo'<tr><td><b> Bitte Startbuchnummer eingeben: </b> ';
   echo'<input type=text name="sb_nr" value="' . $_POST[sb_nr] . '"  size=5 maxlength=5></td></tr>';
   echo'<td colspan = 2><center><input type=submit name="absenden" value="Absenden"></td></tr>';

echo'</table>';  
  
echo'</form>';

// Tanzpaar in Datenbank suchen
  
$sqlab = 'SELECT turniernummer, startklasse, dame, herr, team, platz, punkte, rl_punkte FROM paare WHERE startbuch = ' . $_POST["sb_nr"]; 
// echo"$sqlab<br>"; 
$punkte =  mysqli_query($db, $sqlab);

while($aufstiegspunkte = mysqli_fetch_array($punkte))
      {
//       print_r($aufstiegspunkte);echo"<br>";

       $sqlab = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $aufstiegspunkte["turniernummer"]; 
       $turnier = mysqli_query($db, $sqlab);
       $turnierdaten = mysqli_fetch_array($turnier);
// echo"$sqlab<br>"; 


       echo'<p>Turniernummer: <b>' . $aufstiegspunkte["turniernummer"] . '</b> ' . utf8_encode($turnierdaten["turniername"]) . '<br>'; 
       echo'Datum: <b>' . $turnierdaten["datum"] . '</b><br>';        
       echo'Startklasse: <b>' . $aufstiegspunkte["startklasse"] . '</b><br>'; 
       echo'Dame: <b>' . utf8_encode($aufstiegspunkte["dame"]) . '</b><br>'; 
       echo'Herr: <b>' . utf8_encode($aufstiegspunkte["herr"]) . '</b><br>'; 
       echo'Team: <b>' . utf8_encode($aufstiegspunkte["team"]) . '</b><br>'; 
       echo'Platz: <b>' . $aufstiegspunkte["platz"] . '</b><br>';
       echo'Aufstiegspunkte: <b>' . $aufstiegspunkte["punkte"] . '</b><br>';              
       echo'Ranglistenpunkte: <b>' . $aufstiegspunkte["rl_punkte"] . '</b><br></p>';    
       $summe = $summe + $aufstiegspunkte["punkte"];   
      }
echo'<p>Gesamtsumme: <b>' . $summe . '</b><br></p>';
?>