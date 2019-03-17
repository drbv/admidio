<?php

session_start();

// require_once("../intern/bereinigen.php");
 
 function rundenbezeichnung($r_bez)
 {
   if($r_bez != "Vor_r")
    $rundenbez = $r_bez;
  if($r_bez == "Vor_r")
    $rundenbez = "Vorrunde";
  if($r_bez == "1_Zw_r")
    $rundenbez = "1. Zwischenrunde";
  if($r_bez == "2_Zw_r")
    $rundenbez = "2. Zwischenrunde";   
  if($r_bez == "3_Zw_r")
    $rundenbez = "3. Zwischenrunde";        
  if($r_bez == "End_r")
    $rundenbez = "Endrunde";    
  if(utf8_encode($r_bez) == "End_r_Fuß")
    $rundenbez = "Endrunde Fußtechnik";
  if($r_bez == "End_r_Akro")
    $rundenbez = "Endrunde Akrobatik"; 
  if($r_bez == "Vor_r_lang")
    $rundenbez = "langsame Vorrunde";    
  if($r_bez == "Vor_r_schnell")
    $rundenbez = "schnelle Vorrunde";       
  if($r_bez == "End_r_lang")
    $rundenbez = "langsame Endrunde";
  if($r_bez == "End_r_schnell")
      $rundenbez = "schnelle Endrunde";
  if($r_bez == "Hoff_r")
    $rundenbez = "Hoffnungsrunde";
  if($r_bez == "KO_r")
    $rundenbez = "K.o.-Runde";

  return $rundenbez;    
 }
 
 function eingabebereinigen(&$value, $key)
 { 
   // keine HTML-Tags erlaubt, außer p und br 
   $value = strip_tags($value);
   
   // HTML-Tags maskieren 
   $value = htmlspecialchars($value, ENT_QUOTES);

   // Leerzeichen am Anfang und Ende beseitigen 
   $value = trim($value);
 }

