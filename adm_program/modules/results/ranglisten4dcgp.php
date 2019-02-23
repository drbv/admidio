<?php
/******************************************************************************
 * Ranglisten anzeigen
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
require_once('../../system/classes/table_roles.php');
require_once('../../system/drbv_funktionen.php');

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
//$getMode   = "";

$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getRegion  = admFuncVariableIsValid($_GET, 'regio');   
  
// create user object
$user = new User($gDb, $gProfileFields, $getUserId);
  
unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

if(!$_GET){
  if(!$_SESSION["getAuswahl"] || $_POST["cupserie"] != $_SESSION["getAuswahl"])
      $_SESSION["getAuswahl"] = $_POST["cupserie"];  
} 
$getStartklassen = $_POST['startklassen'];  
      
//Ersatzpunkte internationale Einsätze zuordnen in array mit Turniernummern als key
//und Startbuchnummern als werte
$intein = explode(";", $gPreferences['txt_intein']);//turniernummern splitten
foreach($intein as $value){
  $inteintrn             = substr($value,0,7);//turniernummern extrahieren
  $inteinstb[$inteintrn] = explode(",",substr($value,8));
}
//print_r($inteinstb);echo' :DEBUG::inteinstb<br>';        

$tanzpkte_ansicht = explode(";", $gPreferences['txt_tanzpktansicht']);//user mit ansicht fuer die erzielten tanzpkt
if(in_array($gCurrentUser->getValue('usr_id'), $tanzpkte_ansicht)){
  $enable_tanzpkte_ansicht = true;
}
      
function getRngLstnPkt4cup($StartbuchNr,$startklasse,$region,$mode){
  
  global $gPreferences, $inteinstb;
  
  //Definiere Ranglistenpunkte, ab Platz8 wird das array mit einem
  //Punkt bis Platz200 gefuellt
  //$rangpkttab   = [1 => '20','15','10','8','6','4','2','1'];
  //for($i = 9; $i < 201; $i++){
  //  $rangpkttab[] = 1;
  //}
  //alte Variante zum check
  $rangpkttab   = [1 => '20','15','10','8','6','4','2','1','0'];
  for($i = 10; $i < 201; $i++){
    $rangpkttab[] = 0;
  }
  //print_r($rangpkttab);echo' :DEBUG::rangpkttab<br>';

  //Cup-Turniere werden in den Organisationseinstellungen gesetzt!  
  //und deren turniernummer in einem array zugeordnet
  if($region=='Nord'){
    $trn_nr = explode(";", $gPreferences['txt_nc_trn']);
    $abs_nr = $gPreferences['txt_nc_ndm'];
  }
  if($region=='Sued'){
    $trn_nr = explode(";", $gPreferences['txt_sc_trn']);
    $abs_nr = $gPreferences['txt_sc_sdm'];
  }
  if($region=='DCGP'){
    $trn_nr = explode(";", $gPreferences['txt_gpdat']);
    //$trn_nr = array();
  }
  //print_r($trn_nr);echo' :DEBUG::trn_nr<br>';  
  
  $rnglstnpkt4cup_4stkl = array();
  $anzahl_turniere      = 0;
  $teilnehmende_stb     = array();
  $teilnehmende_cup     = array();
  $erg_htm              = array();

  foreach($trn_nr as $turniernr){
    //arrays leeren
    $rnglstnpkt      = array();
    $erg_korrigiert  = array();
    $erg_ohnekorrkt  = array();
    $erg_mitkorrkt   = array();
    $erg_platzierung = array();
    
    //wenn abschlussturnier, dann multiplikator auf zwei setzen
    //da doppelte ranglistenpunkte vergeben werden
    if($turniernr == $abs_nr){
      $abs_multi = 2;
    } else {
      $abs_multi = 1;
    }
    
    //suche in der DB nach turniernummer und startklasse
    $sqlab = 'SELECT startbuch,platz,cup_serie,dame,herr,verein,paar_id_tlp,RT_ID_Ausgeschieden
              FROM paare WHERE             
              turniernummer = "'.$turniernr.'" AND
              startklasse   = "'.$startklasse.'" ORDER BY platz'; 
    $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
    $platz_korrektur = 1;
    $platz_vorher    = 0;
    $paare[0]        = array();  
    //print_r($sqlab);echo' :DEBUG::sqlab<br>';
    //print_r($punkteDB);echo' :DEBUG::punkteDB<br>';

    //in dem gesuchten ergebnis koennen grenzverkehr paare und paare
    //aus der jeweils anderen region sein: diese werden hier geloescht    
    while($platz = mysqli_fetch_array($punkteDB)){
      //print_r($platz);echo' :DEBUG::platz<br>';
      // Tanzpaarpunkteergebnis in Datenbank suchen
      $paare[$platz['paar_id_tlp']] = $platz;
      $sqlab          = 'SELECT * FROM majoritaet WHERE 
                         turniernummer='.$turniernr.' AND 
                         TP_ID='.$platz["paar_id_tlp"].' AND 
                         RT_ID='.$platz["RT_ID_Ausgeschieden"].'';
      $ergebnisDB_pkt =  mysqli_query(DRBVdb(), $sqlab);
    
      while($rundenDB_pkt = mysqli_fetch_array($ergebnisDB_pkt)){
        $ergDB_pkt_su = number_format($rundenDB_pkt[WR7],2);
      }//end while
      //print_r($ergDB_pkt_su);echo' ergDB_pkt_su<br>';

      if(substr($platz["cup_serie"], 0, 4) == $region){        
        $erg_ohnekorrkt[] = array($platz["platz"],$platz["startbuch"],utf8_encode($platz["dame"]),utf8_encode($platz["herr"]),utf8_encode($platz["verein"]),$ergDB_pkt_su);//geloescht aber platzierung erhalten        
      } elseif($region == 'DCGP'){//im Falle DCGP werden keine Paare geloescht
        $erg_ohnekorrkt[] = array($platz["platz"],$platz["startbuch"],utf8_encode($platz["dame"]),utf8_encode($platz["herr"]),utf8_encode($platz["verein"]),$ergDB_pkt_su);//geloescht aber platzierung erhalten             
      }      
    }
    //print_r($erg_ohnekorrkt);echo' :DEBUG::erg_ohnekorrkt<br>';
    
    //platzierung aufgrund der loeschung nachbessern
    $platzneu      = 1;
    $remind        = 0;
    $punkte        = array();
    $mykey         = array();
      
    for($i = 0; $i < count($erg_ohnekorrkt); ++$i) {
      $curr = $erg_ohnekorrkt[$i];
      $next = $erg_ohnekorrkt[$i+1];
      
      $erg_mitkorrkt[$i+1]   = array($platzneu,$erg_ohnekorrkt[$i][1],$erg_ohnekorrkt[$i][2],$erg_ohnekorrkt[$i][3],$erg_ohnekorrkt[$i][4],$erg_ohnekorrkt[$i][5]);
      $erg_platzierung[$i+1] = $platzneu;
      if($curr[0]!=$next[0]){
        $platzneu++;
        if($remind) {
          $platzneu = $platzneu + $remind;          
        }
        $remind = 0;
      }
      if($curr[0]==$next[0]){
        $remind++;
      }            
    }//end for    
    $erg_mitkorrktrev = array_reverse($erg_mitkorrkt);
    //print_r($erg_mitkorrkt);echo' :DEBUG::erg_mitkorrkt<br>';
    //print_r($erg_mitkorrktrev);echo' :DEBUG::erg_mitkorrktrev<br>';
    
    $count_gs  = 0;
    $remind    = 0;
    unset($paare_geschl_rev);
    //geschlagene Paare ermitteln
    for($i = 0; $i < count($erg_mitkorrktrev); ++$i){
      $paare_geschl_rev[] = $count_gs;       
      if($erg_mitkorrktrev[$i][0] != $erg_mitkorrktrev[$i+1][0]){
        $count_gs++;
        if($remind){
          $count_gs = $count_gs+$remind;
        }        
        $remind = 0;
      };      
      if($erg_mitkorrktrev[$i][0] == $erg_mitkorrktrev[$i+1][0]){
        $remind++;
      };                  
    }
    $paare_geschl = array_reverse($paare_geschl_rev);
    //print_r($paare_geschl);echo' :DEBUG::paare_geschl<br>';
    
    //punkte nach platzierung zuweisen
    unset($punkte);
    for($i = 0; $i < count($erg_mitkorrkt); ++$i){
      $punkte[$i+1] = array('platz' => $erg_mitkorrkt[$i+1][0], 'pkt' => $rangpkttab[$i+1]);
    }
    //print_r($punkte);echo' :DEBUG::punkte<br>';
            
    //punktzuweisung korrigieren und mittelwert bei gleicher platzierung berechnen
    $mittelwert = array();
    for($i = 1; $i <= count($punkte); ++$i){
      if($i < count($punkte)){
        if($punkte[$i]['platz'] == $punkte[$i+1]['platz']-1){
          $punkte_kor[$i] = $punkte[$i]['pkt'];
          unset($mittelwert);
        } 
        if($punkte[$i]['platz'] == $punkte[$i+1]['platz']){
          $mittelwert[$i] = $punkte[$i]['pkt'];
        } 
        elseif(($punkte[$i+1]['platz'] - $punkte[$i]['platz']) > 1) {
          $mittelwert[$i] = $punkte[$i]['pkt'];
        }
      }       
      if($i==count($punkte)){
        if($punkte[$i]['platz'] == $punkte[$i-1]['platz']){
          $mittelwert[$i] = $punkte[$i]['pkt'];
        } else {
          $punkte_kor[$i] = $punkte[$i]['pkt'];
          unset($mittelwert);
        }        
      }
      if($mittelwert){
        foreach($mittelwert as $key => $value){
          $punkte_kor[$key] = array_sum($mittelwert)/count($mittelwert);
        }
      }          
    }
    //print_r($punkte_kor);echo' :DEBUG::punkte_kor<br>';

    //zu den korrigierten punkten die geschlagenen paare addieren
    unset($punkte);    
    foreach($punkte_kor as $key => $value){
      $punkte[$key] = $abs_multi*($punkte_kor[$key] + $paare_geschl[$key-1]);
    }
    //print_r($punkte);echo' :DEBUG::punkte<br>';
                    
    //in das array aus platz,startbuchnr die korr. punkte hinzufuegen
    foreach($erg_mitkorrkt as $key => $value){
      $erg_htm[$turniernr][$value[1]] = array('platz' =>$value[0],
                                     'dame'  =>$value[2],
                                     'herr'  =>$value[3],
                                     'verein'=>$value[4],
                                     'tanzpunkte'=>$value[5],
                                     'punkte'=>$punkte[$key]);//startbuchnr als key
      if($value[0] == 3){
        $punkte4int[$turniernr] = $punkte[$key];//punkte des drittplatzierten merken
      } else {
        $punkte4int[$turniernr] = 10;//wenn keine drei am Start      
      }
      $rnglstnpkt[$value[1]] = $punkte[$key];                                               
      $teilnehmende_stb[]    = $value[1];
      $teilnehmende_cup[$turniernr][] = $value[1];
    }
    //print_r($erg_htm);echo' :DEBUG::erg_htm<br>';
    //print_r($rnglstnpkt);echo' :DEBUG::rnglstnpkt<br>';
    
    $rnglstnpkt4cup_4stkl[$turniernr] = $rnglstnpkt;
    //$rnglstnpkt4cup_4stkl[$turniernr] = $erg_mitkorrkt;
    $teilnehmende_stb = array_unique($teilnehmende_stb);
    $anzahl_turniere++;  
  }//end foreach($trn_nr as $value)    
  //print_r($rnglstnpkt4cup_4stkl);echo' :DEBUG::rnglstnpkt4cup_4stkl<br>';      
  //print_r($anzahl_turniere);echo' :DEBUG::anzahl_turniere<br>';
  //print_r($teilnehmende_stb);echo' :DEBUG::teilnehmende_stb: #'.count($teilnehmende_stb).'<br>';      
  //print_r($teilnehmende_cup);echo' :DEBUG::teilnehmende_cup: #'.count($teilnehmende_cup).'<br>';      
  //print_r($erg_htm);echo' :DEBUG::erg_htm<br>';
  //print_r($punkte4int);echo' :DEBUG::punkte4int<br>';
  
  //alle ranglistenpunkte einer startbuchnummer suchen
  $rlpkte  = array();
  foreach($rnglstnpkt4cup_4stkl as $trnnr => $value){
    foreach($value as $stbnr => $punkte){
      if($stbnr == $StartbuchNr){
        $rlpkte[$trnnr]      = $punkte;
        $tanzpkt_arr[$trnnr] = $erg_htm[$trnnr][$stbnr]["tanzpunkte"];
        $rlpkte_arr[$trnnr]  = $punkte;
        $tplatz_arr[$trnnr]  = $erg_htm[$trnnr][$stbnr]["platz"];
      }
    }
    //hier noch eine nachtraegliche korrektur codieren aus den 
    //$gPreferences['txt_intein']
    //die bei nichtteilnahme die gleichen ranglistenpunkte
    //erhalten wie der drittplatzierte
    if(in_array(substr($startklasse,3).$StartbuchNr, $inteinstb[$trnnr])){
      $rlpkte[$trnnr]     = $punkte4int[$trnnr];
      $rlpkte_arr[$trnnr] = $punkte4int[$trnnr];
      $tplatz_arr[$trnnr] = 'int';      
    }        
  }
  //print_r($rlpkte);echo' :DEBUG::$rlpkte '.$StartbuchNr.'<br>';
  //$rlpkte_arr = $rlpkte;
  //$rlpkte_arr = $erg_htm[$StartbuchNr]["platz"];
  //print_r($rlpkte_arr);echo' :DEBUG::$rlpkte_arr '.$StartbuchNr.'<br>';
  //die gefundenen werte absteigend sortieren und nur die besten 5 ergebnisse summieren,
  //alle weiteren sind streichergebnisse  
  rsort($rlpkte);
  $rlpkte = array_sum(array_slice($rlpkte, 0, 5));
  //print_r($rlpkte);echo' :DEBUG::$rlpkte '.$StartbuchNr.'<br>';
  if($mode == 'teilnehmer'){
    return $teilnehmende_stb;
  } else {
    //print_r($rlpkte);echo' :DEBUG::$rlpkte<br>';
    return array ($rlpkte,$tanzpkt_arr,$rlpkte_arr,$tplatz_arr,$trn_nr,$teilnehmende_cup);
  }
}  

function getPaarInfo($startbuch){
 
  global $gPreferences;

  //suche in der DB nach startbuch
  $sqlab    = 'SELECT startbuch,dame,herr,verein 
               FROM paare 
               WHERE startbuch = "'.$startbuch.'"'; 
  $punkteDB = mysqli_query(DRBVdb(), $sqlab);
  
  while($startbuch = mysqli_fetch_array($punkteDB)){
    $paarinfo["dame"]   = utf8_encode($startbuch[1]);
    $paarinfo["herr"]   = utf8_encode($startbuch[2]);
    $paarinfo["verein"] = utf8_encode($startbuch[3]);
  } 
  return $paarinfo;
}//end function getPaarInfo

function getTurnierInfo($turniernummer){
  
  global $gPreferences;

  $sqlab        = 'SELECT turniername, datum, veranstaltung_ort 
                   FROM Turnier 
                   WHERE turniernummer = "'.$turniernummer.'"'; 
  $turnier      = mysqli_query(DRBVdb(), $sqlab);    
  $turnierdaten = mysqli_fetch_array($turnier);
  $turnierinfo["turniername"]  = utf8_encode($turnierdaten[0]);
  $turnierinfo["turnierdatum"] = utf8_encode($turnierdaten[1]);  
  $turnierinfo["turnierort"]   = substr(utf8_encode($turnierdaten[2]),6);  
  //print_r($turnierinfo);echo' turnierinfo<br>';
  return $turnierinfo;
}//end function getTurnierInfo  
    
function getRanglistenHTML($startklasse,$region,$mode){

  global $gPreferences, $inteinstb, $enable_tanzpkte_ansicht;
  
  //alle teilnehmenden Startbuchnrn holen
  $teilnehmer = getRngLstnPkt4cup('',$startklasse,$region,'teilnehmer');
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';
  
  unset($trn_nr);
  if($region == 'Nord'){
    $zugehor = 1;
  } else {
    $zugehor = 2;
  }
  
  foreach($inteinstb as $int_starter){
    foreach($int_starter as $kl_and_stbnr){
      $tmp = getStartbuchData(substr($kl_and_stbnr,1));
      if(substr($kl_and_stbnr,0,1) == substr($startklasse,3) && $zugehor == $tmp['zugehor']){
        $teilnehmer[] = substr($kl_and_stbnr,1);
      }
    }
  }
  $teilnehmer = array_unique($teilnehmer);
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';
  
  $summe_rnglpkt      = array();
  $summe_rnglpkt_     = array();
  $summe_rnglpkt_sort = array();
  $html  = '';
  $platz = 1;
  foreach($teilnehmer as $stbnr){
    //summe der ranglistenpunkte pro startbuch holen
    list($rlpkte,$tanzpkt_arr,$rlpkte_arr,$tplatz_arr,$trn_nr,$teilnehmende_cup) = getRngLstnPkt4cup($stbnr,$startklasse,$region,'');
    $summe_rnglpkt[$stbnr]    = $rlpkte;
    //und an welchen Turnieren mit welcher Punktzahl
    $trn_teilgenommen[$stbnr] = $rlpkte_arr;
    $trn_tanzpkt[$stbnr]      = $tanzpkt_arr;
    //und welchem platz
    $trn_teilgenommen_platz[$stbnr] = $tplatz_arr;
    //und liste aller Cup-Turniere, nach datum aufsteigend
    $trn_cupserie = array_reverse($trn_nr, true);
    //und liste der teilnehmenden cup-paare
    $trn_teiln_cup = $teilnehmende_cup;
  }
  //print_r($summe_rnglpkt);echo' :DEBUG::summe_rnglpkt:<br>';
  //print_r($trn_tanzpkt);echo' :DEBUG::trn_tanzpkt:<br>';
  
  $klassen    = array('RR_A','RR_B','RR_C','RR_J','RR_S');  
  //Quote DC/GP aus Organisationseinstellungen holen
  $quote      = explode(";", $gPreferences['txt_quote_dcgp']);
  $quote_dcgp = array_combine($klassen,$quote);
  //Turniermodus DC/GP aus Organisationseinstellungen holen
  $modus      = explode(";", $gPreferences['txt_modus_dcgp']);
  $modus_dcgp = array_combine($klassen,$modus);
  //print_r($quote_dcgp);echo' :DEBUG::quote_dcgp<br>';
  //print_r($modus_dcgp);echo' :DEBUG::modus_dcgp<br>';
  
  $teiln_nach_quote = ceil(count($teilnehmer)*($quote_dcgp[$startklasse]/100));
  //print_r($teiln_nach_quote);echo' :DEBUG::teiln_nach_quote<br>';  
  
  //summe ranglistenpunkte modifizieren und paare mit 
  // - ungueltigen startbuch
  // - anderer startklasse (aufstieg) 
  // - disqualifiziert in den OrgaEinstellungen
  // - kein TaT-Flag ab Datum x gesetzt
  //auf 0 setzen
  unset($tmp);
  unset($checktatdat);
  $disqual = explode(";", $gPreferences['txt_disqual']);//disqualifizierte turniernummern splitten  
  if(strtotime($gPreferences['txt_tatdat']) < time()) $checktatdat = true;//check ob TaT Datum abgelaufen 
  
  foreach($summe_rnglpkt as $stbnr => $punkte){
    $tmp = getStartbuchData($stbnr);
    if(getStartklasse($tmp['startkl'])!=$startklasse){
      $summe_rnglpkt[$stbnr] = '0 ('.$punkte.')';
      //print_r($tmp);echo' :DEBUG::$tmp hit ungleiche Startklasse:'.getStartklasse($tmp["startkl"]).' != '.$startklasse.'<br>';  
    } elseif($tmp['startmarke'] == 0){
      $summe_rnglpkt[$stbnr] = '0 ('.$punkte.')';
      //print_r($tmp);echo' :DEBUG::$tmp hit Startmarke ungueltig<br>';        
    }
    if($startklasse != 'RR_A'){
      if($checktatdat && (!$tmp['tatherr'] || !$tmp['tatdame'])){
        $summe_rnglpkt[$stbnr] = '0 ('.$punkte.')';
        //print_r($tmp);echo' :DEBUG::$tmp hit TaT nicht gesetzt<br>';                    
      }
    }
    if(in_array($stbnr, $disqual)){
      $summe_rnglpkt[$stbnr] = '0 ('.$punkte.')';
      //print_r($tmp);echo' :DEBUG::$tmp hit disqualifiziert<br>';              
    }    
  }  
  
  //liste sortieren, höchste Pktzahl zuerst
  natsort($summe_rnglpkt);
  $summe_rnglpkt_sort = array_reverse($summe_rnglpkt, true);
  //print_r($summe_rnglpkt);echo' :DEBUG::summe_rnglpkt:<br>';
  //print_r($summe_rnglpkt_sort);echo' :DEBUG::summe_rnglpkt_sort:<br>';
  //print_r($trn_teilgenommen);echo' :DEBUG::trn_teilgenommen:<br>';
  //print_r($trn_cupserie);echo' :DEBUG::trn_cupserie:<br>';
  //print_r($trn_teiln_cup);echo' :DEBUG::trn_teiln_cup:<br>';
      
  $html .= '
    <tr>
      <th height=55 class="tg-yw4lm"><div class="rotleft">Platz</div></th>
      <th class="tg-yw4l">Dame</th>
      <th class="tg-yw4l">Herr</th>
      <th class="tg-yw4l">Verein</th>
      <th class="tg-yw4lm">Bundes-<br>land</th>';
  if($trn_cupserie){
    foreach($trn_cupserie as $trnnr){
      $turnierdatum     = getTurnierInfo($trnnr)["turnierdatum"];
      $turnierdatum_htm = substr($turnierdatum,8,2).'.'.substr($turnierdatum,5,2).'<br>'.substr($turnierdatum,0,4);    
      $html .= '<th class="tg-yw4lm">
                  <div class="rotleft">      
                    <span class="iconLinkRngListe"><a class="textTooltip" 
                      title="'.getTurnierInfo($trnnr)["turniername"].' / '.getTurnierInfo($trnnr)["turnierort"].'" href="#">'.$turnierdatum_htm.'
                    </a></span></div></th>';
    }
  } else {
    $html .= '<th class="tg-yw4lm" width=300><div>      
                <b>Es liegen noch keine <br>Turnierdaten vor!</b>
              </div></th>';  
  }
  
  if($region == 'Nord'){
    $cup_htm = 'Nord';
  } else {
    $cup_htm = 'S&uuml;d';
  }
  $html .= '  
      <th class="tg-yw4lm"><div class="rotleft">Summe<br>(Top5)</div></th>
      <th class="tg-yw4lm"><div class="rotleft">Quali</div></th>
      <th class="tg-yw4lm"><div class="rotleft">TaT</div></th>
      <th class="tg-yw4lm"><div class="rotleft">Start-<br>marke</div></th>
    <tr>
    <tr>
      <td colspan="5" class="tg-yw4lr"><b>Anzahl '.$cup_htm.'-Cup Paare:</b></td>';
  if($trn_cupserie){
    foreach($trn_cupserie as $trnnr){
      $html .= '<td class="tg-yw4lc">'.count($trn_teiln_cup[$trnnr]).'</td>';  
    }    
  } else {
      $html .= '<td class="tg-yw4lc">&nbsp;</td>';    
  }
  $html .= '
      <td class="tg-yw4lc"><b>'.count($teilnehmer).'</b></td>
      <td class="tg-yw4lc"><b>'.$teiln_nach_quote.'</b></td>
      <td colspan="2" class="tg-yw4l">&nbsp;</td>
    </tr>';
  
  $pkt_davor   = 99999;
  $platz_davor = 0;
  foreach($summe_rnglpkt_sort as $stbnr => $pkt){
    $paarinfo      = getPaarInfo($stbnr);
    $startbuchdata = getStartbuchData($stbnr);
    //print_r($paarinfo);echo' :DEBUG::paarinfo<br>';
    //print_r($startbuchdata);echo' :DEBUG::startbuchdata<br>';
    
    if($pkt == $pkt_davor){
      $platz_davor++;
      $platzierung = $platz-$platz_davor;
    } else {
      $platzierung = $platz+$platz_davor;
      $platz       = $platz+$platz_davor+1;
      $platz_davor = 0;
    }
    
    $modus_htm  = '';
    $linecoltag = 'tg';
    if($platzierung <= $teiln_nach_quote+3){
      $modus_htm  = 'NR';
      $linecoltag = 'tg_gry';
    }
    if($platzierung <= $teiln_nach_quote){
      $modus_htm  = 'VR';
      $linecoltag = 'tg_grn';
    }
    if($platzierung == 1 || $platzierung == 2){
      $modus_htm = $modus_dcgp[$startklasse];
      if($modus_htm == 'VR') $linecoltag = 'tg_grn';
      if($modus_htm == 'HF') $linecoltag = 'tg_yel';
    }
    if(substr($pkt,2,1)=='(') $platzierung = '';//platzierung loeschen, wenn paare ans listenende gesetzt werden
    $html .= '<tr>
      <td class="'.$linecoltag.'-yw4lm">'.$platzierung.'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['dame'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['herr'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['verein'].'</td>
      <td class="'.$linecoltag.'-yw4lm"><img src="'.THEME_PATH.'/icons/bndland'.$startbuchdata['bndland'].'.png" alt="'.getBndLandKurz($startbuchdata['bndland']).'" title="'.getBndLand($startbuchdata['bndland']).'" /></td>';
    foreach($trn_cupserie as $trnnr){      
      if(strlen($trn_teilgenommen[$stbnr][$trnnr]) == 0){
        $html .= '<td class="'.$linecoltag.'-yw4lc">&nbsp;</td>';
      } else {        
        if($enable_tanzpkte_ansicht){
          $html .= '<td class="'.$linecoltag.'-yw4lc">'.$trn_teilgenommen[$stbnr][$trnnr].'<br>('.$trn_teilgenommen_platz[$stbnr][$trnnr].')<br><b><i>'.$trn_tanzpkt[$stbnr][$trnnr].'</i></b></td>';
        } else {
          $html .= '<td class="'.$linecoltag.'-yw4lc">'.$trn_teilgenommen[$stbnr][$trnnr].'<br>('.$trn_teilgenommen_platz[$stbnr][$trnnr].')</td>';
        }
      }
    }
    if($startbuchdata['startmarke'] == '1'){
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="g&uuml;ltig" />';
    } else {
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="ung&uuml;ltig" />';
    }
    if($startbuchdata['tatherr'] == '1' && $startbuchdata['tatdame'] == '1'){
      $tat_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="bestanden" />';
    } elseif($startbuchdata['tatherr'] == '1') {
      $tat_htm = 'Herr';
    } elseif($startbuchdata['tatdame'] == '1') {
      $tat_htm = 'Dame';
    } elseif($startklasse == 'RR_A') {
      $tat_htm = '&nbsp;';
    } else {
      $tat_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="nicht bestanden/teilgenommen" />';
    }
    
    
    $html .= '
      <td class="'.$linecoltag.'-yw4lm"><b>'.$pkt.'</b></td>
      <td class="'.$linecoltag.'-yw4lm">'.$modus_htm.'</td>
      <td class="'.$linecoltag.'-yw4lm">'.$tat_htm.'</td>
      <td class="'.$linecoltag.'-yw4lm">'.$startmarke_htm.'</td>';

    $html .= '</tr>';
    $pkt_davor = $pkt;    
  } 
  return $html;  
}
  
function getRanglistenHTML4dcgp($startklasse){

  global $gPreferences, $inteinstb, $enable_tanzpkte_ansicht;
  
  //alle teilnehmenden Startbuchnrn holen
  $teilnehmer = getRngLstnPkt4cup('',$startklasse,'DCGP','teilnehmer');
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';
  
  //internationale einsaetze addieren - ??? evtl. kann das raus, da es evtl.
  //eine andere TSO regelung geben kann/wird
  unset($trn_nr);
  foreach($inteinstb as $int_starter){
    foreach($int_starter as $kl_and_stbnr){
      $tmp = getStartbuchData(substr($kl_and_stbnr,1));
      if(substr($kl_and_stbnr,0,1) == substr($startklasse,3)){
        $teilnehmer[] = substr($kl_and_stbnr,1);
      }
    }
  }
  $teilnehmer = array_unique($teilnehmer);
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';

  $summe_rnglpkt      = array();
  $summe_rnglpkt_     = array();
  $summe_rnglpkt_sort = array();
  $html  = '';
  $platz = 1;
  foreach($teilnehmer as $stbnr){
    //summe der ranglistenpunkte pro startbuch holen
    list($rlpkte,$tanzpkt_arr,$rlpkte_arr,$tplatz_arr,$trn_nr,$teilnehmende_cup) = getRngLstnPkt4cup($stbnr,$startklasse,'DCGP','');
    $summe_rnglpkt[$stbnr]    = $rlpkte;
    //und an welchen Turnieren mit welcher Punktzahl
    $trn_teilgenommen[$stbnr] = $rlpkte_arr;
    $trn_tanzpkt[$stbnr]      = $tanzpkt_arr;
    //und welchem platz
    $trn_teilgenommen_platz[$stbnr] = $tplatz_arr;
    //und liste aller Cup-Turniere, nach datum aufsteigend
    $trn_cupserie = array_reverse($trn_nr, true);
    //und liste der teilnehmenden cup-paare
    $trn_teiln_cup = $teilnehmende_cup;
  }
  //print_r($summe_rnglpkt);echo' :DEBUG::summe_rnglpkt:<br>';
  
  $klassen  = array('RR_A','RR_B','RR_C','RR_J','RR_S');  
  //Quote DM aus Organisationseinstellungen holen
  $quote    = explode(";", $gPreferences['txt_dmquote']);
  $quote_dm = array_combine($klassen,$quote);
  //print_r($quote_dm);echo' :DEBUG::quote_dm<br>';
  if($quote_dm[$startklasse] > 7){
    $quote_qt   = ($quote_dm[$startklasse]-2)/2;
    $quote_rngl = 2;
  } else {
    $quote_qt   = ($quote_dm[$startklasse]-1)/2;
    $quote_rngl = 1;
  }
  //print_r($quote_qt);echo' :DEBUG::quote_qt<br>';

  //die ersten x paare extrahieren, die sich auf QT1 und QT2 qualifiziert haben
  unset($qt1_result);
  unset($qt2_result);
  $qualitrn = explode(";", $gPreferences['txt_gpdat']);
  $qt1      = $qualitrn[1];
  $qt2      = $qualitrn[0];
  foreach($trn_teilgenommen_platz as $stb_nr => $quali_platz){
    if($quali_platz[$qt1]){
      $qt1_result[$stb_nr] = $quali_platz[$qt1];
      asort($qt1_result);
    }
    if($quali_platz[$qt2]){
      $qt2_result[$stb_nr] = $quali_platz[$qt2];
      asort($qt2_result);
    }
  }
  $qt1_result = array_slice($qt1_result,0,$quote_qt,true);//liste der qualif. QT1 paare
  //print_r($qt1_result);echo' :DEBUG::qt1_result<br>';
  foreach($qt2_result as $stb_nr => $value){
    if(!array_key_exists($stb_nr,$qt1_result)){
      $qt2_result_rm[$stb_nr] = $value;
      asort($qt2_result_rm);      
    }
  }
  $qt2_result_rm = array_slice($qt2_result_rm,0,$quote_qt,true);//liste der qualif. QT2 paare (QT1 paare vorher removed)
  //print_r($qt2_result_rm);echo' :DEBUG::qt2_result_rm<br>';
  $last = array_slice($qt2_result_rm,3,1);//platzierung des letzten der liste bestimmen
  foreach($qt2_result as $stb_nr => $value){//wenn der letzte platz mehrfach(geteilt) ist, dann alle gleichplatzierten wieder hinzufuegen
    if($value == $last[0]){
      $qt2_result_rm[$stb_nr] = $qt2_result[$stb_nr];
      asort($qt2_result_rm);      
    }
  }
  //print_r($qt2_result_rm);echo' :DEBUG::qt2_result_rm_after<br>';    
  
  unset($rngl_result);
  unset($rngl_result_rm);
  foreach($summe_rnglpkt as $stb_nr => $value){
    if(!array_key_exists($stb_nr,$qt1_result) && !array_key_exists($stb_nr,$qt2_result_rm)){
      $rngl_result[$stb_nr] = $value;
    }  
  }
  arsort($rngl_result);
  $rngl_result_rm = array_slice($rngl_result,0,$quote_rngl,true);//liste der qualif. paare nach rnglst, vorher QT1&2 removed  
  //print_r($rngl_result_rm);echo' :DEBUG::rngl_result_rm<br>';
  
    
  //liste sortieren, höchste Pktzahl zuerst
  natsort($summe_rnglpkt);
  $summe_rnglpkt_sort = array_reverse($summe_rnglpkt, true);
  //print_r($summe_rnglpkt);echo' :DEBUG::summe_rnglpkt:<br>';
  //print_r($summe_rnglpkt_sort);echo' :DEBUG::summe_rnglpkt_sort:<br>';
  //print_r($trn_teilgenommen);echo' :DEBUG::trn_teilgenommen:<br>';
  //print_r($trn_teilgenommen_platz);echo' :DEBUG::trn_teilgenommen_platz:<br>';
  //print_r($trn_cupserie);echo' :DEBUG::trn_cupserie:<br>';
  //print_r($trn_teiln_cup);echo' :DEBUG::trn_teiln_cup:<br>';
      
  $html .= '
    <tr>
      <th height=55 class="tg-yw4lm"><div class="rotleft">Platz</div></th>
      <th class="tg-yw4l">Dame</th>
      <th class="tg-yw4l">Herr</th>
      <th class="tg-yw4l">Verein</th>
      <th class="tg-yw4lm">Bundes-<br>land</th>';
  if($trn_cupserie){
    foreach($trn_cupserie as $trnnr){
      $turnierdatum     = getTurnierInfo($trnnr)["turnierdatum"];
      $turnierdatum_htm = substr($turnierdatum,8,2).'.'.substr($turnierdatum,5,2).'<br>'.substr($turnierdatum,0,4);    
      $html .= '<th class="tg-yw4lm">
                  <div class="rotleft">      
                    <span class="iconLinkRngListe"><a class="textTooltip" 
                      title="'.getTurnierInfo($trnnr)["turniername"].' / '.getTurnierInfo($trnnr)["turnierort"].'" href="#">'.$turnierdatum_htm.'
                    </a></span></div></th>';
    }
  } else {
    $html .= '<th class="tg-yw4lm" width=300><div>      
                <b>Es liegen noch keine <br>Turnierdaten vor!</b>
              </div></th>';  
  }
  
  $html .= '  
      <th class="tg-yw4lm"><div class="rotleft">Summe</div></th>
      <th class="tg-yw4lm"><div class="rotleft">DM<br>Quali</div></th>
      <th class="tg-yw4lm"><div class="rotleft">TaT</div></th>
      <th class="tg-yw4lm"><div class="rotleft">Start-<br>marke</div></th>
    </tr>
    <tr>
      <td colspan="5" class="tg-yw4lr"><b>Anzahl Paare zur DM:</b></td>
      <td class="tg-yw4lc">'.$quote_qt.'</td>
      <td class="tg-yw4lc">'.$quote_qt.'</td>
      <td class="tg-yw4lc">'.$quote_rngl.'</td>
      <td class="tg-yw4lc"><b>'.$quote_dm[$startklasse].'</b></td>
      <td colspan="2" class="tg-yw4l">&nbsp;</td>
    </tr>';
    
  $pkt_davor   = 99999;
  $platz_davor = 0;
  foreach($summe_rnglpkt_sort as $stbnr => $pkt){
    $paarinfo      = getPaarInfo($stbnr);
    $startbuchdata = getStartbuchData($stbnr);
    //print_r($paarinfo);echo' :DEBUG::paarinfo<br>';
    //print_r($startbuchdata);echo' :DEBUG::startbuchdata<br>';
        
    if($pkt == $pkt_davor){
      $platz_davor++;
      $platzierung = $platz-$platz_davor;
    } else {
      $platzierung = $platz+$platz_davor;
      $platz       = $platz+$platz_davor+1;
      $platz_davor = 0;
    }
    
    $linecoltag = 'tg';    
    $quali_htm  = '';
    if(array_key_exists($stbnr,$qt1_result)){
      $quali_htm  = 'QT1';
      $linecoltag = 'tg_grn';
    } elseif(array_key_exists($stbnr,$qt2_result_rm)){
      $quali_htm  = 'QT2';      
      $linecoltag = 'tg_grn2';
    }
    if(array_key_exists($stbnr,$rngl_result_rm)){
      $quali_htm  = 'QR';
      $linecoltag = 'tg_grn3';
    }    
    
    $html .= '<tr>
      <td class="'.$linecoltag.'-yw4lm">'.$platzierung.'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['dame'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['herr'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['verein'].'</td>
      <td class="'.$linecoltag.'-yw4lm"><img src="'.THEME_PATH.'/icons/bndland'.$startbuchdata['bndland'].'.png" alt="'.getBndLandKurz($startbuchdata['bndland']).'" title="'.getBndLand($startbuchdata['bndland']).'" /></td>';
    foreach($trn_cupserie as $trnnr){      
      if(strlen($trn_teilgenommen[$stbnr][$trnnr]) == 0){
        $html .= '<td class="'.$linecoltag.'-yw4lc">&nbsp;</td>';
      } else {
//        $html .= '<td class="'.$linecoltag.'-yw4lc">'.$trn_teilgenommen[$stbnr][$trnnr].'<br>('.$trn_teilgenommen_platz[$stbnr][$trnnr].')</td>';
        if($enable_tanzpkte_ansicht){
          $html .= '<td class="'.$linecoltag.'-yw4lc">'.$trn_teilgenommen[$stbnr][$trnnr].'<br>('.$trn_teilgenommen_platz[$stbnr][$trnnr].')<br><b><i>'.$trn_tanzpkt[$stbnr][$trnnr].'</i></b></td>';
        } else {
          $html .= '<td class="'.$linecoltag.'-yw4lc">'.$trn_teilgenommen[$stbnr][$trnnr].'<br>('.$trn_teilgenommen_platz[$stbnr][$trnnr].')</td>';
        }
      }
    }
    if($startbuchdata['startmarke'] == '1'){
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="g&uuml;ltig" />';
    } else {
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="ung&uuml;ltig" />';
    }
    if($startbuchdata['tatherr'] == '1' && $startbuchdata['tatdame'] == '1'){
      $tat_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="bestanden" />';
    } elseif($startbuchdata['tatherr'] == '1') {
      $tat_htm = 'Herr';
    } elseif($startbuchdata['tatdame'] == '1') {
      $tat_htm = 'Dame';
    } elseif($startklasse == 'RR_A') {
      $tat_htm = '&nbsp;';
    } else {
      $tat_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="nicht bestanden/teilgenommen" />';
    }
    
    
    $html .= '
      <td class="'.$linecoltag.'-yw4lm"><b>'.$pkt.'</b></td>
      <td class="'.$linecoltag.'-yw4lm">'.$quali_htm.'</td>
      <td class="'.$linecoltag.'-yw4lm">'.$tat_htm.'</td>
      <td class="'.$linecoltag.'-yw4lm">'.$startmarke_htm.'</td>';

    $html .= '</tr>';
    $pkt_davor = $pkt;    
  } 
  return $html;  
}  
  
function getRanglistenHTML4pktquali($startklasse){

  global $gPreferences, $inteinstb, $enable_tanzpkte_ansicht;

  $klassen  = array('RR_A','RR_B','RR_C','RR_J','RR_S');  
  //Quote DM aus Organisationseinstellungen holen
  $quote    = explode(";", $gPreferences['txt_dmquote']);
  $quote_dm = array_combine($klassen,$quote);
  $disqual  = explode(";", $gPreferences['txt_disqual']);//disqualifizierte turniernummern splitten
  
  //alle teilnehmenden Startbuchnrn holen
  $teilnehmer_dcgp = getRngLstnPkt4cup('',$startklasse,'DCGP','teilnehmer');
  $teilnehmer_nord = getRngLstnPkt4cup('',$startklasse,'Nord','teilnehmer');
  $teilnehmer_sued = getRngLstnPkt4cup('',$startklasse,'Sued','teilnehmer');
  $teilnehmer = array_merge($teilnehmer_dcgp, $teilnehmer_nord, $teilnehmer_sued);
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';
  
  //internationale einsaetze addieren - ??? evtl. kann das raus, da es evtl.
  //eine andere TSO regelung geben kann/wird
  unset($trn_nr);
  foreach($inteinstb as $int_starter){
    foreach($int_starter as $kl_and_stbnr){
      $tmp = getStartbuchData(substr($kl_and_stbnr,1));
      if(substr($kl_and_stbnr,0,1) == substr($startklasse,3)){
        $teilnehmer[] = substr($kl_and_stbnr,1);
      }
    }
  }
  $teilnehmer = array_unique($teilnehmer);
  //print_r($teilnehmer);echo' :DEBUG::teilnehmer:'.count($teilnehmer).'<br>';

  //alle ertanzten punkte aus beiden cupserien und dc und gpvd pro paar einlesen
  $html  = '';
  $platz = 1;
  foreach($teilnehmer as $stbnr){
    //summe der ranglistenpunkte pro startbuch holen
    list($rlpkte_dcgp,$tanzpkt_arr_dcgp,$rlpkte_arr_dcgp,$tplatz_arr_dcgp,$trn_nr_dcgp,$teilnehmende_cup_dcgp) = getRngLstnPkt4cup($stbnr,$startklasse,'DCGP','');
    list($rlpkte_nord,$tanzpkt_arr_nord,$rlpkte_arr_nord,$tplatz_arr_nord,$trn_nr_nord,$teilnehmende_cup_nord) = getRngLstnPkt4cup($stbnr,$startklasse,'Nord','');
    list($rlpkte_sued,$tanzpkt_arr_sued,$rlpkte_arr_sued,$tplatz_arr_sued,$trn_nr_sued,$teilnehmende_cup_sued) = getRngLstnPkt4cup($stbnr,$startklasse,'Sued','');
    
    $trn_tanzpkt_dcgp[$stbnr] = $tanzpkt_arr_dcgp;
    $trn_tanzpkt_nord[$stbnr] = $tanzpkt_arr_nord;
    $trn_tanzpkt_sued[$stbnr] = $tanzpkt_arr_sued;          
  }  
  //print_r($trn_tanzpkt_dcgp);echo' :DEBUG::trn_tanzpkt_dcgp:<br>';
  //print_r($trn_tanzpkt_nord);echo' :DEBUG::trn_tanzpkt_nord:<br>';
  //print_r($trn_tanzpkt_sued);echo' :DEBUG::trn_tanzpkt_sued:<br>';
  
  unset($trn_tanzpkt);
  foreach($trn_tanzpkt_dcgp as $stbnr => $value){
    foreach($value as $trnnr => $pkt){
      $trn_tanzpkt[$stbnr][$trnnr] = $pkt;
    }
  }
  foreach($trn_tanzpkt_nord as $stbnr => $value){
    foreach($value as $trnnr => $pkt){
      $trn_tanzpkt[$stbnr][$trnnr] = $pkt;
    }
  }
  foreach($trn_tanzpkt_sued as $stbnr => $value){
    foreach($value as $trnnr => $pkt){
      $trn_tanzpkt[$stbnr][$trnnr] = $pkt;
    }
  }
  //print_r($trn_tanzpkt);echo' :DEBUG::trn_tanzpkt:<br>';
  
  //die gesammelten ergebnisse aller drei turnier serien nach max. punktzahl
  //sortieren und nur den ersten = maximalwert speichern
  foreach($trn_tanzpkt as $stbnr => $data){
    arsort($data);            
    $trn_tanzpkt_srt[$stbnr] = array_slice($data,0,1,true);
  }
  //print_r($trn_tanzpkt_srt);echo' :DEBUG::trn_tanzpkt_srt:<br>';
        
  //hier durch die liste gehen und nach max. wert ueber alle paare sortieren
  foreach($trn_tanzpkt_srt as $stbnr => $value){
    $tmp = getStartbuchData($stbnr);
    foreach($value as $trnnr => $pkt){  
      if($tmp['startmarke'] == 0){
        $trn_tanzpkt_platz['0 ('.$pkt.')'] = array('stbnr'=>$stbnr,'trnnr'=>$trnnr,'pkt'=>'0 ('.$pkt.')');
        //print_r($tmp);echo' :DEBUG::$tmp hit Startmarke ungueltig<br>';        
      } elseif(in_array($stbnr, $disqual)){
        $trn_tanzpkt_platz['0 ('.$pkt.')'] = array('stbnr'=>$stbnr,'trnnr'=>$trnnr,'pkt'=>'0 ('.$pkt.')');
        //print_r($tmp);echo' :DEBUG::$tmp hit disqualifiziert<br>';              
      } else {      
        $trn_tanzpkt_platz[$pkt] = array('stbnr'=>$stbnr,'trnnr'=>$trnnr,'pkt'=>$pkt);
      }
    }                
  }
  krsort($trn_tanzpkt_platz);    
  //print_r($trn_tanzpkt_platz);echo' :DEBUG::trn_tanzpkt_platz:<br>';
  
  $html .= '
    <tr>
      <th height=55 class="tg-yw4lm"><div class="rotleft">Platz</div></th>
      <th class="tg-yw4l">Dame</th>
      <th class="tg-yw4l">Herr</th>
      <th class="tg-yw4l">Verein</th>
      <th class="tg-yw4lm">Bundes-<br>land</th>  
      <th class="tg-yw4lm"><div class="rotleft">Punkte</div></th>
      <th class="tg-yw4lm"><div class="rotleft">Ertanzt<br>am</div></th>
      <th class="tg-yw4lm"><div class="rotleft">TaT</div></th>
      <th class="tg-yw4lm"><div class="rotleft">Start-<br>marke</div></th>
    </tr>
    <tr>
      <td colspan="5" class="tg-yw4lr"><b>Anzahl Paare zur DM:</b></td>
      <td class="tg-yw4lc">'.$quote_dm[$startklasse].'</td>
      <td class="tg-yw4l" colspan="3" >&nbsp;</td>
    </tr>';
    
  foreach($trn_tanzpkt_platz as $key => $value){
    $paarinfo      = getPaarInfo($value['stbnr']);
    $startbuchdata = getStartbuchData($value['stbnr']);
    $turnierdata   = getTurnierInfo($value['trnnr']);
    //print_r($paarinfo);echo' :DEBUG::paarinfo<br>';
    //print_r($startbuchdata);echo' :DEBUG::startbuchdata<br>';        

    $linecoltag = 'tg';
    if($platz <= $quote_dm[$startklasse]){
      $linecoltag = 'tg_grn';
    }
    
    $html .= '<tr>
      <td class="'.$linecoltag.'-yw4lm">'.$platz.'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['dame'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['herr'].'</td>
      <td class="'.$linecoltag.'-yw4l">'.$paarinfo['verein'].'</td>
      <td class="'.$linecoltag.'-yw4lm"><img src="'.THEME_PATH.'/icons/bndland'.$startbuchdata['bndland'].'.png" alt="'.getBndLandKurz($startbuchdata['bndland']).'" title="'.getBndLand($startbuchdata['bndland']).'" /></td>';
                                      
    if($startbuchdata['startmarke'] == '1'){
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="g&uuml;ltig" />';
    } else {
      $startmarke_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="ung&uuml;ltig" />';
    }
    if($startbuchdata['tatherr'] == '1' && $startbuchdata['tatdame'] == '1'){
      $tat_htm = '<img src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="bestanden" />';
    } elseif($startbuchdata['tatherr'] == '1') {
      $tat_htm = 'Herr';
    } elseif($startbuchdata['tatdame'] == '1') {
      $tat_htm = 'Dame';
    } elseif($startklasse == 'RR_A') {
      $tat_htm = '&nbsp;';
    } else {
      $tat_htm = '<img src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="nicht bestanden/teilgenommen" />';
    }
    
    $html .= '
      <td class="'.$linecoltag.'-yw4lm"><b>'.$key.'</b></td>
      <td class="'.$linecoltag.'-yw4lm">
        <span class="iconLinkRngListe">
          <a class="textTooltip" 
             title="'.$turnierdata["turniername"].' / '.$turnierdata["turnierort"].'" href="#">'.strftime("%d.%m.%Y", strtotime($turnierdata["turnierdatum"])).'
          </a></span></td>
      <td class="'.$linecoltag.'-yw4lm">'.$tat_htm.'</td>
      <td class="'.$linecoltag.'-yw4lm">'.$startmarke_htm.'</td>';

    $html .= '</tr>';
    $platz++;
  }
      
  
  return $html;  
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
    </style>
    <style>
      .rlraw {
        font-size: 0.95em;
        background-color:#f7f5f2;
      }
      p.rlraw {
        font-size: 0.95em;
      }
      h1.rlraw {
        font-size: 27px;
        text-align: center;
        font-family: "Yanone Kaffeesatz";
        color: #039; 
      }
    </style>                  
    <style type="text/css">
      .tg {
        border-collapse:collapse;
        border-spacing:0;
        border-color:#aabcfe;
      }
      .tg td {
        font-family:Arial, sans-serif;
        font-size:11px;
        padding:0px 0px;
        border-style:solid;
        border-width:1px;
        overflow:hidden;
        word-break:normal;
        border-top-width:1px;
        border-bottom-width:1px;
        border-color:#aabcfe;
        color:#669;
        background-color:#e8edff;
      }
      .tg th{
        font-family:Arial, sans-serif;
        font-size:11px;
        font-weight:normal;
        padding:0px 0px;
        border-style:solid;
        border-width:0px;
        overflow:hidden;
        word-break:normal;
        border-top-width:1px;
        border-bottom-width:1px;
        border-color:#aabcfe;
        color:#039;
        background-color:#b9c9fe;
      }
      .tg .tg-yw4l{vertical-align:center;}
      .tg .tg-yw4lr{vertical-align:center;text-align:right;}
      .tg .tg-yw4lc{vertical-align:top;text-align:center;}
      .tg .tg-yw4lm{vertical-align:center;text-align:center;}
         
      .tg .tg_yel-yw4l{vertical-align:center;background-color:#F4FA58;}
      .tg .tg_yel-yw4lr{vertical-align:center;text-align:right;background-color:#F4FA58;}
      .tg .tg_yel-yw4lc{vertical-align:top;text-align:center;background-color:#F4FA58;}
      .tg .tg_yel-yw4lm{vertical-align:center;text-align:center;background-color:#F4FA58;}
         
      .tg .tg_gry-yw4l{vertical-align:center;background-color:#F5A9F2;}
      .tg .tg_gry-yw4lr{vertical-align:center;text-align:right;background-color:#F5A9F2;}
      .tg .tg_gry-yw4lc{vertical-align:top;text-align:center;background-color:#F5A9F2;}
      .tg .tg_gry-yw4lm{vertical-align:center;text-align:center;background-color:#F5A9F2;}

      .tg .tg_grn-yw4l{vertical-align:center;background-color:#BEF781;}
      .tg .tg_grn-yw4lr{vertical-align:center;text-align:right;background-color:#BEF781;}
      .tg .tg_grn-yw4lc{vertical-align:top;text-align:center;background-color:#BEF781;}
      .tg .tg_grn-yw4lm{vertical-align:center;text-align:center;background-color:#BEF781;}
      .tg .tg_grn2-yw4l{vertical-align:center;background-color:#B1E678;}
      .tg .tg_grn2-yw4lr{vertical-align:center;text-align:right;background-color:#B1E678;}
      .tg .tg_grn2-yw4lc{vertical-align:top;text-align:center;background-color:#B1E678;}
      .tg .tg_grn2-yw4lm{vertical-align:center;text-align:center;background-color:#B1E678;}
      .tg .tg_grn3-yw4l{vertical-align:center;background-color:#A7D971;}
      .tg .tg_grn3-yw4lr{vertical-align:center;text-align:right;background-color:#A7D971;}
      .tg .tg_grn3-yw4lc{vertical-align:top;text-align:center;background-color:#A7D971;}
      .tg .tg_grn3-yw4lm{vertical-align:center;text-align:center;background-color:#A7D971;}
         
      .rotleft {
        padding:2px 10px 2px 2px;
        left: 0%;
        top: 40%;
        -ms-transform: rotate(270deg);
        -webkit-transform: rotate(270deg);
        transform: rotate(270deg);
      }
      .iconLinkRngListe a:link{
        text-decoration: none;
      }
         
    </style>';
  
if(!$getRegion){ 
  require(SERVER_PATH. '/adm_program/system/overall_header.php');          

  echo '  
  <div class="formLayout" id="profile_form" width="100px">
    <div class="formHead">Ranglisten</div>
    <div class="formBody">';
                 
    // *******************************************************************************
    // Userdaten-Block
    // *******************************************************************************
    echo'      
      <div style="clear: left; font-size: 1pt;">&nbsp;</div>
      <div class="groupBox">
        <div class="groupBoxHeadline">
        <div style="float: left;">Auswahl der Turniere und Startklassen</div>';          
        echo'
        </div>
        <form action="'.$_SERVER["PHP_SELF"].'" method=post>
        <div class="groupBoxBody">
        <ul class="formFieldList">';
        echo'
          <li>
            <dl>
              <dt>Cup-Serien:</dt>
                <dd>
                <input type="radio" name="cupserie" value="nord" onclick="submit();" ';
                   if($_SESSION['getAuswahl']=='nord') echo 'checked="checked"'; 
                     echo '/>Nord-Cup-Serie<br />
                </dd>
                <dd>
                <input type="radio" name="cupserie" value="sued" onclick="submit();" ';
                   if($_SESSION['getAuswahl']=='sued') echo 'checked="checked"'; 
                   echo '/>S&uuml;d-Cup-Serie<br />
                </dd>
            </dl>       
          </li>';
        echo'
          <li>
            <dl>
              <dt>DM-Qualifikation:</dt>
                <dd>
                <input type="radio" name="cupserie" value="dcgp" onclick="submit();" ';
                   if($_SESSION['getAuswahl']=='dcgp') echo 'checked="checked"'; 
                     echo '/>Deutschland-Cup & Gro&szlig;er Preis von Deutschland<br />
                </dd>
            </dl>       
          </li>';
       echo'
          <li>
            <dl>
              <dt>Startklassen:</dt>
              <dd>
                <select style="width: 150px" size=1 name="startklassen" onchange="submit();">
                  <option value="alle" selected>alle</option>
                  <option value="A"'; if($getStartklassen=="A") echo'selected';echo'>A-Klasse</option>
                  <option value="B"'; if($getStartklassen=="B") echo'selected';echo'>B-Klasse</option>
                  <option value="C"'; if($getStartklassen=="C") echo'selected';echo'>C-Klasse</option>
                  <option value="J"'; if($getStartklassen=="J") echo'selected';echo'>J-Klasse</option>
                  <option value="S"'; if($getStartklassen=="S") echo'selected';echo'>S-Klasse</option>
                </select>      
              </dd>
            </dl>       
          </li>
         </ul>      
        </div>
      </form>
      </div>';

if($getStartklassen=="alle"){
  $startklassen = array('A','B','C','J','S');
} else {
  $startklassen = array($getStartklassen);
}
  
if($_SESSION["getAuswahl"]=='nord'){
  foreach($startklassen as $value){
    echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">'.$value.'-Klasse: Nord-Cup</div>  
    </div>      
      <div class="groupBoxBody">
      <table class="tg">
         '.getRanglistenHTML('RR_'.$value.'','Nord').'
      </table>      
      </div>
    </div>';
    //class="groupBox" end  
  }//end foreach
}
if($_SESSION["getAuswahl"]=='sued'){
  foreach($startklassen as $value){
    echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">'.$value.'-Klasse: S&uuml;d-Cup</div>  
    </div>      
      <div class="groupBoxBody">
      <table class="tg">
         '.getRanglistenHTML('RR_'.$value.'','Sued').'
      </table>      
      </div>
    </div>';
    //class="groupBox" end  
  }//end foreach
}//

if($_SESSION["getAuswahl"]=='dcgp'){
  foreach($startklassen as $value){
    echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
      <div style="float: left;">'.$value.'-Klasse: Deutschland-Cup/Gro&szlig;er Preis von Deutschland</div>  
    </div>      
      <div class="groupBoxBody">
      <table class="tg">';
    if($value == 'A'){
      echo getRanglistenHTML4pktquali('RR_'.$value.'');
    } else {
      //echo getRanglistenHTML4dcgp('RR_'.$value.'');    
      echo getRanglistenHTML4pktquali('RR_'.$value.'');    
    }
    echo'
      </table>      
      </div>
    </div>';
    //class="groupBox" end  
  }//end foreach
}//
      
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
}
  
if($getRegion=='Nord' || $getRegion=='Sued'){
  $startklassen = array('A','B','C','J','S');
  
  echo' 
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
    <head>
      <!-- (c) 2004 - 2013 The Admidio Team - http://www.admidio.org -->
      <!-- (c) 2014 Adapted by DRBV Webteam to a online version of the couples competition book -->  
    <title>Deutscher Rock&#039;n&#039;Roll und Boogie-Woogie Verband e.V. - Startbuch Check</title>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <link rel="shortcut icon" type="image/x-icon" href="https://drbv.de/adm/adm_themes/classic/icons/favicon.png" />
    <link rel="apple-touch-icon" href="https://drbv.de/adm/adm_themes/classic/icons/webclipicon.png" />
    <style type="text/css">
      @import url(https://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:300);
      body {
        font-family: "Yanone Kaffeesatz",Helvetica,Arial,Sans-Serif;
        font-size: 17px;
      }    
    </style>';      

  echo $gLayout['header'];
  echo'
    </head>
    
    <body style="background-color:#f7f5f2;">
    <div align="center" class="rlraw">';
  foreach($startklassen as $value){
    if($getRegion=='Nord'){
      echo'<h1 class="rlraw">'.$value.'-Klasse: Nord-Cup</h1>  ';
    } else {
      echo'<h1 class="rlraw">'.$value.'-Klasse: S&uuml;d-Cup</h1>  ';    
    }
    echo'      
    <div>
      <table class="tg" width=800px>
         '.getRanglistenHTML('RR_'.$value.'',$getRegion).'
      </table>      
    </div>';
  }//end foreach  
  
  echo'
    </div>
    </body>
  </html>';
}//end if($getRegion)  
?>
