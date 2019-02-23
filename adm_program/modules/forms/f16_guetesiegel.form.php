<?php

/******************************************************************************
 * Form for Guetesiegel DRBV
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
$field = array();
//Initialisiere fieldarray
$field['anz_mitglieder_alt'] = 0;
$field['anz_mitglieder_aktuell'] = 0;
$field['anz_mitglieder_kiju'] = 0;
$field['anz_startbuecher_paare'] = 0;
$field['anz_startbuecher_formation'] = 0;
$field['anz_wertungsrichter'] = 0;
$field['anz_turnierleiter'] = 0;
$field['anz_lizenztrainer_drbv'] = 0;
$field['anz_lizenztrainer_dtv'] = 0;
$field['anz_turnierausrichtung'] = 0;
$field['anz_turnierteilnahmen'] = 0;
$field['anz_tsaabnahme'] = 0;
$field['check_kooperation'] = 0;
$field['check_workshops'] = 0;
$field['check_website'] = 0;
$field['check_socialnetworks'] = 0;
$field['check_uebergreifendeangebote'] = 0;

// Fieldarray belegen für POST
if(isset($_POST)){
foreach($_POST as $key => $value){
 if(isset($field[$key])) $field[$key] = $_POST[$key];
}
}

// Checkboxen setzen falls vom User ausgewählt
if($field['check_kooperation']==1) $field['check_kooperation'] = 'checked="checked"';
if($field['check_workshops']==1) $field['check_workshops'] = 'checked="checked"';
if($field['check_website']==1) $field['check_website'] = 'checked="checked"';
if($field['check_socialnetworks']==1) $field['check_socialnetworks'] = 'checked="checked"';
if($field['check_uebergreifendeangebote']==1) $field['check_uebergreifendeangebote'] = 'checked="checked"';

$punkte = array();
// Initialisiere Punktearray

$punkte['anz_mitglieder_aktuell'] = 0;
$punkte['anz_mitglieder_kiju'] = 0;
$punkte['anz_startbuecher_paare'] = 0;
$punkte['anz_startbuecher_formation'] = 0;
$punkte['anz_wertungsrichter'] = 0;
$punkte['anz_turnierleiter'] = 0;
$punkte['anz_lizenztrainer_drbv'] = 0;
$punkte['anz_lizenztrainer_dtv'] = 0;
$punkte['anz_turnierausrichtung'] = 0;
$punkte['anz_turnierteilnahmen'] = 0;
$punkte['anz_tsaabnahme'] = 0;
$punkte['check_kooperation'] = 0;
$punkte['check_workshops'] = 0;
$punkte['check_website'] = 0;
$punkte['check_socialnetworks'] = 0;
$punkte['check_uebergreifendeangebote'] = 0;

// Initialisiere Check-Array
$check['check_kooperation'] = 0;
$check['check_workshops'] = 0;
$check['check_website'] = 0;
$check['check_socialnetworks'] = 0;
$check['check_uebergreifendeangebote'] = 0;

// Initialisiere readonly-Parameter um nachtraegliche Modifizierung der Formulareingaben zu verhindern
$readonly='';

// Initialisiere Versenderinformationen
$versender='';
$versender_mail='';

// Auswertung der Angaben des Vereins

if(isset($_POST['guetesiegel_pruefen'])){
if($_POST['anz_mitglieder_aktuell'] >= 1.0*$_POST['anz_mitglieder_alt']) $punkte['anz_mitglieder_aktuell'] = 40;
if($_POST['anz_mitglieder_kiju'] >= 0.3*$_POST['anz_mitglieder_aktuell']) $punkte['anz_mitglieder_kiju'] = 40;
if($_POST['anz_startbuecher_paare'] >= 2) $punkte['anz_startbuecher_paare'] = 10;
if($_POST['anz_startbuecher_formation'] >= 1) $punkte['anz_startbuecher_formation'] = 10;
if($_POST['anz_wertungsrichter'] >= 1) $punkte['anz_wertungsrichter'] = 10;
if($_POST['anz_turnierleiter'] >= 1) $punkte['anz_turnierleiter'] = 10;
if($_POST['anz_lizenztrainer_drbv'] >= 0.03*$_POST['anz_mitglieder_aktuell']) $punkte['anz_lizenztrainer_drbv'] = 20;
if($_POST['anz_lizenztrainer_dtv'] >= 1) $punkte['anz_lizenztrainer_dtv'] = 10;
if($_POST['anz_turnierausrichtung'] >= 2) $punkte['anz_turnierausrichtung'] = 20;
if($_POST['anz_turnierteilnahmen'] >= 3) $punkte['anz_turnierteilnahmen'] = 10;
if($_POST['anz_tsaabnahme'] >= 1) $punkte['anz_tsaabnahme'] = 10;
if($_POST['check_kooperation']==1){
    $punkte['check_kooperation'] = 10;
    $check['check_kooperation'] = 1;
    }
if($_POST['check_workshops']==1) {
    $punkte['check_workshops'] = 5;
    $check['check_workshops'] = 1;
    }
if($_POST['check_website']==1) {
    $punkte['check_website'] = 5;
    $check['check_website'] = 1;
    }
if($_POST['check_socialnetworks']==1) {
    $punkte['check_socialnetworks'] = 5;
    $check['check_socialnetworks'] = 1;
    }
if($_POST['check_uebergreifendeangebote']==1) {
    $punkte['check_uebergreifendeangebote'] = 5;
    $check['check_uebergreifendeangebote'] = 1;
    }

// Eingabefelder nach Pruefung der Daten sperren 
$readonly='readonly="readonly"';
$hidden_disabled='';
$check_disabled='disabled';

// Versenderinformationen uebernehmen
$versender=$_POST['Versender'];
$versender_mail=$_POST['EMail'];
// Punktsumme berechnen und Zertifizierbarkeit ($status) angeben
$status ='Der Verein hat leider nicht ausreichend Punkte für das Gütesiegel erzielt. ('.array_sum($punkte).'/100 Punkte)';
if(array_sum($punkte) >= 100)  $status = 'Der Verein erhält das Gütesiegel des DRBV. ('.array_sum($punkte).'/100 Punkte)';


}
if(!isset($_POST['guetesiegel_pruefen'])){
// Versteckte Eingabefelder sperren
$hidden_disabled='disabled';
$check_disabled='';
}

// Anzahl Startbuecher/-karten und deren Nummern pro Verein ermitteln
$stb_valid_rr=0;
$stb_valid_bw=0;
$stb_valid_formationen=0;
$stb_rest=0;
$startbuecher=array();  
foreach ($member_array as $memberrow) {
  //foreach ($memberrow as $membercol => $membercont) {   
    //Praeambelturnierstartbuecher 50000+ werden ausgenommen
    if(substr($memberrow[3],0,1) != '5'){ 
      if($memberrow[5] == '1'){
        if(substr($memberrow[4],0,2) == 'RR'){
          $stb_valid_rr++; //RR-Startbuecher
          $startbuecher[] = $memberrow[3];
        }
        elseif(substr($memberrow[4],0,2) == 'BW'){
          $stb_valid_bw++; //BW-Startkarten
          $startbuecher[] = $memberrow[3];
        }
        elseif(substr($memberrow[4],0,1) == 'F'){
          $stb_valid_formationen++; //Formations-Startbuecher
          $startbuecher[] = $memberrow[3];
        }
        else $stb_rest++;
        }
      }
   // }
}
//print_r($member_array);echo" ::DEBUG:member_array<br>";  
//print_r($startbuecher);echo" ::DEBUG:startbuecher<br>";  

// Für vergangenes Kalenderjahr die Anzahl an teilgenommenen Turnieren bestimmen 
$kalenderjahr = date("Y")-1;
$starts       = array();  
$getTurniere  = array();
foreach($startbuecher as $number) {
  $getTurniere[] = getGetanzteTurniere($number);
}  
//print_r($getTurniere);echo" ::DEBUG:getTurniere<br>";
//print_r($kalenderjahr);echo" ::DEBUG:kalenderjahre<br>";

foreach($getTurniere as $trnnum){
  foreach($trnnum as $value){
    if(substr($value,0,3)=='1'.substr($kalenderjahr,2,2)){
      $starts[] = $value;
    }
  }  
}  
$starts = array_unique($starts);  
//print_r($starts);echo' ::DEBUG:starts'.count($starts).'<br>';
  
// Gütesiegeldaten auslesen und berechnen
$mitgliederbestand  = explode('-',$user->getValue('GEMELDETER_MITGLIEDERBESTAND'));
$tanzsportabzeichen = explode('-',$user->getValue('TANZSPORTABZEICHEN_ABNAHME'));
//print_r($mitgliederbestand);echo' ::DEBUG:mitgliederbestand<br>';
//print_r($tanzsportabzeichen);echo' ::DEBUG:tanzsportabzeichen<br>';

foreach($mitgliederbestand as $value){
  $data = explode('/',$value);
  $mtgld_jgd_jahr[$data[0]] = $data[1];
  $mtgld_erw_jahr[$data[0]] = $data[2];
}  
//print_r($mtgld_jgd_jahr);echo' ::DEBUG:mtgld_jgd_jahr<br>';
//print_r($mtgld_erw_jahr);echo' ::DEBUG:mtgld_erw_jahr<br>';
      
if($mtgld_jgd_jahr[date("Y")-2] || $mtgld_erw_jahr[date("Y")-2]){  
  $mtgld_sum_vor2jahren = $mtgld_jgd_jahr[date("Y")-2] + $mtgld_erw_jahr[date("Y")-2];
} else {
  $mtgld_sum_vor2jahren = 'Keine Daten!';
}

$mtgld_sum_aktuell_jgd = $mtgld_jgd_jahr[array_keys($mtgld_jgd_jahr)[count($mtgld_jgd_jahr)-1]]; 
$mtgld_sum_aktuell_erw = $mtgld_erw_jahr[array_keys($mtgld_erw_jahr)[count($mtgld_erw_jahr)-1]];

foreach($tanzsportabzeichen as $value){
  $data = explode('/',$value);
  for($i=1;$i<=count($data)-1;$i++){
    $tsa_jahr[$data[0]] = $i;
  }
}  
//print_r($tsa_jahr);echo' ::DEBUG:tsa_jahr<br>';  
$tsa_jahr_2jahre = $tsa_jahr[date("Y")-2] + $tsa_jahr[date("Y")-1]; 

// Anzahl und Name der Turnierleiter eines Vereins holen  
$tleiter = getTurnierleiter();
$tl      = array();  
foreach ($tleiter as $key => $row) {
  if($row['verein'] == $user->getValue('VEREINSNUMMER')){
    $tl[] = $row['vname'].' '.$row['nname'];
  }
} 
//print_r($tl);echo' ::DEBUG:tl<br>';  
  
// Anzahl und Name der Wertungsrichter eines Vereins holen  
$wrichter = getWertungsrichter();
$wr       = array();  
foreach ($wrichter as $key => $row) {
  if($row['verein'] == $user->getValue('VEREINSNUMMER')){
    $wr[] = $row['vname'].' '.$row['nname'];
  }
} 
//print_r($wr);echo' ::DEBUG:wr<br>';  
  
// Anzahl und Name der Trainer/Kursleiter eines Vereins holen  
$trainer = getTrainer();
$tr      = array();  
$tru     = array();  
foreach ($trainer as $key => $row) {
  if($row['verein'] == $user->getValue('VEREINSNUMMER')){
    $tr[] = $row['vname'].' '.$row['nname'];
  }
} 
$tru = array_unique($tr);  
//print_r($tru);echo' ::DEBUG:tru<br>';

// Info zu ausgerichteten Turnieren eines Vereins holen  
$ausg_tur_pjahr = array();
$turniere       = getEigeneTurniere($user->getValue('VEREINSNUMMER'));

//testen  
//$turniere = array( 
//    0 => array("dat_begin"=>"2016-11-29 13:00:00", "dat_tform"=>"A", "dat_headline"=>"11"), 
//    1 => array("dat_begin"=>"2016-11-29 13:00:00", "dat_tform"=>"B", "dat_headline"=>"22"), 
//    2 => array("dat_begin"=>"2017-11-28 13:00:00", "dat_tform"=>"C", "dat_headline"=>"33"), 
//); 
  
foreach ($turniere as $key => $row) {
  $turnierjahr = substr($row['dat_begin'],0,4);
  //nur Turniere der letzten beiden Jahre berücksichtigen
  if($turnierjahr == date("Y")-1 || $turnierjahr == date("Y")-2){
    $ausg_tur_pjahr[] = array($row['dat_begin'],$row['dat_tform'],$row['dat_headline'],$key);
  }  
}
//print_r($ausg_tur_pjahr);echo' ::DEBUG:ausg_tur_pjahr<br>';
//unterscheiden: zwei oder mehr Turniernummern am gleichen Tag, gelten als eine Veranstaltung 
$turniercount = 0;    
for($i=1;$i<=count($ausg_tur_pjahr);$i++){
  if(substr($ausg_tur_pjahr[$i-1][0],8,2) != substr($ausg_tur_pjahr[$i][0],8,2)) $turniercount++ ;
}

$sum_baspkt = 0;
$sum_xtrpkt = 0;
$sum_punkte = 0;
//Werte der zur Verfügung stehenden Kriterien
$prz_krit01 = round(((($mtgld_sum_aktuell_jgd+$mtgld_sum_aktuell_erw)*100)/$mtgld_sum_vor2jahren),1);  
if($mtgld_sum_aktuell_jgd+$mtgld_sum_aktuell_erw >= $mtgld_sum_vor2jahren){ 
  $pkt_krit01 = 40;
  $ico_krit01 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Mitgliederzahl konstant oder erhöht!">';
  $sum_baspkt = $sum_baspkt + 40;
} else {
  $pkt_krit01 = 0;
  $ico_krit01 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Mitgliederzahl leider gesunken!">';
}
  
$prz_krit02 = round((($mtgld_sum_aktuell_jgd)*100)/($mtgld_sum_aktuell_jgd+$mtgld_sum_aktuell_erw),1);  
if($prz_krit02 >= 30){ 
  $pkt_krit02 = 40;
  $ico_krit02 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Anteil Kinder/Jugend liegt über 30%!">';
  $sum_baspkt = $sum_baspkt + 40;
} else {
  $pkt_krit02 = 0;
  $ico_krit02 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Anteil Kinder/Jugend liegt unter 30%!">';
}

if(($stb_valid_rr+0.5*$stb_valid_bw) >= 2){ 
  $pkt_krit03 = 10;
  $ico_krit03 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Anzahl startberechtigter Paare sind mehr als zwei!">';
  $sum_baspkt = $sum_baspkt + 10;
} else {
  $pkt_krit03 = 0;
  $ico_krit03 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Anzahl startberechtigter Paare liegt unter zwei!">';
}

if($stb_valid_formationen >= 1){ 
  $pkt_krit04 = 10;
  $ico_krit04 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Mindestens eine startberechtigte Formation!">';
  $sum_baspkt = $sum_baspkt + 10;
} else {
  $pkt_krit04 = 0;
  $ico_krit04 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Leider keine startberechtigte Formation!">';
}

if(count($wr) >= 1){ 
  $pkt_krit05 = 10;
  $ico_krit05 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Mindestens einen Wertungsrichter gibt es im Verein!">';
  $sum_baspkt = $sum_baspkt + 10;
} else {
  $pkt_krit05 = 0;
  $ico_krit05 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Leider kein Wertungsrichter im Verein verfügbar!">';
}

if(count($tl) >= 1){ 
  $pkt_krit06 = 10;
  $ico_krit06 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Mindestens einen Turnierleiter gibt es im Verein!">';
  $sum_baspkt = $sum_baspkt + 10;
} else {
  $pkt_krit06 = 0;
  $ico_krit06 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Leider kein Turnierleiter im Verein verfügbar!">';
}

$prz_krit07 = round((count($tr)*100)/($mtgld_sum_aktuell_jgd+$mtgld_sum_aktuell_erw),1);  
if($prz_krit07 >= 3){
  $pkt_krit07 = 20;
  $ico_krit07 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Anteil Trainer/Kursleiter liegt über 3%!">';
  $sum_baspkt = $sum_baspkt + 20;
} else {
  $pkt_krit07 = 0;
  $ico_krit07 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Anteil Trainer/Kursleiter liegt unter 3%!">';
}
 
if($turniercount >= 2){
  $pkt_krit08 = 20;
  $ico_krit08 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Es wurden mehr als zwei Turniere ausgerichtet!">';
  $sum_baspkt = $sum_baspkt + 20;
} else {
  $pkt_krit08 = 0;
  $ico_krit08 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Es wurden weniger als zwei Turniere ausgerichtet!">';
}
 
if(count($starts) >= 3){
  $pkt_krit09 = 10;
  $ico_krit09 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Es liegen mehr als drei Turnierteilnahmen vor!">';
  $sum_baspkt = $sum_baspkt + 10;
} else {
  $pkt_krit09 = 0;
  $ico_krit09 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Es liegen weniger als drei Turnierteilnahmen vor!">';
}

if($tsa_jahr_2jahre >= 2){
  $pkt_krit10 = 20;
  $ico_krit10 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Es wurden mehr als zwei Tanzsportabnahmen angeboten!">';
  $sum_baspkt = $sum_baspkt + 20;
} else {
  $pkt_krit10 = 0;
  $ico_krit10 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Es wurden weniger als zwei Tanzsportabnahmen angeboten!">';
}
 
if($sum_baspkt >= 100){
  $ico_bassum = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/siegelj.png" title="Toll, das Gütesiegel kann bereits beantragt werden!">';
} else {
  $ico_bassum = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/siegelv.png" title="Mal sehen, evtl. sind noch einige Zusatzpunkte zu erzielen!">';
}

$andere_trainer = 0;
if($andere_trainer){
  $pkt_krit16 = 10;
  $txt_krit16 = 'Anzahl?';
  $ico_krit16 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Kooperation Verein/Schule wurde angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 10;
} else {
  $pkt_krit16 = 0;
  $txt_krit16 = 'Nein';
  $ico_krit16 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Kooperation Verein/Schule wurde nicht angeboten!">';
}
  
if($user->getValue('GEPRüFT:_KOOPERATION_VEREIN/SCHULE')){
  $pkt_krit11 = 10;
  $txt_krit11 = 'Ja';
  $ico_krit11 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Kooperation Verein/Schule wurde angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 10;
} else {
  $pkt_krit11 = 0;
  $txt_krit11 = 'Nein';
  $ico_krit11 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Kooperation Verein/Schule wurde nicht angeboten!">';
}

if($user->getValue('GEPRüFT:_OFFENE_WORKSHOPS_IN_DEN_LETZTEN_ZWEI_KALENDERJAHREN')){
  $pkt_krit12 = 5;
  $txt_krit12 = 'Ja';
  $ico_krit12 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Offene Workshops wurden angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 5;
} else {
  $pkt_krit12 = 0;
  $txt_krit12 = 'Nein';
  $ico_krit12 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Offene Workshops wurden nicht angeboten!">';
}

if($user->getValue('GEPRüFT:_FUNKTIONIERENDER_UND_GEPFLEGTER_INTERNETAUFTRITT')){
  $pkt_krit13 = 5;
  $txt_krit13 = 'Ja';
  $ico_krit13 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Offene Workshops wurden angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 5;
} else {
  $pkt_krit13 = 0;
  $txt_krit13 = 'Nein';
  $ico_krit13 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Offene Workshops wurden nicht angeboten!">';
}

if($user->getValue('GEPRüFT:_AKTIVE_NUTZUNG_VON_SOZIALEN_NETZWERKEN')){
  $pkt_krit14 = 5;
  $txt_krit14 = 'Ja';
  $ico_krit14 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Offene Workshops wurden angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 5;
} else {
  $pkt_krit14 = 0;
  $txt_krit14 = 'Nein';
  $ico_krit14 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Offene Workshops wurden nicht angeboten!">';
}

if($user->getValue('GEPRüFT:_SPORTARTüBERGREIFENDE_SPORTANGEBOTE')){
  $pkt_krit15 = 5;
  $txt_krit15 = 'Ja';
  $ico_krit15 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/ok.png" title="Offene Workshops wurden angeboten!">';
  $sum_xtrpkt = $sum_xtrpkt + 5;
} else {
  $pkt_krit15 = 0;
  $txt_krit15 = 'Nein';
  $ico_krit15 = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/no.png" title="Offene Workshops wurden nicht angeboten!">';
}

if(($sum_baspkt + $sum_xtrpkt)  >= 100){
  $ico_sum = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/siegelj.png" title="Toll, das Gütesiegel kann beantragt werden!">';
} else {
  $ico_sum = '<img style="vertical-align: top;" src="'.THEME_PATH.'/icons/siegeln.png" title="Die Vorraussetzungen für das Gütesiegel wurden leider nicht erfüllt!">';
}

$letztes_gsiegel_datum = '';  
if($user->getValue('GüTESIEGEL_DATUM')){
 $letztes_gsiegel_datum = $user->getValue('GüTESIEGEL_DATUM');
}
if($user->getValue('GüTESIEGEL_ERTEILT')){
  //check zwei Jahre Gültigkeit
  if((time()-strtotime($letztes_gsiegel_datum)) > 3600*24*365){
    $gsiegel_erteilt = 'Gütesiegel abgelaufen!<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/siegeln.png" title="Gütesiegel abgelaufen!">';  
  } else {
    $gsiegel_erteilt = 'Gütesiegel erteilt! <img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/siegelj.png" title="Gütesiegel erteilt!">';
  }
} else {
  $gsiegel_erteilt = 'Gütesiegel beantragen. <img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/siegelv.png" title="Gütesiegel beantragen.">';
} 
    
$html = ' 
  <form name="Guetesiegelantrag" action="'.$g_root_path.'/adm_program/modules/forms/mail_send2gsiegel.php?form_id=16" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
  <!-- Vereinsdaten '.$_SERVER['PHP_SELF'].' -->
  <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Absender Informationen</legend>
                     <table>
                       <tr>
                         <td width="30%">Ihr Name:</td>
                         <td><input type="text" name="Versender" value="'.$versender.'" tabindex="1" size="50"></td>
                       </tr>
                       <tr>
                         <td>Ihre Mailadresse:</td>
                         <td><input type="text" name="EMail" value="'.$versender_mail.'" tabindex="2" size="50"></td>
                       </tr>
                         <tr>
                         <td>&nbsp;</td>
                         <td>
                           <div class="formlabel">
                             Zusätzlich zur Vereinsmail wird die Formularbestätigung an diese Mail versendet.
                           </div>
                         </td>
                       </tr>
                       <tr>
                         <td>Verein:</td>
                         <td><input type="text" name="Verein" value="'.$user->getValue('VEREIN').'" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Vereinsnummer:</td>
                         <td><input type="text" name="Vereinsnummer" value="'.$user->getValue('VEREINSNUMMER').'" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Bundesland:</td>
                         <td><input type="text" name="Bundesland" value="'.$user->getValue('BUNDESLAND').'" readonly="readonly"></td>
                       </tr>
                     </table>
                   </fieldset>
                   </font>

<!-- Basispunkte -->
<font face="Verdana" size="3" color="#000080"><br />
  <fieldset>
    <legend>Gütesiegel Status</legend>
    <table cellspacing="12px">';
      if($letztes_gsiegel_datum != ''){
        $html .= '
          <tr>
            <th>Folgeantrag</th>
            <th>Letzter Antrag</th>
            <th>Status</th>
          </tr>    
          <tr>
            <td><input type="hidden" name="antrag" value="Folgeantrag" style="readonly="readonly"/></td>
            <td>'.$letztes_gsiegel_datum.'</td>
            <td>'.$gsiegel_erteilt.'</td>
          </tr>          
        '; 
      } else {
        $html .= '
          <tr>
            <th>Erstantrag</th>
            <th>Datum</th>
            <th>Status</th>
          </tr>    
          <tr>
            <td><input type="hidden" name="antrag" value="Erstantrag" style="readonly="readonly"/></td>
            <td>'.date("d.m.Y").'</td>
            <td>'.$gsiegel_erteilt.'</td>
          </tr>          
        ';
      }       
      $html .= '    
    </table>                                                                                                                                                                                                                    
  </fieldset>
</font>
                                                                                             
<!-- Basispunkte -->
<font face="Verdana" size="3" color="#000080"><br />
  <fieldset>
  <legend>Basispunkte</legend>
  <table cellspacing="12px">
    <tr>
        <th width="400px">Bewertungskriterien</th>
        <th width="80px" align="center">Ihr Verein</th>
        <th width="50px" align="center">Soll</th>
        <th width="70px" align="center">Punkte</th>
    </tr>
    <tr>
        <td width="400px" align="left">Gemeldeter Mitgliederbestand mit DRBV-Zugehörigkeit vor 2 Jahren</td>
        <td align="center"><input type="text" name="anz_mitglieder_alt" value="'.$mtgld_sum_vor2jahren.'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center"></td>
        <td width="50px" align="center"></td>
    </tr>
    <tr>
        <td width="400px" align="left">Letzter gemeldeter Mitgliederbestand mit DRBV-Zugehörigkeit</td>
        <td align="center"><input type="text" name="anz_mitglieder_aktuell" value="'.($mtgld_sum_aktuell_jgd+$mtgld_sum_aktuell_erw).' ('.$prz_krit01.'%)" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">100%</td>
        <td width="70px" align="center">'.$pkt_krit01.'/40</td>
        <td width="30px" align="center">'.$ico_krit01.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl Kinder und Jugendlicher bis 18 Jahre aktuelle Mitgliedermeldung</td>
        <td align="center"><input type="text" name="anz_mitglieder_kiju" value="'.$mtgld_sum_aktuell_jgd.' ('.$prz_krit02.'%)" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">30%</td>
        <td width="70px" align="center">'.$pkt_krit02.'/40</td>
        <td width="30px" align="center">'.$ico_krit02.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl Startbücher/-karten (Paare)</td>
        <td align="center"><input type="number" name="anz_startbuecher_paare" value="'.($stb_valid_rr+0.5*$stb_valid_bw).'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">2</td>
        <td width="70px" align="center">'.$pkt_krit03.'/10</td>
        <td width="30px" align="center">'.$ico_krit03.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl Startbücher Formationen</td>
        <td align="center"><input type="number" name="anz_startbuecher_formation" value="'.$stb_valid_formationen.'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">1</td>
        <td width="70px" align="center">'.$pkt_krit04.'/10</td>
        <td width="30px" align="center">'.$ico_krit04.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl Wertungsrichter im Verein</td>
        <td><input type="number" name="anz_wertungsrichter" value="'.count($wr).'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">1</td>
        <td width="70px" align="center">'.$pkt_krit05.'/10</td>
        <td width="30px" align="center">'.$ico_krit05.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl Turnierleiter im Verein</td>
        <td align="center"><input type="number" name="anz_turnierleiter" value="'.count($tl).'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">1</td>
        <td width="70px" align="center">'.$pkt_krit06.'/10</td>
        <td width="30px" align="center">'.$ico_krit06.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl lizenzierter DRBV-Trainer/Kursleiter</td>
        <td align="center"><input type="text" name="anz_lizenztrainer_drbv" value="'.count($tru).' ('.$prz_krit07.'%)"" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">3%</td>
        <td width="70px" align="center">'.$pkt_krit07.'/20</td>
        <td width="30px" align="center">'.$ico_krit07.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl ausgerichteter, tagesverschiedener DRBV-Turniere und Wettbewerbe in den letzten zwei Kalenderjahren</td>
        <td align="center"><input type="number" name="anz_turnierausrichtung" value="'.$turniercount.'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">2</td>
        <td width="70px" align="center">'.$pkt_krit08.'/20</td>
        <td width="30px" align="center">'.$ico_krit08.'</td>
    </tr>
        <tr>
        <td width="400px" align="left">Anzahl Turnierteilnahmen des Vereins im letzten Kalenderjahr (pro Turnier nur 1x mgl.)</td>
        <td align="center"><input type="number" name="anz_turnierteilnahmen" value="'.count($starts).'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">3</td>
        <td width="70px" align="center">'.$pkt_krit09.'/10</td>
        <td width="30px" align="center">'.$ico_krit09.'</td>
    </tr>
    <tr>
        <td width="400px" align="left">Anzahl TSA-Abnahmen (Prüfungsveranstaltung) in den letzen zwei Kalenderjahren</td>
        <td align="center"><input type="number" name="anz_tsaabnahme" value="'.$tsa_jahr_2jahre.'" autofocus style="width: 100px; background:#ebebeb; text-align:center;" readonly="readonly"/></td>
        <td width="50px" align="center">2</td>
        <td width="70px" align="center">'.$pkt_krit10.'/20</td>
        <td width="30px" align="center">'.$ico_krit10.'</td>
    </tr>
    <tr>
        <td colspan="5" align="center"><hr></td>
    </tr>
    <tr>
        <td colspan="3" align="right"><b>Erreichte Grundpunktzahl</b></td>
        <td width="70px" align="center"><input type="hidden" name="res_basispkt" value="'.$sum_baspkt.'" style="readonly="readonly"/><b>'.$sum_baspkt.'/100</b></td>
        <td width="30px" align="center">'.$ico_bassum.'</td>
    </tr>
  </table>
</fieldset>
</font>

<!-- Zusatzpunkte --> 
<font face="Verdana" size="3" color="#000080"><br />
  <fieldset>
  <legend>Zusatzpunkte</legend>
  <table cellspacing="12px"> 
  <tr>
        <th width="400px">Bewertungskriterien</th>
        <th width="90px" align="center">Ihr Verein</th>
        <th width="70px" align="center">Soll</th>
        <th width="90px" align="center">Punkte</th>
        <td width="30px" align="center"></td>
        <td width="30px" align="center"></td>
    </tr>    
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>
    <tr>
        <td width="400px" align="left">Andere eingesetzte Trainer mit DTV oder LSB Lizenz
          <span id="admAddLizTrainer" class="iconTextLink" style="display: inline;">
	    <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_ADD_LIZTRAIN&amp;message_var1=&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_ADD_LIZTRAIN&amp;message_var1=\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>	    				   
	  </span>         
        </td>
        <td width="90px" align="center" valign="top">'.$txt_krit16.'</td>
        <td width="70px" align="center" valign="top">1</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit16.'/10</td>
        <td width="30px" align="center" valign="top">'.$ico_krit16.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_liztrainer'].' name="check_liztrainer" '.$check_disabled.'/><input type="hidden" value="'.$check['check_liztrainer'].'" name="check_liztrainer" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">Anzahl:</td>
        <td colspan="4" align="left"><input type="number" name="anz_lizenztrainer_dtv" value="0" autofocus style="width: 25px; background:#yellow; text-align:left;" /></td>
        <td width="30px" align="center"></td>
    </tr>
    <script type="text/javascript"><!--
      $(document).ready(function() {
      $("#Versender").focus();
      $(".admLinkAddAttachmentLizTrainer").css("cursor", "pointer");
      // add new line to add new attachment to this mail
      $(".admLinkAddAttachmentLizTrainer").click(function () {
	newAttachmentLizTrainer = document.createElement("input");
	$(newAttachmentLizTrainer).attr("type", "file");
	$(newAttachmentLizTrainer).attr("name", "userfile[]");
	$(newAttachmentLizTrainer).css("display", "block");
	$(newAttachmentLizTrainer).css("width", "90%");
	$(newAttachmentLizTrainer).css("margin-bottom", "5px");
	$(newAttachmentLizTrainer).hide();
	$("#admAddAttachmentLizTrainer").before(newAttachmentLizTrainer);
	$(newAttachmentLizTrainer).show("slow");
      });
     });   
    //--></script>						    
    <tr>
      <td width="400px" align="right">Lizenz Anhang:</td>
      <td colspan="4" align="left">
	<input type="hidden" name="AttachmentLizTrainer" value="' . ($gPreferences['max_email_attachment_size'] * 1024) . '" />
	<span id="admAddAttachmentLizTrainer" class="iconTextLink" style="display: block;">
	   <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_MAX_ATTACHMENT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_MAX_ATTACHMENT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
	   <a class="admLinkAddAttachmentLizTrainer"><img
	    src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('MAI_ADD_ATTACHEMENT').'" /></a>
	   <a class="admLinkAddAttachmentLizTrainer" style="font-size:.8em;"></a>				   
	</span>  
      </td>
      <td width="30px" align="center"></td>
    </tr>
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>           
    <tr>
        <td width="400px" align="left" valign="top">Durchführung Kooperation Verein/Schule/GTA
          <br>(im letzten Kalenderjahr)          
          <span id="admAddKoop" class="iconTextLink" style="display: inline;">
	    <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_ADD_KOOP&amp;message_var1=&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_ADD_KOOP&amp;message_var1=\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>	    				   
	  </span>                 
        </td>
        <td width="90px" align="center" valign="top">'.$txt_krit11.'</td>
        <td width="70px" align="center" valign="top">ja</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit11.'/10</td>
        <td width="30px" align="center" valign="top">'.$ico_krit11.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_kooperation'].' name="check_kooperation" '.$check_disabled.'/><input type="hidden" value="'.$check['check_kooperation'].'" name="check_kooperation" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">zu prüfende Url:</td>
        <td colspan="4" align="left"><input type="text" name="gta_url" value="?" autofocus style="width: 250px; background:#yellow; text-align:left;" /></td>
        <td width="30px" align="center"></td>
    </tr>
     <script type="text/javascript"><!--
      $(document).ready(function() {
      $("#Versender").focus();
      $(".admLinkAddAttachmentKoopVertrag").css("cursor", "pointer");
      // add new line to add new attachment to this mail
      $(".admLinkAddAttachmentKoopVertrag").click(function () {
	newAttachmentKoopVertrag = document.createElement("input");
	$(newAttachmentKoopVertrag).attr("type", "file");
	$(newAttachmentKoopVertrag).attr("name", "userfile[]");
	$(newAttachmentKoopVertrag).css("display", "block");
	$(newAttachmentKoopVertrag).css("width", "90%");
	$(newAttachmentKoopVertrag).css("margin-bottom", "5px");
	$(newAttachmentKoopVertrag).hide();
	$("#admAddAttachmentKoopVertrag").before(newAttachmentKoopVertrag);
	$(newAttachmentKoopVertrag).show("slow");
      });
     });   
    //--></script>						    
    <tr>
      <td width="400px" align="right">Kooperationsvertrag Anhang:</td>
      <td colspan="4" align="left">
	<input type="hidden" name="AttachmentKoopVertrag" value="' . ($gPreferences['max_email_attachment_size'] * 1024) . '" />
	<span id="admAddAttachmentKoopVertrag" class="iconTextLink" style="display: block;">
	   <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_MAX_ATTACHMENT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_MAX_ATTACHMENT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
	   <a class="admLinkAddAttachmentKoopVertrag"><img
	    src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('MAI_ADD_ATTACHEMENT').'" /></a>
	   <a class="admLinkAddAttachmentKoopVertrag" style="font-size:.8em;"></a>				   
	</span>  
      </td>
      <td width="30px" align="center"></td>
    </tr>               
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>    
    <tr>
        <td width="400px" align="left" valign="top">Offene Workshops<br>(der letzten zwei Kalenderjahre)
          <span id="admAddWorkshop" class="iconTextLink" style="display: inline;">
	    <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_ADD_WORKSHOP&amp;message_var1=&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_ADD_WORKSHOP&amp;message_var1=\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>	    				   
	  </span>                 
        </td>
        <td width="90px" align="center" valign="top">'.$txt_krit12.'</td>
        <td width="70px" align="center" valign="top">ja</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit12.'/5</td>
        <td width="30px" align="center" valign="top">'.$ico_krit12.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_workshops'].' name="check_workshops" '.$check_disabled.'/><input type="hidden" value="'.$check['check_workshops'].'" name="check_workshops" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">zu prüfende Url:</td>
        <td colspan="4" align="left"><input type="text" name="workshop_url" value="?" autofocus style="width: 250px; background:#yellow; text-align:left;" /></td>
        <td width="30px" align="center"></td>
    </tr>
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>    
    <tr>
        <td width="400px" align="left" valign="top">Funktionierender und gepflegter Internetauftritt<br>des Vereines/der Abteilung</td>
        <td width="90px" align="center" valign="top">'.$txt_krit13.'</td>
        <td width="70px" align="center" valign="top">ja</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit13.'/5</td>
        <td width="30px" align="center" valign="top">'.$ico_krit13.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_website'].' name="check_website" '.$check_disabled.'/><input type="hidden" value="'.$check['check_website'].'" name="check_website" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">zu prüfende Url:</td>
        <td colspan="4" align="left">
           <input type="text" name="website_url" value="'.$user->getValue('WEBSEITE').'" autofocus style="width: 250px; background:#yellow; text-align:left;" readonly="readonly"/></td>
           <input type="hidden" name="website" value="'.$user->getValue('WEBSEITE').'" readonly="readonly"/></td>
        <td width="30px" align="center"></td>
    </tr>
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>    
    <tr>
        <td width="400px" align="left">Aktive Nutzung von sozialen Netzwerken<br>z.B. Facebook für Vereinsdarstellung</td>
        <td width="90px" align="center" valign="top">'.$txt_krit14.'</td>
        <td width="70px" align="center" valign="top">ja</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit14.'/5</td>
        <td width="30px" align="center" valign="top">'.$ico_krit14.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_socialnetworks'].' name="check_socialnetworks" '.$check_disabled.'/><input type="hidden" value="'.$check['check_socialnetworks'].'" name="check_socialnetworks" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">zu prüfende Url:</td>
        <td colspan="4" align="left"><input type="text" name="social_url" value="'.$user->getValue('SOZIALES_NETZWERK').'" autofocus style="width: 250px; background:#yellow; text-align:left;" readonly="readonly"/></td>
        <td width="30px" align="center"></td>
    </tr>
    <tr>
        <td width="400px" colspan="6" align="center"><hr style="border-style: dashed; size: 1px;"></td>
    </tr>    
    <tr>
        <td width="400px" align="left">Sportartübergreifende Sportangebote
          <span id="admAddSportangebote" class="iconTextLink" style="display: inline;">
	    <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_ADD_SPORTANGEBOTE&amp;message_var1=&amp;inline=true"><img 
	    onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_ADD_SPORTANGEBOTE&amp;message_var1=\',this)" onmouseout="ajax_hideTooltip()"
	    class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>	    				   
	  </span>          
        </td>
        <td width="90px" align="center" valign="top">'.$txt_krit15.'</td>
        <td width="70px" align="center" valign="top">ja</td>
        <td width="90px" align="center" valign="top">'.$pkt_krit15.'/5</td>
        <td width="30px" align="center" valign="top">'.$ico_krit15.'</td>
        <td width="30px" align="center" valign="top"><input type="checkbox" value="1" '.$field['check_uebergreifendeangebote'].' name="check_uebergreifendeangebote" '.$check_disabled.'/><input type="hidden" value="'.$check['check_uebergreifendeangebote'].'" name="check_uebergreifendeangebote" '.$hidden_disabled.'/></td>
    </tr>
    <tr>
        <td width="400px" align="right">zu prüfende Url:</td>
        <td colspan="4" align="left"><input type="text" name="sportangebote_url" value="?" autofocus style="width: 250px; background:#yellow; text-align:left;" /></td>
        <td width="30px" align="center"></td>
    </tr>
    <tr>
        <td colspan="6" align="center"><hr></td>
    </tr>
    <tr>
        <td colspan="3" align="right"><b>Erreichte Extrapunktzahl</b></td>
        <td width="90px" align="center"><input type="hidden" name="res_xtrpkt" value="'.$sum_xtrpkt.'" style="readonly="readonly"/><b>'.$sum_xtrpkt.'/40</b></td>
        <td width="30px" align="center">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3" align="right"><b>Erreichte Gesamtpunktzahl</b></td>
        <td width="90px" align="center"><b>'.($sum_baspkt+$sum_xtrpkt).'/100</b></td>
        <td width="30px" align="center">'.$ico_sum.'</td>
    </tr>
   
  </table>
</fieldset>
</font>
                                                                                                                                                                                                                   
<font face="Verdana" size="3" color="#000080">
  <fieldset>
  <legend>Zertifizierung</legend>  ';

  if(date("n") > 6 || $gCurrentUser->isWebmaster()){                     
    if(!isset($_POST['guetesiegel_pruefen'])){ 
      $html .= '<input type="submit" style="width: 200px;" name="guetesiegel_pruefen" value="Zertifizierung prüfen" align="right"/>';  
    } else {
      $html .= $status;
      $html .= '<input type="hidden" name="guetesiegel_status" value="'.$status.'"/> ';
      $html .= '<br /><br /><input type="submit" style="width: 145px;" name="button_mail" value="Beantragen" align="right"/> ';
    }                    
  } else {
    $html .= '<i>Validierung und Antragsstellung nur von Juli bis Dezember möglich!</i>';  
  }
  $html .= '  
  </fieldset>
  </font>
</form> 
<div id="pasteMe"></div> ';

echo $html;

?>