function getRundenErgebnis($runde_name){
  global $runde, $rd_erg, $wert_rich, $_SESSION, $gemeldete_akros;
  $wrtg_rr = false;
  $wrtg_bw = false;
  if($_SESSION["startklasse"] == "RR_S" || $_SESSION["startklasse"] == "RR_J" || $_SESSION["startklasse"] == "RR_C" || $_SESSION["startklasse"] == "RR_B" || $_SESSION["startklasse"] == "RR_A" || $_SESSION["startklasse"] == "F_RR_GF" || $_SESSION["startklasse"] == "F_RR_LF" || $_SESSION["startklasse"] == "F_RR_ST" || $_SESSION["startklasse"] == "F_RR_J" || $_SESSION["startklasse"] == "F_RR_M" || $_SESSION["startklasse"] == "F_BW_M"){
    $wrtg_rr = true;
  }
  if($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA"){
    $wrtg_bw = true;  
  }  
  $html  = '';
  $html .= '<tr><td colspan=34 align=center><b>' . $runde_name . '</b></td></tr>';
  $html .= '<tr>';
  for($x = 1;$x<50;$x++)
       {
         $z = current($runde) . '_' . $x;
         if($rd_erg[$z][0])
         {
         // echo $z . '<br>';
         for($y = 4;$y<39;$y++)
           {
//           echo $y;
            if($y == 5)
               $y = 6;
            
            if($y == 9 && ($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA"))
               $y = 10; 
                           
            if($y == 4)
               {
                $name_wr = intval($rd_erg[$z][$y]);
                $anzeige = utf8_encode($wert_rich[$name_wr][1]);
               }
            else   
              $anzeige = $rd_erg[$z][$y];
             if($anzeige == 0 && $y == 6)
                $flag1 = 1;
             if($anzeige == 0 && $y >= 15)
                $flag2 = 2;
             else
                unset($flag2);   
             if($y == 13)
                unset($flag1);
            if($y == 13 && $anzeige != "")
                $flag3 = 3;                
            if($flag1)             
               $anzeige = "&nbsp";
             if($flag2 && !$flag3)
               $anzeige = "&nbsp";

            if($y > 5 && $y < 8 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                   $punkte = $anzeige/10*5;
                elseif($wrtg_bw)
                   $punkte = $anzeige/10*15;
                   
                   $punkte_technik = $punkte_technik + $punkte;         
                   $prozent = $anzeige * 10;

                $anzeige = $prozent . '%' . '<br>' . $punkte;
               }
               
            if($y == 8 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                   $punkte = $anzeige/10*5;
                elseif($wrtg_bw)
                   $punkte = $anzeige/10*20;
                   
                   $punkte_technik = $punkte_technik + $punkte;         
                   $prozent = $anzeige * 10;

                $anzeige = $prozent . '%' . '<br>' . $punkte;
               }
               
            if($y == 9 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                  {
                   $punkte = $anzeige/10*5;
                   $prozent = $anzeige * 10;
                   
                   $punkte_technik = $punkte_technik + $punkte;
                   $anzeige = $prozent . '%' . '<br>' . $punkte;                  
                  }
                  /*
                elseif($wrtg_bw)
                  {
                   //$y = 6;
                   
                   $punkte = "";              
                   $prozent = "";
                   $anzeige = $prozent . ' ' . '<br>' . $punkte;
                  }
                  */
               }               
              
            if($y == 10 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                   $punkte = $anzeige/10*6;
                elseif($wrtg_bw)
                   $punkte = $anzeige/10*30;
                   
                   $punkte_technik = $punkte_technik + $punkte;             
                   $prozent = $anzeige * 10;

                $anzeige = $prozent . '%' . '<br>' . $punkte;
               }               
               
            if($y == 11 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                   $punkte = $anzeige/10*6;
                elseif($wrtg_bw)
                   $punkte = $anzeige/10*10;
                   
                   $punkte_technik = $punkte_technik + $punkte;               
                   $prozent = $anzeige * 10;

                $anzeige = $prozent . '%' . '<br>' . $punkte;
               } 
                
            if($y == 12 && !$flag1 && !$flag2)
               {
                if($wrtg_rr)
                   $punkte = $anzeige/10*8;
                elseif($wrtg_bw)
                   $punkte = $anzeige/10*10;
                   
                   $punkte_technik = $punkte_technik + $punkte;                
                   $prozent = $anzeige * 10;

                $anzeige = $prozent . '%' . '<br>' . $punkte;
               }

            if($y == 13 && ($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA"))
               { 
                $anzeige = $punkte_technik;
                unset($punkte_technik);
                $y = 38;
               }                // Akrowertungen Spalte 15 bis 38
        // ------------------------------
        if($runde_name == "Vorrunde"){
          $ga_start = 15;//gemeldete_akros_startwert
        }
        if($runde_name == "1. Zwischenrunde" || $runde_name == "2. Zwischenrunde" || $runde_name == "3. Zwischenrunde"){
          $ga_start = 31;//gemeldete_akros_startwert
        }
        if($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde"){
          $ga_start = 47;//gemeldete_akros_startwert          
        }      
        if($y == 15 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}
        if($y == 18 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+2]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}      
        if($y == 21 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+4]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}
        if($y == 24 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+6]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}
        if($y == 27 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+8]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}
        if($y == 30 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+10]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}      
        if($y == 33 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+12]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}
        if($y == 36 && !$flag1 && !$flag2){
          $prozent = round($anzeige * 100 / $gemeldete_akros[$ga_start+14]);
          $anzeige = $anzeige . '<br>' . $prozent .'%';}        
                                             
          
           if($prozent > -0 && $prozent < 50)
             $html .= '<td align="center" style="background-color: #ff0000;">' . $anzeige . '</td>';
           if($prozent >= 50 && $prozent < 62.5)
             $html .= '<td align="center" style="background-color: #ff00ff;">' . $anzeige . '</td>';             
           if($prozent >= 62.5 && $prozent < 75)
             $html .= '<td align="center" style="background-color: #00ccff;">' . $anzeige . '</td>';
           if($prozent >= 75 && $prozent < 87.5)
             $html .= '<td align="center" style="background-color: #ffff00;">' . $anzeige . '</td>';
           if($prozent >= 87.5)
             $html .= '<td align="center" style="background-color: #00ff33;">' . $anzeige . '</td>';             
            if(!$prozent && ($y == 15 || $y == 16 || $y == 17 || $y == 21 || $y == 22 || $y == 23 || $y == 27 || $y == 28 || $y == 29 || $y == 33 || $y == 34 || $y == 35))
             $html .= '<td align="center" class="grau">' . $anzeige . '</td>';
            elseif(!$prozent)
             $html .= '<td align="center" style="background-color:white;">' . $anzeige . '</td>';
             
             unset($prozent);             
             
             if($y == 38)
                {
                 unset($flag2);
                 unset($flag3);  
                } 
           }
           $html .= '</tr><tr>';
         }  
       }
    return $html;
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

