<?php 
/******************************************************************************
 * Wertungsergebnisse
 *
 * Copyright    : (c) 2017 The DRBV Webteam
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 *****************************************************************************/
require_once('../../system/common.php');
require_once('../../system/drbv_funktionen.php');

$tnrsel = '';

$_POST["mitglied_nummer"] = htmlentities($_POST["mitglied_nummer"], ENT_QUOTES);
$_POST["passwort"]        = $_POST["passwort"];
$tnrsel                   = $_POST["trnsel"];
//print_r($_POST);echo" :POST<br>";
//print_r($_POST["mitglied_nummer"]);echo" :POST<br>";
//print_r($_POST["passwort"]);echo" :POST<br>";

$pkt_datum = "";
  
// Funktion Rundenergebnis als Tabellenzeile
// -----------------------------------------
function getRundenErgebnis($runde_name){  
  global $runde, $rd_erg, $wert_richter, $twrnrfix, $paarinfo, $gemeldete_akros, $datum, $datumDM, $datumDMF, $datum12P, $startklasse_mit_akro, $isStartbuchFormation, $isStartbuchBW, $wrtg_bw;
  $twrnr       = 0;
  $awrnr       = 0;
  $wertungs_ar = array();
  $akropkte_ar = array();
  $akroproz_ar = array();
  $tanzpkte_ar = array();
  $tanzproz_ar = array();
  $t_abzg_text = array();
  $t_abzg_pkte = array();
  $startklasse_mit_akro = TRUE;
  
  if($paarinfo["startklasse"] == "RR_S" || $paarinfo["startklasse"] == "RR_J" || $paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A"){
     $wrtg_rr = TRUE;
  }  
  if($paarinfo["startklasse"] == "BW_MA" || $paarinfo["startklasse"] == "BW_MB" || $paarinfo["startklasse"] == "BW_SA" || $paarinfo["startklasse"] == "BW_SB" || $paarinfo["startklasse"] == "BW_JA"){  
     $wrtg_bw        = TRUE; 
     $isStartbuchBW  = TRUE;
  }
  if($paarinfo["startklasse"] == "F_RR_GF" || $paarinfo["startklasse"] == "F_RR_LF" ||
     $paarinfo["startklasse"] == "F_RR_ST" || $paarinfo["startklasse"] == "F_RR_J" || 
     $paarinfo["startklasse"] == "F_RR_M"  || $paarinfo["startklasse"] == "F_BW_M"){
     $wrtg_fo = TRUE;
     $isStartbuchFormation = TRUE;
  }    
  if($paarinfo["startklasse"] == "RR_S" || $paarinfo["startklasse"] == "F_RR_GF" || $paarinfo["startklasse"] == "F_RR_LF" ||
     $paarinfo["startklasse"] == "F_RR_ST" || $paarinfo["startklasse"] == "F_RR_J" || $paarinfo["startklasse"] == "F_BW_M" ||
     $paarinfo["startklasse"] == "BW_MA" || $paarinfo["startklasse"] == "BW_MB" || $paarinfo["startklasse"] == "BW_SA" || 
     $paarinfo["startklasse"] == "BW_SB" || $paarinfo["startklasse"] == "BW_JA"){
     $startklasse_mit_akro = FALSE;
  }  
  
  $twrnr = 0;
  $awrnr = 0;
  $tanzwrtg = array();
  $akrowrtg = array();  
  for($x = 1;$x<50;$x++){
    $z = current($runde).'_'.$x;
    //print_r($z);echo" <-- z<br>";
    if($rd_erg[$z][0]){
      unset($wertungs_ar);
      for($y = 4;$y<39;$y++){
        if($y == 4){
          $name_wr = intval($rd_erg[$z][$y]);          
        }        
        // Tanzwertungen Spalte 4 bis 14
        // -----------------------------
        $wertungs_ar[] = $rd_erg[$z][$y];
      }
      //wenn werte in tanzwr col dann
      if(array_sum(array_slice($wertungs_ar, 2, 8)) != 0){
        $tanzwrtg[] = $wertungs_ar;
        $twrname[] = utf8_encode($wert_richter[$name_wr][1]);
        $twrnr++;      
      }
      //wenn werte in akrowr col dann ! Theoretisch 0 moeglich aber unwahrscheinlich
      if(array_sum(array_slice($wertungs_ar,11,34)) != 0){        
        $akrowrtg[] = $wertungs_ar;
        $awrname[] = utf8_encode($wert_richter[$name_wr][1]);
        $awrnr++;      
      }      
    }  
  }
  //print_r($wertungs_ar);echo " <-- wertungs_ar<br>";
  //print_r($name_wr);echo " <-- name_wr<br>";
  //print_r($twrname);echo " <-- twrname<br>";
  //print_r($awrname);echo " <-- awrname<br>";
  
  // twrnr fix S-Klasse mit 8 (lt. TSO nicht > 4)
  if($paarinfo["startklasse"] == "RR_S" && $twrnr == 8){
    $twrnr    = 4;
    $twrnrfix = TRUE;
  } 

  //12 Punktesystem Faktoranpassung
  if($datum <= $datum12P){
    //vor 12Pkt Einführung  
    $pktfaktor_a = array("TTH" => 5, "HDH" => 5, "TTD" => 5, "HDD" => 5, "TFI" => 6, "TDA" => 6, "CHO" => 8);
  } else {
    //nach 12Pkt Einführung
    if($paarinfo["startklasse"] == "RR_S" || $paarinfo["startklasse"] == "RR_J"){
      $pktfaktor_a = array("TTH" => 4.5, "HDH" => 4.5, "TTD" => 4.5, "HDD" => 4.5, "TFI" => 5.4, "TDA" => 5.4, "CHO" => 7.2);
    } elseif($paarinfo["startklasse"] == "RR_C"){
      $pktfaktor_a = array("TTH" => 6, "HDH" => 6, "TTD" => 6, "HDD" => 6, "TFI" => 7.2, "TDA" => 7.2, "CHO" => 9.6);    
    } else {
      //A&B Klasse: Unterscheidung nach Rundentyp
      if($runde_name == "Endrunde Fußtechnik"|| $runde_name == "Endrunde Akrobatik"){
        //Finale Fusstechnik und Akrobatik
        $pktfaktor_a = array("TTH" => 4.375, "HDH" => 4.375, "TTD" => 4.375, "HDD" => 4.375, "TFI" => 5.25, "TDA" => 5.25, "CHO" => 7);          
      } elseif($runde_name == "Semifinale") {
        //Semifinalrunden
        $pktfaktor_a = array("TTH" => 7.25, "HDH" => 7.25, "TTD" => 7.25, "HDD" => 7.25, "TFI" => 8.7, "TDA" => 8.7, "CHO" => 11.6);          
      } else {
        //Vorrunden/Zischenrunden/Hoffnungsrunden
        $pktfaktor_a = array("TTH" => 6.25, "HDH" => 6.25, "TTD" => 6.25, "HDD" => 6.25, "TFI" => 7.5, "TDA" => 7.5, "CHO" => 10);          
      }    
    }
  }
  
  // Tanzwertungen slice 2..8
  // ------------------------ 
  for($i = 0;$i < $twrnr;$i++){
    for($j=2;$j<=8;$j++){    
      //reduzierte Vorrundenwertung mit Einfuehrung 12Pkt. TLP Version    
      if($runde_name != "Endrunde Akrobatik" && $runde_name != "Endrunde" && $runde_name != "Endrunde Fußtechnik" && $datum > $datum12P){
        if($j=2)  $tanzproz_ar[] = $tanzwrtg[$i][$j]*10;
        if($j=3)  $tanzproz_ar[] = $tanzwrtg[$i][$j-1]*10;
        if($j=4)  $tanzproz_ar[] = $tanzwrtg[$i][$j]*10;       
        if($j=5)  $tanzproz_ar[] = $tanzwrtg[$i][$j-1]*10;
        if($j=6)  $tanzproz_ar[] = $tanzwrtg[$i][$j]*10;
        if($j=7)  $tanzproz_ar[] = $tanzwrtg[$i][$j-1]*10;
        if($j=8)  $tanzproz_ar[] = $tanzwrtg[$i][$j-2]*10;
      } else {
        $tanzproz_ar[] = $tanzwrtg[$i][$j]*10;
      }
    }
  } 
  for($i = 0;$i < $twrnr;$i++){
    for($j=2;$j<=10;$j++){    
      if($wrtg_rr){
        //reduzierte Vorrundenwertung mit Einfuehrung 12Pkt. TLP Version
        if($runde_name != "Endrunde Akrobatik" && $runde_name != "Endrunde" && $runde_name != "Endrunde Fußtechnik" && $datum > $datum12P){
          if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TTH"];
          if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j-1]/10*$pktfaktor_a["HDH"];
          if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TTD"];        
          if($j=5)  $tanzpkte_ar[] = $tanzwrtg[$i][$j-1]/10*$pktfaktor_a["HDD"];//nur bei Einzelpaaren sonst 0
          if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TFI"];
          if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j-1]/10*$pktfaktor_a["TDA"];
          if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j-2]/10*$pktfaktor_a["CHO"];
        } else {
          if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TTH"];
          if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["HDH"];
          if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TTD"];        
          if($j=5)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["HDD"];//nur bei Einzelpaaren sonst 0
          if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TFI"];
          if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["TDA"];
          if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*$pktfaktor_a["CHO"];          
        }
        if($j=9)  $t_abzg_text[] = $tanzwrtg[$i][$j];
        if($j=10) $t_abzg_pkte[] = $tanzwrtg[$i][$j];        
      } elseif($wrtg_bw){
        if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*15;
        if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*15;
        if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*20;
        if($j=5)  $tanzpkte_ar[] = '';
        if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;
        if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;      
        if($j=9)  $t_abzg_text[] = $tanzwrtg[$i][$j];
        if($j=10) $t_abzg_pkte[] = $tanzwrtg[$i][$j];        
      } elseif($paarinfo["startklasse"] == "F_RR_ST"){
        if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*15;
        if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*35;
        if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*5;
        if($j=5)  $tanzpkte_ar[] = '';
        if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*7.5;
        if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*7.5;
        if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;      
        if($j=9)  $t_abzg_text[] = $tanzwrtg[$i][$j];
        if($j=10) $t_abzg_pkte[] = $tanzwrtg[$i][$j];        
      } elseif($paarinfo["startklasse"] == "F_RR_GF" || $paarinfo["startklasse"] == "F_RR_LF"){
        if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;
        if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;
        if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*5;
        if($j=5)  $tanzpkte_ar[] = '';
        if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*15;      
        if($j=9)  $t_abzg_text[] = $tanzwrtg[$i][$j];
        if($j=10) $t_abzg_pkte[] = $tanzwrtg[$i][$j];        
      } elseif($paarinfo["startklasse"] == "F_RR_J" || $paarinfo["startklasse"] == "F_RR_M"){
        if($j=2)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;
        if($j=3)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*30;
        if($j=4)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=5)  $tanzpkte_ar[] = '';
        if($j=6)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=7)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;
        if($j=8)  $tanzpkte_ar[] = $tanzwrtg[$i][$j]/10*10;      
        if($j=9)  $t_abzg_text[] = $tanzwrtg[$i][$j];
        if($j=10) $t_abzg_pkte[] = $tanzwrtg[$i][$j];        
      }     
      break;
    }
  } 
  // Akrowertungen Slice 11..34
  // ------------------------------
  if($runde_name == "Vorrunde" || $runde_name == "Hoffnungsrunde"){
    $ga_start = 16;//gemeldete_akros_startwert
  }
  if($runde_name == "1. Zwischenrunde" || $runde_name == "2. Zwischenrunde" || $runde_name == "3. Zwischenrunde"){
    $ga_start = 32;//gemeldete_akros_startwert
  }
  if($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde" || $runde_name == "KO-Runde" || $runde_name == "Semifinale"){
    $ga_start = 48;//gemeldete_akros_startwert          
  }
  //print_r($gemeldete_akros);echo' '.$ga_start.':gemeldete_akros<br>';  
  for($i = 0;$i < $awrnr;$i++){
    for($j = 11;$j <= 34;$j++){
      if($j=11) $akropkte_ar[] = $akrowrtg[$i][$j];
      if($j=11) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start]);
      if($j=12) $a_abzg_text[] = $akrowrtg[$i][$j];
      if($j=13) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      //
      if($j=14) $akropkte_ar[] = $akrowrtg[$i][$j];
      if($j=14) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+2]);
      if($j=15) $a_abzg_text[] = $akrowrtg[$i][$j];
      if($j=16) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      //
      if($j=17) $akropkte_ar[] = $akrowrtg[$i][$j];
      if($j=17) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+4]);
      if($j=18) $a_abzg_text[] = $akrowrtg[$i][$j];
      if($j=19) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      if($paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
        if($j=20) $akropkte_ar[] = $akrowrtg[$i][$j];
        if($j=20) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+6]);
        if($j=21) $a_abzg_text[] = $akrowrtg[$i][$j];
        if($j=22) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      }
      if($paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
        if($j=23) $akropkte_ar[] = $akrowrtg[$i][$j];
        if($j=23) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+8]);
        if($j=24) $a_abzg_text[] = $akrowrtg[$i][$j];
        if($j=25) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      }      
      if($datum <= $datumDM){
        //if vor DM
        if($paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
          if($j=26) $akropkte_ar[] = $akrowrtg[$i][$j];
          if($j=26) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+10]);
          if($j=27) $a_abzg_text[] = $akrowrtg[$i][$j];
          if($j=28) $a_abzg_pkte[] = $akrowrtg[$i][$j];
        }                    
      } else {
        //if nach DM
        if($runde_name == "Endrunde Akrobatik" || $paarinfo["startklasse"] == "F_RR_M"){
          if($j=26) $akropkte_ar[] = $akrowrtg[$i][$j];
          if($j=26) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+10]);
          if($j=27) $a_abzg_text[] = $akrowrtg[$i][$j];
          if($j=28) $a_abzg_pkte[] = $akrowrtg[$i][$j];
        }                  
      }                  
      if($paarinfo["startklasse"] == "F_RR_M"){
        if($j=29) $akropkte_ar[] = $akrowrtg[$i][$j];
        if($j=29) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+12]);
        if($j=30) $a_abzg_text[] = $akrowrtg[$i][$j];
        if($j=31) $a_abzg_pkte[] = $akrowrtg[$i][$j];
        //
        if($j=32) $akropkte_ar[] = $akrowrtg[$i][$j];
        if($j=32) $akroproz_ar[] = round($akrowrtg[$i][$j] * 100 / $gemeldete_akros[$ga_start+14]);
        if($j=33) $a_abzg_text[] = $akrowrtg[$i][$j];
        if($j=34) $a_abzg_pkte[] = $akrowrtg[$i][$j];
      }  
      break;      
    }
  }   
  //print_r($wertungs_ar);echo' wertungsarray<br>';
  //print_r($tanzwrtg);echo' tanz wr<br>';
  //print_r($akrowrtg);echo' akro wr<br>';
  //print_r($akropkte_ar);echo" akropkte_ar:akropkte<br>"; 
  //print_r($akroproz_ar);echo" akroproz_ar:akro%<br>";           
  //print_r($tanzpkte_ar);echo" tanzpkte<br>";           
  //print_r($tanzproz_ar);echo" tanz%<br>";           
  //print_r($t_abzg_text);echo" t_abzg_text%<br>";           
  //print_r($t_abzg_pkte);echo" t_abzg_pkte%<br>";           
  //print_r($a_abzg_text);echo" a_abzg_text%<br>";           
  //print_r($a_abzg_pkte);echo" a_abzg_pkte%<br>";           
  return array($tanzpkte_ar, $tanzproz_ar,$akropkte_ar, $akroproz_ar, $t_abzg_text, $t_abzg_pkte, $a_abzg_text, $a_abzg_pkte, $twrnr, $awrnr, $awrname, $twrname);
} //End function getRundenErgebnis 
  
