<?php
/******************************************************************************
 * Profil mit Wertung Wertungsrichter anzeigen
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * user_id: zeigt das Profil der uebergebenen user_id an
 *          (wird keine user_id uebergeben, dann Profil des eingeloggten Users anzeigen)
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/drbv_funktionen.php');
require_once('../../system/classes/table_roles.php');
require_once '../../../../vendor/autoload.php';
  
//Use timezone
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, "de_DE.utf8");
$monate = array(1=>"Januar",
                2=>"Februar",
                3=>"M&auml;rz",
                4=>"April",
                5=>"Mai",
                6=>"Juni",
                7=>"Juli",
                8=>"August",
                9=>"September",
               10=>"Oktober",
               11=>"November",
               12=>"Dezember");
    
// Initialize and check the parameters
$getMode   = "";

$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getTnrNr   = admFuncVariableIsValid($_GET, 'tnrsel', 'numeric', 0);  
$getSelYr   = admFuncVariableIsValid($_GET, 'selyr', 'numeric', 0);  
$getMode    = admFuncVariableIsValid($_GET, 'mode');
  
if($getSelYr == 0){$getSelYr=date(Y);}  
unset($_SESSION['turnier']);

if(!$getTnrNr) $getTnrNr = $_SESSION["turnier"];  
if($_GET['tn']) $_SESSION['turniernummer'] = $_GET['tn'];
  
// create user object
$user = new User($gDb, $gProfileFields, $getUserId);
  
unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

// Funktion Akrowertung in %
// -------------------------  
function akrowrtgproz($wert,$akrovw){
  global $getView;
  $wertproz = round($wert * 100 / $akrovw);
  if($getView == 'paare'){
    $wertproz = $wertproz;
  } else {
    $wertproz = 100-$wertproz; 
  } 
  return $wertproz;
}

// Funktion Abweichung
// -------------------  
function abweichung($mittelwert,$wert){
  $abweichung = $mittelwert - $wert;
  $abweichung = $abweichung * $abweichung;
  return sqrt($abweichung);
}
  
// Funktion Mittelwert
// -------------------  
function mwert($wert){  
  if(count($wert) == 2 || count($wert) == 3){
    $mittelwert = array_sum($wert)/count($wert);
  } else {
    $mittelwert = (array_sum($wert)-min($wert)-max($wert))/(count($wert)-2);  
  }  
  return $mittelwert;
}

// Funktion WrtgCountTnz
// ---------------------  
function wrtgcounttnz($wrichtername){
  global $wrichter_wrtg_tnz_cnt_key;
  
  $wrtgcnt = array_count_values($wrichter_wrtg_tnz_cnt_key[$wrichtername]);
  return ($wrtgcnt);
}

// Funktion WrtgCountAkr
// ---------------------  
function wrtgcountakr($wrichtername){
  global $wrichter_wrtg_akr_cnt_key;
  
  $wrtgcnt = array_count_values($wrichter_wrtg_akr_cnt_key[$wrichtername]);
  return ($wrtgcnt);
}
  
// Funktion Turniere gewertet
// --------------------------  
function turniere_gewertet($jahr, $lizenznr){
  
  $trnr_gewertet   = array();
  $anzahl_gewertet = 0;
  
  $sqlab = 'SELECT turniernummer,lizenznummer,name FROM wertungsrichter WHERE lizenznummer='.$lizenznr.'';
  $turnier_gewertet = mysqli_query(DRBVdb(), $sqlab);

  while($temp = mysqli_fetch_array($turnier_gewertet)){
    $sqlab     = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$temp[0].' AND YEAR(datum) = "'.$jahr.'"'; 
    $turnier   = mysqli_query(DRBVdb(), $sqlab);
    $aktuell   = mysqli_fetch_row($turnier);
    
    if($jahr != '' && substr($aktuell[2],0,4) == $jahr){
//      $trnr_gewertet[] = utf8_encode($aktuell[1]).' ('.$aktuell[0].')';
      $trnr_gewertet[] = '('.$aktuell[0].') '.utf8_encode($aktuell[1]);
      $anzahl_gewertet++;
    }
  }
  //print_r($trnr_gewertet);echo' :trnr_gewertet<br>';
  //print_r($anzahl_gewertet);echo' :anzahl_gewertet<br>';
  return array($trnr_gewertet,$anzahl_gewertet);
}

// Turnierinfo aus DB suchen:
// --------------------------  
$sqlab       = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$getTnrNr.''; 
$turniersel  = mysqli_query(DRBVdb(), $sqlab);
$turnierinfo = mysqli_fetch_row($turniersel);    
$getTnrName  = utf8_encode($turnierinfo[1]);      
$getTnrDatum = new DateTime($turnierinfo[2]);
    
// HTML generieren für alle Runden und Paare einer Turniernummer & Startklasse
// ---------------------------------------------------------------------------  
function gen_html($getTnrNr,$getStartkl){
  global $gCurrentUser, $user_liznr, $getView, $getMode, 
         $wrichter_wrtg_tnz_cnt_key, $wrichter_wrtg_akr_cnt_key;
  $html = '';
  $text = '';
  $csv  = '';
  $pdf  = '';

  // Wertungsformate zuordnen
  // ------------------------
  if($getStartkl == "RR_S" || $getStartkl == "RR_J" || $getStartkl == "RR_C" || $getStartkl == "RR_B" || $getStartkl == "RR_A"){
     $wrtg_rr = TRUE; 
  }  
  if($getStartkl == "BW_MA" || $getStartkl == "BW_MB" || $getStartkl == "BW_SA" || $getStartkl == "BW_SB" || $getStartkl == "BW_JA"){  
     $wrtg_bw = TRUE; 
  }
  if($getStartkl == "F_RR_GF" || $getStartkl == "F_RR_LF" ||
     $getStartkl == "F_RR_ST" || $getStartkl == "F_RR_J"  || 
     $getStartkl == "F_RR_M"  || $getStartkl == "F_BW_M"){
     $wrtg_fo = TRUE;
  }    
  if($getStartkl == "RR_S"    || $getStartkl == "F_RR_GF" || $getStartkl == "F_RR_LF" ||
     $getStartkl == "F_RR_ST" || $getStartkl == "F_RR_J"  || $getStartkl == "F_BW_M"  ||
     $getStartkl == "BW_MA"   || $getStartkl == "BW_MB"   || $getStartkl == "BW_SA"   || 
     $getStartkl == "BW_SB"   || $getStartkl == "BW_JA"){
     $startklasse_mit_akro = FALSE;
  } else {
     $startklasse_mit_akro = TRUE;  
  }  
  
  // Tanzrunden suchen
  // -----------------
  $sqlab = 'SELECT rt_id_tlp, startklasse, runde FROM rundentab WHERE turniernummer = "'.$getTnrNr.'" AND startklasse = "'.$getStartkl.'"';
  $runden = mysqli_query(DRBVdb(),$sqlab);
  $tanzrunden[0] = array();
  $sel_tanzrnd   = array();  

  while($temp = mysqli_fetch_array($runden)){ 
    $tanzrunden[$temp['rt_id_tlp']] = $temp;
    $sel_tanzrnd[] = $temp[0];
  }
  
  // Paare suchen
  // ------------  
  $sqlab     = 'SELECT * FROM paare WHERE turniernummer = "'.$getTnrNr.'" AND startklasse = "'.$getStartkl.'" ORDER BY platz';
  $paare_ges = mysqli_query(DRBVdb(),$sqlab);
  $paare[0]  = array();  
    
  while($temp = mysqli_fetch_array($paare_ges)){
    $paare[$temp['paar_id_tlp']] = $temp;
    //print_r($paare[$temp['paar_id_tlp']]);echo' :paare<br>';
    
    // Tanzpaarpunkteergebnis in Datenbank suchen
    $sqlab          = 'SELECT * FROM majoritaet WHERE turniernummer='.$getTnrNr.' AND TP_ID='.$temp["paar_id_tlp"].' AND RT_ID='.$temp["RT_ID_Ausgeschieden"].'';
    $ergebnisDB_pkt =  mysqli_query(DRBVdb(), $sqlab);
    
    while($rundenDB_pkt = mysqli_fetch_array($ergebnisDB_pkt)){
      //print_r($rundenDB_pkt);echo' rundenDB_pkt<br>';
      $ergDB_pkt_ft = number_format($rundenDB_pkt[WR1],2);
      $ergDB_pkt_bl = number_format($rundenDB_pkt[WR1],2);
      $ergDB_pkt_ta = number_format($rundenDB_pkt[WR2],2);    
      $ergDB_pkt_ak = number_format($rundenDB_pkt[WR3],2);    
      $ergDB_pkt_bs = number_format($rundenDB_pkt[WR5] + $rundenDB_pkt[WR4],2);    
      $ergDB_pkt_su = number_format($rundenDB_pkt[WR7],2);
      
      if($wrtg_fo){    
        //Header einfügen
        if($temp[platz] == 1){
          $html .= '<tr><td></td><td></td><td></td><td align=center><b>Pkt:<b></td></tr>';
          $csv  .= '"Platz";"Teamname";"Verein";"Pkt.";'."\r\n"; 
        }
        //Trennlinie bei Rundenwechsel einfügen
        $rndt  = $temp[RT_ID_Ausgeschieden];
        if($rndt != $rndt_last && $temp[platz] > 1){
          $html .= '<tr><td colspan=4><hr class="resulttab"></td><tr>';
          $pdf  .= '<tr><td colspan=4><hr class="resulttab"></td><tr>';
        }
        $rndt_last = $rndt;        
        //tablerow für Platz x bauen         
        $html .= '
          <tr>
            <td align=right><b>'.$temp[platz].'.</b></td>
            <td width=40%>'.utf8_encode($temp["team"]).'</td>
            <td width=40%><i>'.utf8_encode($temp["verein"]).'</i></td>
            <td align=center><b>'.$ergDB_pkt_su.'</b></td>
          </tr>';
        $text .= $temp[platz].'. '.utf8_encode($temp["team"]).' ('.utf8_encode($temp["verein"]).')<br>';        
        $csv  .= '"'.$temp[platz].'";"'.utf8_encode($temp["team"]).'";"'.utf8_encode($temp["verein"]).'";"'.number_format($ergDB_pkt_su,2,',','.').'";'."\r\n";        
        $pdf  .= '
          <tr>
            <td width=7% align=left><b>'.$temp[platz].'.</b></td>
            <td width=35%>'.utf8_encode($temp["team"]).'</td>
            <td width=50%><i>'.utf8_encode($temp["verein"]).'</i></td>
            <td width=8% align=right><b>'.$ergDB_pkt_su.'</b></td>
          </tr>';        
      } 
      if($wrtg_rr){
        //Header einfügen
        if($temp[platz] == 1){
          $html .= '<tr><td></td><td></td><td></td><td align=center><b>';
          if($temp[platz] < 8 && ($getStartkl == "RR_B" || $getStartkl == "RR_A")){
            $html .= 'FT:';
          }
          $html .= '</b></td><td align=center><b>';
          if($startklasse_mit_akro){
            $html .= 'TWR:';
          }
          $html .= '</b></td><td align=center><b>';
          if($startklasse_mit_akro){
            $html .= 'AWR:';
          }
          $html .= '</b></td><td align=center><b>Pkt:</b></td></tr>';
          $csv  .= '"Platz";"Dame";"Herr";"Verein";"FT";"TWR";"AWR";"Pkt.";'."\r\n"; 
        }
        //Trennlinie bei Rundenwechsel einfügen
        $rndt  = $temp[RT_ID_Ausgeschieden];
        if($rndt != $rndt_last && $temp[platz] > 1){
          $html .= '<tr><td colspan=7><hr class="resulttab"></td><tr>';
        }
        $rndt_last = $rndt;
        //tablerow für Platz x bauen 
        $html .= '<tr><td align=left><b>'.$temp[platz].'.</b></td><td width=40%>'.utf8_encode($temp["dame"]).' - '.utf8_encode($temp["herr"]).'</td><td width=40%><i>'.utf8_encode($temp["verein"]).'</i></td><td align=center>';
        if($temp[platz] < 8 && ($getStartkl == "RR_B" || $getStartkl == "RR_A")){
          $html .= $ergDB_pkt_ft;
        }
        $html .= '</td><td align=center>';
        if($startklasse_mit_akro){
          $html .= $ergDB_pkt_ta;
        }
        $html .= '</td><td align=center>';
        if($startklasse_mit_akro){
          $html .= $ergDB_pkt_ak; 
        }        
        $html .= '</td><td align=center><b>'.$ergDB_pkt_su.'</b></td></tr>';
        $text .= $temp[platz].'. '.utf8_encode($temp["dame"]).' - '.utf8_encode($temp["herr"]).' ('.utf8_encode($temp["verein"]).')<br>';        
        $csv  .= '"'.$temp[platz].'";"'
                    .utf8_encode($temp["dame"]).'";"'
                    .utf8_encode($temp["herr"]).'";"'
                    .utf8_encode($temp["verein"])
                    .'";"'.number_format($ergDB_pkt_ft,2,',','.').'";"'.number_format($ergDB_pkt_ta,2,',','.').'";"'.number_format($ergDB_pkt_ak,2,',','.').'";"'.number_format($ergDB_pkt_su,2,',','.').'";'."\r\n";        
        $pdf = $html;
      }
      if($wrtg_bw){
        //Header einfügen
        if($temp[platz] == 1){
          $html .= '<tr><td></td><td></td><td></td><td align=center>';
          if($temp[platz] < 8 && ($getStartkl == "BW_MA" || $getStartkl == "BW_SA")){
            $html .= 'LR:';
          }
          $html .= '</td><td align=center>';
          if($temp[platz] < 8 && ($getStartkl == "BW_MA" || $getStartkl == "BW_SA")){
            $html .= 'SR:';
          }
          $html .= '</td><td align=center><b>Pkt:<b></td></tr>';
          $csv  .= '"Platz";"Dame";"Herr";"Verein";"LR";"SR";"Pkt.";'."\r\n"; 
        }
        //Trennlinie bei Rundenwechsel einfügen
        $rndt  = $temp[RT_ID_Ausgeschieden];
        if($rndt != $rndt_last && $temp[platz] > 1){
          $html .= '<tr><td colspan=6><hr class="resulttab"></td><tr>';
        }
        $rndt_last = $rndt;
        //tablerow für Platz x bauen 
        $html .= '
          <tr>
            <td align=right><b>'.$temp[platz].'.</b></td>
            <td width=35%>'.utf8_encode($temp["dame"]).' - '.utf8_encode($temp["herr"]).'</td>
            <td width=50%><i>'.utf8_encode($temp["verein"]).'</i></td>
            <td align=center>&nbsp;';
        if($temp[platz] < 8 && ($getStartkl == "BW_MA" || $getStartkl == "BW_SA")){
          if($ergDB_pkt_bl != "0.00"){
            $html .= $ergDB_pkt_bl;
          }
        }
        $html .= '&nbsp;</td><td align=center>&nbsp;';
        if($temp[platz] < 8 && ($getStartkl == "BW_MA" || $getStartkl == "BW_SA")){
          if($ergDB_pkt_bl != "0.00"){
            $html .= $ergDB_pkt_bs;
          }
        }
        $html .= '&nbsp;</td><td align=center>&nbsp;<b>'.$ergDB_pkt_su.'</b>&nbsp;</td></tr>';
        $text .= $temp[platz].'. '.utf8_encode($temp["dame"]).' - '.utf8_encode($temp["herr"]).' ('.utf8_encode($temp["verein"]).')<br>';                
        $csv  .= '"'.$temp[platz].'";"'
                    .utf8_encode($temp["dame"]).'";"'
                    .utf8_encode($temp["herr"]).'";"'
                    .utf8_encode($temp["verein"])
                    .'";"'.number_format($ergDB_pkt_bl,2,',','.').'";"'.number_format($ergDB_pkt_bs,2,',','.').'";"'.number_format($ergDB_pkt_su,2,',','.').'"'."\r\n";                
        $pdf = $html;
      }
    }    
  }
  if($getMode=="printview"){
    return $text;          
  } elseif($getMode=="csv"){  
    return $csv;          
  } elseif($getMode=="pdf"){  
    return $pdf;          
  } else {  
    return $html;          
  }
}//end function gen_html()        

if($getMode == 'printview'){
  echo'
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
    <head>
      <link rel="stylesheet" type="text/css" href="https://drbv.de/adm/adm_themes/classic/css/print.css" />    
    </head>
    <body class="bodyPrintResult">';  
  echo'Ergebnisliste '.$_SESSION["turniername"].'<br>';
  if(gen_html($getTnrNr,'RR_A')){
    echo'<br>--------<br>A-Klasse<br>--------<br>';
    echo gen_html($getTnrNr,'RR_A');
  }
  if(gen_html($getTnrNr,'RR_B')){
    echo'<br>--------<br>B-Klasse<br>--------<br>';
    echo gen_html($getTnrNr,'RR_B');
  }
  if(gen_html($getTnrNr,'RR_C')){
    echo'<br>--------<br>C-Klasse<br>--------<br>';
    echo gen_html($getTnrNr,'RR_C');
  }
  if(gen_html($getTnrNr,'RR_J')){
    echo'<br>--------------<br>Juniorenklasse<br>--------------<br>';
    echo gen_html($getTnrNr,'RR_J');
  }
  if(gen_html($getTnrNr,'RR_S')){
    echo'<br>-------------<br>Sch&uuml;lerklasse<br>-------------<br>';
    echo gen_html($getTnrNr,'RR_S');
  }
  if(gen_html($getTnrNr,'F_RR_M')){
    echo'<br>----------------------------<br>Rock\'n\'Roll Formation Master<br>----------------------------<br>';
    echo gen_html($getTnrNr,'F_RR_M');     
  }
  if(gen_html($getTnrNr,'F_RR_J')){
    echo'<br>----------------------------<br>Rock\'n\'Roll Formation Jugend<br>----------------------------<br>';
    echo gen_html($getTnrNr,'F_RR_J');     
  }
  if(gen_html($getTnrNr,'F_RR_LF')){
    echo'<br>--------------------------<br>Rock\'n\'Roll Formation Lady<br>--------------------------<br>';
    echo gen_html($getTnrNr,'F_RR_LF');     
  }
  if(gen_html($getTnrNr,'F_RR_GF')){
    echo'<br>--------------------------<br>Rock\'n\'Roll Formation Girl<br>--------------------------<br>';
    echo gen_html($getTnrNr,'F_RR_GF');     
  }
  if(gen_html($getTnrNr,'F_RR_ST')){
    echo'<br>------------------------------<br>Rock\'n\'Roll Formation Showteam<br>------------------------------<br>';
    echo gen_html($getTnrNr,'F_RR_ST');     
  }
  if(gen_html($getTnrNr,'F_BW_M')){
    echo'<br>------------------------------<br>Boogie-Woogie Formation Master<br>------------------------------<br>';
    echo gen_html($getTnrNr,'F_BW_M');     
  }
  if(gen_html($getTnrNr,'BW_MA')){
    echo'<br>--------------------<br>Boogie-Woogie Main A<br>--------------------<br>';
    echo gen_html($getTnrNr,'BW_MA');     
  }
  if(gen_html($getTnrNr,'BW_SA')){
    echo'<br>----------------------<br>Boogie-Woogie Senior A<br>----------------------<br>';
    echo gen_html($getTnrNr,'BW_SA');     
  }
  if(gen_html($getTnrNr,'BW_JA')){
    echo'<br>--------------------<br>Boogie-Woogie Junior<br>--------------------<br>';
    echo gen_html($getTnrNr,'BW_JA');     
  }
  if(gen_html($getTnrNr,'BW_MB')){
    echo'<br>--------------------<br>Boogie-Woogie Main B<br>--------------------<br>';
    echo gen_html($getTnrNr,'BW_MB');     
  }
  if(gen_html($getTnrNr,'BW_SB')){
    echo'<br>----------------------<br>Boogie-Woogie Senior B<br>----------------------<br>';
    echo gen_html($getTnrNr,'BW_SB');     
  }
  
  echo'
    </body>
    </html>';
  die;  
}

if($getMode == 'csv'){  
  header('Content-Type: text/comma-separated-values; charset=iso-8859-1');
  header('Content-Disposition: attachment; filename="Ergebnisliste_'.$getTnrNr.'.csv"');    
  //neccessary for IE, because without it the download with SSL has problems
  header('Cache-Control: private');
  header('Pragma: public');  
  echo'"Ergebnisliste '.utf8_decode($_SESSION["turniername"]).'";'."\r\n";
  if(gen_html($getTnrNr,'RR_A')){
    echo '"A-Klasse";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'RR_A'));
  }
  if(gen_html($getTnrNr,'RR_B')){
    echo '"B-Klasse";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'RR_B'));
  }
  if(gen_html($getTnrNr,'RR_C')){
    echo '"C-Klasse";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'RR_C'));
  }
  if(gen_html($getTnrNr,'RR_J')){
    echo '"Juniorenklasse";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'RR_J'));
  }
  if(gen_html($getTnrNr,'RR_S')){
    echo utf8_decode("Schülerklasse").';'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'RR_S'));
  }
  if(gen_html($getTnrNr,'F_RR_M')){
    echo'"Rock\'n\'Roll Formation Master";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_RR_M'));     
  }
  if(gen_html($getTnrNr,'F_RR_J')){
    echo'"Rock\'n\'Roll Formation Jugend";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_RR_J'));     
  }
  if(gen_html($getTnrNr,'F_RR_LF')){
    echo'"Rock\'n\'Roll Formation Lady";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_RR_LF'));     
  }
  if(gen_html($getTnrNr,'F_RR_GF')){
    echo'"Rock\'n\'Roll Formation Girl";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_RR_GF'));     
  }
  if(gen_html($getTnrNr,'F_RR_ST')){
    echo'"Rock\'n\'Roll Formation Showteam";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_RR_ST'));     
  }
  if(gen_html($getTnrNr,'F_BW_M')){
    echo'"Boogie-Woogie Formation Master";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'F_BW_M'));     
  }
  if(gen_html($getTnrNr,'BW_MA')){
    echo'"Boogie-Woogie Main A";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'BW_MA'));     
  }
  if(gen_html($getTnrNr,'BW_SA')){
    echo'"Boogie-Woogie Senior A";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'BW_SA'));     
  }
  if(gen_html($getTnrNr,'BW_JA')){
    echo'"Boogie-Woogie Junior";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'BW_JA'));     
  }
  if(gen_html($getTnrNr,'BW_MB')){
    echo'"Boogie-Woogie Main B";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'BW_MB'));     
  }
  if(gen_html($getTnrNr,'BW_SB')){
    echo'"Boogie-Woogie Senior B";'."\r\n";
    echo utf8_decode(gen_html($getTnrNr,'BW_SB'));     
  }
  die;
}

if($getMode == 'pdf'){  
  $pdf_output  = '';
  $pdf_output .= '<img src="'.THEME_PATH.'/images/DRBV_DTV_Logo.png" width="300" height="66"><br><br>';
  $pdf_output .= '<b>Ergebnisliste '.$_SESSION["turniername"].'</b><br><br>';
  if(gen_html($getTnrNr,'RR_A')){
    $pdf_output .= "<b>A-Klasse</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'RR_A').'</table><br>';
  }
  if(gen_html($getTnrNr,'RR_B')){
    $pdf_output .= "<b>B-Klasse</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'RR_B').'</table><br>';
  }
  if(gen_html($getTnrNr,'RR_C')){
    $pdf_output .= "<b>C-Klasse</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'RR_C').'</table><br>';
  }
  if(gen_html($getTnrNr,'RR_J')){
    $pdf_output .= "<b>Juniorenklasse</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'RR_J').'</table><br>';
  }
  if(gen_html($getTnrNr,'RR_S')){
    $pdf_output .= "<b>Schülerklasse</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'RR_S').'</table><br>';
  }
  if(gen_html($getTnrNr,'F_RR_M')){
    $pdf_output .= "<b>Rock'n'Roll Formation Master</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_RR_M').'</table><br>';     
  }
  if(gen_html($getTnrNr,'F_RR_J')){
    $pdf_output .= "<b>Rock'n'Roll Formation Jugend</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_RR_J').'</table><br>';     
  }
  if(gen_html($getTnrNr,'F_RR_LF')){
    $pdf_output .= "<b>Rock'n'Roll Formation Lady</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_RR_LF').'</table><br>';     
  }
  if(gen_html($getTnrNr,'F_RR_GF')){
    $pdf_output .= "<b>Rock'n'Roll Formation Girl</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_RR_GF').'</table><br>';     
  }
  if(gen_html($getTnrNr,'F_RR_ST')){
    $pdf_output .= "<b>Rock'n'Roll Formation Showteam</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_RR_ST').'</table><br>';     
  }
  if(gen_html($getTnrNr,'F_BW_M')){
    $pdf_output .= "<b>Boogie-Woogie Formation Master</b><br>";
    $pdf_output .= '<table width=100%>'.gen_html($getTnrNr,'F_BW_M').'</table><br>';     
  }
  if(gen_html($getTnrNr,'BW_MA')){
    $pdf_output .= "<b>Boogie-Woogie Main A</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'BW_MA').'</table><br>';     
  }
  if(gen_html($getTnrNr,'BW_SA')){
    $pdf_output .= "<b>Boogie-Woogie Senior A</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'BW_SA').'</table><br>';     
  }
  if(gen_html($getTnrNr,'BW_JA')){
    $pdf_output .= "<b>Boogie-Woogie Junior</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'BW_JA').'</table><br>';     
  }
  if(gen_html($getTnrNr,'BW_MB')){
    $pdf_output .= "<b>Boogie-Woogie Main B</b><br>";
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'BW_MB').'</table><br>';     
  }
  if(gen_html($getTnrNr,'BW_SB')){
    $pdf_output .= "<b>Boogie-Woogie Senior B</b><br>";;
    $pdf_output .= '<table width=100% style="font-size:12px;">'.gen_html($getTnrNr,'BW_SB').'</table><br>';     
  }
  //print_r($pdf_output);echo' :pdf_output<br>';
    
  $dateiname = 'Ergebnisliste_'.$getTnrNr.'.pdf';            
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($pdf_output);
  $mpdf->Output($dateiname,'I');      
  die;
}
  
// Html-Kopf ausgeben
$gLayout['header'] = '
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/modules/profile/profile.js"></script>
    <script type="text/javascript">       
    <!--
        var profileJS = new profileJSClass();
            profileJS.deleteRole_ConfirmText   = \''.$gL10n->get('ROL_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.deleteFRole_ConfirmText   = \''.$gL10n->get('ROL_LINK_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.changeRoleDates_ErrorText = \''.$gL10n->get('ROL_CHANGE_ROLE_DATES_ERROR').'\';
            profileJS.setBy_Text        = \''.$gL10n->get('SYS_SET_BY').'\';
            profileJS.usr_id = '.$user->getValue('usr_id').';
            
            function showHideMembershipInformation(element)
            {
          id = "#" + element.attr("id") + "_Content";

          if($(id).css("display") == "none") {
              $(id).show("fast");
          }
          else {
              $(id).hide("fast");
          }
            }

      $(document).ready(function() {
        profileJS.init();
        $("a[rel=\'lnkDelete\']").colorbox({rel:\'nofollow\', scrolling:false, onComplete:function(){$("#admButtonNo").focus();}});
          $(".admMemberInfo").click(function () { showHideMembershipInformation($(this)) });
      });
      
    //-->
    </script>
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadeakrovr\').click(function(){$(\'#refakrovr\').toggle(\'slow\');
      })
      });
    </script>
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadeakrozr\').click(function(){$(\'#refakrozr\').toggle(\'slow\');
      })
      });
    </script>         
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadeakroer\').click(function(){$(\'#refakroer\').toggle(\'slow\');
      })
      });
    </script>         
    <style>
      .resulttab {
        font-size: 0.85em;
      }
      p.resulttab {
        font-size: 0.95em;
      }
      h1.resulttab {
        font-size: 1.1em;
        text-align: left;
        color: black; 
      }
      hr.resulttab {
        width: 98%;
        border-color:#ddd;
        background:#ddd;
        height:1px;
      }   
    </style>';
  
require(SERVER_PATH. '/adm_program/system/overall_header.php');          
    
echo '  
<div class="formLayout" id="profile_form" width="100px">
  <div class="formHead">Turniergebnisse</div>
  <div class="formBody">';
                 
  // *******************************************************************************
  // Userdaten-Block
  // *******************************************************************************
    
  if($current_year == ''){$current_year=date(Y);}    
  $current_year = $getSelYr;
  echo'  
    <div class="pagination">';
      if($current_year > 2016){
        echo '<a href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel=0&selyr='.($current_year-1).'" class="last">&laquo;&laquo;</a>'; 
      } else {
        echo '<span class="last">&laquo;&laquo;</span>';  
      }
      echo '<span class="pagihead">&nbsp;'.$getSelYr.'&nbsp;</span>';
      if($current_year >= date(Y)){
        echo '<span class="last">&raquo;&raquo;</span>';  
      } else {
        echo '<a href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel=0&selyr='.($current_year+1).'" class="next">&raquo;&raquo;</a>';
      }
  echo'</div>'; 
  
  $sqlab = 'SELECT turniernummer,turniername,datum,veranstaltung_ort FROM Turnier WHERE YEAR(datum) = "'.$current_year.'" ORDER BY datum DESC';
  $turnier = mysqli_query(DRBVdb(), $sqlab);
          
  $option_turnier = '';
  while($temp = mysqli_fetch_array($turnier)){
    $option_turnier .= ''.date("d.m.Y", strtotime($temp[2])).' - <a href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel='.$temp[0].'&selyr='.$current_year.'">'.utf8_encode($temp[1]).'</a></br>';
    $trnr_name_key[$temp[0]] = utf8_encode($temp[1]);
    $trnr_date_key[$temp[0]] = utf8_encode($temp[2]);
    $trnr_ort_key[$temp[0]]  = utf8_encode($temp[3]);
  }
                        
  echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">Turnierauswahl:</div>  
    </div>
      <form action="'.$_SERVER["PHP_SELF"].'" method=post>
      <div class="groupBoxBody">
      <ul class="formFieldList">
         '.$option_turnier.'
      </ul>      
      </div>
    </form>
    </div>';
    //class="groupBox" end  
  
  echo'      
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">Turnierergebnis: <i><span style="color:#41a0fa;">'.$trnr_name_key[$getTnrNr].'</i></span></div>';
  if($getTnrNr!=0){
    echo'
      <div style="text-align: right;">                 
        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel='.$getTnrNr.'&mode=printview" target="_blank" title="Ergebnisliste als Druckansicht"><img src="'.THEME_PATH.'/icons/print.png" alt="Druckansicht" /></a>
        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel='.$getTnrNr.'&mode=csv" target="_blank" title="Ergebnisliste mit Punkten als CSV-Datei" ><img src="'.THEME_PATH.'/icons/database_save.png" alt="CSV Download" /></a>               
        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel='.$getTnrNr.'&mode=pdf" target="_blank" title="Ergebnisliste mit Punkten als PDF-Datei" ><img src="'.THEME_PATH.'/icons/pdf.png" alt="PDF Download" /></a> 
      </div>';
  }  
  $nurort = explode(" ", $trnr_ort_key[$getTnrNr]);
  array_shift($nurort);    
  echo'                                                                                                                                                                                                    
    </div>
      <form action="'.$g_root_path.'/adm_program/modules/results/turnierergebnisse.php?tnrsel='.$getTnrNr.'" method="post">
      <div class="groupBoxBody">';
    
  $suchdie = array("Meister","meister","NDM","SDM","LM","DM","Tanzgala");
  $hitdie  = false;
  $suchein = array("Sportturnier","Sport-/Präambelturnier","Präambel-Turnier");
  $hitein  = false;
  foreach($suchdie as $hit){
    if(strpos($trnr_name_key[$getTnrNr], $hit) !== false){
      if($hit == "LM" && strpos($trnr_name_key[$getTnrNr], $hit) > 0){
        $hitdie = false;
      } else {
        $hitdie = true;      
      }        
    }
  }
  foreach($suchein as $hit){
    if(strpos($trnr_name_key[$getTnrNr], $hit) !== false){
      $hitein = true;
    }
  }
  if($hitdie){
    $artikel = "die";
  } elseif($hitein) {
    $artikel = "ein";
  } else {
    $artikel = "der";
  }          
  
  if($getTnrNr==0){
    echo'<p class="resulttab">Noch kein Turnier ausgewählt!</p>';  
  } else {
    echo'<p class="resulttab">Am '.date("d. ",strtotime($trnr_date_key[$getTnrNr])).$monate[date("n",strtotime($trnr_date_key[$getTnrNr]))].
               date(" Y", strtotime($trnr_date_key[$getTnrNr])).' fand '.$artikel.' '.
               str_replace("Großer","Große",$trnr_name_key[$getTnrNr]).' in '.
               implode(" ", $nurort).' statt.<br>Hier sind die Ergebnisse:</p>';
    $_SESSION["turniername"] = $trnr_name_key[$getTnrNr];  
  }
  echo'<table border=0 class="resulttab">';
  if(gen_html($getTnrNr,'RR_A')){
    echo'<tr><td colspan=7><h1 class="resulttab">A-Klasse</h1></td></tr>';
    echo gen_html($getTnrNr,'RR_A');     
  }
  if(gen_html($getTnrNr,'RR_B')){
    echo'<tr><td colspan=7><br><br><h1 class="resulttab">B-Klasse</h1></td></tr>';
    echo gen_html($getTnrNr,'RR_B') ;
  }
  if(gen_html($getTnrNr,'RR_C')){
    echo'<tr><td colspan=7><br><br><h1 class="resulttab">C-Klasse</h1></td></tr>';
    echo gen_html($getTnrNr,'RR_C') ;
  }
  if(gen_html($getTnrNr,'RR_J')){
    echo'<tr><td colspan=7><br><br><h1 class="resulttab">Juniorenklasse</h1></td></tr>';
    echo gen_html($getTnrNr,'RR_J') ;
  }
  if(gen_html($getTnrNr,'RR_S')){
    echo'<tr><td colspan=7><br><br><h1 class="resulttab">Sch&uuml;lerklasse</h1></td></tr>';
    echo gen_html($getTnrNr,'RR_S');
  }
  if(gen_html($getTnrNr,'F_RR_M')){
    echo'<tr><td colspan=4><br><h1 class="resulttab">Rock\'n\'Roll Formation Master</h1></td></tr>';
    echo gen_html($getTnrNr,'F_RR_M');     
  }
  if(gen_html($getTnrNr,'F_RR_J')){
    echo'<tr><td colspan=4><br><br><h1 class="resulttab"><b>Rock\'n\'Roll Formation Jugend</h1></td></tr>';
    echo gen_html($getTnrNr,'F_RR_J');     
  }
  if(gen_html($getTnrNr,'F_RR_LF')){
    echo'<tr><td colspan=4><br><br><h1 class="resulttab"><b>Rock\'n\'Roll Formation Lady</h1></td></tr>';
    echo gen_html($getTnrNr,'F_RR_LF');     
  }
  if(gen_html($getTnrNr,'F_RR_GF')){
    echo'<tr><td colspan=4><br><br><h1 class="resulttab"><b>Rock\'n\'Roll Formation Girl</h1></td></tr>';
    echo gen_html($getTnrNr,'F_RR_GF');     
  }
  if(gen_html($getTnrNr,'F_RR_ST')){
    echo'<tr><td colspan=4><br><br><h1 class="resulttab"><b>Rock\'n\'Roll Formation Showteam</h1></td></tr>';
    echo gen_html($getTnrNr,'F_RR_ST');     
  }
  if(gen_html($getTnrNr,'F_BW_M')){
    echo'<tr><td colspan=4><br><br><h1 class="resulttab"><b>Boogie-Woogie Formation Master</h1></td></tr>';
    echo gen_html($getTnrNr,'F_BW_M');     
  }
  if(gen_html($getTnrNr,'BW_MA')){
    echo'<tr><td colspan=6><br><h1 class="resulttab"><b>Boogie-Woogie Main A</h1></td></tr>';
    echo gen_html($getTnrNr,'BW_MA');     
  }
  if(gen_html($getTnrNr,'BW_SA')){
    echo'<tr><td colspan=6><br><br><h1 class="resulttab"><b>Boogie-Woogie Senior A</h1></td></tr>';
    echo gen_html($getTnrNr,'BW_SA');     
  }
  if(gen_html($getTnrNr,'BW_JA')){
    echo'<tr><td colspan=6><br><br><h1 class="resulttab"><b>Boogie-Woogie Junior</h1></td></tr>';
    echo gen_html($getTnrNr,'BW_JA');     
  }
  if(gen_html($getTnrNr,'BW_MB')){
    echo'<tr><td colspan=6><br><br><h1 class="resulttab"><b>Boogie-Woogie Main B</h1></td></tr>';
    echo gen_html($getTnrNr,'BW_MB');     
  }
  if(gen_html($getTnrNr,'BW_SB')){
    echo'<tr><td colspan=6><br><br><h1 class="resulttab"><b>Boogie-Woogie Senior B</h1></td></tr>';
    echo gen_html($getTnrNr,'BW_SB');     
  }
  echo'
      </table>    
      </div>
      </form>
    </div>';
    //class="groupBox" end  
  
  echo '
  </div>
</div>';//class="formLayout" end
  
echo '
<ul class="iconTextLinkList">
  <li>
  <span class="iconTextLink">
     <a href="'.$g_root_path.'/adm_program/modules/profile/profile.php?user_id='.$user->getValue('usr_id').'">'.$gL10n->get('SYS_BACK').'</a>
  </span>
  </li>
</ul>';
    
require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>