if(!$_SESSION["turnier_auswahl"] || $_POST["turnier_auswahl"] != $_SESSION["turnier_auswahl"] )
    $_SESSION["turnier_auswahl"] = $_POST["turnier_auswahl"];  
if(!$_SESSION["sb_nr"] || $_POST["sb_nr"] != $_SESSION["sb_nr"] )
    $_SESSION["sb_nr"] = $_POST["sb_nr"];
if(!$_SESSION["boogie_sb_herr"] || $_POST["sb_nr"] != $_SESSION["boogie_sb_herr"] )
    $_SESSION["boogie_sb_herr"] = $_POST["sb_nr"];
if(!$_SESSION["boogie_sb_dame"] || $_POST["sb_nr"] != $_SESSION["boogie_sb_dame"] )
    $_SESSION["boogie_sb_dame"] = $_POST["sb_nr"];
              
?>
<!DOCTYPE HTML>
<html lang="de">
<head>
<link rel="icon" href="favicon.ico" type="image/ico">
<meta charset="UTF-8">
<title>Turnierergebnisse</title>
<meta name="viewport" content="width = 1280, minimum-scale = 0.25, maximum-scale = 1.60">
<link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
<style type="text/css">
  body  {
         font-family: 'Yanone Kaffeesatz',Helvetica,Arial,Sans-Serif ;
         font-size: 17px;
        }
        
  .vertical-text {
    width: 1.2em;
    position: absolute;
    top: 278px;
    padding: 0px 0px 0px 10px;
    white-space: nowrap;
  transform: rotate(-90deg);
    -webkit-transform: rotate(-90deg);
    -moz-transform: rotate(-90deg);
    -ms-transform: rotate(-90deg);
    -o-transform: rotate(-90deg);
  writing-mode: sideways-rl;
    filter: flipv fliph;
}

  .vertical-text_bw {
    width: 1.2em;
    position: absolute;
    top: 380px;
    padding: 0px 0px 0px 10px;
    white-space: nowrap;
  transform: rotate(-90deg);
  writing-mode: sideways-rl;
  filter: flipv fliph;
}

  td.grau {
    background-color: #cccccc;
    }   

  .browser {
    font-size: 24px;
    color: #ff0000;
    }
     
</style>
</head>
<body>

<?php
 
// Turnier auswählen
echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';  
echo'<center><table>';
echo'<tr><td><b>Bitte Turnier auswählen: ';
echo'<select name="turnier_auswahl" size="1"  onchange="submit();">';

// Turnier aus Datenbank lesen

$sqlab = "SELECT turniernummer,turniername,datum FROM Turnier ORDER BY datum DESC";
$turniere = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turniere))
  {
   $t_dat = substr($temp[2],8,2) . '.' . substr($temp[2],5,2) . '.' . substr($temp[2],0,4);
   $t_name = utf8_encode($temp[1]);  
   if($_SESSION["turnier_auswahl"] == $temp[0])
      echo'<option value="' . $temp[0] . '"  selected>' . $t_name . ' ' . $t_dat . '</option>';
   else
       echo'<option value="' . $temp[0] . '">' . $t_name . ' ' . $t_dat . '</option>';
  }
   
echo'</select>';
   
   echo'</b></td><td><b> Bitte Startbuchnummer eingeben: </b> ';
   echo'<input type=text name="sb_nr" value="' . $_SESSION[sb_nr] . '"  size=5 maxlength=5></td></tr>';
   echo'<td colspan = 2><center><input type=submit name="absenden" value="Absenden"></td></tr>';

echo'</table>';  
  
echo'</form>';  

// Wertungsrichter einlesen

$sqlab = 'SELECT * FROM wertungsrichter WHERE turniernummer = ' . $_SESSION["turnier_auswahl"];
$temp = mysqli_query($db, $sqlab);