// Ergebnisse:
// ----------- 
$sqlab        = 'SELECT turniernummer, platz, rl_punkte, punkte, paar_id_tlp, startklasse, RT_ID_Ausgeschieden FROM paare 
                 WHERE startbuch = '.$_POST["mitglied_nummer"].' OR 
                 boogie_sb_herr  = '.$_POST["mitglied_nummer"].' OR 
                 boogie_sb_dame  = '.$_POST["mitglied_nummer"].' ORDER BY turniernummer DESC'; 
$platz        =  mysqli_query(DRBVdb(), $sqlab);
$html_rgbnss  = '';
$html_wrtg    = '';
  
$html_rgbnss .= '
  <p><a name="List of results"></a><hr /><br><b>List of results & given points for: '.$_POST["mitglied_nummer"].'</b></p> 
  <table border="0" width="720px" cellspacing=2 cellpadding=3 style="font-size: 11px;">
    <tr bgcolor=33cc00>
      <td align=center><b>Date</b></td>
      <td><b>Competition</b></td>
      <td align=center><b>RglPts.</b></td>
      <td align=center><b>CmpPts.</b></td>
      <td align=center><b>&sum; FT</b></td>
      <td align=center><b>&sum; dance</b></td>
      <td align=center><b>&sum; acrob</b></td>
      <td align=center><b>Result</b></td>
      <td align=center><b>Placement</b></td>  
      <td align=center><b>Judgement</b></td>  
    <tr>';
  
while($platzierungen = mysqli_fetch_array($platz)){
  $sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $platzierungen["turniernummer"]; 
  $turnier      = mysqli_query(DRBVdb(), $sqlab);
  
  $sqlab        = 'SELECT * FROM majoritaet WHERE turniernummer = ' . $platzierungen["turniernummer"] . ' AND TP_ID = ' . $platzierungen["paar_id_tlp"]  . ' AND RT_ID = ' . $platzierungen["RT_ID_Ausgeschieden"];
  $ergebnis_pkt = mysqli_query(DRBVdb(), $sqlab);
  //print_r($ergebnis_pkt);echo' ergebnis_pkt<br>';
  
  unset($runden_pkt_html);
  while($runden_pkt = mysqli_fetch_array($ergebnis_pkt)){
    //print_r($runden_pkt);echo' runden_pkt<br>';
    $erg_pkt_ft = $runden_pkt[WR1];
    $erg_pkt_ta = $runden_pkt[WR2];    
    $erg_pkt_ak = $runden_pkt[WR3];    
    $erg_pkt_su = $runden_pkt[WR7];    
  }
  //print_r($erg_pkt_su);echo' erg_pkt_su<br>';
  
  //print_r($turnier);echo' turnier<br>';
  //print_r($ergebnis_pkt);echo' ergebnis_pkt<br>';
  $turnierdaten = mysqli_fetch_array($turnier);
  //print_r($turnierdaten);echo'<br>';  
  $datum        = new DateTime($turnierdaten["datum"]);
  $t_nummer     = $platzierungen["turniernummer"];
  
  if($platzierungen["startklasse"] == "F_RR_M"  || $platzierungen["startklasse"] == "F_RR_J"  || 
     $platzierungen["startklasse"] == "F_RR_LF" || $platzierungen["startklasse"] == "F_RR_GF" ||                                                            
     $platzierungen["startklasse"] == "F_RR_ST" || $platzierungen["startklasse"] == "F_BW_M"){
     $isFormation = TRUE;
  }
  if($platzierungen["startklasse"] == "F_RR_M" || $platzierungen["startklasse"] == "RR_J" ||
     $platzierungen["startklasse"] == "RR_C"   || $platzierungen["startklasse"] == "RR_B" || 
     $platzierungen["startklasse"] == "RR_A"){
     $isAkrobatik = TRUE;
  }
  if($platzierungen["startklasse"] == "BW_MA" || $platzierungen["startklasse"] == "BW_MB" ||
     $platzierungen["startklasse"] == "BW_SA" || $platzierungen["startklasse"] == "BW_SB" || 
     $platzierungen["startklasse"] == "BW_JA"){
     $isBoogieWoogie = TRUE;
  }
  
  $html_rgbnss  .= '
      <tr bgcolor=f4f4f4>
        <td align=center>'.$datum->format("d.m.Y").'</td>
        <td>'.utf8_encode($turnierdaten["turniername"]).'</td>
        <td align=center>'.$platzierungen["rl_punkte"].'</td>
        <td align=center>'.$platzierungen["punkte"].'</td>';
  if($platzierungen["startklasse"] == "RR_A" || $platzierungen["startklasse"] == "RR_B"){
    $html_rgbnss .= '<td align=center>'.number_format($erg_pkt_ft,2).'</td>';
  
  } else {
    $html_rgbnss .= '<td align=center>&nbsp;</td>';
  }  
  $html_rgbnss  .= '
        <td align=center>'.number_format($erg_pkt_ta,2).'</td>
        <td align=center>'.number_format($erg_pkt_ak,2).'</td>
        <td align=center>'.number_format($erg_pkt_su,2).'</td>
        <td align=center><b>'.$platzierungen["platz"].'. Place<b></td>
        <td align=center>           
           <button type="submit" name="trnsel" value="'.$t_nummer.'" />
             <img src="https://drbv.de/adm/adm_themes/classic/icons/add.png" title="Details" alt="Details" /> 
           </button>                
        </td>
      </tr>';                  
}

$html_rgbnss  .= '</table></form>';  
  
if ($html_rgbnss == ''){
  $html_rgbnss = '<li><dl><dt>'.date("d.m.Y",time()).'</dt><dd><b><i>Currently there a no competition results available.</i></b></dd></dl></li>'; 
}   

// --------------
// ErgebnisseEnde

