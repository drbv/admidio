<?php

// error_reporting (E_ALL);

session_start();
 
 function eingabebereinigen(&$value, $key)
 { 
   // keine HTML-Tags erlaubt, außer p und br 
   $value = strip_tags($value);
   
   // HTML-Tags maskieren 
   $value = htmlspecialchars($value, ENT_QUOTES);

   // Leerzeichen am Anfang und Ende beseitigen 
   $value = trim($value);
 }
  
array_walk ( $_POST, 'eingabebereinigen' );
array_walk ( $_GET, 'isint' );
array_walk ( $_REQUEST, 'eingabebereinigen' );
 
require("./intern/dboeffnen.inc.php");

  foreach ( $_POST as $var => $val )
  {
    $_POST[$var] = htmlspecialchars ( $val, ENT_QUOTES);
  }

  foreach ( $_GET as $var => $val )
  {
    $_GET[$var] = htmlspecialchars ( $val, ENT_QUOTES);
  }

// print_r($_POST);echo"<br>";
// print_r($_SESSION);echo"<br>";
// print_r($_GET);echo"<br>";


if(!$_GET)
{
if(!$_SESSION["lizenznr"] || $_POST["lizenznr"] != $_SESSION["lizenznr"] )
    $_SESSION["lizenznr"] = $_POST["lizenznr"];  
 if(!$_SESSION["t_jahr"] || $_POST["t_jahr"] != $_SESSION["t_jahr"] )
    $_SESSION["t_jahr"] = $_POST["t_jahr"];  
}
if($_GET['tn'])
   $_SESSION['turniernummer'] = $_GET['tn'];
 
?>
<!DOCTYPE HTML>
<html lang="de">
<head>
<link rel="icon" href="favicon.ico" type="image/ico">
<meta charset="UTF-8">
<title>Wertungsrichter</title>
<meta name="viewport" content="width = 1280, minimum-scale = 0.25, maximum-scale = 1.60">
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
</head>
<body>

<?php
 
// Wertungsrichter auswählen

$wr_vorhanden = array();

echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';  
echo'<center><table>';
echo'<tr><td colspan="2"><b>Bitte Wertungsrichter auswählen: ';
echo'<select name="lizenznr" size="1"  onchange="submit();">';
echo'<option value="---">---</option>';

// Wertungsrichter aus Datenbank lesen

$sqlab = "SELECT lizenznummer,name FROM wertungsrichter ORDER BY lizenznummer";
$wertungsrichter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($wertungsrichter))
  {
   if(!in_array($temp[0], $wr_vorhanden))
      {
       $wr_name = utf8_encode($temp[1]);  
       if($_SESSION["lizenznr"] == $temp[0])
          echo'<option value="' . $temp[0] . '"  selected>' . $temp[0] . ' ' . $wr_name . '</option>';
       else
          echo'<option value="' . $temp[0] . '">' . $temp[0] . ' ' . $wr_name . '</option>';
       
          $wr_vorhanden[] = $temp[0];
        }
  }
echo'</select></tr></td>';

// Jahr auswählen

echo'<tr><td colspan="2"><b>Bitte das Jahr auswählen: ';
echo'<select name="t_jahr" size="1"  onchange="submit();">';
echo'<option value="---">---</option>';

for($x = 2016; $x < 2026; $x++ )
{
 if($_SESSION["t_jahr"] == $x)
    echo'<option value="' . $x . '"  selected>' . $x . '</option>';
 else
    echo'<option value="' . $x . '">' . $x  . '</option>';
}
echo'</select></tr></td>';

echo'</form>';

echo'<tr><td colspan="2"><center>&nbsp</td></tr>';

// gewertete Turniere auswählen

$sqlab = "SELECT turniernummer,lizenznummer,name FROM wertungsrichter WHERE lizenznummer = " .  $_SESSION["lizenznr"];
$turnier_gewertet = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_gewertet))
  {
   $sqlab = "SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = " .  $temp["0"]; 
   $turnier = mysqli_query($db, $sqlab);
   $aktuell = mysqli_fetch_row($turnier);
   $datum = substr($aktuell[2], 8, 2) . '.'  . substr($aktuell[2], 5, 2) .'.' . substr($aktuell[2], 0, 4); 
   
   $sqlab = "SELECT wr_id_tlp, name FROM wertungsrichter WHERE turniernummer = '" . $aktuell[0] . "' AND lizenznummer = '" . $_SESSION["lizenznr"] ."'";
   $wr_id = mysqli_query($db, $sqlab);
   $wr_id_tlp = mysqli_fetch_row($wr_id);

   if(substr($aktuell[2], 0, 4) == $_SESSION["t_jahr"])
     {
      echo'<tr><td>' . $datum . '</td><td><a href="wertungsrichter_turniere.php?tn=' . $aktuell[0] . '&id=' . $wr_id_tlp[0] . '&name=' . utf8_encode($wr_id_tlp[1]) . '&turniername=' . utf8_encode($aktuell[1]) . '">' . utf8_encode($aktuell[1]) . '</a></td></tr>';
      $anzahl_turniere = $anzahl_turniere +1;
     }
  }

echo'<tr><td colspan="2"><center><b>Es wurden ' . $anzahl_turniere . ' Turniere gewertet.</td></tr>';

echo'</table>';