while($wr = mysqli_fetch_array($temp))
      {
//      print_r($wr);echo'<br>';
       $i = $wr[1];
       $wert_rich[$i] = array($wr[1],$wr[4]);     
      }
//  print_r($wert_rich[3][1]);echo'<br>'; 
       
// Tanzrunden einlesen

$sqlab = 'SELECT runden_rf, rt_id_tlp, turniernummer, startklasse, runde FROM rundentab WHERE turniernummer = ' . $_SESSION["turnier_auswahl"] . ' ORDER BY runden_rf';
$temp = mysqli_query($db, $sqlab);
unset($i);

while($t_runden = mysqli_fetch_array($temp))
      {
       $i = $i + 1;
       $t_runde[$i] = $t_runden;  
//         echo $t_runde[$i][0] . ' -0-  ' . $t_runde[$i][1] . ' -1-  ' . $t_runde[$i][3] . ' -3- ' . $t_runde[$i][4] ;echo" -4-<br>";
//print_r($t_runde[$i]);echo'<br>';   
      }
            
// Tanzpaar in Datenbank suchen
  
$sqlab = 'SELECT paar_id_tlp, startklasse, dame, herr, team, platz, punkte, rl_punkte FROM paare WHERE Turniernummer = ' . $_SESSION["turnier_auswahl"] . ' AND  startbuch = ' . $_SESSION["sb_nr"] . ' OR Turniernummer = ' . $_SESSION["turnier_auswahl"] . ' AND  boogie_sb_herr = ' .$_SESSION["boogie_sb_herr"] . ' OR Turniernummer = ' . $_SESSION["turnier_auswahl"] . ' AND  boogie_sb_dame = ' . $_SESSION["boogie_sb_dame"] . ' ORDER BY paar_id_tlp'; 
// echo"$sqlab<br>"; 
$paar_id =  mysqli_query($db, $sqlab);