// Wertungen
// ---------
if($tnrsel != ''){
$sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = '.$tnrsel;
$turniere     = mysqli_query(DRBVdb(), $sqlab);
$turnierdaten = mysqli_fetch_array($turniere); 
$datum        = new DateTime($turnierdaten["datum"]);
$datumDM      = new DateTime("2016-06-18");//ab hier wurden die Akroteiler B/A angepasst    
$datumDMF     = new DateTime("2016-10-29");//ab hier wurde die Anzahl Aktive bei Showteam korrigiert        
$datum12P     = new DateTime("2018-04-17");//ab hier wurden alle Teiler auf die 12 Punkte Akroregelung angepasst
$turniername  = utf8_encode($turnierdaten["turniername"]);
  
// Wertungsrichter einlesen
$sqlab = 'SELECT * FROM wertungsrichter WHERE turniernummer = '.$tnrsel;
$temp = mysqli_query(DRBVdb(), $sqlab);

while($wr = mysqli_fetch_array($temp)){
  $i = $wr[1];
  $wert_richter[$i] = array($wr[1],$wr[4]);     
}
  
// Tanzrunden einlesen
$sqlab = 'SELECT * FROM rundentab WHERE turniernummer = '.$tnrsel;
$temp  = mysqli_query(DRBVdb(), $sqlab);
unset($i);

while($t_runden = mysqli_fetch_array($temp)){
  $i = $i + 1;
  $t_runde[$i] = $t_runden;  
  //echo $t_runde[$i][1] . ' ' . $t_runde[$i][3] . ' ' . $t_runde[$i][4] ;echo"<br>";
}      
      
// Tanzpaar in Datenbank suchen  
$sqlab          = 'SELECT paar_id_tlp,startklasse, dame, herr, team, platz, punkte, rl_punkte, RT_ID_Ausgeschieden, anzahl_taenzer FROM paare 
                   WHERE Turniernummer = '.$tnrsel.' AND 
                   (startbuch = '.$_POST["mitglied_nummer"].' OR 
                    boogie_sb_herr = '.$_POST["mitglied_nummer"].' OR 
                    boogie_sb_dame = '.$_POST["mitglied_nummer"].')'; 
$paar_id        =  mysqli_query(DRBVdb(), $sqlab);
  
while($paar_id_paare = mysqli_fetch_array($paar_id)){

$paarinfo["paar_id"] = $paar_id_paare[0];
$paarinfo["dame"] = utf8_encode($paar_id_paare[2]);
$paarinfo["herr"] = utf8_encode($paar_id_paare[3]);
$paarinfo["team"] = utf8_encode($paar_id_paare[4]);
$paarinfo["startklasse"] = $paar_id_paare[1];
$paarinfo["platz"] = $paar_id_paare[5];
$paarinfo["punkte"] = $paar_id_paare[6];
$paarinfo["rl_punkte"] = $paar_id_paare[7];
$paarinfo["anzakt"] = $paar_id_paare[9];

if(substr($paarinfo["startklasse"],0,2) == "RR"){  
  $tanzhead_ar = array("Male footwork","Male posture&spins","Female footwork","Female posture&spins","Choreographie","Dance figurs","Dance performance");
} elseif(substr($paarinfo["startklasse"],0,2) == "F_") {
  $tanzhead_ar = array("Technic","Dance","Dace figures","","Positions","Position change","F-figurs/effects");
} else {
  $tanzhead_ar = array("Basic footwork","Basic dancing","Dance figurs", "", "Interpretation","Spontaneous Interpretation","Dance performance");
}            
    
// Tanzpaarpunkteergebnis in Datenbank suchen fuer Endrunde
$sqlab          = 'SELECT * FROM majoritaet WHERE turniernummer = '.$tnrsel.' AND TP_ID = ' . $paar_id_paare["paar_id_tlp"]  . ' AND RT_ID = ' . $paar_id_paare["RT_ID_Ausgeschieden"];
$ergebnisDB_pkt =  mysqli_query(DRBVdb(), $sqlab);

while($rundenDB_pkt = mysqli_fetch_array($ergebnisDB_pkt)){
  //print_r($rundenDB_pkt);echo' rundenDB_pkt<br>';
  $ergDB_pkt_ft = $rundenDB_pkt[WR1];
  $ergDB_pkt_bl = $rundenDB_pkt[WR1];
  $ergDB_pkt_ta = $rundenDB_pkt[WR2];    
  $ergDB_pkt_ak = $rundenDB_pkt[WR3];    
  $ergDB_pkt_bs = $rundenDB_pkt[WR5] + $rundenDB_pkt[WR4];    
  $ergDB_pkt_su = $rundenDB_pkt[WR7];    
}
//print_r($ergDB_pkt_ta);echo' ergDB_pkt_ta<br>';
//print_r($ergDB_pkt_bl);echo' ergDB_pkt_bl<br>';
//print_r($ergDB_pkt_bs);echo' ergDB_pkt_bs<br>';

// Tanzpaarpunkteergebnis in Datenbank suchen fuer alle Runden ausser ER
$sqlab          = 'SELECT * FROM majoritaet WHERE turniernummer = '.$tnrsel.' AND TP_ID = ' . $paar_id_paare["paar_id_tlp"]  . ' AND NOT RT_ID = ' . $paar_id_paare["RT_ID_Ausgeschieden"];
$ergebnisDB_pkt =  mysqli_query(DRBVdb(), $sqlab);

while($rundenDB_pkt = mysqli_fetch_array($ergebnisDB_pkt)){
  //print_r($rundenDB_pkt);echo' rundenDB_pkt<br>';
  $ergDB_pkt_ft_a[] = $rundenDB_pkt[WR1];
  $ergDB_pkt_bl_a[] = $rundenDB_pkt[WR1];
  $ergDB_pkt_ta_a[] = $rundenDB_pkt[WR2];    
  $ergDB_pkt_ak_a[] = $rundenDB_pkt[WR3];    
  $ergDB_pkt_bs_a[] = $rundenDB_pkt[WR5] + $rundenDB_pkt[WR4];    
  $ergDB_pkt_su_a[] = $rundenDB_pkt[WR7];    
}
//print_r($ergDB_pkt_ta_a);echo' ergDB_pkt_ta_a<br>';    
//print_r($ergDB_pkt_bl_a);echo' ergDB_pkt_bl_a<br>';    
//print_r($ergDB_pkt_bs_a);echo' ergDB_pkt_bs_a<br>';    

//Akromultiplikatoren:
if($datum <= $datum12P){
  //vor 12Pkt Einführung
  $akromult_a = array("akromult_43" => 4/3, "akromult_45" => 4/5, "akromult_46" => 4/6, "akromult_85" => 8/5, "akromult_86" => 8/6, "akromult_56" => 5/6, "akromult_57" => 5/7, "akromult_58" => 5/8);  
  $max_pkt_vr_a = array("RR_J" => 40, "RR_C" => 40, "RR_B" => 40, "RR_A" => 40);
  $max_pkt_sf_a = array("RR_J" => 40, "RR_C" => 40, "RR_B" => 40, "RR_A" => 40);
  $max_pkt_er_a = array("RR_J" => 40, "RR_C" => 40, "RR_B" => 80, "RR_A" => 80);
} else {
  //nach 12Pkt Einführung
  $akromult_a = array("akromult_43" => 1, "akromult_45" => 1, "akromult_46" => 1, "akromult_85" => 1, "akromult_86" => 1, "akromult_56" => 1, "akromult_57" => 1, "akromult_58" => 1);  
  // Select max. Punkte bei 12 Punktesystem
  $max_pkt_vr_a = array("RR_J" => 36, "RR_C" => 48, "RR_B" => 50, "RR_A" => 50);
  $max_pkt_sf_a = array("RR_J" => 36, "RR_C" => 48, "RR_B" => 58, "RR_A" => 58);
  $max_pkt_er_a = array("RR_J" => 36, "RR_C" => 48, "RR_B" => 70, "RR_A" => 70);
}                  
  
$akr_tlr_fm_vr = 6;
$akr_tlr_fm_zr = 6;
$akr_tlr_fm_er = 6;  
// gemeldete Akrobatiken suchen 
if($paarinfo["startklasse"] == "RR_J" || $paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
  $sqlab           = 'SELECT * FROM paare WHERE turniernummer = '.$tnrsel.' AND  paar_id_tlp = '.$paarinfo["paar_id"];
  $gemeldet        = mysqli_query(DRBVdb(), $sqlab);
  $gemeldete_akros = mysqli_fetch_array($gemeldet);

  // Vorrunde
  $max_pkt          = '40';
  $insert_akrovr    = '';
  $insert_akrovrt   = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akrovr   .= '<ul> Vorrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akrovr   .= '<ul><a class="admLink" href="#akrovr" id="fadeakrovr"><img src="'.THEME_PATH. '/icons/info.png" alt="AcrobaticVR" />
      </a> Vorrunde<div style="font-size: 12px;display:none;padding-left:40px;" id="refakrovr">';
  }
  $insert_akrovr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[16],2).'</b><i> '.utf8_encode($gemeldete_akros[15]).'</i></dt></dl></li>';
  $insert_akrovr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[18],2).'</b><i> '.utf8_encode($gemeldete_akros[17]).'</i></dt></dl></li>';
  $insert_akrovr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[20],2).'</b><i> '.utf8_encode($gemeldete_akros[19]).'</i></dt></dl></li>';
  $vorwertvr        = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20])*$akromult_a["akromult_43"];
  if($paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[22],2).'</b><i> '.utf8_encode($gemeldete_akros[21]).'</i></dt></dl></li>';
    $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22]);
  }
  if($paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[24],2).'</b><i> '.utf8_encode($gemeldete_akros[23]).'</i></dt></dl></li>';
    $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22] + $gemeldete_akros[24])*$akromult_a["akromult_45"];
  }
  if($paarinfo["startklasse"] == "RR_A" && $datum <= $datumDM){
    $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[26],2).'</b><i> '.utf8_encode($gemeldete_akros[25]).'</i></dt></dl></li>';
    $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22] + $gemeldete_akros[24] + $gemeldete_akros[26])*$akromult_a["akromult_46"];
  }  
  if($paarinfo["startklasse"] == "F_RR_M"){
    $max_pkt        = '50';  
    $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[26],2).'</b><i> '.utf8_encode($gemeldete_akros[25]).'</i></dt></dl></li>';
    $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22] + $gemeldete_akros[24] + $gemeldete_akros[26])*$akromult_a["akromult_56"];    
    if($gemeldete_akros[28]!=0){
      $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[28],2).'</b><i> '.utf8_encode($gemeldete_akros[27]).'</i></dt></dl></li>';
      $akr_tlr_fm_vr  = 7;
      $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22] + $gemeldete_akros[24] + $gemeldete_akros[26] + $gemeldete_akros[28])*$akromult_a["akromult_57"];  
    }
    if($gemeldete_akros[30]!=0){
      $insert_akrovr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[30],2).'</b><i> '.utf8_encode($gemeldete_akros[29]).'</i></dt></dl></li>';
      $akr_tlr_fm_vr  = 8;
      $vorwertvr      = ($gemeldete_akros[16] + $gemeldete_akros[18] + $gemeldete_akros[20] + $gemeldete_akros[22] + $gemeldete_akros[24] + $gemeldete_akros[26] + $gemeldete_akros[28] + $gemeldete_akros[30])*$akromult_a["akromult_58"];  
    }
  }
  $insert_akrovr   .= '<b>Acrobatic value = <i>'.number_format(round($vorwertvr,2),2).' Pts. (max. '.$max_pkt.') </i></b>';
  $insert_akrovr   .= '</div></ul>';

  // Zwischenrunde  
  $max_pkt          = '40';  
  $insert_akrozr    = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akrozr   .= '<ul> Zwischenrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akrozr   .= '<ul><a class="admLink" href="#akrozr" id="fadeakrozr"><img src="'.THEME_PATH. '/icons/info.png" alt="AcrobaticVR" />
      </a> Zwischenrunde<div style="font-size: 12px;display:none;padding-left:40px;" id="refakrozr">';
  }
  $insert_akrozr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[32],2).'</b><i> '.utf8_encode($gemeldete_akros[31]).'</i></dt></dl></li>';
  $insert_akrozr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[34],2).'</b><i> '.utf8_encode($gemeldete_akros[33]).'</i></dt></dl></li>';
  $insert_akrozr   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[36],2).'</b><i> '.utf8_encode($gemeldete_akros[35]).'</i></dt></dl></li>';
  $vorwertzr        = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36])*$akromult_a["akromult_43"];
  if($paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[38],2).'</b><i> '.utf8_encode($gemeldete_akros[37]).'</i></dt></dl></li>';
    $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38]);
  }
  if($paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[40],2).'</b><i> '.utf8_encode($gemeldete_akros[39]).'</i></dt></dl></li>';
    $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38] + $gemeldete_akros[40])*$akromult_a["akromult_45"];
  }
  if($paarinfo["startklasse"] == "RR_A" && $datum <= $datumDM){
    $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[40],2).'</b><i> '.utf8_encode($gemeldete_akros[39]).'</i></dt></dl></li>';
    $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38] + $gemeldete_akros[40] + $gemeldete_akros[42])*$akromult_a["akromult_46"];
  }  
  if($paarinfo["startklasse"] == "F_RR_M"){
    $max_pkt        = '50';  
    $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[42],2).'</b><i> '.utf8_encode($gemeldete_akros[41]).'</i></dt></dl></li>';
    $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38] + $gemeldete_akros[40] + $gemeldete_akros[42])*$akromult_a["akromult_56"];
    if($gemeldete_akros[44]!=0){
      $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[44],2).'</b><i> '.utf8_encode($gemeldete_akros[43]).'</i></dt></dl></li>';
      $akr_tlr_fm_zr  = 7;
      $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38] + $gemeldete_akros[40] + $gemeldete_akros[42] + $gemeldete_akros[44])*$akromult_a["akromult_57"];  
    }
    if($gemeldete_akros[46]!=0){
      $insert_akrozr .= '<li><dl><dt><b>'.number_format($gemeldete_akros[46],2).'</b><i> '.utf8_encode($gemeldete_akros[45]).'</i></dt></dl></li>';
      $akr_tlr_fm_zr  = 8;
      $vorwertzr      = ($gemeldete_akros[32] + $gemeldete_akros[34] + $gemeldete_akros[36] + $gemeldete_akros[38] + $gemeldete_akros[40] + $gemeldete_akros[42] + $gemeldete_akros[44] + $gemeldete_akros[46])*$akromult_a["akromult_58"];  
    }
  }
  $insert_akrozr   .= '<b>Acrobatic value = <i>'.number_format(round($vorwertzr,2),2).' Pts. (max. '.$max_pkt.')</i></b>';
  $insert_akrozr   .= '</div></ul>';
  
  // Endrunde    
  $max_pkt          = '40';  
  $insert_akroer    = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akroer   .= '<ul> Endrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akroer   .= '<ul><a class="admLink" href="#akroer" id="fadeakroer"><img src="'.THEME_PATH. '/icons/info.png" alt="AcrobaticVR" />
      </a> Endrunde<div style="font-size: 12px;display:none;padding-left:40px;" id="refakroer">';
  }
  $insert_akroer   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[48],2).'</b><i> '.utf8_encode($gemeldete_akros[47]).'</i></dt></dl></li>';
  $insert_akroer   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[50],2).'</b><i> '.utf8_encode($gemeldete_akros[49]).'</i></dt></dl></li>';
  $insert_akroer   .= '<li><dl><dt><b>'.number_format($gemeldete_akros[52],2).'</b><i> '.utf8_encode($gemeldete_akros[51]).'</i></dt></dl></li>';
  $vorwerter        = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52])*$akromult_a["akromult_43"];
  if($paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $insert_akroer .= '<li><dl><dt><b>'.number_format($gemeldete_akros[54],2).'</b><i> '.utf8_encode($gemeldete_akros[53]).'</i></dt></dl></li>';
    $vorwerter      = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54]);
  }
  if($paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){
    $max_pkt        = '80';  
    $insert_akroer .= '<li><dl><dt><b>'.number_format($gemeldete_akros[56],2).'</b><i> '.utf8_encode($gemeldete_akros[55]).'</i></dt></dl></li>';
    if($paarinfo["startklasse"] == "RR_B" && $datum <= $datumDM){
      $insert_akroer .= '';
      $vorwerter    = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54] + $gemeldete_akros[56])*$akromult_a["akromult_85"];
    } else {
      $insert_akroer .= '<li><dl><dt><b>'.number_format($gemeldete_akros[58],2).'</b><i> '.utf8_encode($gemeldete_akros[57]).'</i></dt></dl></li>';
      $vorwerter    = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54] + $gemeldete_akros[56] + $gemeldete_akros[58])*$akromult_a["akromult_86"];  
    }
  }
  if($paarinfo["startklasse"] == "F_RR_M"){
    $max_pkt        = '50';
    $vorwerter      = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54] + $gemeldete_akros[56] + $gemeldete_akros[58])*$akromult_a["akromult_56"];      
    if($gemeldete_akros[60]!=0){
      $insert_akroer .= '<li><dl><dt><b>'.number_format($gemeldete_akros[60],2).'</b><i> '.utf8_encode($gemeldete_akros[59]).'</i></dt></dl></li>';
      $akr_tlr_fm_er  = 7;
      $vorwerter      = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54] + $gemeldete_akros[56] + $gemeldete_akros[58] + $gemeldete_akros[60])*$akromult_a["akromult_57"];  
    }
    if($gemeldete_akros[62]!=0){
      $insert_akroer .= '<li><dl><dt><b>'.number_format($gemeldete_akros[62],2).'</b><i> '.utf8_encode($gemeldete_akros[61]).'</i></dt></dl></li>';
      $akr_tlr_fm_er  = 8;
      $vorwerter      = ($gemeldete_akros[48] + $gemeldete_akros[50] + $gemeldete_akros[52] + $gemeldete_akros[54] + $gemeldete_akros[56] + $gemeldete_akros[58] + $gemeldete_akros[60] + $gemeldete_akros[62])*$akromult_a["akromult_58"];  
    }
  }
  $insert_akroer   .= '<b>Acrobatic value = <i>'.number_format(round($vorwerter,2),2).' / Pts. (max. '.$max_pkt.')</i></b>';
  $insert_akroer   .= '</div></ul>';
}
  