if($_GET['tn'])
{ 
 // Tanzrunden suchen
    $sqlab = "SELECT rt_id_tlp, startklasse, runde FROM rundentab WHERE turniernummer = '" . $_SESSION['turniernummer'] . "'";
    $runden = mysqli_query($db,$sqlab);
    $tanzrunden[0] = array();
    
    while($temp = mysqli_fetch_array($runden))
          {
           $tanzrunden[$temp['rt_id_tlp']] = $temp;
           // print_r($tanzrunden[$temp['rt_id_tlp']]);echo'<br>';
          }
    
// Paare suchen
    $sqlab = "SELECT paar_id_tlp, dame, herr, team FROM paare WHERE turniernummer = '" . $_SESSION['turniernummer'] . "'";
    $paare_ges = mysqli_query($db,$sqlab);
    $paare[0] = array();
    
    while($temp = mysqli_fetch_array($paare_ges))
          {
           $paare[$temp['paar_id_tlp']] = $temp;
           // print_r($paare[$temp['paar_id_tlp']]);echo'<br>';
          }
 
 // Wertungsrichter suchen
    $sqlab = "SELECT wr_id_tlp, name FROM wertungsrichter WHERE turniernummer = '" . $_SESSION['turniernummer'] . "'";
    $wr_ges = mysqli_query($db,$sqlab);
    $wr[0] = array();
    
    while($temp = mysqli_fetch_array($wr_ges))
          {
           $wr_schrank[$temp['wr_id_tlp']] = $temp;
           // print_r($paare[$temp['paar_id_tlp']]);echo'<br>';
          }

 // Wertungen ausgeben
 echo'<p />';
 echo'<center><table border="1">';
 echo'<tr><th colspan="34" align=center>' . $_GET['turniername'] . '</th></tr>';
 echo'<tr><th>Name</th> <th>Herr GT</th> <th>Haltung-DT</th> <th>Dame GT</th> <th>Haltung DT</th> <th>Choreo</th> <th>TF</th> <th>Darbietung</th> <th>GF</th> <th>GF</th> <th>Akro 1</th> <th>GF 1</th> <th>GF 1</th> <th>A 2</th> <th>GF 2</th> <th>GF 2</th> <th>A 3</th> <th>GF 3</th> <th>GF 3</th> <th>A 4</th> <th>GF 4</th> <th>GF 4</th> <th>A 5</th> <th>GF 5</th> <th>GF 5</th> <th>A 6</th> <th>GF 6</th> <th>GF 6</th> <th>A 7</th> <th>GF 7</th> <th>GF 7</th> <th>A 8</th> <th>GF 8</th> <th>GF 8</th></tr>';

 $sqlab = "SELECT * FROM wertungen WHERE turniernummer = " .  $_SESSION["turniernummer"] . ' ORDER BY rund_tab_id , paar_id_tlp' ;
 $wertungen = mysqli_query($db, $sqlab);
 
 while($temp = mysqli_fetch_array($wertungen))
          {
           if($temp['rund_tab_id'] != $rund_tab_id)
           {
            $rund_tab_id = $temp['rund_tab_id'];
            echo'<tr><td colspan="34" bgcolor="#FFFF00">' . $tanzrunden[$rund_tab_id]['startklasse'] . ' - ' . utf8_encode($tanzrunden[$rund_tab_id]['runde']) . '</td></tr>';
           }
           
           if($temp['paar_id_tlp'] != $paar_id)
           {
            $paar_id = $temp['paar_id_tlp'];
	            echo'<tr><td colspan="34" bgcolor="#00FF00">' . utf8_encode($paare[$paar_id]['dame']) . ' ' . utf8_encode($paare[$paar_id]['herr']) . ' ' . utf8_encode($paare[$paar_id]['team']) . '</td></tr>';
           }
           echo'<tr><td>';
           if($temp['wr_id'] == $_GET['id'])
           
              echo '<b>' . $_GET['name'] . '</b>';
           else
              echo utf8_encode($wr_schrank[$temp['wr_id']] ['name']);
              
           echo'</td><td>' . $temp['herr_gt'] . '</td> <td>' . $temp['herr_halt_dt'] . '</td> <td>' . $temp['dame_gt'] . '</td> <td>' . $temp['dame_halt_dt'] . '</td> <td>' . $temp['choreo'] . '</td> <td>' . $temp['tanzfiguren'] . '</td> <td>' . $temp['taenz_darbietung'] . '</td> <td>' . $temp['grobfehler_text'] . '</td> <td>' . $temp['grobfehler_summe'] . '</td> <td>' . $temp['akro1'] . '</td> <td>' . $temp['akro1_grobfehler_text'] . '</td> <td>' . $temp['akro1_grobfehler_summe'] . '</td> <td>' . $temp['akro2'] . '</td> <td>' . $temp['akro2_grobfehler_text'] . '</td> <td>' . $temp['akro2_grobfehler_summe'] . '</td><td>' . $temp['akro3'] . '</td> <td>' . $temp['akro3_grobfehler_text'] . '</td> <td>' . $temp['akro3_grobfehler_summe'] . '</td> <td>' . $temp['akro4'] . '</td> <td>' . $temp['akro4_grobfehler_text'] . '</td> <td>' . $temp['akro4_grobfehler_summe'] . '</td>  <td>' . $temp['akro5'] . '</td> <td>' . $temp['akro5_grobfehler_text'] . '</td> <td>' . $temp['akro5_grobfehler_summe'] . '</td> <td>' . $temp['akro6'] . '</td> <td>' . $temp['akro6_grobfehler_text'] . '</td> <td>' . $temp['akro6_grobfehler_summe'] . '</td> <td>' . $temp['akro7'] . '</td> <td>' . $temp['akro7_grobfehler_text'] . '</td> <td>' . $temp['akro7_grobfehler_summe'] . '</td> <td>' . $temp['akro8'] . '</td> <td>' . $temp['akro8_grobfehler_text'] . '</td> <td>' . $temp['akro8_grobfehler_summe'] . '</td>';
           echo'</tr>';
          }
 
 
 
 
 echo'</table>';
}
?>
</body>
</html>