while($paar_id_paare = mysqli_fetch_array($paar_id))
{
// print_r($paar_id_paare);echo' = Paar ID<br>';

$_SESSION["paar_id"] = $paar_id_paare[0];
$_SESSION["dame"] = utf8_encode($paar_id_paare[2]);
$_SESSION["herr"] = utf8_encode($paar_id_paare[3]);
$_SESSION["team"] = utf8_encode($paar_id_paare[4]);
$_SESSION["startklasse"] = $paar_id_paare[1];
$_SESSION["platz"] = $paar_id_paare[5];
$_SESSION["punkte"] = $paar_id_paare[6];
$_SESSION["rl_punkte"] = $paar_id_paare[7];
// echo 'Paar ID: ' . $_SESSION["paar_id"] . $_SESSION["dame"] . ' - ' . $_SESSION["herr"] . ' <br>';

if(!$_SESSION["team"])
   echo '<h2>' . $_SESSION["dame"] . ' - ' . $_SESSION["herr"] . ' - ' . $_SESSION["startklasse"] . ' - Platz: ' . $_SESSION["platz"] . ' - Aufstiegspunkte: ' . $_SESSION["punkte"] . ' - Ranglistenpunkte: ' . $_SESSION["rl_punkte"] . '</h2>';
else
   echo '<h2>' . $_SESSION["team"] . ' - ' . $_SESSION["startklasse"] . ' - Platz: ' . $_SESSION["platz"] . ' - Aufstiegspunkte: ' . $_SESSION["punkte"] . ' - Ranglistenpunkte: ' . $_SESSION["rl_punkte"] . '</h2>';
echo'<br>';

// Hinweis Firefox

$browser = get_browser();
if($browser->browser == "Firefox")
   echo'<p class = "browser">Die senkrechte Spaltendarstellung funktioniert leider nicht beim Firefox. Bitte einen anderen Browser verwenden!</p>';
   
// Tabelle mit Ergebnissen

// Tabellenkopf RR

if($_SESSION["startklasse"] == "RR_S" || $_SESSION["startklasse"] == "RR_J" || $_SESSION["startklasse"] == "RR_C" || $_SESSION["startklasse"] == "RR_B" || $_SESSION["startklasse"] == "RR_A")
{
echo'<table width= 1500 border=1 cellspacing=2 cellpadding=3>';
echo'<tr><td width="120" height=150 valign="bottom">Wertungsrichter</td><td width="35" height=150><p class="vertical-text">Herr Grundtechnik</p></td><td width="35"><p class="vertical-text">Haltungs- und Drehtechnik</p></td>';
echo'<td width="35" align=left><p class="vertical-text">Dame Grundtechnik</p></td><td width="35"><p class="vertical-text">Haltungs- und Drehtechnik</p></td>';
echo'<td width="35"><p class="vertical-text">Choreographie</p></td><td width="35"><p class="vertical-text">Tanzfiguren</p></td>';
echo'<td width="35"><p class="vertical-text">tänzerische Darbietung</p></td>';
echo'<td width="35"><p class="vertical-text">Grobfehler Text</p></td><td width="35"><p class="vertical-text">Grobfehler Summe</p></td>';
echo'<td width="35" class="grau"><p class="vertical-text">Akrobatik 1</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 1 Text</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 1 Summe</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 2</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 2</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 2</p></td>';
echo'<td width="35" class="grau"><p class="vertical-text">Akrobatik 3</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 3</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 3</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 4</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 4</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 4</p></td>';
echo'<td width="35" class="grau"><p class="vertical-text">Akrobatik 5</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 5</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 5</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 6</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 6</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 6</p></td>';
echo'<td width="35" class="grau"><p class="vertical-text">Akrobatik 7</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 7</p></td><td width="35" class="grau"><p class="vertical-text">Grobfehler Akrobatik 7</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 8</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 8</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 8</p></td></tr>';
}

// Tabellenkopf BW

if(($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA") && $ausgabe != 1)
{
echo'<table width="450" border=1 cellspacing="2" cellpadding="3">';
echo'<tr><td width="120" height=150 valign="bottom">Wertungsrichter</td><td width="35" height="250"><p class="vertical-text_bw">Grundschritt (Rhythmus & Fußtechnik)</p></td><td width="35"><p class="vertical-text_bw">Basic Dancing, Lead & Follow, Harmonie</p></td>';
echo'<td width="35"><p class="vertical-text_bw">Tanzfiguren (Komplexe, Highlight)</p></td>';
echo'<td width="35"><p class="vertical-text_bw">Interpretation (Komplexe und Highlight Figuren)</p></td><td width="35"><p class="vertical-text_bw">Spontane Interpretation</p></td>';
echo'<td width="35"><p class="vertical-text_bw">Dance Performance</p></td><td width="35"><p class="vertical-text_bw">Gesamt-Punkte (max. 80)</p></td>';
echo'</tr>';
}
if(($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA") && $ausgabe == 1)
{
echo'<table width="450" border=1 cellspacing="2" cellpadding="3">';
echo'<tr><td width="120"valign="bottom">Wertungsrichter</td><td width="35"><p class="vertical-text_bw">&nbsp</p></td><td width="35"><p class="vertical-text_bw">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text_bw">&nbsp</p></td><td width="35"><p class="vertical-text_bw">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text_bw">&nbsp</p></td><td width="35"><p class="vertical-text_bw">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text_bw">&nbsp</p></td>';
echo'</tr>';
}
if(($_SESSION["startklasse"] == "BW_MA" || $_SESSION["startklasse"] == "BW_MB" || $_SESSION["startklasse"] == "BW_SA" || $_SESSION["startklasse"] == "BW_SB" || $_SESSION["startklasse"] == "BW_JA") && $ausgabe != 1)
{
$ausgabe = 1;
}

// Tabellenkopf Formationen

if($_SESSION["startklasse"] == "F_RR_GF" || $_SESSION["startklasse"] == "F_RR_LF" || $_SESSION["startklasse"] == "F_RR_ST" || $_SESSION["startklasse"] == "F_RR_J" || $_SESSION["startklasse"] == "F_RR_M" || $_SESSION["startklasse"] == "F_BW_M")
{
echo'<table width="1500" border=1 cellspacing="2" cellpadding="3">';
echo'<tr><td width="120" height=150 valign="bottom">Wertungsrichter</td><td width="35" height="150"><p class="vertical-text">Technik - GT, HAT, DT</p></td><td width="35"><p class="vertical-text">Tanz - Int., TD, Choreo</p></td>';
echo'<td width="35"><p class="vertical-text">Tanz - Tanzfiguren</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">AF - Bilder </p></td><td width="35"><p class="vertical-text">AF - Bildwechsel</p></td>';
echo'<td width="35"><p class="vertical-text"> je Akrobatik 0-100 %</p></td><td width="35"><p class="vertical-text">Abzüge</p></td>';
echo'<td width="35"><p class="vertical-text">AF - Formationsfiguren</p></td>';
echo'<td class="grau" width="35"><p class="vertical-text">Akrobatik 1</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 1 Text</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 1 Summe</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 2</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 2</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 2</p></td>';
echo'<td class="grau" width="35"><p class="vertical-text">Akrobatik 3</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 3</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 3</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 4</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 4</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 4</p></td>';
echo'<td class="grau" width="35"><p class="vertical-text">Akrobatik 5</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 5</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 5</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 6</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 6</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 6</p></td>';
echo'<td class="grau" width="35"><p class="vertical-text">Akrobatik 7</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 7</p></td><td class="grau" width="35"><p class="vertical-text">Grobfehler Akrobatik 7</p></td>';
echo'<td width="35"><p class="vertical-text">Akrobatik 8</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 8</p></td><td width="35"><p class="vertical-text">Grobfehler Akrobatik 8</p></td>';
echo'<tr>';
}

// Tabellenkopf BW Formationen

if($_SESSION["startklasse"] == "F_BW")
{
echo'<table width="1500" border=1 cellspacing=2 cellpadding=3>';
echo'<tr><td width="120" height=150 valign="bottom">Wertungsrichter</td><td width="35" height=150><p class="vertical-text">Tanztechnik</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35" align=left><p class="vertical-text">Ausführung</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">Choreographie</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">Tanzfiguren</p></td>';
echo'<td width="35"><p class="vertical-text">Grobfehler Text</p></td><td width="35"><p class="vertical-text">Grobfehler Summe</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><td width="35"><p class="vertical-text">&nbsp</p></td>';
echo'<td width="35"><p class="vertical-text">&nbsp</p></td><tr>';
}
 
// gemeldete Akrobatiken suchen
if($_SESSION["startklasse"] == "RR_S" || $_SESSION["startklasse"] == "RR_J" || $_SESSION["startklasse"] == "RR_C" || $_SESSION["startklasse"] == "RR_B" || $_SESSION["startklasse"] == "RR_A" || $_SESSION["startklasse"] == "F_RR_GF" || $_SESSION["startklasse"] == "F_RR_LF" || $_SESSION["startklasse"] == "F_RR_ST" || $_SESSION["startklasse"] == "F_RR_J" || $_SESSION["startklasse"] == "F_RR_M" || $_SESSION["startklasse"] == "F_BW_M")
{
$sqlab = 'SELECT * FROM paare WHERE turniernummer = ' . $_SESSION["turnier_auswahl"] . ' AND  paar_id_tlp = ' . $_SESSION["paar_id"];
$gemeldet = mysqli_query($db, $sqlab);

$gemeldete_akros = mysqli_fetch_array($gemeldet);

// print_r($gemeldete_akros);echo'<br>';

echo'<tr><td align="right" colspan="10">Akrobatiken Vorrunde:</td><td class="grau" align="center">' . $gemeldete_akros[16] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[15]) . '</td><td align="center">' . $gemeldete_akros[18] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[17]) . '</td><td class="grau" align="center">' . $gemeldete_akros[20] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[19]) . '</td><td align="center">' . $gemeldete_akros[22] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[21]) . '</td><td class="grau" align="center">' . $gemeldete_akros[24] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[23]) . '</td><td align="center">' . $gemeldete_akros[26] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[25]) . '</td><td class="grau" align="center">' . $gemeldete_akros[28] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[27]) . '</td><td align="center">' . $gemeldete_akros[30] . '</td><td "align="center" colspan="2">' . utf8_encode($gemeldete_akros[29]) . '</td></tr>';