// Wertungen für Tanzpaar in Datenbank suchen
$sqlab       = 'SELECT * FROM wertungen WHERE turniernummer = '.$tnrsel. ' AND paar_id_tlp = '.$paarinfo["paar_id"].' ORDER BY rund_tab_id, wert_id'; 
$wertung     = mysqli_query(DRBVdb(), $sqlab);
$html_lavor  = '';
$html_snvor  = '';
$html_akvor  = '';
$html_akzr1  = '';
$html_akzr2  = '';
$html_akzr3  = '';
$html_hoff   = '';
$html_ko     = '';
$html_se     = '';
$html_akend  = '';
$html_laend  = '';
$html_snend  = '';

while($wertungen = mysqli_fetch_array($wertung)){
  if(!$akt_runde){ 
    $akt_runde            = $wertungen[5];
    $runde[$wertungen[5]] = $wertungen[5];
  }
  if($akt_runde != $wertungen[5]){    
    $akt_runde            = $wertungen[5];
    $runde[$wertungen[5]] = $wertungen[5];
  }       
  $rd_wertung = $akt_runde.'_'.$wertungen[4];
  //print_r($rd_wertung);echo" :rd_wertung<br>";
  $rd_erg[$rd_wertung] = $wertungen;
  //print_r($rd_erg);echo" :rd_erg<br>";
}
//print_r($rd_wertung);echo" :rd_wertung<br>";
//print_r($rd_erg);echo" :rd_erg<br>";
 
  // Fuer alle Runden Table Rows bestimmen
  for ($runden_cnt = 1; $runden_cnt <= count($runde); $runden_cnt++){
    //print_r(count($runde));echo' runden_anzahl<br>';
    if ($runden_cnt > 1){
      next($runde);
    }  
    for($tr = 1; $tr < 50; $tr++){
      if($t_runde[$tr][1] == current($runde))
      break;   
    }
    $twrnr = "";
    $awrnr = "";
    $runde_name = rundenbezeichnung($t_runde[$tr][4]);
    //print_r($t_runde[$tr][4]);echo' rundenname:'.$runde_name.'<br>';
    list($tanzpkte_ar, $tanzproz_ar,$akropkte_ar, $akroproz_ar, $t_abzg_text, $t_abzg_pkte, $a_abzg_text, $a_abzg_pkte, $twrnr, $awrnr, $awrname, $twrname) = getRundenErgebnis($runde_name);
    //print_r($akropkte_ar);echo" akropkte<br>"; 
    //print_r($akroproz_ar);echo" akro%<br>";           
    //print_r($tanzpkte_ar);echo" tanzpkte<br>";           
    //print_r($tanzproz_ar);echo" tanz%<br>";
    //print_r($t_abzg_text);echo" t_abzg_text<br>";
    //print_r($t_abzg_pkte);echo" t_abzg_pkte<br>";
    //print_r($a_abzg_text);echo" a_abzg_text<br>";
    //print_r($a_abzg_pkte);echo" a_abzg_pkte<br>";
    //print_r($twrnr);echo" TWR<br>";
    //print_r($awrname);echo" awrname<br>";
    //print_r($twrname);echo" twrname<br>";
    //print_r($awrnr);echo" AWR<br>";
    //print_r($paarinfo["anzakt"]);echo" AnzahlAktive<br>";

    if($isStartbuchBW && ($runde_name == "Vorrunde" || $runde_name == "Endrunde" || $runde_name == "Hoffnungsrunde" || 
       $runde_name == "Langsame Vorrunde" || $runde_name == "Schnelle Vorrunde" ||
       $runde_name == "Langsame Endrunde" || $runde_name == "Schnelle Endrunde")){
      $html_vrbw  = '';
      $html_vrbw .= '
          <table class="prfl_wrtg_rgbns">
            <tr style="background-color: orange;">';
               if($runde_name == "Vorrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Vorrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Endrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Endrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Langsame Vorrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Langsame Vorrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Schnelle Vorrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Schnelle Vorrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Hoffnungsrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Hoffnungsrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Langsame Endrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Langsame Endrunde Boogie-Woogie</b></td>';               
               } elseif($runde_name == "Schnelle Endrunde"){
                 $html_vrbw .= '
                 <td width=250px align=center><b>Schnelle Endrunde Boogie-Woogie</b></td>';               
               }
               for($i = 0; $i < $twrnr; $i++){
                   $html_vrbw .= '
                     <td colspan=2 align=center><b>WR'.($i+1).'</b></td>';                 
               }
               $html_vrbw .= '
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>';
               for($i = 0; $i < $twrnr; $i++){
                 $html_vrbw .= '         
                   <td align=center>%</td>
                   <td align=center class="cellcol1">Pts.</td>';
               }
               $html_vrbw .= '
            </tr>';      
         for($i = 0; $i < 7; $i++){
           if($i == 3) $i = 4;  
           $html_vrbw .= '
           <tr style="background-color: #eaeaea;">
              <td align=right>'.$tanzhead_ar[$i].'</td>';  
              for($j = 0; $j < $twrnr; $j++){
                $html_vrbw .= '                               
                <td align=center>'.$tanzproz_ar[$i+($j*7)].'</td>
                <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+($j*7)],2).'</td>';
              }                 
           $html_vrbw .= '</tr>';
         }
         $tanzpkte_sum_ar = array();
         for($i = 0; $i < $twrnr; $i++){
           $tanzpkte_sum_ar[] = array_sum(array_slice($tanzpkte_ar,$i*7,7));
         }          
         
         if($twrnr < 5) {
           //Mittelwert bei bis zu 4WR
           $tanzpkte_sum = (array_sum($tanzpkte_sum_ar))/$twrnr;
         } else {
           //Hoechster und niedrigster streichen und Mittelwert der verbleibenden bei 5WR & 6WR & 7WR
           $tanzpkte_sum = (array_sum($tanzpkte_sum_ar)-min($tanzpkte_sum_ar)-max($tanzpkte_sum_ar))/($twrnr-2);         
         }
      
         $html_vrbw .= '
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>';
               for($j = 0; $j < $twrnr; $j++){
                 $html_vrbw .= '
                   <td align=center>&nbsp;</td>
                   <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,($j*7),7)),2).'</td>';              
               }
            $html_vrbw .= '
            </tr>
            <tr style="background-color: orange;">
               <td align=right><b>&sum; Dance&nbsp;</b></td>
               <td align=center colspan='.(2*$twrnr).'><b>'.number_format($tanzpkte_sum,2).'</b></td>
            </tr>';
         $html_vrbw .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>Distraction Dance&nbsp;</b></td>
               <td align=center colspan='.(2*$twrnr).'><b></b></td>
             </tr>';
         $html_vrbw .= '
            <tr style="background-color: #BCF5A9;">
               <td align=center colspan='.(1+2*$twrnr).'><b>Result = '.number_format($tanzpkte_sum,2).' Pts.'.$compare_html.'</b></td>
             </tr>';
         if($runde_name == "Schnelle Vorrunde"){
           $tanzpkte_sum_lasn = $tanzpkte_sum_lv + 1.1*$tanzpkte_sum;
           $html_vrbw .= '
           <tr style="background-color: #BCF5A9;">
             <td align=center colspan='.(1+2*$twrnr).'><b>Final result slow(1.0) + fast(1.1) = '.number_format($tanzpkte_sum_lasn,2).' Pts.'.$compare_html_sum.'</b></td>
           </tr>';         
         }
         if($runde_name == "Schnelle Endrunde"){
           $tanzpkte_sum_lasn = $tanzpkte_sum_la + 1.1*$tanzpkte_sum;
           $html_vrbw .= '
           <tr style="background-color: #BCF5A9;">
             <td align=center colspan='.(1+2*$twrnr).'><b>Final result slow(1.0) + fast(1.1) = '.number_format($tanzpkte_sum_lasn,2).' Pts.'.$compare_html_sum.'</b></td>
           </tr>';         
         }
         $html_vrbw .= '
          </table>';
         
      if($runde_name == "Langsame Vorrunde"){                 
        $html_lavor         = $html_vrbw;                                  
        $tanzpkte_sum_lv    = $tanzpkte_sum;
        $tanzpkte_sum_ar_lv = $tanzpkte_sum_ar;
        $erg_pkt_su_lv      = $tanzpkte_sum;
        $teilnahme_lavr     = TRUE;
      } elseif($runde_name == "Schnelle Vorrunde"){
        $html_snvor         = $html_vrbw;                                  
        $tanzpkte_sum_sv    = $tanzpkte_sum;
        $tanzpkte_sum_ar_sv = $tanzpkte_sum_ar;
        $erg_pkt_su_sv      = $tanzpkte_sum;
        $teilnahme_snvr     = TRUE;
      } elseif($runde_name == "Vorrunde"){
        $html_vor           = $html_vrbw;                                  
        $tanzpkte_sum_vo    = $tanzpkte_sum;
        $tanzpkte_sum_ar_vo = $tanzpkte_sum_ar;
        $erg_pkt_su_vo      = $tanzpkte_sum;
        $teilnahme_vor      = TRUE;        
      } elseif($runde_name == "Hoffnungsrunde"){
        $html_hoff          = $html_vrbw;                                  
        $tanzpkte_sum_ho    = $tanzpkte_sum;
        $tanzpkte_sum_ar_ho = $tanzpkte_sum_ar;
        $erg_pkt_su_ho      = $tanzpkte_sum;
        $teilnahme_hoff     = TRUE;        
      } elseif($runde_name == "Langsame Endrunde"){                 
        $html_laend         = $html_vrbw;                                  
        $tanzpkte_sum_la    = $tanzpkte_sum;
        $tanzpkte_sum_ar_la = $tanzpkte_sum_ar;
        $erg_pkt_su_la      = $tanzpkte_sum;
        $teilnahme_laer     = TRUE;
      } elseif($runde_name == "Schnelle Endrunde"){
        $html_snend         = $html_vrbw;                                  
        $tanzpkte_sum_sn    = $tanzpkte_sum;
        $tanzpkte_sum_ar_sn = $tanzpkte_sum_ar;
        $erg_pkt_su_sn      = $tanzpkte_sum;
        $teilnahme_sner     = TRUE;
      } elseif($runde_name == "Endrunde"){
        $html_end           = $html_vrbw;                                  
        $tanzpkte_sum_er    = $tanzpkte_sum;
        $tanzpkte_sum_ar_er = $tanzpkte_sum_ar;
        $erg_pkt_su_er      = $tanzpkte_sum;
        $teilnahme_end      = TRUE;
      }                     
    }//end "Vorrunde/Endrunde/Langsam/Schnell Boogie-Woogie"
    
    if($runde_name == "Endrunde Fußtechnik"){
      $html_ft  = '';
      $html_ft .= '
          <table class="prfl_wrtg_rgbns">
            <tr style="background-color: orange;">
               <td align=center><b>Final round footwork</b></td>';        
               for($i = 0; $i < 4; $i++){
                  $html_ft .= '               
                    <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
              }        
          $html_ft .= '        
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
            </tr>';      
         for($i = 0; $i < 7; $i++){
         $html_ft .= '
            <tr style="background-color: #eaeaea;">
               <td align=right>'.$tanzhead_ar[$i].'</td>                                     
               <td align=center>'.$tanzproz_ar[$i].'</td>
               <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i],2).'</td>                 
               <td align=center>'.$tanzproz_ar[$i+7].'</td>
               <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+7],2).'</td>';
               if($twrnr == 3 || $twrnr == 4){
                 $html_ft .= '
                    <td align=center>'.$tanzproz_ar[$i+14].'</td>
                    <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+14],2).'</td>                   
                    ';                                     
               } else {
                 $html_ft .= '                                     
                   <td align=center></td>
                   <td align=center></td>';
               }
               if($twrnr == 4){
                 $html_ft .= '
                    <td align=center>'.$tanzproz_ar[$i+21].'</td>
                    <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+21],2).'</td>
                    ';                                     
               } else {
                 $html_ft .= '                                     
                   <td align=center></td>
                   <td align=center></td>';
               }
               $html_ft .= '                                                                               
            </tr>';
          }
         $tanzpkte_sum = array(array_sum(array_slice($tanzpkte_ar,0,7)), array_sum(array_slice($tanzpkte_ar,7,7)), array_sum(array_slice($tanzpkte_ar,14,7)), array_sum(array_slice($tanzpkte_ar,21,7)));
         
         if($twrnr != 4) {
           //Mittelwert bei 2WR und 3WR
           $tanzpkte_sum = (array_sum($tanzpkte_sum))/$twrnr;
         } else {
           //Hoechster und niedrigster streichen und Mittelwert der beiden verbleibenden bei 4WR
           $tanzpkte_sum = (array_sum($tanzpkte_sum)-min($tanzpkte_sum)-max($tanzpkte_sum))/2;         
         }
      
         $html_ft .= '
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,0,7)),2).'</td>
               <td align=center>&nbsp;</td>
               <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,7,7)),2).'</td>
               <td align=center>&nbsp;</td>';
               if(array_sum(array_slice($tanzpkte_ar,14,7)) != 0){
               $html_ft .= '
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,14,7)),2).'</td>';               
               } else {
               $html_ft .= '
                 <td align=center class="cellcol3">&nbsp;</td>';                              
               }               
               $html_ft .= '
                 <td align=center>&nbsp;</td>';
               if(array_sum(array_slice($tanzpkte_ar,21,7)) != 0){
                 $html_ft .= '
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,21,7)),2).'</td>';               
               } else {
                 $html_ft .= '
                 <td align=center class="cellcol3">&nbsp;</td>';               
               }
         $html_ft .= '
            </tr>
            <tr style="background-color: orange;">
               <td align=right><b>&sum; Dance&nbsp;</b></td>
               <td align=center colspan=8><b>'.number_format($tanzpkte_sum,2).'</b></td>
            </tr>';
         $html_ft .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>Distraction Dance&nbsp;</b></td>';
         if($t_abzg_pkte[0] != 0){
           $html_ft .= '<td align=center colspan=8><b>'.$t_abzg_pkte[0].'</b>&nbsp;('.$t_abzg_text[0].')</td></tr>';         
         } else {
           $html_ft .= '<td align=center colspan=8><b>&nbsp;</b></td></tr>';         
         }
         $html_ft .= '<tr style="background-color: #BCF5A9;">';
         if($tanzpkte_sum <= $t_abzg_pkte[0]){
           $html_ft .= '<td align=center colspan=9><b>Result = 0,00 Pts.'.$compare_html.'</b></td>';                   
         } else {
           $html_ft .= '<td align=center colspan=9><b>Result = '.number_format($tanzpkte_sum-$t_abzg_pkte[0],2).' Pts.'.$compare_html.'</b></td>';                            
         }
         $html_ft .= '
            </tr>
          </table>';              
    }//end "Endrunde Fußtechnik"
    
    if(!$isStartbuchBW && ($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde" 
    || $runde_name == "Vorrunde" || $runde_name == "Hoffnungsrunde" || $runde_name == "KO-Runde" || $runde_name == "Semifinale"
    || $runde_name == "1. Zwischenrunde" || $runde_name == "2. Zwischenrunde" || $runde_name == "3. Zwischenrunde")){      
      $html_ak     = '';
      $html_ak    .= '
          <table class="prfl_wrtg_rgbns" width="720px">
            <tr style="background-color: orange;">';
            if($runde_name == "Endrunde Akrobatik"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Final acrobatic</b></td>';
            } elseif($runde_name == "Endrunde"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Final</b></td>';
            } elseif($runde_name == "Vorrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_vr;
              $html_ak .= '<td align=center><b>1st round</b></td>';
            } elseif($runde_name == "Hoffnungsrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_vr;
              $html_ak .= '<td align=center><b>Hope round</b></td>';
            } elseif($runde_name == "KO-Runde"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>KO-round</b></td>';
            } elseif($runde_name == "Semifinale"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Semi final</b></td>';
            } elseif($runde_name == "1. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>1st qualification</b></td>';
            } elseif($runde_name == "2. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>2nd qualification</b></td>';
            } elseif($runde_name == "3. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>3rd qualification</b></td>';
            }
            for($i = 0; $i < 4; $i++){
                $html_ak .= '               
                  <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
            }
            if($isStartbuchFormation && !$startklasse_mit_akro){
              for($i = 4; $i < 6; $i++){
                  $html_ak .= '
                     <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
              }  
            }                                       
            if($startklasse_mit_akro){
            $html_ak .= '                     
               <td align=center>&nbsp;</td>
               <td colspan=2 align=center><b>AK1</b></td>
               <td colspan=2 align=center><b>AK2</b></td>
               <td colspan=2 align=center><b>AK3</b></td>
               <td colspan=2 align=center><b>AK4</b></td>
               <td colspan=2 align=center><b>AK5</b></td>
               <td colspan=2 align=center><b>AK6</b></td>';
               if($akr_tlr_fm == 7) $html_ak .= '<td colspan=2 align=center><b>AK7</b></td>';
               if($akr_tlr_fm == 8) $html_ak .= '<td colspan=2 align=center><b>AK8</b></td>';              
               $html_ak .= '
               <td align=center><b>&sum; Acrobatic</b></td>';}
            $html_ak .= '    
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>';
            if($isStartbuchFormation && !$startklasse_mit_akro){
            $html_ak .= '
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>';}                           
            if($startklasse_mit_akro){
            $html_ak .= '        
               <td align=center><b>Acrobatic<br>initial value</b></td>               
               <td align=center>&nbsp;</td>';
               if($paarinfo["startklasse"] == "RR_J" || $paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){               
                 $html_ak .= '
                      <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset-1]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset],2).'</span></b></td>                              
                      <td align=center>&nbsp;</td>
                      <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+1]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+2],2).'</span></b></td>               
                      <td align=center>&nbsp;</td>               
                      <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+3]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+4],2).'</span></b></td>
                      <td align=center>&nbsp;</td>';
               } else {
                 $html_ak .= '
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>';               
               }
               if($paarinfo["startklasse"] == "RR_C" || $paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){               
                 $html_ak .= '
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+5]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+6],2).'</span></b></td>';
               } else {
                 $html_ak .= '
                       <td align=center>&nbsp;</td>';
               }
               if($paarinfo["startklasse"] == "RR_B" || $paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M"){               
                 $html_ak .= '
                       <td align=center>&nbsp;</td>               
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+7]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+8],2).'</span></b></td>';
               } else {
                 $html_ak .= '
                       <td align=center>&nbsp;</td>               
                       <td align=center>&nbsp;</td>';               
               }
               if($datum <= $datumDM && ($paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "F_RR_M")){               
                 $html_ak .= '
                       <td align=center>&nbsp;</td>
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+9]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+10],2).'</span></b></td>';
               } elseif(($datum > $datumDM && $runde_name == "Endrunde Akrobatik") || $paarinfo["startklasse"] == "F_RR_M") {
                 $html_ak .= '
                       <td align=center>&nbsp;</td>
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+9]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+10],2).'</span></b></td>';               
               } else {
                 $html_ak .= '
                       <td align=center>&nbsp;</td>
                       <td align=center>&nbsp;</td>';                                             
               }
               if($akr_tlr_fm == 7){
                 $html_ak .= ' 
                       <td align=center>&nbsp;</td>
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+11]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+12],2).'</span></b></td>';               
               }
               if($akr_tlr_fm == 8){
                 $html_ak .= ' 
                       <td align=center>&nbsp;</td>
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+13]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+14],2).'</span></b></td>';               
               }

               if($runde_name == "Endrunde Akrobatik" && $paarinfo["startklasse"] != "RR_S"){
                 $html_ak .= '<td align=center><b>'.round($vorwerter,2).'</b></td></tr>';
               } elseif(($runde_name == "Endrunde" || $runde_name == "KO-Runde") && $paarinfo["startklasse"] != "RR_S"){
                 $html_ak .= '<td align=center><b>'.round($vorwerter,2).'</b></td></tr>';
               } elseif(($runde_name == "Vorrunde" || $runde_name == "Hoffnungsrunde") && $paarinfo["startklasse"] != "RR_S"){
                 $html_ak .= '<td align=center><b>'.round($vorwertvr,2).'</b></td></tr>';
               } elseif(($runde_name == "1. Zwischenrunde" || $runde_name == "2. Zwischenrunde" || $runde_name == "3. Zwischenrunde") && $paarinfo["startklasse"] != "RR_S"){
                 $html_ak .= '<td align=center><b>'.round($vorwertzr,2).'</b></td></tr>';
               } else {
               $html_ak .= '<td align=center><b>&nbsp;</b></td>';
               }
             }
               $html_ak .= '</tr>';      
               $html_ak .= '      
             <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>';
               if($isStartbuchFormation && !$startklasse_mit_akro){
               $html_ak .= ' 
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>';               
               }
               if($startklasse_mit_akro){
               $html_ak .= '        
               <td align=center>&nbsp;</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pts.</td>';
               if($akr_tlr_fm == 7){  
                 $html_ak .= '
                 <td align=center>%</td>
                 <td align=center class="cellcol1">Pts.</td>';
               }  
               if($akr_tlr_fm == 8){  
                 $html_ak .= '
                 <td align=center>%</td>
                 <td align=center class="cellcol1">Pts.</td>';
               }  
               $html_ak .= '  
               <td align=center class="cellcol1">Pts.</td>';
               }  
               $html_ak .= '        
            </tr>';
         unset($akropkte_sum_ar);

         if($isStartbuchFormation){
           $i_row = array(0,1,2,4,5,6);//Zeile 3 auslassen, da keine Wertungen enthalten bei Formationen         
         } else {
           $i_row = array(0,1,2,3,4,5,6);         
         }
         foreach($i_row AS $i){
           $html_ak .= '
            <tr style="background-color: #eaeaea;">
               <td align=right>'.$tanzhead_ar[$i].'</td>
               <td align=center>'.$tanzproz_ar[$i].'</td>
               <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i],2).'</td>
               <td align=center>'.$tanzproz_ar[$i+7].'</td>
               <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+7],2).'</td>
               <td align=center>'.$tanzproz_ar[$i+14].'</td>';
               if($twrnr == 3 || $twrnr == 4 || $twrnr == 5 || $twrnr == 6){
               $html_ak .= '
                 <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+14],2).'</td>';
               } else {
                 if(!$isStartbuchFormation || $startklasse_mit_akro) $html_ak .= '<td align=center>&nbsp;</td>';
               }           
               $html_ak .= '
               <td align=center>'.$tanzproz_ar[$i+21].'</td>';
               if($twrnr == 4 || $twrnr == 5 || $twrnr == 6){
                 $html_ak .= '
                 <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+21],2).'</td>';
               } else {
                 if(!$isStartbuchFormation || $startklasse_mit_akro) $html_ak .= '<td align=center>&nbsp;</td>';
               }           
               if($twrnr == 5 || $twrnr == 6){
                 $html_ak .= '
                 <td align=center>'.$tanzproz_ar[$i+28].'</td>
                 <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+28],2).'</td>';
               }
               if($twrnr == 6){
                 $html_ak .= '
                 <td align=center>'.$tanzproz_ar[$i+35].'</td>
                 <td align=center class="cellcol2">'.number_format($tanzpkte_ar[$i+35],2).'</td>';
               }
              if($startklasse_mit_akro){
              if($i < 4){
                  $html_ak .= '<td align=center><b>AWR'.($i+1).'</b></td>';                 
                $akropkte_awrx = 0;
                for($y = 0; $y < count($akroproz_ar)/($awrnr); $y++){                                  
                  $anzahl_akros = count($akropkte_ar)/($awrnr);//nimmt anzahl werte im array durch anzahl wr -> also immer alle 8 moeglichen                 
                  if($y < 6 || ($y == 6 && $akr_tlr_fm == 7) || ($y == 7 && $akr_tlr_fm == 8)){  
                    $html_ak .= '               
                    <td align=center>'.$akroproz_ar[$y+($i*($anzahl_akros))].'</td>';
                    if($akropkte_ar[$y+($i*(count($akropkte_ar)/($awrnr)))] != ''){
                      $html_ak .= '
                      <td align=center class="cellcol2"><b>'.number_format($akropkte_ar[$y+($i*(count($akropkte_ar)/($awrnr)))],2).'</b></td>';
                    } else {
                      $html_ak .= '
                      <td align=center class="cellcol3">&nbsp;</td>';
                    }                                
                    $akropkte_awrx = $akropkte_awrx + $akropkte_ar[$y+($i*(count($akropkte_ar)/($awrnr)))];
                    //print_r($akropkte_awrx);echo' akropkte_awrx-'.count($akroproz_ar).'<br>';
                  }                
                }                 
                for($y = 0; $y < (6 - count($akropkte_ar)/($awrnr)); $y++){
                  $html_ak .= '               
                    <td align=center>&nbsp;</td>
                    <td align=center>&nbsp;</td>';                                                                                                           
                }                
                
                $akromult = 4;
                if($runde_name == "Endrunde Akrobatik"){
                  $akromult = 8;
                }
                
                if($datum <= $datum12P){
                  //vor 12Pkt Einführung
                  if($datum <= $datumDM && $paarinfo["startklasse"] == "RR_B"){
                    $akropkte_sum = $akropkte_awrx*$akromult/5;                                              
                  } else {
                    $akropkte_sum = $akropkte_awrx*$akromult/(count($akropkte_ar)/($awrnr));               
                  }                                  
                } else {                 
                  //nach 12Pkt Einführung
                  $akropkte_sum = $akropkte_awrx;
                }
                 
                if($paarinfo["startklasse"] == "F_RR_M"){
                  if($akr_tlr_fm == 7){
                    $akropkte_sum = $akropkte_awrx*$akromult_a['akromult_57'];               
                  } elseif($akr_tlr_fm == 8){
                    $akropkte_sum = $akropkte_awrx*$akromult_a['akromult_58'];               
                  } else {
                    $akropkte_sum = $akropkte_awrx*$akromult_a['akromult_56'];               
                  }
                }                                    
                //print_r($akropkte_sum);echo' akropkte_sum:'.$akromult.':'.(count($akropkte_ar)/($awrnr)).'<br>';
                if($akropkte_sum != 0){
                  $html_ak .= '<td align=center class="cellcol1"><b>'.number_format($akropkte_sum,2).'</b></td>';                                                                                                           
                } else {
                  $html_ak .= '<td align=center class="cellcol3">&nbsp;</td>';                                                                                                           
                }
                $akropkte_sum_ar[] = $akropkte_sum;
                //print_r($akropkte_sum_ar);echo' akrosum_ar<br>';
            } elseif($i == 4){
              $html_ak .= '
                <td align=center><b>Distractions</b></td>';
              for($y = 0; $y < count($akroproz_ar)/($awrnr); $y++){
                $html_ak .= '
                  <td colspan="2" align=center style="color: #ed595d;">'.$a_abzg_text[$y].'</td>';              
              }
              for($y = 0; $y < (6 - count($akropkte_ar)/($awrnr)); $y++){
                $html_ak .= '
                  <td colspan="2" align=center>&nbsp;</td>';
              }
              if(array_sum($a_abzg_pkte)/($awrnr) > 0){
                $html_ak .= '              
                  <td align=center style="color: #ed595d;">'.number_format(array_sum($a_abzg_pkte)/($awrnr),2).'</td>';                                                                                                   
              } else {
                $html_ak .= '              
                  <td align=center style="color: #ed595d;"></td>';                                                                                     
              }
            } else {
              $html_ak .= '';                                                                       
            }
            }              
            $html_ak .= '       
            </tr>';
           }      
      
         if(($twrnr) == 2) {
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)));
         } elseif(($twrnr) == 3){
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)),
                                      array_sum(array_slice($tanzpkte_ar,14,7)));          
         } elseif(($twrnr) == 4){
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)),
                                      array_sum(array_slice($tanzpkte_ar,14,7)),
                                      array_sum(array_slice($tanzpkte_ar,21,7)));          
         } elseif(($twrnr) == 5){
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)),
                                      array_sum(array_slice($tanzpkte_ar,14,7)),
                                      array_sum(array_slice($tanzpkte_ar,21,7)),
                                      array_sum(array_slice($tanzpkte_ar,28,7)));
                 
         } elseif(($twrnr) == 6){
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)),
                                      array_sum(array_slice($tanzpkte_ar,14,7)),
                                      array_sum(array_slice($tanzpkte_ar,21,7)),
                                      array_sum(array_slice($tanzpkte_ar,28,7)),
                                      array_sum(array_slice($tanzpkte_ar,35,7)));
                 
         } else {
           $tanzpkte_sum_ar   = array(array_sum(array_slice($tanzpkte_ar,0,7)), 
                                      array_sum(array_slice($tanzpkte_ar,7,7)),
                                      array_sum(array_slice($tanzpkte_ar,14,7)), 
                                      array_sum(array_slice($tanzpkte_ar,21,7)), 
                                      array_sum(array_slice($tanzpkte_ar,28,7)), 
                                      array_sum(array_slice($tanzpkte_ar,35,7)), 
                                      array_sum(array_slice($tanzpkte_ar,42,7)));
         } 
                 
         //print_r($tanzpkte_sum_ar);echo' tanzpkte_sum_ar<br>';      
                 
        
         if($twrnr == 2 || $twrnr == 3) {
           //Mittelwert bei 2WR und 3WR
           $tanzpkte_sum  = (array_sum($tanzpkte_sum_ar))/($twrnr);
         } else {
           //Hoechster und niedrigster streichen und Mittelwert der verbleibenden WR
           $tanzpkte_sum  = (array_sum($tanzpkte_sum_ar)-min($tanzpkte_sum_ar)-max($tanzpkte_sum_ar))/($twrnr-2);
         }         
         if($awrnr == 2 || $awrnr == 3) {
           //Mittelwert bei 2WR und 3WR
           $akropkte_sum  = (array_sum($akropkte_sum_ar))/($awrnr);
         } else {
           //Hoechster und niedrigster streichen und Mittelwert der verbleibenden WR
           $akropkte_sum  = (array_sum($akropkte_sum_ar)-min($akropkte_sum_ar)-max($akropkte_sum_ar))/($awrnr-2);
         }         
         
         //Bei Formationen Anzahl Taenzer beruecksichtigen
         $anzaktabzg = 0;        
         if($paarinfo["startklasse"] == "F_RR_MA"){
           $anzaktabzg     = (12-$paarinfo["anzakt"])*0.0125*$tanzpkte_sum;      
           $anzaktabzgakro = (12-$paarinfo["anzakt"])*0.0125*$akropkte_sum;      
         }
         if($paarinfo["startklasse"] == "F_RR_J"){
           $anzaktabzg = (12-$paarinfo["anzakt"])*0.0125*$tanzpkte_sum;      
         }
         if($paarinfo["startklasse"] == "F_RR_GF"){
           $anzaktabzg = (12-$paarinfo["anzakt"])*0.0175*$tanzpkte_sum;      
         }                         
         if($paarinfo["startklasse"] == "F_RR_LF"){
           $anzaktabzg = (16-$paarinfo["anzakt"])*0.0125*$tanzpkte_sum;      
         }                 
         if($datum <= $datumDMF){
           //if vor DMF
           // Berechnung wie bei Lady => nach der DM korrigiert, d.h. ohne Abzug bei Showteam
           if($paarinfo["startklasse"] == "F_RR_ST"){
             $anzaktabzg = (16-$paarinfo["anzakt"])*0.0125*$tanzpkte_sum;      
           }                                  
         }
                                  
         //print_r($tanzpkte_sum);echo' tanzpkte_sum<br>';        
         //print_r($anzaktabzg);echo' anzaktabzg<br>';        
         //print_r($paarinfo["startklasse"]);echo' startklasse<br>';        
                 
         $html_ak .= '
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,0,7)),2).'</td>
               <td align=center>&nbsp;</td>
               <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,7,7)),2).'</td>
               <td align=center>&nbsp;</td>';
               if(array_sum(array_slice($tanzpkte_ar,14,7)) != 0){
                 $html_ak .= '
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,14,7)),2).'</td>';        
               } else {
                 $html_ak .= '
                 <td align=center class="cellcol3">&nbsp;</td>';
               }
               $html_ak .= '
                 <td align=center>&nbsp;</td>';
               if(array_sum(array_slice($tanzpkte_ar,21,7)) != 0){
                 $html_ak .= '
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,21,7)),2).'</td>';
               } else {
                 $html_ak .= '
                 <td align=center class="cellcol3">&nbsp;</td>';
               }                 
               if(array_sum(array_slice($tanzpkte_ar,28,7)) != 0){
                 $html_ak .= '
                 <td align=center>&nbsp;</td>
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,28,7)),2).'</td>';
               }
               if(array_sum(array_slice($tanzpkte_ar,35,7)) != 0){
                 $html_ak .= '
                 <td align=center>&nbsp;</td>
                 <td align=center class="cellcol1">'.number_format(array_sum(array_slice($tanzpkte_ar,35,7)),2).'</td>';
               }                 
                 
         $html_ak .= '
            </tr>
            <tr style="background-color: orange;">
               <td align=right><b>Judgemnt dance&nbsp;</b></td>';
         if($isStartbuchFormation && !$startklasse_mit_akro){
           $colspan_value = 12;
         } else {
           $colspan_value = 8;
         }
         if($paarinfo["startklasse"] == "F_RR_MA" || $paarinfo["startklasse"] == "F_RR_LF"  || $paarinfo["startklasse"] == "F_RR_GF"  || $paarinfo["startklasse"] == "F_RR_J"){
           $html_ak .= '     
               <td align=center colspan='.$colspan_value.'><b>'.number_format($tanzpkte_sum-$anzaktabzg,2).'</b> (+'.number_format($anzaktabzg,2).')</td>';
         } else {        
           $html_ak .= '     
               <td align=center colspan='.$colspan_value.'><b>'.number_format($tanzpkte_sum-$anzaktabzg,2).'</b></td>';
         }
         $tanzpkte_sum = $tanzpkte_sum-$anzaktabzg;        
         if($startklasse_mit_akro){
           if($akr_tlr_fm == 7){
             $colspan_add = 2;
           } elseif ($akr_tlr_fm == 8){
             $colspan_add = 4;
           } else {      
             $colspan_add = 0;
           }                                                   
           $html_ak .= '
               <td align=left colspan='.(13+$colspan_add).'><b>Judgmnt acrobatic&nbsp;</b></td>
               <td align=center><b>'.number_format($akropkte_sum,2).'</b></td></tr>';         
         } else {
           $html_ak .= '
            </tr>';                  
         }
         $html_ak .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>Deduction dance&nbsp;</b></td>
               <td align=center colspan='.$colspan_value.'><b>'.$t_abzg_text[0].'</b></td>';
         if($startklasse_mit_akro){
           $html_ak .= '<td align=left colspan='.(13+$colspan_add).'><b>Deduction acrobatic&nbsp;</b></td>';
           if(array_sum($a_abzg_pkte) != 0){
               $html_ak .= '<td align=center><b>'.number_format(array_sum($a_abzg_pkte)/($awrnr),2).'</b></td>';
           } else {
               $html_ak .= '<td align=center><b>&nbsp;</b></td>';
           }      
         }
         $html_ak .= '    
            </tr>
            <tr style="background-color: #BCF5A9;">
               <td align=right><b>&sum; dance&nbsp;</b></td>
               <td align=center colspan='.$colspan_value.'><b>';          
         if($tanzpkte_sum <= $t_abzg_pkte[0]){
           $html_ak .= '0.00'.$compare_ta_html;
         } else {
           $html_ak .= number_format($tanzpkte_sum-$t_abzg_pkte[0],2).$compare_ta_html;
         }
         $html_ak .= '</b></td>';
         if($startklasse_mit_akro){
           if($akropkte_sum <= array_sum($a_abzg_pkte)){
             $html_ak .= '
                <td align=left colspan='.(13+$colspan_add).'><b>&sum; acrobatic&nbsp;</b></td>
                <td align=center><b>0,00'.$compare_ak_html.'</b></td></tr>';                                   
           } else {
             $html_ak .= '
                <td align=left colspan='.(13+$colspan_add).'><b>&sum; acrobatic&nbsp;</b></td>
                <td align=center><b>'.number_format($akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr)),2).$compare_ak_html.'</b></td></tr>';                                   
           }                       
         } else {
           $html_ak .= '
              </tr>';                           
         }
         $html_ak .= '
            <tr style="background-color: #BCF5A9;">';
         if($startklasse_mit_akro){                 
           $html_ak .= '<td align=center colspan='.(23+$colspan_add).'><b>Result = ';         
         } else {
           $html_ak .= '<td align=center colspan='.($colspan_value + 1).'><b>Result = ';         
         }
               if(($tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr))) > 0){
                 $erg_pkt_su = $tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr));
                 $html_ak .= number_format($tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr)),2).' Pts.'.$compare_html.'</b></td>';                 
               } else {
                 $erg_pkt_su = 0;
                 $html_ak .= '0.00 Pts.'.$compare_html ;
               }
         $html_ak .= '
            </tr>';          
         $html_ak .= '
          </table>';
                 
      if($runde_name == "Vorrunde"){
        if($wrtg_bw){
          $html_akvor       = $html_vrbw;       
        } else {        
          $html_akvor       = $html_ak;
        }
        $akrovorwert        = $vorwertvr;
        $tanzpkte_sum_vr    = $tanzpkte_sum;
        $tanzpkte_sum_ar_vr = $tanzpkte_sum_ar;
        $erg_pkt_su_vr      = $erg_pkt_su;
        $teilnahme_vr       = TRUE;
      } elseif($runde_name == "Hoffnungsrunde"){
        $html_akhr1         = $html_ak;
        $akrovorwert        = $vorwertvr;
        $tanzpkte_sum_h1    = $tanzpkte_sum;
        $tanzpkte_sum_ar_h1 = $tanzpkte_sum_ar;
        $erg_pkt_su_h1      = $erg_pkt_su;
        $teilnahme_h1       = TRUE;
      } elseif($runde_name == "KO-Runde"){
        $html_akko          = $html_ak;
        $akrovorwert        = $vorwerter;
        $tanzpkte_sum_ko    = $tanzpkte_sum;
        $tanzpkte_sum_ar_ko = $tanzpkte_sum_ar;
        $erg_pkt_su_ko      = $erg_pkt_su;
        $teilnahme_ko       = TRUE;
      } elseif($runde_name == "Semifinale"){
        $html_akse          = $html_ak;
        $akrovorwert        = $vorwerter;
        $tanzpkte_sum_se    = $tanzpkte_sum;
        $tanzpkte_sum_ar_se = $tanzpkte_sum_ar;
        $erg_pkt_su_se      = $erg_pkt_su;
        $teilnahme_se       = TRUE;
      } elseif($runde_name == "1. Zwischenrunde"){
        $html_akzr1         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z1    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z1 = $tanzpkte_sum_ar;
        $erg_pkt_su_z1      = $erg_pkt_su;
        $teilnahme_z1       = TRUE;
      } elseif($runde_name == "2. Zwischenrunde"){
        $html_akzr2         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z2    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z2 = $tanzpkte_sum_ar;
        $erg_pkt_su_z2      = $erg_pkt_su;
        $teilnahme_z2       = TRUE;
      } elseif($runde_name == "3. Zwischenrunde"){
        $html_akzr3         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z3    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z3 = $tanzpkte_sum_ar;
        $erg_pkt_su_z3      = $erg_pkt_su;
        $teilnahme_z3       = TRUE;
      } elseif($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde"){
        if($wrtg_bw){
          $html_akend       = $html_vrbw;                                  
        } else {
          $html_akend       = $html_ak;                 
        }         
        $akrovorwert        = $vorwerter;
        $tanzpkte_sum_er    = $tanzpkte_sum;
        $tanzpkte_sum_ar_er = $tanzpkte_sum_ar;
        $erg_pkt_su_er      = $erg_pkt_su;
        $teilnahme_er       = TRUE;
      }
    }//end "Endrunde Akrobatik || Endrunde || Vorrunde || Hoffnungsrunde || KO-Runde || Zwischenrunden"          
    
  }//End alle Runden Table Rows
  
