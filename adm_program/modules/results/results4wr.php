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
require_once('../../system/login_valid.php');
require_once('../../system/drbv_funktionen.php');
require_once('../../system/classes/table_roles.php');
  
// Initialize and check the parameters
$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getTnrNr   = admFuncVariableIsValid($_GET, 'tnrsel', 'numeric', 0);  

unset($_SESSION['turnier']);

$getStartkl = $_POST['skl'];  
$getView    = $_POST['view'];  
$user_liznr = $_SESSION['profile_user_liznr'];

if($_POST['view'] == '') $getView = 'bogen';
  
if(!$_GET){
  if(!$_SESSION["lizenznr"] || $_POST["lizenznr"] != $_SESSION["lizenznr"])
      $_SESSION["lizenznr"] = $_POST["lizenznr"];  
  if(!$_SESSION["t_jahr"] || $_POST["t_jahr"] != $_SESSION["t_jahr"])
      $_SESSION["t_jahr"] = $_POST["t_jahr"];
  if(!$_SESSION["turnier"] || $_POST["turnier"] != $_SESSION["turnier"])
      $_SESSION["turnier"] = $_POST["turnier"];
  if(!$_SESSION["getAuswahl"] || $_POST["auswahl"] != $_SESSION["getAuswahl"])
      $_SESSION["getAuswahl"] = $_POST["auswahl"];  
} 

if(!$getTnrNr) $getTnrNr = $_SESSION["turnier"];  
if($_GET['tn']) $_SESSION['turniernummer'] = $_GET['tn'];

// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
  
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
$datum12P    = new DateTime("2018-04-17");//ab hier wurden alle Teiler auf die 12 Punkte Akroregelung angepasst
  