echo'<tr><td align="right" colspan="10">Akrobatiken Zwischenrunde:</td><td class="grau" align="center">' . $gemeldete_akros[32] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[31]) . '</td><td align="center">' . $gemeldete_akros[34] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[33]) . '</td><td class="grau" align="center">' . $gemeldete_akros[36] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[35]) . '</td><td align="center">' . $gemeldete_akros[38] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[37]) . '</td><td class="grau" align="center">' . $gemeldete_akros[40] . '</td><td class="grau" align="cent class="grau"er" colspan="2">' . utf8_encode($gemeldete_akros[39]) . '</td><td align="center">' . $gemeldete_akros[42] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[41]) . '</td><td class="grau" align="center">' . $gemeldete_akros[44] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[43]) . '</td><td align="center">' . $gemeldete_akros[46] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[45]) . '</td></tr>';


echo'<tr><td align="right" colspan="10">Akrobatiken Endrunde:</td><td class="grau" align="center">' . $gemeldete_akros[48] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[47]) . '</td><td align="center">' . $gemeldete_akros[50] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[49]) . '</td><td class="grau" align="center">' . $gemeldete_akros[52] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[51]) . '</td><td align="center">' . $gemeldete_akros[54] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[53]) . '</td><td class="grau" align="center">' . $gemeldete_akros[56] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[55]) . '</td><td align="center">' . $gemeldete_akros[58] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[57]) . '</td><td class="grau" align="center">' . $gemeldete_akros[60] . '</td><td class="grau" align="center" colspan="2">' . utf8_encode($gemeldete_akros[59]) . '</td><td align="center">' . $gemeldete_akros[62] . '</td><td align="center" colspan="2">' . utf8_encode($gemeldete_akros[61]) . '</td></tr>';
}
// Wertungen für Tanzpaar in Datenbank suchen 

  $sqlab = 'SELECT * FROM wertungen WHERE turniernummer = ' . $_SESSION["turnier_auswahl"] . ' AND  paar_id_tlp = ' . $_SESSION["paar_id"]; // . ' ORDER BY rund_tab_id'; 
  $wertung = mysqli_query($db, $sqlab);

  while($wertungen = mysqli_fetch_array($wertung))
  {
    if(!$akt_runde)
     { 
      $akt_runde = $wertungen[5];
      $runde[$wertungen[5]] = $wertungen[5];
     }        
    if($akt_runde != $wertungen[5])
     {    
      $akt_runde = $wertungen[5];
      $runde[$wertungen[5]] = $wertungen[5];
     }       
    $rd_wertung = $akt_runde . '_' . $wertungen[4];       
    //print_r($rd_wertung);echo"<br>";
  
    $rd_erg[$rd_wertung] = $wertungen;         
    // print_r($wertungen);echo"<br>";
    // echo'Runde: ' . $wertungen[5] . ' | Wertungsrichter: ' . $wertungen[4] . ' : ';
  }  
  // print_r($runde);echo'-'. $runde[4];echo"<br>";
  // echo 'current = ' . current($runde) . '<br>';

  // Für alle vorhandenen Runden die table rows erzeugen 