if($teilnahme_vr){
  $tanzpkte_sum    = $tanzpkte_sum_vr;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_vr;
  $erg_pkt_su      = $erg_pkt_su_vr;
}
if($teilnahme_h1){
  $tanzpkte_sum    = $tanzpkte_sum_h1;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_h1;
  $erg_pkt_su      = $erg_pkt_su_h1;
}
if($teilnahme_ko){
  $tanzpkte_sum    = $tanzpkte_sum_ko;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_ko;
  $erg_pkt_su      = $erg_pkt_su_ko;
}                                    
if($teilnahme_se){
  $tanzpkte_sum    = $tanzpkte_sum_se;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_se;
  $erg_pkt_su      = $erg_pkt_su_se;
} 
if($teilnahme_z1){
  $tanzpkte_sum    = $tanzpkte_sum_z1;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z1;
  $erg_pkt_su      = $erg_pkt_su_z1;
}  
if($teilnahme_z2){
  $tanzpkte_sum    = $tanzpkte_sum_z2;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z2;
  $erg_pkt_su      = $erg_pkt_su_z2;
}  
if($teilnahme_z3){
  $tanzpkte_sum    = $tanzpkte_sum_z3;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z3;
  $erg_pkt_su      = $erg_pkt_su_z3;
}  
if($teilnahme_er){
  $tanzpkte_sum    = $tanzpkte_sum_er;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_er;
  $erg_pkt_su      = $erg_pkt_su_er;
}
if($teilnahme_lavor){
  $tanzpkte_sum    = $tanzpkte_sum_lv;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_lv;
  $erg_pkt_su      = $erg_pkt_su_lv;
}                 
if($teilnahme_snvr){
  $tanzpkte_sum    = $tanzpkte_sum_sv;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_sv;
  $erg_pkt_su      = $erg_pkt_su_sv;
} 
if($teilnahme_vor){
  $tanzpkte_sum    = $tanzpkte_sum_vo;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_vo;
  $erg_pkt_su      = $erg_pkt_su_vo;
}                 
if($teilnahme_hoff){
  $tanzpkte_sum    = $tanzpkte_sum_ho;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_ho;
  $erg_pkt_su      = $erg_pkt_su_ho;
}                  
if($teilnahme_laer){
  $tanzpkte_sum    = $tanzpkte_sum_la;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_la;
  $erg_pkt_su      = $erg_pkt_su_la;
}                 
if($teilnahme_sner){
  $tanzpkte_sum    = $tanzpkte_sum_sn;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_sn;
  $erg_pkt_su      = $erg_pkt_su_sn;
} 
if($teilnahme_end){
  $tanzpkte_sum    = $tanzpkte_sum_er;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_er;
  $erg_pkt_su      = $erg_pkt_su_er;
} 
                 
}// end: while($paar_id_paare) 
  