// HTML generieren für alle Runden und Paare einer Turniernummer & Startklasse
// ---------------------------------------------------------------------------  
function gen_html($getTnrNr,$getStartkl){
  global $gCurrentUser, $user_liznr, $getView, 
         $wrichter_wrtg_tnz_cnt_key, $wrichter_wrtg_akr_cnt_key,
         $bgcolor_VOK,$bgcolor_FOK,$bgcolor_NOK,$getTnrDatum,$datum12P;

  if($getView == 'paare'){
    $bgcolor_VOK = 'CCFFFF';
  } else {
    $bgcolor_VOK = '07c000';
  }
  $bgcolor     = $bgcolor_VOK;//voll ok

  $bgcolor_FOK = 'eadb5e';//fast ok
  $diff2FOK    = 20;//Abweichung FT fast OK
  $diff2FOK_AK = 15;//Abweichung AK fast OK

  $bgcolor_NOK = 'ed595d';//nich ok
  $diff2NOK    = 30;//Abweichung FT nich OK
  $diff2NOK_AK = 25;//Abweichung AK nich OK

  unset($cntVOK);
  unset($cntFOK);
  unset($cntNOK);
  
  $rundenliste_sortiert = array("Vor_r","Vor_r_lang","Vor_r_schnell",
                                "Hoff_r","1_Zw_r","2_Zw_r","3_Zw_r","KO_r","Semi",
                                "End_r","End_r_Fuß","End_r_Akro","End_r_lang","End_r_schnell");

  foreach($rundenliste_sortiert as $rundenname_sortiert){
    // Tanzrunden suchen
    // -----------------
    $sqlab  = 'SELECT rt_id_tlp, startklasse, runde FROM rundentab WHERE turniernummer = "'.$getTnrNr.'" 
               AND startklasse = "'.$getStartkl.'" AND runde = "'.utf8_decode($rundenname_sortiert).'"';
    $runden = mysqli_query(DRBVdb(),$sqlab);
    $tanzrunden[0] = array();
    $sel_tanzrnd   = array();  
  
    while($temp = mysqli_fetch_array($runden)){ 
      $tanzrunden[$temp['rt_id_tlp']] = $temp;
      $sel_tanzrnd[] = $temp[0];
    }
    
    // Paare suchen
    // ------------  
    $sqlab     = 'SELECT * FROM paare WHERE turniernummer = "'.$getTnrNr.'" AND startklasse = "'.$getStartkl.'"';
    $paare_ges = mysqli_query(DRBVdb(),$sqlab);
    $paare[0]  = array();
    
    while($temp = mysqli_fetch_array($paare_ges)){
      $paare[$temp['paar_id_tlp']] = $temp;
      //print_r($paare[$temp['paar_id_tlp']]);echo' :paare<br>';
    }
     
    // Wertungsrichter suchen
    // ----------------------  
    $sqlab = 'SELECT wr_id_tlp, name, lizenznummer FROM wertungsrichter WHERE turniernummer = "'.$getTnrNr.'"';
    $wr_ges = mysqli_query(DRBVdb(),$sqlab);
    
    while($temp = mysqli_fetch_array($wr_ges)){
      $wr[$temp['wr_id_tlp']] = $temp;
      //print_r($wr[$temp['wr_id_tlp']]);echo' :wr<br>';
    }
    
    // Akrobatiken suchen
    // ------------------  
    //$sqlab         = 'SELECT * FROM paare WHERE turniernummer = '.$getTnrNr.' AND  paar_id_tlp = '.$paarinfo["paar_id"];
    //$gemeldet        = mysqli_query(DRBVdb(), $sqlab);
    //$gemeldete_akros = mysqli_fetch_array($gemeldet);
      
    // Wertungen ausgeben:
    // -------------------
    $htmlout   = '';  
    $sqlab     = 'SELECT * FROM wertungen WHERE turniernummer = "'.$getTnrNr.'" AND rund_tab_id IN('.join(", ", $sel_tanzrnd).') ORDER BY rund_tab_id DESC, paar_id_tlp' ;
    $wertungen = mysqli_query(DRBVdb(), $sqlab);
    
    while($temp = mysqli_fetch_array($wertungen)){
      //Rundenname
      if($temp['rund_tab_id'] != $rund_tab_id){
        $rund_tab_id   = $temp['rund_tab_id'];
        $html_rnd[]    = rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']));
        $html_rndskl[] = $tanzrunden[$rund_tab_id]['startklasse'];
      }
      
      //Paarname  
      if($temp['paar_id_tlp'] != $paar_id){
    $paar_id = $temp['paar_id_tlp'];
    if(substr($getStartkl,0,2) != 'F_'){
      $teilnehmername = utf8_encode($paare[$paar_id]['dame']).' - '.utf8_encode($paare[$paar_id]['herr']);
    } else {
      $teilnehmername = utf8_encode($paare[$paar_id]['team']);  
    }
    $html_ppr[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][] = $teilnehmername;
    $html_apr[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][] = array($teilnehmername,
                          utf8_encode($paare[$paar_id]['Akro1_VR']),utf8_encode($paare[$paar_id]['Wert1_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro2_VR']),utf8_encode($paare[$paar_id]['Wert2_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro3_VR']),utf8_encode($paare[$paar_id]['Wert3_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro4_VR']),utf8_encode($paare[$paar_id]['Wert4_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro5_VR']),utf8_encode($paare[$paar_id]['Wert5_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro6_VR']),utf8_encode($paare[$paar_id]['Wert6_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro7_VR']),utf8_encode($paare[$paar_id]['Wert7_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro8_VR']),utf8_encode($paare[$paar_id]['Wert8_VR'])
                               ,utf8_encode($paare[$paar_id]['Akro1_ZR']),utf8_encode($paare[$paar_id]['Wert1_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro2_ZR']),utf8_encode($paare[$paar_id]['Wert2_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro3_ZR']),utf8_encode($paare[$paar_id]['Wert3_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro4_ZR']),utf8_encode($paare[$paar_id]['Wert4_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro5_ZR']),utf8_encode($paare[$paar_id]['Wert5_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro6_ZR']),utf8_encode($paare[$paar_id]['Wert6_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro7_ZR']),utf8_encode($paare[$paar_id]['Wert7_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro8_ZR']),utf8_encode($paare[$paar_id]['Wert8_ZR'])
                               ,utf8_encode($paare[$paar_id]['Akro1_ER']),utf8_encode($paare[$paar_id]['Wert1_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro2_ER']),utf8_encode($paare[$paar_id]['Wert2_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro3_ER']),utf8_encode($paare[$paar_id]['Wert3_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro4_ER']),utf8_encode($paare[$paar_id]['Wert4_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro5_ER']),utf8_encode($paare[$paar_id]['Wert5_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro6_ER']),utf8_encode($paare[$paar_id]['Wert6_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro7_ER']),utf8_encode($paare[$paar_id]['Wert7_ER'])
                               ,utf8_encode($paare[$paar_id]['Akro8_ER']),utf8_encode($paare[$paar_id]['Wert8_ER']));
      }
    
      //Wertungsrichtername
      if($gCurrentUser->isWebmaster() || hasRole("Wertungsrichterdozent RR") || hasRole("Wertungsrichterdozent BW")){
    $wrname   =  utf8_encode($wr[$temp['wr_id']]['name']);
      } else {
    $wrname   = 'WRx';
      }      
      
      if($temp['herr_gt'] == '0' && $temp['herr_halt_dt'] == '0' && $temp['dame_gt'] == '0' && $temp['dame_halt_dt'] == '0' && 
     $temp['choreo'] == '0' && $temp['tanzfiguren'] == '0' && $temp['taenz_darbietung'] == '0')
      {
    //Akro
    // nur, wenn ersten drei ungleich 0
    if(!($temp['akro1'] == '0' && $temp['akro2'] == '0' && $temp['akro3'] == '0')){
      //$wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][utf8_encode($paare[$paar_id]['dame']).' - '.utf8_encode($paare[$paar_id]['herr'])][] = array('AKRO',$wrname,($temp['herr_gt']*10),($temp['herr_halt_dt']*10),($temp['dame_gt']*10),($temp['dame_halt_dt']*10),($temp['choreo']*10),($temp['tanzfiguren']*10),($temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8'],$temp['akro1_grobfehler_text'],$temp['akro2_grobfehler_text'],$temp['akro3_grobfehler_text'],$temp['akro4_grobfehler_text'],$temp['akro5_grobfehler_text'],$temp['akro6_grobfehler_text'],$temp['akro7_grobfehler_text'],$temp['akro8_grobfehler_text']);
      if($getView == 'paare'){
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('AKRO',$wrname,($temp['herr_gt']*10),($temp['herr_halt_dt']*10),($temp['dame_gt']*10),($temp['dame_halt_dt']*10),($temp['choreo']*10),($temp['tanzfiguren']*10),($temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8'],$temp['akro1_grobfehler_text'],$temp['akro2_grobfehler_text'],$temp['akro3_grobfehler_text'],$temp['akro4_grobfehler_text'],$temp['akro5_grobfehler_text'],$temp['akro6_grobfehler_text'],$temp['akro7_grobfehler_text'],$temp['akro8_grobfehler_text']);
      } else {
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('AKRO',$wrname,(100-$temp['herr_gt']*10),(100-$temp['herr_halt_dt']*10),(100-$temp['dame_gt']*10),(100-$temp['dame_halt_dt']*10),(100-$temp['choreo']*10),(100-$temp['tanzfiguren']*10),(100-$temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8'],$temp['akro1_grobfehler_text'],$temp['akro2_grobfehler_text'],$temp['akro3_grobfehler_text'],$temp['akro4_grobfehler_text'],$temp['akro5_grobfehler_text'],$temp['akro6_grobfehler_text'],$temp['akro7_grobfehler_text'],$temp['akro8_grobfehler_text']);
      }
    }
      } else {
    //Tanz
    $runde_name = rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']));
    //$wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][utf8_encode($paare[$paar_id]['dame']).' - '.utf8_encode($paare[$paar_id]['herr'])][] = array('TANZ',$wrname,($temp['herr_gt']*10),($temp['herr_halt_dt']*10),($temp['dame_gt']*10),($temp['dame_halt_dt']*10),($temp['choreo']*10),($temp['tanzfiguren']*10),($temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8']);
    if($getView == 'paare'){
      //reduzierte Vorrundenwertung mit Einfuehrung 12Pkt. TLP Version    
      if($runde_name != "Endrunde Akrobatik" && $runde_name != "Endrunde" && $runde_name != "Endrunde Fußtechnik" && $getTnrDatum > $datum12P){
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('TANZ',$wrname,($temp['herr_gt']*10),($temp['herr_gt']*10),($temp['dame_gt']*10),($temp['dame_gt']*10),($temp['choreo']*10),($temp['choreo']*10),($temp['choreo']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8']);    
      } else {
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('TANZ',$wrname,($temp['herr_gt']*10),($temp['herr_halt_dt']*10),($temp['dame_gt']*10),($temp['dame_halt_dt']*10),($temp['choreo']*10),($temp['tanzfiguren']*10),($temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8']);
      }
    } else {
      //fix verk. VR
      //reduzierte Vorrundenwertung mit Einfuehrung 12Pkt. TLP Version    
      if($runde_name != "Endrunde Akrobatik" && $runde_name != "Endrunde" && $runde_name != "Endrunde Fußtechnik" && $getTnrDatum > $datum12P){
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('TANZ',$wrname,(100-$temp['herr_gt']*10),(100-$temp['herr_gt']*10),(100-$temp['dame_gt']*10),(100-$temp['dame_gt']*10),(100-$temp['choreo']*10),(100-$temp['choreo']*10),(100-$temp['choreo']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8']);
      } else {
        $wrtg[rundenbezeichnung(utf8_encode($tanzrunden[$rund_tab_id]['runde']))][$teilnehmername][] = array('TANZ',$wrname,(100-$temp['herr_gt']*10),(100-$temp['herr_halt_dt']*10),(100-$temp['dame_gt']*10),(100-$temp['dame_halt_dt']*10),(100-$temp['choreo']*10),(100-$temp['tanzfiguren']*10),(100-$temp['taenz_darbietung']*10),($temp['grobfehler_text']),$temp['akro1'],$temp['akro2'],$temp['akro3'],$temp['akro4'],$temp['akro5'],$temp['akro6'],$temp['akro7'],$temp['akro8']);  
      }
    }
      }    
    }
    if($gCurrentUser->isWebmaster()){
      //print_r($wrtg);echo' :wrtg<br>';
      //print_r($rnd_name);echo' :rnd_name<br>';
      //print_r($html_rnd);echo' :html_rnd<br>';
      //print_r($html_rndskl);echo' :html_rndskl<br>';
    }  
    
    $html = '';  
    $i    = 0;  
    foreach($html_rnd AS $rundenname){
  
      unset($mwertGTH);
      unset($mwertHDH);
      unset($mwertGTD);
      unset($mwertHDD);
      unset($mwertCHO);
      unset($mwertTFI);
      unset($mwertTDA);
      unset($mwertAK1);
      unset($mwertAK2);
      unset($mwertAK3);
      unset($mwertAK4);
      unset($mwertAK5);
      unset($mwertAK6);
      unset($mwertAK7);
      unset($mwertAK8);
      unset($mwertGTH_FT);
      unset($mwertHDH_FT);
      unset($mwertGTD_FT);
      unset($mwertHDD_FT);
      unset($mwertCHO_FT);
      unset($mwertTFI_FT);
      unset($mwertTDA_FT);  
    
      $i++;
      $html .= '       
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">'.sklbezeichnung($html_rndskl[$i-1]).': '.$rundenname.'</div>
      </div>
      <div class="groupBoxBody">
      <table border="0">';
      
      if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == RR_C || $html_rndskl[$i-1] == RR_J || $html_rndskl[$i-1] == F_RR_M){
    $sklmitakro = TRUE;  
      } else {
    $sklmitakro = FALSE;     
      }    
      if($html_rndskl[$i-1] == BW_MA || $html_rndskl[$i-1] == BW_MB || $html_rndskl[$i-1] == BW_SA || $html_rndskl[$i-1] == BW_SB || $html_rndskl[$i-1] == BW_JA){
    $sklbw = TRUE;     
      } else {
    $sklbw = FALSE;       
      }    
      if($html_rndskl[$i-1] == F_RR_M || $html_rndskl[$i-1] == F_BW_M || $html_rndskl[$i-1] == F_RR_J || $html_rndskl[$i-1] == F_RR_LF || $html_rndskl[$i-1] == F_RR_GF || $html_rndskl[$i-1] == F_RR_ST){
    $sklform = TRUE;     
      } else {
    $sklform = FALSE;        
      }    
        
      if($rundenname == 'Vorrunde' || $rundenname == 'Hoffnungsrunde') $akr_offset = 1 ;
      if($rundenname == '1. Zwischenrunde' || $rundenname == '2. Zwischenrunde' || $rundenname == '3. Zwischenrunde') $akr_offset = 17;
      if($rundenname == 'Endrunde' || $rundenname == 'Endrunde Akrobatik' || $rundenname == 'KO-Runde' || $rundenname == 'Semifinale') $akr_offset = 33;
        
      $j=0;     
      foreach($html_ppr[$rundenname] AS $nameaktive){
    $j++;
    $twrcnt=0;    
    $awrcnt=0;          
    if($sklmitakro && $rundenname != 'Endrunde Fußtechnik'){
  
      if($sklform){
        $faktor  = 1;
        $colspan = 24;    
        $html .= '
        <tr style="background-color: #eaeaea;"><td colspan="'.$colspan.'"><b>'.$nameaktive.'</b></td></tr>
        <tr style="font-style:italic;font-weight:bold;">
          <td align="center">&nbsp;</td>
          <td align="center"><span data-tooltip="Technik" data-tooltip-position="top">TEC</span></td>
          <td align="center"><span data-tooltip="Tanz" data-tooltip-position="top">TNZ</span></td>
          <td align="center"><span data-tooltip="Tanzfiguren" data-tooltip-position="top">TFI</span></td>
          <td align="center"><span data-tooltip="Bilder" data-tooltip-position="top">BIL</span></td>
          <td align="center"><span data-tooltip="Bildwechsel" data-tooltip-position="top">BIW</span></td>
          <td align="center"><span data-tooltip="F-Figuren/Effekte" data-tooltip-position="top">EFF</span></td>
          <td align="center"><span data-tooltip="Abz&uuml;ge Tanz" data-tooltip-position="top">ABZ</span></td>';      
      } else {      
        $faktor  = 1;
        $colspan = 25;    
        $html .= '
        <tr style="background-color: #eaeaea;"><td colspan="'.$colspan.'"><b>'.$nameaktive.'</b></td></tr>
        <tr style="font-style:italic;font-weight:bold;">
          <td align="center">&nbsp;</td>
          <td align="center"><span data-tooltip="Grundtechnik Herr" data-tooltip-position="top">GTH</span></td>
          <td align="center"><span data-tooltip="Haltungs-&Drehtechnik Herr" data-tooltip-position="top">HDH</span></td>
          <td align="center"><span data-tooltip="Grundtechnik Dame" data-tooltip-position="top">GTD</span></td>
          <td align="center"><span data-tooltip="Haltungs-&Drehtechnik Dame" data-tooltip-position="top">HDD</span></td>
          <td align="center"><span data-tooltip="Choreografie" data-tooltip-position="top">CHO</span></td>
          <td align="center"><span data-tooltip="Tanzfiguren" data-tooltip-position="top">TFG</span></td>
          <td align="center"><span data-tooltip="T&auml;nzerische Darbietung" data-tooltip-position="top">TDB</span></td>
          <td align="center"><span data-tooltip="Abz&uuml;ge Tanz" data-tooltip-position="top">ABZ</span></td>';
      }
      $html .= '
          <td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset].'" data-tooltip-position="top">A1</span></td>
          <td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+2].'" data-tooltip-position="top">A2</span></td>
          <td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+4].'" data-tooltip-position="top">A3</span></td>';
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == RR_C){
      $html .= '<td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+6].'" data-tooltip-position="top">A4</span></td>';
          } else {
      $html .= '<td colspan="2" align="center">A4</td>';
          }
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B){
      $html .= '<td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+8].'" data-tooltip-position="top">A5</span></td>';
          } else {
      $html .= '<td colspan="2" align="center">A5</td>';
          }
          if(($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B) && 
      ($rundenname == 'Endrunde' || $rundenname == 'Endrunde Akrobatik' || $rundenname == 'KO-Runde')){
      $html .= '<td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+10].'" data-tooltip-position="top">A6</span></td>';
          } else {
      $html .= '<td colspan="2" align="center">A6</td>';
          }
          if($html_rndskl[$i-1] == F_RR_M){
      $html .= '<td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+12].'" data-tooltip-position="top">A7</span></td>';
      $html .= '<td colspan="2" align="center"><span data-tooltip="'.$html_apr[$rundenname][$j-1][$akr_offset+14].'" data-tooltip-position="top">A8</span></td>';
          } else {
      $html .= '<td colspan="2" align="center">A7</td>';
      $html .= '<td colspan="2" align="center">A8</td>';
          }          
        $html .= '</tr>';
        
        foreach($wrtg[$rundenname][$nameaktive] AS $wertung){
        //print_r($wertung);echo' :wertung<br>';
        if($wertung[0] == 'TANZ'){
          $mwertGTH[$nameaktive][] .= $wertung[2];
          $mwertHDH[$nameaktive][] .= $wertung[3];
          $mwertGTD[$nameaktive][] .= $wertung[4];
          $mwertHDD[$nameaktive][] .= $wertung[5];
          $mwertCHO[$nameaktive][] .= $wertung[6];
          $mwertTFI[$nameaktive][] .= $wertung[7];
          $mwertTDA[$nameaktive][] .= $wertung[8];
        } else {
          $mwertAK1[$nameaktive][] .= akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]);
          $mwertAK2[$nameaktive][] .= akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]);     
          $mwertAK3[$nameaktive][] .= akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]);     
          $mwertAK4[$nameaktive][] .= akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]);     
          $mwertAK5[$nameaktive][] .= akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]);     
          $mwertAK6[$nameaktive][] .= akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]);      
          $mwertAK7[$nameaktive][] .= akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]);      
          $mwertAK8[$nameaktive][] .= akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]);      
        }
        //print_r($mwertGTH);echo' :mwertGTH<br>';              
        //print_r($mwertAK1);echo' :mwertAK1<br>';              
      }     
      $html .= '
        <tr style="color:#41a0fa;">
          <td>Mwert</td>
          <td align="center">'.number_format(mwert($mwertGTH[$nameaktive]),0).'</td>
          <td align="center">'.number_format(mwert($mwertHDH[$nameaktive]),0).'</td>
          <td align="center">'.number_format(mwert($mwertGTD[$nameaktive]),0).'</td>';
        if(!($sklbw || $sklform)){  
          $html .= '<td align="center">'.number_format(mwert($mwertHDD[$nameaktive]),0).'</td>';
        }
        $html .= '
          <td align="center">'.number_format(mwert($mwertCHO[$nameaktive]),0).'</td>
          <td align="center">'.number_format(mwert($mwertTFI[$nameaktive]),0).'</td>
          <td align="center">'.number_format(mwert($mwertTDA[$nameaktive]),0).'</td>
          <td></td>
          <td colspan="2" align="center">'.number_format(mwert($mwertAK1[$nameaktive]),0).'</td>
          <td colspan="2" align="center">'.number_format(mwert($mwertAK2[$nameaktive]),0).'</td>
          <td colspan="2" align="center">'.number_format(mwert($mwertAK3[$nameaktive]),0).'</td>';
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == RR_C || $html_rndskl[$i-1] == F_RR_M){
      $html .= '<td colspan="2" align="center">'.number_format(mwert($mwertAK4[$nameaktive]),0).'</td>';
          } else {
      $html .= '<td colspan="2" align="center"></td>';    
          }
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == F_RR_M){
      $html .= '<td colspan="2" align="center">'.number_format(mwert($mwertAK5[$nameaktive]),0).'</td>';
          } else {
      $html .= '<td colspan="2" align="center"></td>';        
          }
          if($html_rndskl[$i-1] == F_RR_M || (($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B) && 
      ($rundenname == 'Endrunde' || $rundenname == 'Endrunde Akrobatik' || $rundenname == 'KO-Runde'))){
      $html .= '<td colspan="2" align="center">'.number_format(mwert($mwertAK6[$nameaktive]),0).'</td>';
          } else {
      $html .= '<td colspan="2" align="center"></td>';        
          }
          if($html_rndskl[$i-1] == F_RR_M){
      $html .= '<td colspan="2" align="center">'.number_format(mwert($mwertAK7[$nameaktive]),0).'</td>';
      $html .= '<td colspan="2" align="center">'.number_format(mwert($mwertAK8[$nameaktive]),0).'</td>';
          } else {
      $html .= '<td colspan="2" align="center"></td>';        
      $html .= '<td colspan="2" align="center"></td>';        
          }
        $html .= '</tr>';
  
      unset($streichmin);
      unset($streichmax);
        
      foreach($wrtg[$rundenname][$nameaktive] AS $wertung){    
        if($wertung[0] == 'TANZ'){
          $twrcnt++;
          $html .= '<tr>';
          if($wertung[1] != 'WRx'){
      $set_bold = '<b>';
      $html .= '<td><b>'.$wertung[1].'<b></td>';
          } else {
      $set_bold = '';
      $html .= '<td>TWR'.$twrcnt.'</td>';
          }
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertGTH[$nameaktive]),$wertung[2]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}        
          if(abweichung(mwert($mwertGTH[$nameaktive]),$wertung[2]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';$cntNOK++;}             
          if($wertung[2] == min($mwertGTH[$nameaktive]) && $streichmin[2] == FALSE && count($mwertGTH[$nameaktive]) == 4){
      //print_r($mwertGTH);echo' :mwertGTH<br>';
      //print_r(count($mwertGTH));echo' :count mwertGTH<br>';
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[2].'</s></span></td>';
      $streichmin[2] = TRUE;
          } elseif($wertung[2] == max($mwertGTH[$nameaktive]) && $streichmax[2] == FALSE && count($mwertGTH[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[2].'</s></span></td>';
      $streichmax[2] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[2].'</span></td>';
          }
          $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertHDH[$nameaktive]),$wertung[3]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}        
          if(abweichung(mwert($mwertHDH[$nameaktive]),$wertung[3]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}               
          if($wertung[3] == min($mwertHDH[$nameaktive]) && $streichmin[3] == FALSE && count($mwertHDH[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[3].'</s></span></td>';
      $streichmin[3] = TRUE;
          } elseif($wertung[3] == max($mwertHDH[$nameaktive]) && $streichmax[3] == FALSE && count($mwertHDH[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[3].'</s></span></td>';
      $streichmax[3] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[3].'</span></td>';
          }
          $wrichter_wrtg_cnt_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertGTD[$nameaktive]),$wertung[4]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}        
          if(abweichung(mwert($mwertGTD[$nameaktive]),$wertung[4]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}           
          if($wertung[4] == min($mwertGTD[$nameaktive]) && $streichmin[4] == FALSE && count($mwertGTD[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[4].'</s></span></td>';
      $streichmin[4] = TRUE;
          } elseif($wertung[4] == max($mwertGTD[$nameaktive]) && $streichmax[4] == FALSE && count($mwertGTD[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[4].'</s></span></td>';
      $streichmax[4] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[4].'</span></td>';
          }
          $wrichter_wrtg_cnt_tnz_key[$wertung[1]][] = $bgcolor;
           
          if(!($sklbw || $sklform)){  
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertHDD[$nameaktive]),$wertung[5]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertHDD[$nameaktive]),$wertung[5]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}             
      if($wertung[5] == min($mwertHDD[$nameaktive]) && $streichmin[5] == FALSE && count($mwertHDD[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[5].'</s></span></td>';
        $streichmin[5] = TRUE;
      } elseif($wertung[5] == max($mwertHDD[$nameaktive]) && $streichmax[5] == FALSE && count($mwertHDD[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[5].'</s></span></td>';
        $streichmax[5] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[5].'</span></td>';
      }
          }
          $wrichter_wrtg_cnt_tnz_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertCHO[$nameaktive]),$wertung[6]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}       
          if(abweichung(mwert($mwertCHO[$nameaktive]),$wertung[6]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}           
          if($wertung[6] == min($mwertCHO[$nameaktive]) && $streichmin[6] == FALSE && count($mwertCHO[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[6].'</s></span></td>';
      $streichmin[6] = TRUE;
          } elseif($wertung[6] == max($mwertCHO[$nameaktive]) && $streichmax[6] == FALSE && count($mwertCHO[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[6].'</s></span></td>';
      $streichmax[6] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[6].'</span></td>';
          }
          $wrichter_wrtg_cnt_tnz_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertTFI[$nameaktive]),$wertung[7]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}      
          if(abweichung(mwert($mwertTFI[$nameaktive]),$wertung[7]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}           
          if($wertung[7] == min($mwertTFI[$nameaktive]) && $streichmin[7] == FALSE && count($mwertTFI[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[7].'</s></span></td>';
      $streichmin[7] = TRUE;
          } elseif($wertung[7] == max($mwertTFI[$nameaktive]) && $streichmax[7] == FALSE && count($mwertTFI[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[7].'</s></span></td>';
      $streichmax[7] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[7].'</span></td>';
          }
          $wrichter_wrtg_cnt_tnz_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertTDA[$nameaktive]),$wertung[8]) > $diff2FOK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}        
          if(abweichung(mwert($mwertTDA[$nameaktive]),$wertung[8]) > $diff2NOK){
      $bgcolor = $bgcolor_NOK;$ttip=' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}           
          if($wertung[8] == min($mwertTDA[$nameaktive]) && $streichmin[8] == FALSE && count($mwertTDA[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[8].'</s></span></td>';
      $streichmin[8] = TRUE;
          } elseif($wertung[8] == max($mwertTDA[$nameaktive]) && $streichmax[8] == FALSE && count($mwertTDA[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[8].'</s></span></td>';
      $streichmax[8] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[8].'</span></td>';
          }
          $wrichter_wrtg_cnt_tnz_key[$wertung[1]][] = $bgcolor;
          
          $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[9].'</td>';        
          if($sklform){
      $html .= '<td colspan="16">&nbsp;</td></b></tr><tr><td colspan=24></td></tr>';
          } else {
      $html .= '<td colspan="8">&nbsp;</td></b></tr><tr><td colspan=10></td></tr>';
          }
        } else {
          $awrcnt++;
          $html .= '<tr>';
          if($wertung[1] != 'WRx'){
      $set_bold = '<b>';
      $html   .= '<td><b>'.$wertung[1].'</b></td>';
          } else {
      $set_bold = '';
      $html .= '<td>AWR'.$awrcnt.'</td>';
          }
          if($sklform){     
      $html .= '<td colspan="7">&nbsp;</td>';
          } else {
      $html .= '<td colspan="8">&nbsp;</td>';
          }
  
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertAK1[$nameaktive]),akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1])) > $diff2FOK_AK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}          
          if(abweichung(mwert($mwertAK1[$nameaktive]),akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1])) > $diff2NOK_AK){
      $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}          
          if(akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]) == min($mwertAK1[$nameaktive]) && $streichmin[10] == FALSE && count($mwertAK1[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]).'</s></span></td>';
      $streichmin[10] = TRUE;
          } elseif(akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]) == max($mwertAK1[$nameaktive]) && $streichmax[10] == FALSE && count($mwertAK1[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]).'</s></span></td>';
      $streichmax[10] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[10],$html_apr[$rundenname][$j-1][$akr_offset+1]).'</span></td>';
          }
          $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[18].'</td>';
          $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertAK2[$nameaktive]),akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3])) > $diff2FOK_AK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}          
          if(abweichung(mwert($mwertAK2[$nameaktive]),akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3])) > $diff2NOK_AK){
      $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}          
          if(akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]) == min($mwertAK2[$nameaktive]) && $streichmin[11] == FALSE && count($mwertAK2[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]).'</s></span></td>';
      $streichmin[11] = TRUE;
          } elseif(akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]) == max($mwertAK2[$nameaktive]) && $streichmax[11] == FALSE && count($mwertAK2[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]).'</s></span></td>';
      $streichmax[11] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[11],$html_apr[$rundenname][$j-1][$akr_offset+3]).'</span></td>';
          }
          $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[19].'</td>';
          $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
        
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if(abweichung(mwert($mwertAK3[$nameaktive]),akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5])) > $diff2FOK_AK){
      $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}          
          if(abweichung(mwert($mwertAK3[$nameaktive]),akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5])) > $diff2NOK_AK){
      $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}          
          if(akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]) == min($mwertAK3[$nameaktive]) && $streichmin[12] == FALSE && count($mwertAK3[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]).'</s></span></td>';
      $streichmin[12] = TRUE;
          } elseif(akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]) == max($mwertAK3[$nameaktive]) && $streichmax[12] == FALSE && count($mwertAK3[$nameaktive]) == 4){
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]).'</s></span></td>';
      $streichmax[12] = TRUE;
          } else {
      $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[12],$html_apr[$rundenname][$j-1][$akr_offset+5]).'</span></td>';
          }
          $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[20].'</td>';
          $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
  
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == RR_C || $html_rndskl[$i-1] == F_RR_M){
      if(abweichung(mwert($mwertAK4[$nameaktive]),akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7])) > $diff2FOK_AK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}           
      if(abweichung(mwert($mwertAK4[$nameaktive]),akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7])) > $diff2NOK_AK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}           
      if(akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]) == min($mwertAK4[$nameaktive]) && $streichmin[13] == FALSE && count($mwertAK4[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]).'</s></span></td>';
        $streichmin[13] = TRUE;
      } elseif(akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]) == max($mwertAK4[$nameaktive]) && $streichmax[13] == FALSE && count($mwertAK4[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]).'</s></span></td>';
        $streichmax[13] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[13],$html_apr[$rundenname][$j-1][$akr_offset+7]).'</span></td>';
      }
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[21].'</td>';   
      $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
          } else {
      $html .= '<td colspan="2" style="background-color: #d0d0d0;">&nbsp;</td>';
          }
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B || $html_rndskl[$i-1] == F_RR_M){
      if(abweichung(mwert($mwertAK5[$nameaktive]),akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9])) > $diff2FOK_AK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}           
      if(abweichung(mwert($mwertAK5[$nameaktive]),akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9])) > $diff2NOK_AK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}           
      if(akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]) == min($mwertAK5[$nameaktive]) && $streichmin[14] == FALSE && count($mwertAK5[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]).'</s></span></td>';
        $streichmin[14] = TRUE;
      } elseif(akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]) == max($mwertAK5[$nameaktive]) && $streichmax[14] == FALSE && count($mwertAK5[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]).'</s></span></td>';
        $streichmax[14] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[14],$html_apr[$rundenname][$j-1][$akr_offset+9]).'</span></td>';
      }
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[22].'</td>';   
      $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
          } else {
      $html .= '<td colspan="2" style="background-color: #d0d0d0;">&nbsp;</td>';
          }
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if($html_rndskl[$i-1] == F_RR_M || (($html_rndskl[$i-1] == RR_A || $html_rndskl[$i-1] == RR_B) && 
      ($rundenname == 'Endrunde' || $rundenname == 'Endrunde Akrobatik' || $rundenname == 'KO-Runde'))){
      if(abweichung(mwert($mwertAK6[$nameaktive]),akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11])) > $diff2FOK_AK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}           
      if(abweichung(mwert($mwertAK6[$nameaktive]),akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11])) > $diff2NOK_AK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}           
      if(akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]) == min($mwertAK6[$nameaktive]) && $streichmin[15] == FALSE && count($mwertAK6[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]).'</s></span></td>';
        $streichmin[15] = TRUE;
      } elseif(akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]) == max($mwertAK6[$nameaktive]) && $streichmax[15] == FALSE && count($mwertAK6[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]).'</s></span></td>';
        $streichmax[15] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[15],$html_apr[$rundenname][$j-1][$akr_offset+11]).'</span></td>';
      }
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[23].'</td>';   
      $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
          } else {
      $html .= '<td colspan="2" style="background-color: #d0d0d0;">&nbsp;</td>';
          }
          $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
          if($html_rndskl[$i-1] == F_RR_M){
      if(abweichung(mwert($mwertAK7[$nameaktive]),akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13])) > $diff2FOK_AK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}           
      if(abweichung(mwert($mwertAK7[$nameaktive]),akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13])) > $diff2NOK_AK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}           
      if(akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]) == min($mwertAK7[$nameaktive]) && $streichmin[16] == FALSE && count($mwertAK7[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]).'</s></span></td>';
        $streichmin[16] = TRUE;
      } elseif(akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]) == max($mwertAK7[$nameaktive]) && $streichmax[16] == FALSE && count($mwertAK7[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]).'</s></span></td>';
        $streichmax[16] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[16],$html_apr[$rundenname][$j-1][$akr_offset+13]).'</span></td>';
      }
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[24].'</td>';   
      $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
      
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertAK8[$nameaktive]),akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15])) > $diff2FOK_AK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK_AK.'" data-tooltip-position="top"';$cntFOK++;}           
      if(abweichung(mwert($mwertAK8[$nameaktive]),akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15])) > $diff2NOK_AK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK_AK.'" data-tooltip-position="top"';}           
      if(akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]) == min($mwertAK8[$nameaktive]) && $streichmin[17] == FALSE && count($mwertAK8[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]).'</s></span></td>';
        $streichmin[17] = TRUE;
      } elseif(akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]) == max($mwertAK8[$nameaktive]) && $streichmax[17] == FALSE && count($mwertAK8[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]).'</s></span></td>';
        $streichmax[17] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.akrowrtgproz($wertung[17],$html_apr[$rundenname][$j-1][$akr_offset+15]).'</span></td>';
      }
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[25].'</td>';           
      $wrichter_wrtg_akr_cnt_key[$wertung[1]][] = $bgcolor;
          } else {
      $html .= '<td colspan="2" style="background-color: #d0d0d0;">&nbsp;</td>';
      $html .= '<td colspan="2" style="background-color: #d0d0d0;">&nbsp;</td>';
          }
          $html .= '</b></tr>';
        }       
      }
    } else {
      
      if($sklbw){
        $faktor  = 0.1;
        $colspan = 8;         
        $html .= '
        <tr style="background-color: #eaeaea;"><td colspan="'.$colspan.'"><b>'.$nameaktive.'</b></td></tr>
        <tr style="font-style:italic;font-weight:bold;">
          <td align="center">&nbsp;</td>
          <td align="center"><span data-tooltip="Grundschritt" data-tooltip-position="top">GRS</span></td>
          <td align="center"><span data-tooltip="Basic Dancing" data-tooltip-position="top">BDA</span></td>
          <td align="center"><span data-tooltip="Tanzfiguren" data-tooltip-position="top">TFI</span></td>
          <td align="center"><span data-tooltip="Interpretation" data-tooltip-position="top">INT</span></td>
          <td align="center"><span data-tooltip="Spontane Interpretation" data-tooltip-position="top">SINT</span></td>
          <td align="center"><span data-tooltip="Dance Performance" data-tooltip-position="top">DAP</span></td>
          <td align="center"><span data-tooltip="Abz&uuml;ge Tanz" data-tooltip-position="top">ABZ</span></td>
        </tr>';     
      
      } elseif($sklform){
        $faktor  = 1;
        $colspan = 8;         
        $html .= '
        <tr style="background-color: #eaeaea;"><td colspan="'.$colspan.'"><b>'.$nameaktive.'</b></td></tr>
        <tr style="font-style:italic;font-weight:bold;">
          <td align="center">&nbsp;</td>
          <td align="center"><span data-tooltip="Technik" data-tooltip-position="top">TEC</span></td>
          <td align="center"><span data-tooltip="Tanz" data-tooltip-position="top">TNZ</span></td>
          <td align="center"><span data-tooltip="Tanzfiguren" data-tooltip-position="top">TFI</span></td>
          <td align="center"><span data-tooltip="Bilder" data-tooltip-position="top">BIL</span></td>
          <td align="center"><span data-tooltip="Bildwechsel" data-tooltip-position="top">BIW</span></td>
          <td align="center"><span data-tooltip="F-Figuren/Effekte" data-tooltip-position="top">EFF</span></td>
          <td align="center"><span data-tooltip="Abz&uuml;ge Tanz" data-tooltip-position="top">ABZ</span></td>
        </tr>';     
      } else {
        $faktor  = 1;
        $colspan = 9;         
        $html .= '
        <tr style="background-color: #eaeaea;"><td colspan="'.$colspan.'"><b>'.$nameaktive.'</b></td></tr>
        <tr style="font-style:italic;font-weight:bold;">
          <td align="center">&nbsp;</td>
          <td align="center"><span data-tooltip="Grundtechnik Herr" data-tooltip-position="top">GTH</span></td>
          <td align="center"><span data-tooltip="Haltungs-&Drehtechnik Herr" data-tooltip-position="top">HDH</span></td>
          <td align="center"><span data-tooltip="Grundtechnik Dame" data-tooltip-position="top">GTD</span></td>
          <td align="center"><span data-tooltip="Haltungs-&Drehtechnik Dame" data-tooltip-position="top">HDD</span></td>
          <td align="center"><span data-tooltip="Choreografie" data-tooltip-position="top">CHO</span></td>
          <td align="center"><span data-tooltip="Tanzfiguren" data-tooltip-position="top">TFG</span></td>
          <td align="center"><span data-tooltip="T&auml;nzerische Darbietung" data-tooltip-position="top">TDB</span></td>
          <td align="center"><span data-tooltip="Abz&uuml;ge Tanz" data-tooltip-position="top">ABZ</span></td>
        </tr>';
        
      } 
  
      foreach($wrtg[$rundenname][$nameaktive] AS $wertung){
        //print_r($wertung);echo' :wertung<br>';
        if($wertung[0] == 'TANZ'){
          $mwertGTH_FT[$nameaktive][] .= $wertung[2];
          $mwertHDH_FT[$nameaktive][] .= $wertung[3];
          $mwertGTD_FT[$nameaktive][] .= $wertung[4];
          $mwertHDD_FT[$nameaktive][] .= $wertung[5];
          $mwertCHO_FT[$nameaktive][] .= $wertung[6];
          $mwertTFI_FT[$nameaktive][] .= $wertung[7];
          $mwertTDA_FT[$nameaktive][] .= $wertung[8];
        }
        //print_r($mwertGTH_FT);echo' :mwertGTH_FT<br>';                  
      }           
      $html .= '       
        <tr style="color:#41a0fa;">
          <td>Mwert</td>
          <td align="center">'.number_format(mwert($mwertGTH_FT[$nameaktive]),0)*$faktor.'</td>
          <td align="center">'.number_format(mwert($mwertHDH_FT[$nameaktive]),0)*$faktor.'</td>
          <td align="center">'.number_format(mwert($mwertGTD_FT[$nameaktive]),0)*$faktor.'</td>';
      if(!($sklbw || $sklform)){
        $html .= '
          <td align="center">'.number_format(mwert($mwertHDD_FT[$nameaktive]),0)*$faktor.'</td>';
      }
      $html .= '
          <td align="center">'.number_format(mwert($mwertCHO_FT[$nameaktive]),0)*$faktor.'</td>
          <td align="center">'.number_format(mwert($mwertTFI_FT[$nameaktive]),0)*$faktor.'</td>
          <td align="center">'.number_format(mwert($mwertTDA_FT[$nameaktive]),0)*$faktor.'</td>
          <td></td>
        </tr>';
            
        unset($streichmin);
        unset($streichmax);
        foreach($wrtg[$rundenname][$nameaktive] AS $wertung){
        //print_r($wertung);echo' :wertung<br>';
        
        if($wertung[0] == 'TANZ'){
          $twrcnt++;    
          $html .= '<tr>';        
      if($wertung[1] != 'WRx'){
        $set_bold = '<b>';
        $html .= '<td>'.$wertung[1].'</td>';
      } else {
        $set_bold = '';
        $html .= '<td>TWR'.$twrcnt.'</td>';
      }   
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertGTH_FT[$nameaktive]),$wertung[2]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertGTH_FT[$nameaktive]),$wertung[2]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}
      if($wertung[2] == min($mwertGTH_FT[$nameaktive]) && $streichmin[2] == FALSE && count($mwertGTH_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[2]*$faktor.'</s></span></td>';
        $streichmin[2] = TRUE;
      } elseif($wertung[2] == max($mwertGTH_FT[$nameaktive]) && $streichmax[2] == FALSE && count($mwertGTH_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[2]*$faktor.'</s></span></td>';
        $streichmax[2] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[2]*$faktor.'</span></td>';
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
        
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertHDH_FT[$nameaktive]),$wertung[3]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertHDH_FT[$nameaktive]),$wertung[3]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}             
      if($wertung[3] == min($mwertHDH_FT[$nameaktive]) && $streichmin[3] == FALSE && count($mwertHDH_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[3]*$faktor.'</s></span></td>';
        $streichmin[3] = TRUE;
      } elseif($wertung[3] == max($mwertHDH_FT[$nameaktive]) && $streichmax[3] == FALSE && count($mwertHDH_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[3]*$faktor.'</s></span></td>';
        $streichmax[3] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[3]*$faktor.'</span></td>';
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
      
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertGTD_FT[$nameaktive]),$wertung[4]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertGTD_FT[$nameaktive]),$wertung[4]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}             
      if($wertung[4] == min($mwertGTD_FT[$nameaktive]) && $streichmin[4] == FALSE && count($mwertGTD_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[4]*$faktor.'</s></span></td>';
        $streichmin[4] = TRUE;
      } elseif($wertung[4] == max($mwertGTD_FT[$nameaktive]) && $streichmax[4] == FALSE && count($mwertGTD_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[4]*$faktor.'</s></span></td>';
        $streichmax[4] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[4]*$faktor.'</span></td>';
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
      
      if(!($sklbw || $sklform)){
        $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
        if(abweichung(mwert($mwertHDD_FT[$nameaktive]),$wertung[5]) > $diff2FOK){
          $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
        if(abweichung(mwert($mwertHDD_FT[$nameaktive]),$wertung[5]) > $diff2NOK){
          $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}                   
        if($wertung[5] == min($mwertHDD_FT[$nameaktive]) && $streichmin[5] == FALSE && count($mwertHDD_FT[$nameaktive]) == 4){
          $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[5]*$faktor.'</s></span></td>';
          $streichmin[5] = TRUE;
        } elseif($wertung[5] == max($mwertHDD_FT[$nameaktive]) && $streichmax[5] == FALSE && count($mwertHDD_FT[$nameaktive]) == 4){
          $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[5]*$faktor.'</s></span></td>';
          $streichmax[5] = TRUE;
        } else {
          $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[5]*$faktor.'</span></td>';
        }
      }  
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
          
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertCHO_FT[$nameaktive]),$wertung[6]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertCHO_FT[$nameaktive]),$wertung[6]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}                 
      if($wertung[6] == min($mwertCHO_FT[$nameaktive]) && $streichmin[6] == FALSE && count($mwertCHO_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[6]*$faktor.'</s></span></td>';
        $streichmin[6] = TRUE;
      } elseif($wertung[6] == max($mwertCHO_FT[$nameaktive]) && $streichmax[6] == FALSE && count($mwertCHO_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[6]*$faktor.'</s></span></td>';
        $streichmax[6] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[6]*$faktor.'</span></td>';
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
      
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertTFI_FT[$nameaktive]),$wertung[7]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertTFI_FT[$nameaktive]),$wertung[7]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}                 
      if($wertung[7] == min($mwertTFI_FT[$nameaktive]) && $streichmin[7] == FALSE && count($mwertTFI_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[7]*$faktor.'</s></span></td>';
        $streichmin[7] = TRUE;
      } elseif($wertung[7] == max($mwertTFI_FT[$nameaktive]) && $streichmax[7] == FALSE && count($mwertTFI_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[7]*$faktor.'</s></span></td>';
        $streichmax[7] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[7]*$faktor.'</span></td>';
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
          
      $bgcolor     = $bgcolor_VOK;$ttip='';//voll ok
      if(abweichung(mwert($mwertTDA_FT[$nameaktive]),$wertung[8]) > $diff2FOK){
        $bgcolor = $bgcolor_FOK;$ttip = ' data-tooltip="Abweichung > '.$diff2FOK.'" data-tooltip-position="top"';$cntFOK++;}          
      if(abweichung(mwert($mwertTDA_FT[$nameaktive]),$wertung[8]) > $diff2NOK){
        $bgcolor = $bgcolor_NOK;$ttip = ' data-tooltip="Abweichung > '.$diff2NOK.'" data-tooltip-position="top"';}                 
      if($wertung[8] == min($mwertTDA_FT[$nameaktive]) && $streichmin[8] == FALSE && count($mwertTDA_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[8]*$faktor.'</s></span></td>';
        $streichmin[8] = TRUE;
      } elseif($wertung[8] == max($mwertTDA_FT[$nameaktive]) && $streichmax[8] == FALSE && count($mwertTDA_FT[$nameaktive]) == 4){
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'><s>'.$set_bold.$wertung[8]*$faktor.'</s></span></td>';
        $streichmax[8] = TRUE;
      } else {
        $html .= '<td style="background-color: #'.$bgcolor.';" align="center"><span'.$ttip.'>'.$set_bold.$wertung[8]*$faktor.'</span></td>';        
      }
      $wrichter_wrtg_tnz_cnt_key[$wertung[1]][] = $bgcolor;
      
      $bgcolor     = $bgcolor_VOK;//voll ok
      $html .= '<td style="background-color: #ada87e;" align="center">'.$set_bold.$wertung[9].'</td>';
        }//endif wertung TANZ
        $html .= '</tr>';       
      }//endif foreach      
      $html .= '<tr><td colspan="'.$colspan.'"><hr /></td></b></tr>';     
    }
    }  
      $html .= '
      </table>   
      </div>
    </div>';
    //class="groupBox" end
    }
  }//end foreach ($rundenliste_sortiert as $rundenname_sortiert)
        
  //print_r($html_rnd);echo' :html_rnd<br>';
  //print_r($html_rndskl);echo' :html_rndskl<br>';
  //print_r($html_ppr);echo' :html_ppr<br>';
  //print_r($html_apr);echo' :html_apr<br>';        
  //print_r($wrtg);echo' :wrtg<br>';
  //print_r($mwertGTH);echo' :mwertGTH<br>';
  //print_r($mwertHDH);echo' :mwertHDH<br>';
  //print_r($mwertGTD);echo' :mwertGTD<br>';
  //print_r($mwertHDD);echo' :mwertHDD<br>';
  //print_r($html_rndskl[$i-1]);echo' :html_rndskl[$i-1]<br>';
  //print_r(count($html_runde));echo' :cnt_rnd<br>';
  //print_r($wrichter_wrtg_cnt_key);echo' :wrichter_wrtg_cnt_key<br>';
  //foreach ($html_ppr as $key => $elem) {
  //    echo $key." has ".count($elem). " elements\n";
  //}
  return $html;          
}//end function gen_html()        
            
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
    </script>';
  