// echo'$runde : '; print_r($runde);echo' Count: ' . count($t_runde); echo'<br>';
// print_r($t_runde);echo'<br>'; 
    for ($runden_cnt = 1; $runden_cnt <= count($t_runde); $runden_cnt++)
    {
     if ($runden_cnt > 1){
      next($runde);
    }

// Rundenbezeichnung
    for($tr = 1; $tr < 20; $tr++){
      if($t_runde[$tr][1] == current($runde))
      break;   
    }

   if($t_runde[$tr][4] != "")
    {
//     print_r($t_runde);echo'<br>';
//     echo $t_runde[$tr][0] . ' -0-  ' . $t_runde[$tr][1] . ' -1-  ' . $t_runde[$tr][3] . ' -3- ' . $t_runde[$tr][4] ;echo" -4-<br>"; 
     $runde_name = rundenbezeichnung($t_runde[$tr][4]);
     echo getRundenErgebnis($runde_name);
    }
  }
  echo'</table>'; 
}
  
if($_SESSION["startklasse"] != "BW_MA" && !$_SESSION["startklasse"] != "BW_MB" && !$_SESSION["startklasse"] != "BW_SA" && !$_SESSION["startklasse"] != "BW_SB" && !$_SESSION["startklasse"] != "BW_JA")
 {
//   echo 'Die Technik-Werte sind in % angegeben. Die Werte aller Akrobatiken sind die Wertigkeit minus der Abzüge. Die Zahlen in Klammer inf %<p /> ';
 }
// print_r($wr);

?>
</body>
</html>