$html_wrtg    = '
  <p><hr /><br><b>Judgements for: '.$turniername.'</b></p>';

               if($html_lavor != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_lavor.'
                 </div><br>';                 
               }                              
               if($html_snvor != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_snvor.'
                 </div><br>';                 
               }                              
               if($html_vor != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_vor.'
                 </div><br>';                 
               }                              
               if($html_akvor != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akvor.'
                 </div><br>';                 
               }                              
               if($html_akhr1 != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akhr1.'
                 </div><br>';                 
               }                              
               if($html_akko != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akko.'
                 </div><br>';                 
               }                              
               if($html_akse != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akse.'
                 </div><br>';                 
               }                              
               if($html_akzr1 != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akzr1.'
                 </div><br>';                 
               }                              
               if($html_akzr2 != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akzr2.'
                 </div><br>';                 
               }                              
               if($html_akzr3 != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akzr3.'
                 </div><br>';                 
               }                              
               if($html_hoff != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_hoff.'
                 </div><br>';                 
               }                              
               if($html_laend != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_laend.'
                 </div><br>';                 
               }                              
               if($html_snend != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_snend.'
                 </div><br>';                 
               }                              
               if($html_end != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_end.'
                 </div><br>';                 
               }                              
               if($html_ft != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_ft.'
                 </div><br>';                 
               }                              
               if($html_akend != ''){
                 $html_wrtg .=  '
                 <div class="TabelleWrtg" style="font-size: 11px;" align="center">
                       '.$html_akend.'
                 </div><br>';                 
               }                                
}
// -------------
// WertungenEnde
      
  
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
<head>
  <title>Deutscher Rock&#039;n&#039;Roll und Boogie-Woogie Verband e.V. - Startbuch Check</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
  <link rel="shortcut icon" type="image/x-icon" href="http://drbv.de/adm/adm_themes/classic/icons/favicon.png" />
  <link rel="apple-touch-icon" href="http://drbv.de/adm/adm_themes/classic/icons/webclipicon.png" />
  <link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz' rel='stylesheet' type='text/css'>
  <style>
    body{
      font-family: 'Yanone Kaffeesatz',Helvetica,Arial,Sans-Serif ;
      font-size: 17px;
    }
  </style> 
  <link rel="stylesheet" type="text/css" href="https://drbv.de/adm/adm_themes/classic/css/system.css" />