require(SERVER_PATH. '/adm_program/system/overall_header.php');          
    
echo '  
<div class="formLayout" id="profile_form" width="100px">
  <div class="formHead">Wertungsergebnisse aller Wertungsrichter</div>
  <div class="formBody">';
                 
  // *******************************************************************************
  // Userdaten-Block
  // *******************************************************************************
          
  // Wertungsrichter aus Datenbank lesen
  $sqlab = 'SELECT lizenznummer,name FROM wertungsrichter ORDER BY lizenznummer';
  $wertungsrichter = mysqli_query(DRBVdb(), $sqlab);

  if(!$_SESSION["t_jahr"]){        
    $sqlab = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE YEAR(datum) = "2016"';
  } else {
    $sqlab = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE YEAR(datum) = "'.$_SESSION["t_jahr"].'"';  
  } 
  $turnier = mysqli_query(DRBVdb(), $sqlab);
  
  $option_turnier = '';
  while($temp = mysqli_fetch_array($turnier)){
    if($_SESSION["turnier"] == $temp[0]){
      $option_turnier .= '<option value="' . $temp[0] . '"  selected>' . $temp[0] . ' ' . utf8_encode($temp[1]) . '</option>';
    } else {
      $option_turnier .= '<option value="' . $temp[0] . '">' . $temp[0] . ' ' . utf8_encode($temp[1]) . '</option>';
    }
    $trnr_name_key[$temp[0]] = utf8_encode($temp[1]);
  }
            
  foreach($trnr_name_key as $key => $value){
    gen_html($key,'RR_A');
    gen_html($key,'RR_B'); 
    gen_html($key,'RR_C'); 
    gen_html($key,'RR_J'); 
    gen_html($key,'RR_S'); 
//    gen_html($key,'BW_MA');
//    gen_html($key,'BW_MB'); 
//    gen_html($key,'BW_SA'); 
//    gen_html($key,'BW_SB'); 
//    gen_html($key,'BW_JA'); 
  }        
  //print_r($wrichter_wrtg_tnz_cnt_key);echo' :wrichter_wrtg_tnz_cnt_key<br>';
  //$ops = wrtgcounttnz("Roland Stockmaier");
  //print_r($wertungsrichter);echo' :wertungsrichter<br>';
  
  $bw_wrichter = array("0006","0126");
  
  $option_wrichter = '';       
  while($temp = mysqli_fetch_array($wertungsrichter)){
    if(!in_array($temp[0], $wr_lizenz)){
      if($_SESSION["lizenznr"] == $temp[0]){
        $option_wrichter .= '<option value="'.$temp[0].'" selected>'.$temp[0].' '.utf8_encode($temp[1]).'</option>';
      } else {
        $option_wrichter .= '<option value="'.$temp[0].'">'.$temp[0].' '.utf8_encode($temp[1]).'</option>';
      }
      $wr_lizenz[] = $temp[0];
      $wr_name[]   = utf8_encode($temp[1]);
      $wr_name_key[$temp[0]] = utf8_encode($temp[1]);
    }
  }         
          
  echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">';
        if(hasRole("Bundestrainer")){
          echo'<div style="float: left;">Auswahl Jahr und Turnier</div>';
        } else {
          echo'<div style="float: left;">Auswahl Jahr, Turnier oder Wertungsrichter</div>';          
        }
      echo'
      </div>
      <form action="'.$_SERVER["PHP_SELF"].'" method=post>
      <div class="groupBoxBody">
      <ul class="formFieldList">
        <li>
          <dl>
            <dt>Jahr:</dt>
            <dd>
              <select name="t_jahr" size="1" onchange="submit();">';
                for($x = 2016;$x < 2030;$x++){
                  if($_SESSION["t_jahr"] == $x){
                    echo '<option value="'.$x.'"  selected>'.$x.'</option>';
                  } else {
                    echo'<option value="' . $x . '">' . $x  . '</option>';
                  }
                }
   echo'      </select>          
            </dd>
          </dl>       
        </li>';
   if($gCurrentUser->isWebmaster() || hasRole("Wertungsrichterdozent RR") || hasRole("Wertungsrichterdozent BW")){
     echo'
        <li>
          <dl>
            <dt>Auswahl:</dt>
              <dd>
              <input type="radio" name="auswahl" value="turnier" onclick="submit();" ';
                 if($_SESSION['getAuswahl']=='turnier') echo 'checked="checked"'; 
                   echo '/>nach Turnier<br />
              </dd>
              <dd>
              <input type="radio" name="auswahl" value="wrichter" onclick="submit();" ';
                 if($_SESSION['getAuswahl']=='wrichter') echo 'checked="checked"'; 
                   echo '/>nach Wertungsrichter<br />
              </dd>
          </dl>       
        </li>';
   }
   if($_SESSION['getAuswahl'] == 'turnier' || hasRole("Bundestrainer")){       
     echo'
        <li>
          <dl>
            <dt>Turniere:</dt>
            <dd>
              <select style="width: 450px" size=1 name="turnier" onchange="submit();">';
                echo'<option value="---">---</option>';
                echo $option_turnier;
     echo'    </select>      
            </dd>
          </dl>       
        </li>';
   }
   if($_SESSION['getAuswahl'] == 'wrichter'){       
     echo'
        <li>
          <dl>
            <dt>Wertungsrichter:</dt>
            <dd>
              <select style="width: 450px" name="lizenznr" size="1" onchange="submit();">';
                echo'<option value="---">---</option>';
                echo $option_wrichter;
     echo'    </select>     
            </dd>
          </dl>       
        </li>';
   }
   echo'
      </ul>      
      </div>
    </form>
    </div>';
    //class="groupBox" end
  
  if($gCurrentUser->isWebmaster() || hasRole("Wertungsrichterdozent RR") || hasRole("Wertungsrichterdozent BW")){
    echo'      
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadetab\').click(function(){$(\'#reftab\').toggle(\'slow\');
      })
      });
    </script>    
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Wertungsrichter&uuml;bersicht&nbsp;
          <a class="admGesamttabelle" href="#tab" id="fadetab"><img src="'. THEME_PATH. '/icons/add.png" alt="Gesamttabelle" /><br></a>
        </div>
      </div>
      <div class="groupBoxBody">
      <div style="display:none;" class="groupBoxBody" id="reftab">
        <table border=0 style="font-size: 10pt;">
          <tr valign=top>
            <td><b>Lizenz - Name</b></td>';
            if(!$_SESSION["t_jahr"]){
              echo'<td colspan=2 align="left"><b>Anzahl Turniere 2016</b></td>';
            } else {
              echo'<td colspan=2 align="left"><b>Anzahl Turniere '.$_SESSION["t_jahr"].'</b></td>';          
            }
            echo'<td colspan=7><b>Wertungen</b></td>
          </tr>
          <tr valign=top>
            <td colspan=3>&nbsp</td>
            <td colspan=3><b>TANZ</b></td>
            <td colspan=3><b>AKRO</b></td>
            <td><b>SUMME</b></td>  
          </tr>';
        for($i=0;$i<count($wr_lizenz);$i++){
          echo'
            <tr valign=top>
              <td>'.$wr_lizenz[$i].' - '.$wr_name[$i].'</td>';
          $today = getdate();
          if(!$_SESSION["t_jahr"]){
            list($trnr_gewertet,$anzahl_gewertet) = turniere_gewertet(2016,$wr_lizenz[$i]);
          } else {
            list($trnr_gewertet,$anzahl_gewertet) = turniere_gewertet($_SESSION["t_jahr"],$wr_lizenz[$i]);          
          }
          echo'
              <td width=25px align="center">'.$anzahl_gewertet.'</td>
              <td>';            
          foreach($trnr_gewertet as $name){
            echo'<a href="'.$g_root_path.'/adm_program/modules/results/results4wr.php?tnrsel='.substr($name,1,7).'">'.substr($name,1,7).'</a> '.substr($name,10).'<br>';                      
          }
          $wr_cnt_tnz = wrtgcounttnz($wr_name[$i]);
          $wr_cnt_akr = wrtgcountakr($wr_name[$i]);
          
          $wr_cnt_sum = ($wr_cnt_tnz[$bgcolor_VOK]+$wr_cnt_tnz[$bgcolor_FOK]+$wr_cnt_tnz[$bgcolor_NOK]+
                         $wr_cnt_akr[$bgcolor_VOK]+$wr_cnt_akr[$bgcolor_FOK]+$wr_cnt_akr[$bgcolor_NOK]);
          $wr_cnt_err = ($wr_cnt_tnz[$bgcolor_FOK]+$wr_cnt_tnz[$bgcolor_NOK]+
                         $wr_cnt_akr[$bgcolor_FOK]+$wr_cnt_akr[$bgcolor_NOK]);
          $wr_cnt_prz = 100-((100 * ($wr_cnt_err)) / $wr_cnt_sum);
          
          echo'
              </td>
              <td align=center width=25px bgcolor='.$bgcolor_VOK.'>'.$wr_cnt_tnz[$bgcolor_VOK].'</td>
              <td align=center width=25px bgcolor='.$bgcolor_FOK.'>'.$wr_cnt_tnz[$bgcolor_FOK].'</td>
              <td align=center width=25px bgcolor='.$bgcolor_NOK.'>'.$wr_cnt_tnz[$bgcolor_NOK].'</td>
              <td align=center width=25px bgcolor='.$bgcolor_VOK.'>'.$wr_cnt_akr[$bgcolor_VOK].'</td>
              <td align=center width=25px bgcolor='.$bgcolor_FOK.'>'.$wr_cnt_akr[$bgcolor_FOK].'</td>
              <td align=center width=25px bgcolor='.$bgcolor_NOK.'>'.$wr_cnt_akr[$bgcolor_NOK].'</td>';
              if($wr_cnt_sum != '0'){                                                                   
                echo'<td bgcolor="dfac20" align=center width=25px>'.$wr_cnt_sum.' ('.number_format($wr_cnt_prz,1).'%)</td>';
              } else {
                echo'<td bgcolor="dfac20" align=center width=25px>&nbsp;</td>';
              }
          echo'
            </tr>';    
        }
  echo'</table>        
      </div>
      </div>
    </div>';
    //class="groupBox" end

  list($trnr_gewertet,$anzahl_gewertet) = turniere_gewertet($_SESSION["t_jahr"],$_SESSION["lizenznr"]);
  
  echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Wertungsrichterauswahl</div>
      </div>
      <div class="groupBoxBody">
      <ul class="formFieldList">
        <li>
          <dl>
            <dt>Wertungsrichter:</dt>';
       if(!$_SESSION["lizenznr"] || $_SESSION["lizenznr"] == '---'){
         echo'<dd>noch kein Wertungsrichter ausgew&auml;hlt!</dd>
          </dl>       
        </li>';
       } else {
         echo'<dd>'.$_SESSION["lizenznr"].' - '.$wr_name_key[$_SESSION["lizenznr"]].'</dd>         
          </dl>       
        </li>
        <li>
          <dl>
            <dt>Gewertete Turniere:</dt>
            <dd>';
              $trnrcnt = 0; 
              foreach($trnr_gewertet as $name){
                $trnrcnt++;
                echo'<a href="'.$g_root_path.'/adm_program/modules/results/results4wr.php?tnrsel='.substr($name,1,7).'">'.$name.'</a><br>';                      
              }
       if($trnrcnt == 0) echo'&nbsp';       
       echo'</dd>
          </dl>       
        </li>
        <li>
          <dl>
            <dt>Anzahl:</dt>
            <dd>'.$trnrcnt.'</dd>
          </dl>       
        </li>';
       }
       echo'
      </ul>
      </div>
    </div>';
    //class="groupBox" end
  }
  echo'      
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">Turnierauswahl:</div>  
    </div>
      <form action="'.$g_root_path.'/adm_program/modules/results/results4wr.php?tnrsel='.$getTnrNr.'" method="post">
      <div class="groupBoxBody">
      <ul class="formFieldList">';
  if(!$getTnrNr || $getTnrNr=="---"){
    echo'<li>
          <dl>
            <dt>Turnier:</dt>
            <dd>noch kein Turnier ausgew&auml;hlt!</dd>
          </dl>       
        </li>';    
  } else {
    echo'<li>
            <table border=0><tr>
              <td>Turnier:</td>
              <td colspan=4>
                <b>&nbsp;'.$trnr_name_key[$getTnrNr].'</b>
              </td>
            </tr>
            <tr valign=top>
              <td>Ansicht w&auml;hlen:</td>
              <td colspan=4>            
                <input type="radio" name="view" value="bogen" onclick="submit();" ';
                  if(($getView)=='bogen') echo 'checked="checked"'; 
                    echo '/>Wertungsbogen-Ansicht <font style="background-color:#07c000">(Abz&uuml;ge)</font><br>
                <input type="radio" name="view" value="paare" onclick="submit();" ';
                  if(($getView)=='paare') echo 'checked="checked"'; 
                    echo '/>Paar-Ansicht <font style="background-color:#CCFFFF">(Positivwertung)</font>            
              </td>
            </tr>
            <tr valign=top>
              <td>Bitte ausw&auml;hlen:&nbsp;&nbsp;&nbsp;&nbsp;</td>
              <td>
                <input type="radio" name="skl" value="RR_A" onclick="submit();" ';
                  if(($getStartkl)=='RR_A') echo 'checked="checked"'; 
                    echo '/>A-Klasse<br />
                <input type="radio" name="skl" value="RR_B" onclick="submit();" ';
                  if(($getStartkl)=='RR_B') echo 'checked="checked"'; 
                    echo '/>B-Klasse<br />
                <input type="radio" name="skl" value="RR_C" onclick="submit();" ';
                  if(($getStartkl)=='RR_C') echo 'checked="checked"'; 
                    echo '/>C-Klasse<br />
                <input type="radio" name="skl" value="RR_J" onclick="submit();" ';
                  if(($getStartkl)=='RR_J') echo 'checked="checked"'; 
                    echo '/>J-Klasse<br />
                <input type="radio" name="skl" value="RR_S" onclick="submit();" ';
                  if(($getStartkl)=='RR_S') echo 'checked="checked"'; 
                    echo '/>S-Klasse<br />
                <input type="radio" name="skl" value="ALLE" onclick="submit();" ';
                  if(($getStartkl)=='ALLE') echo 'checked="checked"'; 
                    echo '/>Alle Klassen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
              </td>
              <td>
                <input type="radio" name="skl" value="BW_MA" onclick="submit();" ';
                  if(($getStartkl)=='BW_MA') echo 'checked="checked"'; 
                    echo '/>BW Main_A<br />
                <input type="radio" name="skl" value="BW_MB" onclick="submit();" ';
                  if(($getStartkl)=='BW_MB') echo 'checked="checked"'; 
                    echo '/>BW Main_B<br />
                <input type="radio" name="skl" value="BW_SA" onclick="submit();" ';
                  if(($getStartkl)=='BW_SA') echo 'checked="checked"'; 
                    echo '/>BW Senior_A<br />
                <input type="radio" name="skl" value="BW_SB" onclick="submit();" ';
                  if(($getStartkl)=='BW_SB') echo 'checked="checked"'; 
                    echo '/>BW Senior_B<br />
                <input type="radio" name="skl" value="BW_JA" onclick="submit();" ';
                  if(($getStartkl)=='BW_JA') echo 'checked="checked"'; 
                    echo '/>BW Junior<br />
                <input type="radio" name="skl" value="ALLEBW" onclick="submit();" ';
                  if(($getStartkl)=='ALLEBW') echo 'checked="checked"'; 
                    echo '/>Alle Klassen&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />
              </td>
              <td>
                <input type="radio" name="skl" value="F_RR_M" onclick="submit();" ';
                  if(($getStartkl)=='F_RR_M') echo 'checked="checked"'; 
                    echo '/>Formation Master RR<br />
                <input type="radio" name="skl" value="F_BW_M" onclick="submit();" ';
                  if(($getStartkl)=='F_BW_M') echo 'checked="checked"'; 
                    echo '/>Formation Master BW<br />
                <input type="radio" name="skl" value="F_RR_J" onclick="submit();" ';
                  if(($getStartkl)=='F_RR_J') echo 'checked="checked"'; 
                    echo '/>Formation Jugend<br />
                <input type="radio" name="skl" value="F_RR_LF" onclick="submit();" ';
                  if(($getStartkl)=='F_RR_LF') echo 'checked="checked"'; 
                    echo '/>Formation Lady<br />
                <input type="radio" name="skl" value="F_RR_GF" onclick="submit();" ';
                  if(($getStartkl)=='F_RR_GF') echo 'checked="checked"'; 
                    echo '/>Formation Girl<br />
                <input type="radio" name="skl" value="F_RR_ST" onclick="submit();" ';
                  if(($getStartkl)=='F_RR_ST') echo 'checked="checked"'; 
                    echo '/>Formation Showteam<br />
                <input type="radio" name="skl" value="ALLEFO" onclick="submit();" ';
                  if(($getStartkl)=='ALLEFO') echo 'checked="checked"'; 
                    echo '/>Alle Klassen<br />
              </td>
            </tr>
          </table>
        </li>';
  }
  echo'
      </ul>
      </div>
      </form>
    </div>';
    //class="groupBox" end  
  
  if($getStartkl == ALLE){
    echo gen_html($getTnrNr,'RR_A');
    echo gen_html($getTnrNr,'RR_B');    
    echo gen_html($getTnrNr,'RR_C');    
    echo gen_html($getTnrNr,'RR_J');    
    echo gen_html($getTnrNr,'RR_S');    
  } elseif($getStartkl == ALLEBW) {
    echo gen_html($getTnrNr,'BW_MA');  
    echo gen_html($getTnrNr,'BW_MB');  
    echo gen_html($getTnrNr,'BW_SA');  
    echo gen_html($getTnrNr,'BW_SB');  
    echo gen_html($getTnrNr,'BW_JA');  
  } elseif($getStartkl == ALLEFO) {
    echo gen_html($getTnrNr,'F_RR_M');  
    echo gen_html($getTnrNr,'F_RR_J');  
    echo gen_html($getTnrNr,'F_RR_LF');  
    echo gen_html($getTnrNr,'F_RR_GF');  
    echo gen_html($getTnrNr,'F_RR_ST');  
    echo gen_html($getTnrNr,'F_BW_M');  
  } elseif ($getStartkl != '') {
    if(gen_html($getTnrNr,$getStartkl) != ''){
      echo gen_html($getTnrNr,$getStartkl);  
    } else {
      echo '
      <div class="groupBox">
        <div class="groupBoxHeadline">
          <div style="float: left;">Hinweis!</div>
        </div>
        <div class="groupBoxBody">
          Die gew&auml;hlte Startklasse wurde nicht gefunden!</dt>
        </div>
      </div>';
      //class="groupBox" end    
    }      
  } else {
    echo '';      
  }
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