</head>
     
<?php
// error_reporting (E_ALL);

date_default_timezone_set("Europe/Berlin");
 
// Datum festlegen
// $datum_unix = time() - 1296000 ; // 15 Tage
// $datum_unix = time() - 2419200 ; // 28 Tage
$datum_unix = time() - 3628800 ; // 42 Tage
// $datum_unix = time() - 5184000 ; // 60 Tage
$datum_1 = date("Y-m-d", $datum_unix);
$datum = date("Y-m-d", time() + 864000);

$nicht_vorhanden = 0;

// Startbuchnummer eingeben
if($tnrsel != ''){
  echo '<body onLoad="if (location.hash != \'#Ergebnissliste\') location.hash = \'#Ergebnissliste\';">';
} else {
  echo '<body>';               
}
//echo'<div align="center">';
echo'<h1 class="moduleHeadline">Judgement results</h1>';
echo'<div class="formLayout" id="profile_form">';
echo'  <div class="formHead">Profile information</div>';
echo'    <form method="POST" action="' . $_SERVER["PHP_SELF"] . '">';
echo'    <div class="formBody" align="left">';
echo'      <font face="Verdana" size="3" color="#000080">
             <p align="left">Please enter your five digit couple-ID and personal password:<br></p>
             <table>
               <tr align="left">
                 <td>Couple-ID:</td>
                 <td><input name="mitglied_nummer" size="5" maxlength="5" onfocus="this.removeAttribute(\'readonly\');" readonly autocomplete="new-password" value='.$_POST["mitglied_nummer"].'></td>
               </tr>
               <tr align="left">
                 <td>Password:</td>
                 <td><input name="passwort" type="password" maxlength="50" autocomplete="new-password" value='.$_POST["passwort"].'></td>
               </tr>
               <tr align="left">
                 <td>&nbsp;</td>
                 <td><input type="submit" name="senden" value="Show results!"/></td>
               </tr>            
             </table>    
           </font>';                                
echo'  </div>';//end div class="formBody"
echo'</div>';//end div class="formLayout"

if(!$_POST["mitglied_nummer"])
{
  $alert_stnr_fehlt = '<h3>Please enter your couple-ID!</h3>';
} else {
  $nicht_vorhanden = 0;
  // Startbuch finden
  // Suche in Datenbank (aktuelle Daten)
  // mem_rol_id = 103 ==> Startbuch Rock'n'Roll
  // mem_rol_id = 109 ==> Formationen
  // mem_rol_id = 110 ==> Startkarte Boogie-Woogie  
  $sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 103 AND mem_end > '" .  $datum . "'
            UNION
            SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 109 AND mem_end > '" .  $datum . "'
            UNION
            SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 110 AND mem_end > '" .  $datum . "'";

  $startbuch_auswahl = mysqli_query(ADMIDIOdb(), $sqlab);

  while($temp = mysqli_fetch_array($startbuch_auswahl)){
    $daten_satz   = $temp[0];
    $sqlab        = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $daten_satz";
    $suchergebnis = mysqli_query(ADMIDIOdb(), $sqlab);
    unset($vereinsname);
    unset($vereinsnummer);
    unset($team_name);
    unset($nn_herr);
    unset($vn_herr);
    unset($nn_dame);
    unset($vn_dame);
    unset($startklasse);
    unset($startbuch_nr);
    unset($gueltig); 
    unset($wertungsfreigabe);
       
    while($name = mysqli_fetch_array($suchergebnis)){                 
      $temp_id   = $name[0];
      $temp_wert = $name[1];
      if($temp_id == 28) $vereinsname = $temp_wert;
      if($temp_id == 29) $startklasse = $temp_wert;
      if($startklasse == 1)
          $startklasse = "RR_S";
      if($startklasse == 2)
          $startklasse = "RR_J";               
      if($startklasse == 3)
          $startklasse = "RR_C";
      if($startklasse == 4)
          $startklasse = "RR_B";
      if($startklasse == 5)
          $startklasse = "RR_A";
      if($startklasse == 6) 
          $startklasse = "F_RR_M";  
      if($startklasse == 7) 
          $startklasse = "F_RR_J";         
      if($startklasse == 8) 
          $startklasse = "F_RR_LF";
      if($startklasse == 9) 
          $startklasse = "F_RR_GF";         
      if($startklasse == 10) 
          $startklasse = "F_RR_ST";
      if($startklasse == 11) 
          $startklasse = "F_BW_M";                                
      if($startklasse == 12) 
          $startklasse = "BW_A";                                
      if($startklasse == 13) 
          $startklasse = "BW_B";                                
                                                              
      if($temp_id == 31)
         $nn_herr = $temp_wert;
      if($temp_id == 32)
         $vn_herr = $temp_wert;                    
      if($temp_id == 37)
         $nn_dame = $temp_wert;
      if($temp_id == 38)
         $vn_dame = $temp_wert;                    

      $datenwert = explode(':', $temp_wert);
      $datenwert = rtrim($datenwert[0]); 
       
      if($temp_id == 53)
         $vereinsnummer = $temp_wert;             
      if($temp_id == 54)
         $team_name = $temp_wert;        
      if($temp_id == 65 && $temp_wert == 1)            
        {
         $gueltig = 1;
        }     
                          
      if($temp_id == 66)
        $startbuch_nr = $temp_wert; 

      if($temp_id == 66 && $temp_wert == $_POST["mitglied_nummer"]){
        if($startklasse == "BW_A" || $startklasse == "BW_B"){
          if($nn_herr){   
            // Mitglied ID finden
            $startbuch_nr_h = $startbuch_nr;  
         
            $sqlab = "SELECT * FROM adm_user_data WHERE usd_usf_id = '66' AND usd_value = '" . $startbuch_nr . "' ";
            $bw_herr = mysqli_query(ADMIDIOdb(), $sqlab);
            $daten_herr = mysqli_fetch_array($bw_herr);
            
            // Starklasse auslesen
            $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $daten_herr[1] . "' ";
            $bw_startklasse = mysqli_query(ADMIDIOdb(), $sqlab);
            
            while($bw_start = mysqli_fetch_array($bw_startklasse)){
              // Jugendklasse
              if($bw_start[0] == 146 && $bw_start[1]) $bw_jun = 4;            
              if($bw_start[0] == 147 && $bw_jun == 4) {
              $dame_jun = $bw_start[1];        
              $dame_jk  = 1;
            
              $sqlab       = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_jun . "' ";
              $dame_jun    = mysqli_query(ADMIDIOdb(), $sqlab);
              $id_dame_jun = mysqli_fetch_row($dame_jun);

              $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_jun[0] . "' ORDER BY usd_usf_id";
              $daten_dame_schleife = mysqli_query(ADMIDIOdb(), $sqlab);
              while($daten_dame = mysqli_fetch_array($daten_dame_schleife)){
                if($daten_dame[0] == 28) $verein_dame = $daten_dame[1];           
                if($daten_dame[0] == 29) {
                  if($daten_dame[1] == 12) $startklasse_dame_j = "BW_A";
                  if($daten_dame[1] == 13) $startklasse_dame_j = "BW_B";     
                  if($startklasse != $startklasse_dame_j) {  
                     $gueltig = 0;
                     echo"<p>Paarung nicht m\ufffdglich!<p>";  
                  }                
                }
                if($daten_dame[0] == 37) $nn_dame = $daten_dame[1];     
                if($daten_dame[0] == 38) $vn_dame = $daten_dame[1];
                if($daten_dame[0] == 65 && $daten_dame[1] == 1) $gueltig_dame = 1;          
                if($daten_dame[0] == 66) $startbuch_nr_d = $daten_dame[1];          
              } // end while $daten_dame
              } // end if Jugendklasse
              
              // Hauptklasse  
              if($bw_start[0] == 138 && $bw_start[1]) $bw_main = 1;             
              if($bw_start[0] == 139 && $bw_main == 1) {  
              $dame_main = $bw_start[1];
              $dame_hk = 1;
            
              $sqlab = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_main . "' ";
              $dame_main = mysqli_query(ADMIDIOdb(), $sqlab);
              $id_dame_main = mysqli_fetch_row($dame_main);

              $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_main[0] . "' ORDER BY usd_usf_id";
              $daten_dame_schleife = mysqli_query(ADMIDIOdb(), $sqlab);
              while($daten_dame = mysqli_fetch_array($daten_dame_schleife)){          
                if($daten_dame[0] == 28) $verein_dame = $daten_dame[1];           
                if($daten_dame[0] == 29) {
                  if($daten_dame[1] == 12) $startklasse_dame = "BW_A";
                  if($daten_dame[1] == 13) $startklasse_dame = "BW_B";    
                  if($startklasse != $startklasse_dame) {  
                    $gueltig = 0;
                    echo"<p>Paarung nicht m\ufffdglich!<p>";  
                  }                
                }
                if($daten_dame[0] == 37) $nn_dame = $daten_dame[1];     
                if($daten_dame[0] == 38) $vn_dame = $daten_dame[1];
                if($daten_dame[0] == 65 && $daten_dame[1] == 1) $gueltig_dame = 1;          
                if($daten_dame[0] == 66) $startbuch_nr_d = $daten_dame[1];          
               } // end while $daten_dame 
              } // end if Hauptklasse

              // Seniorklasse
              if($bw_start[0] == 140 && $bw_start[1]) $bw_senior = 2;        
              if($bw_start[0] == 141 && $bw_senior == 2) {           
              $dame_senior = $bw_start[1];
              $dame_ok = 1;

              $sqlab = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_senior . "' ";
              $dame_senior = mysqli_query(ADMIDIOdb(), $sqlab);
              $id_dame_senior = mysqli_fetch_row($dame_senior);
            
              $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_senior[0] . "' ";
              $daten_dame_schleife = mysqli_query(ADMIDIOdb(), $sqlab);
              while($daten_dame = mysqli_fetch_array($daten_dame_schleife)) {
                if($daten_dame[0] == 28) $verein_dame = $daten_dame[1];           
                if($daten_dame[0] == 29) {
                  if($daten_dame[1] == 12) $startklasse_dame_s = "BW_A";
                  if($daten_dame[1] == 13) $startklasse_dame_s = "BW_B";    
                  if($startklasse != $startklasse_dame_s) {
                    $gueltig = 0;
                    echo"<p>Paarung nicht m\ufffdglich!<p>";
                  }            
                }  
                if($daten_dame[0] == 37) $nn_dame = $daten_dame[1];     
                if($daten_dame[0] == 38) $vn_dame = $daten_dame[1];
                if($daten_dame[0] == 65 && $daten_dame[1] == 1) $gueltig_dame = 1;             
                if($daten_dame[0] == 66) $startbuch_nr_d = $daten_dame[1];                
              } // end while $daten_dame
              } // end if Seniorklasse
              
              if($bw_start[0] == 148 && $bw_start[1] == 1){
               $dame_start = 1;
               $vereinsname = $verein_dame; 
              }            
            } // end while $bw_start
           
            if($dame_jk + $dame_hk + $dame_ok < 1)
              echo"<p><font color=ff0033><h4>Startbuch: $startbuch_nr_h Startklasse: $startklasse Verein: $vereinsname: Ung\ufffdltige Startmeldung!</h4><font color=000><p>";                   
              $bw_main_senior = $bw_jun + $bw_main + $bw_senior; 
          } // end if $nn_herr          
          $abbruch = 1;
        } else {
          $abbruch = 1;
        }
      }
    if($temp_id == 185) $wertungsfreigabe = $temp_wert;
    } // end while [suchergebnis]                  
    if($abbruch) break;
//    $nicht_vorhanden = 1;
//    print_r($nicht_vorhanden);echo" :nicht_vorhanden<br>";
//    print_r($startbuch_nr);echo" :startbuch_nr<br>";
             
  }// end while [startbuch_auswahl] Datenbanksuche
}// end $_POST

                 
// Ausgabe der Werte
// -----------------
$html_startbuch = '';
if($abbruch){                 
  $html_startbuch .= '<div class="formLayout" id="profile_form">';
  if($startklasse == "F_RR_J"  || 
     $startklasse == "F_RR_LF" || 
     $startklasse == "F_RR_GF" || 
     $startklasse == "F_RR_ST" || 
     $startklasse == "F_RR_M"  || 
     $startklasse == "F_BW_M") {
    $html_startbuch .= '<div class="formHead">Profile Rock\'n\'Roll formations</div>';     
  } elseif(substr($startklasse,0,3) == "BW_") {
    $html_startbuch .= '<div class="formHead">Profile Boogie-Woogie</div>';
  } else {
    $html_startbuch .= '<div class="formHead">Profile Rock\'n\'Roll</div>';
  } 
  $html_startbuch .= '<div class="formBody" align="left">';
  $html_startbuch .= '<div align="center"><table width=720 border=0 cellspacing=2 cellpadding=3 style="font-size: 11px;">';

  if(substr($startklasse,0,3) == "BW_"){
    $html_startbuch .= '<tr bgcolor=e9e9e9><td>Category</td><td colspan=2>' . $startklasse . " ";
    if($bw_main_senior == 1) $html_startbuch .= "Main";
    if($bw_main_senior == 2) $html_startbuch .= "Senior";
    if($bw_main_senior == 3) $html_startbuch .= "Main, Senior";    
    if($bw_main_senior == 4) $html_startbuch .= "Junior";
    if($bw_main_senior == 5) $html_startbuch .= "Junior, Main";
    $html_startbuch .= "</td></tr>";  
  } else {
    $html_startbuch .= '<tr bgcolor=e9e9e9><td width=170>Couple-ID</td><td colspan=4>' . $startbuch_nr . '</td></tr>';
    $html_startbuch .= '<tr bgcolor=e9e9e9><td>Name of dance club</td><td colspan=4>' . utf8_encode($vereinsname) . '</td></tr>';
    $html_startbuch .= '<tr bgcolor=e9e9e9><td>Category</td><td colspan=4>' . $startklasse . '</td></tr>';  
  }
  
  if(substr($startklasse,0,3) == "BW_"){
    $html_startbuch .= '<tr><td>Name male</td><td colspan=2>' . utf8_encode($vn_herr) . " " . utf8_encode($nn_herr) . '</td></tr>';
    $html_startbuch .= '<tr><td>Club male</td><td colspan=2>' . utf8_encode($vereinsname) . '</td></tr>';
    $html_startbuch .= '<tr><td width=170>ID male</td><td colspan=2>' . $startbuch_nr . $startk_bw_hr .'</td></tr>';
    $html_startbuch .= '<tr><td>ID male valid</td>';
    if($gueltig == 1) {
      $html_startbuch .= '<td colspan=2 bgcolor=33cc00>YES</td></tr>';
    } else {   
      $html_startbuch .= '<td colspan=2 bgcolor=ff0033>NO</td></tr>';
    }
    $html_startbuch .= '<tr><td>Name female</td><td colspan=2>' . utf8_encode($vn_dame) . " " . utf8_encode($nn_dame) . '</td></tr>';
    $html_startbuch .= '<tr><td>Club female</td><td colspan=2>' . utf8_encode($verein_dame) . '</td></tr>';
    $html_startbuch .= '<tr><td width=170>ID female</td><td colspan=2>' . utf8_encode($startbuch_nr_d) . $startk_bw_da .'</td></tr>';
    $html_startbuch .= '<tr><td>ID female valid</td>';
    if($gueltig_dame == 1) {
      $html_startbuch .= '<td colspan=2 bgcolor=33cc00>YES</td></tr>';
    } else {   
      $html_startbuch .= '<td colspan=2 bgcolor=ff0033>NO</td></tr>'; 
    }
  } else {
    $html_startbuch .= '<tr><td bgcolor=e9e9e9>Couple-ID valid</td>';
    if ($gueltig == 1) {
      $html_startbuch .= '<td colspan=4 bgcolor=33cc00>YES</td></tr>';
    } else {   
      $html_startbuch .= '<td colspan=4 bgcolor=ff0033>NO</td></tr>';
    }  
  }      
  $html_startbuch .= '</table></div>';  
  
  if($_POST["passwort"] != '' && $_POST["passwort"] == $wertungsfreigabe){
    $html_startbuch .= '<div align="center">';
    $html_startbuch .=  $html_rgbnss;    
    $html_startbuch .= '</div>';
  } else {
    $html_startbuch .=  '<h4>The password is wrong or not given!</h4>';
    if($gCurrentUser->isWebmaster()){
      //print_r($wertungsfreigabe);echo':wertungsfreigabe<br>';
      //print_r($_POST["passwort"]);echo':passwort<br>';
    }                              
  }
  
  if($tnrsel){
    $html_startbuch .=  $html_wrtg;
  }                               
} //end if $abbruch

echo $html_startbuch;
echo '</div>';
echo '</div><br>';                 

// link to module overall view
//if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false){
//  echo '<div style="text-align: center; margin-top: 5px;">
//          <a href="'.$g_root_path.'/adm_program/index.php">'.$gL10n->get('SYS_BACK_TO_MODULE_OVERVIEW').'</a>
//        </div>';
//}
echo '<div style="text-align: center; margin: 15px;">  
        <span style="font-size: 9pt; vertical-align: bottom;">&nbsp;&nbsp;&copy; 2017&nbsp;&nbsp;</span>
      </div>';  
                
// Startbuch nicht vorhanden
if($startbuch_nr != $_POST["mitglied_nummer"]){
  echo '<h4>This couple-ID is not known!<br>Please give it another try!</h4>';
}
  
?>
</body>
