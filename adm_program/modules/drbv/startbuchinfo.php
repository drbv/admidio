<?php
/******************************************************************************
 * Alle Startkarten eines Vereins anzeigen, mit Infos
 *
 * Copyright    : (c) 2016 DRBV Webteam
 * Homepage     : http://www.drbv.de
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
require_once('../../system/classes/list_configuration.php');
require_once('../../system/classes/email.php');  
require_once('../../system/classes/table_roles.php');
require_once('../../modules/profile/roles_functions.php');
require_once('../../system/drbv_funktionen.php');
     
// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getFormId = admFuncVariableIsValid($_GET, 'form_id', 'numeric', 0);

// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

function getAufstiegspunkte($AnfangsPunkte, $PunkteDatum, $StartbuchNr){
    
  // Aufstiegspunkte berechnen:
  // --------------------------  
  // Tanzpaar in TLP Datenbank suchen  
  $sqlab = 'SELECT turniernummer, startklasse, dame, herr, team, platz, punkte, rl_punkte 
            FROM paare WHERE 
            startbuch       = '.$StartbuchNr.' OR
            boogie_sb_herr  = '.$StartbuchNr.' OR 
            boogie_sb_dame  = '.$StartbuchNr; 
  $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
  $pkt_summe       = 0;  
  $pkt_ertanzt     = 0;  
  $anzahl_turniere = 0;
  
  while($aufstiegspunkte = mysqli_fetch_array($punkteDB)){
    //print_r($aufstiegspunkte);echo' :DEBUG::aufstiegspunkte<br>';
    $sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $aufstiegspunkte["turniernummer"]; 
    $turnier      = mysqli_query(DRBVdb(), $sqlab);
    $turnierdaten = mysqli_fetch_array($turnier);
    //echo"$sqlab<br>";
    // Nur gefundene Turniere ab Aufstiegspunkte_Datum einbeziehen
    if(strtotime($PunkteDatum) < strtotime($turnierdaten["datum"])){
      $anzahl_turniere++;
      $pkt_ertanzt = $pkt_ertanzt + $aufstiegspunkte["punkte"]; 
    }    
  }
  //Punkte = Anfangswert + ertanzte Punkte ab Datum
  //print_r($anzahl_turniere);echo' :DEBUG::anzahl_turniere<br>';
  return array(($AnfangsPunkte + $pkt_ertanzt),$anzahl_turniere);
}  
  
function getRngLstnPkt4cup($StartbuchNr,$startklasse,$region){
  
  global $gPreferences;
  
  //Definiere Ranglistenpunkte, ab Platz8 wird das array mit einem
  //Punkt bis Platz200 gefuellt
  $rangpkttab   = [1 => '20','15','10','8','6','4','2','1'];
  for($i = 9; $i < 201; $i++){
    $rangpkttab[] = 1;
  }
  //print_r($rangpkttab);echo' :DEBUG::rangpkttab<br>';

  //Cup-Turniere werden in den Organisationseinstellungen gesetzt!  
  //und deren turniernummer in einem array zugeordnet
  if($region='Nord_Cup'){
    $trn_nr = explode(";", $gPreferences['txt_nc_trn']);
    $abs_nr = $gPreferences['txt_nc_ndm'];
  }
  else {
    $trn_nr = explode(";", $gPreferences['txt_sc_trn']);
    $abs_nr = $gPreferences['txt_sc_sdm'];
  }
  //print_r($trn_nr);echo' :DEBUG::trn_nr<br>';
  
  $rnglstnpkt4cup_4stkl = array();
  $anzahl_turniere      = 0;

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
    $sqlab = 'SELECT startbuch,platz,cup_serie 
              FROM paare WHERE             
              turniernummer = "'.$turniernr.'" AND
              startklasse   = "'.$startklasse.'" ORDER BY platz'; 
    $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
    $platz_korrektur = 1;
    $platz_vorher    = 0;
    //print_r($sqlab);echo' :DEBUG::sqlab<br>';
    //print_r($punkteDB);echo' :DEBUG::punkteDB<br>';

    //in dem gesuchten ergebnis koennen grenzverkehr paare und paare
    //aus der jeweils anderen region sein: diese werden hier geloescht    
    while($platz = mysqli_fetch_array($punkteDB)){
      //print_r($platz);echo' :DEBUG::platz<br>';
      if($platz["cup_serie"] == $region){        
        $erg_ohnekorrkt[] = array($platz["platz"],$platz["startbuch"]);//geloescht aber platzierung erhalten        
      }
    }
    //print_r($erg_ohnekorrkt);echo' :DEBUG::erg_ohnekorrkt<br>';
    
    //platzierung aufgrund der loeschung nachbessern
    $platzneu      = 1;
    $remind        = 0;
    $punkte        = array();
    
    for($i = 0; $i < count($erg_ohnekorrkt); ++$i) {
      $curr = $erg_ohnekorrkt[$i];
      $next = $erg_ohnekorrkt[$i+1];
      //print_r($curr);echo' :DEBUG::curr<br>';
      //print_r($next);echo' :DEBUG::next<br>';
     
      $erg_mitkorrkt[$i+1]   = array($platzneu,$erg_ohnekorrkt[$i][1]);
      $erg_platzierung[$i+1] = $platzneu;
      if($curr[0]!=$next[0]){
        $punkte[$i+1] = $abs_multi*($rangpkttab[$platzneu]+(count($erg_ohnekorrkt)-$i-1));
        $platzneu++;
        if($remind) {
          $platzneu++;
          $punkte[$i+1]   = $abs_multi*($rangpkttab[$platzneu]+(count($erg_ohnekorrkt)-$i+$remind));//zweiter wert (und folgende?)
        }
        $remind = 0;
      }
      if($curr[0]==$next[0]){
        $remind++;
        $punkte[$i+1] = $abs_multi*($rangpkttab[$platzneu]+(count($erg_ohnekorrkt)-$i-1));//erster wert        
      }            
    }//end for
    //print_r($erg_mitkorrkt);echo' :DEBUG::erg_mitkorrkt<br>';
    //print_r($punkte);echo' :DEBUG::punkte<br>';
    //print_r($erg_platzierung);echo' :DEBUG::erg_platzierung<br>';
    
    //suchen, welche platzierung doppelt ist
    $unique = array_unique($erg_platzierung);
    $double = (array_diff_assoc($erg_platzierung, $unique));  
    
    //fuer gefundene doppelte, die punkte mittelwerten und die entsprechenden
    //keys modifizieren
    $korr_rlpkt = array();
    foreach($double as $key => $value){
      $korr_rlpkt[]   = $punkte[$key];
      $korr_rlpkt[]   = $punkte[$value];
      $result         = (!empty($korr_rlpkt) ? array_sum($korr_rlpkt) / count($korr_rlpkt) : 0);
      $punkte[$key]   = $result;
      $punkte[$value] = $result;
    }
    //print_r($punkte);echo' :DEBUG::punkteneu<br>';
    
    //in das array aus platz,startbuchnr die korr. punkte hinzufuegen
    foreach($erg_mitkorrkt as $key => $value){
      $erg_mitkorrkt[$key]   = array($value[0],$value[1],$punkte[$key]);//platz,startbuchnr,rl_punkte
      $rnglstnpkt[$value[1]] = $punkte[$key];                           //startbuchnr als key,rl_punkte
    }
    //print_r($erg_mitkorrkt);echo' :DEBUG::erg_mitkorrkt<br>';
    //print_r($rnglstnpkt);echo' :DEBUG::rnglstnpkt<br>';
    
    $rnglstnpkt4cup_4stkl[$turniernr] = $rnglstnpkt;
    $anzahl_turniere++;  
  }//end foreach($trn_nr as $value)    
  //print_r($rnglstnpkt4cup_4stkl);echo' :DEBUG::rnglstnpkt4cup_4stkl<br>';      
  //print_r($anzahl_turniere);echo' :DEBUG::anzahl_turniere<br>';
  
  //alle ranglistenpunkte einer startbuchnummer suchen
  $rlpkte = array();
  foreach($rnglstnpkt4cup_4stkl as $value){
    foreach($value as $stbnr => $punkte){
      if($stbnr == $StartbuchNr){
        $rlpkte[] = $punkte;
      }
      //hier noch eine nachtraegliche korrektur codieren aus den 
      //$gPreferences['txt_paare_internationaler_start']
      //die bei nichtteilnahme die gleichen ranglistenpunkte
      //erhalten wie der drittplatzierte
    }
  }
  //die gefundenen werte absteigend sortieren und nur die besten 5 ergebnisse summieren,
  //alle weiteren sind streichergebnisse
  rsort($rlpkte);
  $rlpkte = array_sum(array_slice($rlpkte, 0, 5));
  //print_r($rlpkte);echo' :DEBUG::$rlpkte '.$StartbuchNr.'<br>';  
  return $rlpkte;
}  
  
// diese Funktion gibt den Html-Code fuer ein Feld mit Beschreibung wieder
// dabei wird der Inhalt richtig formatiert
function getFieldCode($fieldNameIntern, $user, $akro_class, $as_html)
{
    global $gPreferences, $g_root_path, $gCurrentUser, $gProfileFields, $gL10n;
    $html      = '';
    $value     = '';
    $msg_image = '';

    if($gCurrentUser->editProfile($user) == false && $gProfileFields->getProperty($fieldNameIntern, 'usf_hidden') == 1)
    {
        return '';
    }

  // get value of field in html format
  $value = $user->getValue($fieldNameIntern, 'html');

  // if birthday then show age
  if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'BIRTHDAY')
  {
    $birthday = new DateTimeExtended($user->getValue($fieldNameIntern, $gPreferences['system_date']), $gPreferences['system_date'], 'date');
    $value = $value. '&nbsp;&nbsp;&nbsp;('. $birthday->getAge(). ' '.$gL10n->get('PRO_YEARS').')';
  }

  // show html of field, if user has a value for that field or it's a checkbox field
    if(strlen($user->getValue($fieldNameIntern)) > 0 || $gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'CHECKBOX')
    {
      $html = '<li>
                 <dl>
                   <dt style="width:40%;">'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</dt>
                   <dd>'. $value. '&nbsp;</dd>
                 </dl>       
               </li>';    
    }
    if($as_html){
      return $html;
    } else {
      return $value;
    }
}
  
unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

// Alle Rollen auflisten, die dem Mitglied zugeordnet sind
// und die Rollen-ID des Vereins suchen aus dem Vereinsnamen
$result_role  = getRolesFromDatabase($user->getValue('usr_id'));
$count_role   = $gDb->num_rows($result_role);
  
$countShowRoles  = 0;
$role   = new TableRoles($gDb);

while($row = $gDb->fetch_array($result_role))
{
  if($gCurrentUser->viewRole($row['mem_rol_id']) && $row['rol_visible']==1)
  {
    $role->clear();
    $role->setArray($row);
            
    if($role->getValue('rol_name')==$user->getValue('FIRST_NAME')){
      $getRoleId = $row['mem_rol_id'];
    } else {
      $getRoleId = 17;//17 = 1_Dresdner_RRC (nur zu Testzwecken), sollte nicht erreicht werden
      //$getRoleId = 37;//17 = Iserlohn
      //$getRoleId = 49;//49 = Worms
    }
    $countShowRoles++;
  }
}  
require('../forms/role_members.php');//daten aus listID170 holen
require('../forms/role_members_ID65.php');//daten aus listID65 holen
  
// Html-Kopf ausgeben
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = 'Startbuch/Startkarten &Uuml;bersicht';
}
else
{
    $gLayout['title'] = $gL10n->get('PRO_PROFILE_FROM', $user->getValue('FIRST_NAME'), $user->getValue('LAST_NAME'));
}
$gLayout['header'] = '
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <link rel="stylesheet" href="'.$g_root_path.'/adm_program/modules/forms/forms.css" type="text/css" />         
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>         
    <!-- Einbindung der Jquery Libary -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>         
    <script type="text/javascript">
      function stopEnterKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
      }
      document.onkeypress = stopEnterKey;
    </script>
    <style>
      .balken {
        float: left;
        padding: 0px;
        font-size:.8em;
      }
    </style>';

require(SERVER_PATH. '/adm_program/system/overall_header.php');

echo '
<div class="formLayout" id="profile_form">
    <div class="formHead">'. $gLayout['title']. '</div>
    <div class="formBody">
        <div>';
        // *******************************************************************************
        // Userdaten-Block
        // *******************************************************************************

        echo '
          <div style="width: 58%; float: left;">
            <div id="startbuchinfo" class="groupBox">
               <div class="groupBoxHeadline">
                 <div style="float: left;">Vereinsdaten:'; 
                  //$user->getValue('FIRST_NAME')
                  //$user->getValue('LAST_NAME')                            
                  echo '</div>
               </div>                                        
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   <li>
                     <dl>
                       <dt style="width:40%;">'.$gL10n->get('SYS_USERNAME').':</dt>
                       <dd><i>';
                       if(strlen($user->getValue('usr_login_name')) > 0)
                       {
                         echo $user->getValue('usr_login_name');
                       }
                       else
                       {
                         echo $gL10n->get('SYS_NOT_REGISTERED');
                       }
                         echo '&nbsp;</i></dd>
                     </dl>
                   </li>';

                   $bAddressOutput = false;    // Merker, ob die Adresse schon angezeigt wurde

                   // Schleife ueber alle Felder der Stammdaten
                   foreach($gProfileFields->mProfileFields as $field)
                   {
                     // nur Felder der Stammdaten anzeigen
                     if($field->getValue('cat_name_intern') == 'MASTER_DATA'
                     && ($gCurrentUser->editProfile($user) == true || $field->getValue('usf_hidden') == 0 ))
                     {
                        switch($field->getValue('usf_name_intern'))
                        {
                          // only show these fields in default profile list
                          case 'VEREIN':
                          case 'VEREINSNUMMER':
                          case 'VERANTWORTLICHER_NAME':  
                          case 'VERANTWORTLICHER_VORNAME':
                          case 'BUNDESLAND':
                            echo getFieldCode($field->getValue('usf_name_intern'), $user,'',TRUE);                          
                          break;
                          // define special html view for this fields
                          case 'ADDRESS':
                          case 'POSTCODE':
                          case 'CITY':
                          case 'COUNTRY':
                          if($bAddressOutput == false // output of address only once
                          && (  strlen($user->getValue('ADDRESS')) > 0 || strlen($user->getValue('POSTCODE')) > 0
                          || strlen($user->getValue('CITY')) > 0 || strlen($user->getValue('COUNTRY')) > 0 ))  
                          {
                            $bAddressOutput = true;
                            echo '<li>
                              <dl>
                                <dt>'.$gL10n->get('SYS_ADDRESS').':</dt>
                                <dd>';
                                    $address = '';
                                    urlencode($gCurrentUser->getValue('ADDRESS')).
                                    ',%20'. urlencode($gCurrentUser->getValue('POSTCODE')).
                                    ',%20'. urlencode($gCurrentUser->getValue('CITY')).
                                    ',%20'. urlencode($gCurrentUser->getValue('COUNTRY')).
                                    '&amp;daddr=';

                                    if(strlen($user->getValue('ADDRESS')) > 0
                                    && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('ADDRESS', 'usf_hidden') == 0))
                                    {
                                        $address   .= '<div>'.$user->getValue('ADDRESS'). '</div>';
                                    }

                                    if(strlen($user->getValue('POSTCODE')) > 0
                                    && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('POSTCODE', 'usf_hidden') == 0))
                                    {
                                      $address   .= '<div>'.$user->getValue('POSTCODE');
                                      // Ort und PLZ in eine Zeile schreiben, falls man beides sehen darf
                                      if(strlen($user->getValue('CITY')) == 0
                                      || ($gCurrentUser->editProfile($user) == false && $gProfileFields->getProperty('CITY', 'usf_hidden') == 1))
                                      {
                                        $address   .= '</div>';
                                      }
                                    }

                                    if(strlen($user->getValue('CITY')) > 0
                                    && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('CITY', 'usf_hidden') == 0))
                                    {
                                      // Ort und PLZ in eine Zeile schreiben, falls man beides sehen darf
                                      if(strlen($user->getValue('POSTCODE')) == 0
                                      || ($gCurrentUser->editProfile($user) == false && $gProfileFields->getProperty('POSTCODE', 'usf_hidden') == 1))
                                      {
                                        $address   .= '<div>';
                                      }
                                      $address   .= ' '. $user->getValue('CITY'). '</div>';
                                    }

                                    if(strlen($user->getValue('COUNTRY')) > 0
                                    && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('COUNTRY', 'usf_hidden') == 0))
                                    {
                                      $country    = $user->getValue('COUNTRY');
                                      $address   .= '<div>'.$country. '</div>';
                                    }
                                    echo $address;
                                echo '</dd>
                              </dl>
                            </li>';
                          }
                          break;
                          default:
                            //don't show any other fields in default profile list
                            echo '';
                          break;
                        }
                      }
                    }
                    echo '</ul>
                  </div>
                </div>
             </div>
           <div style="width: 100%; float: right">
         </div>
       </div>
       <div style="width: 100%; float: left;">
         <div id="admProfileMasterData" class="groupBox">                   
           <div class="groupBoxBody">
             <ul class="formFieldList">
               <li>
                 <dl>
                  <br>
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">';

                        $stb_invalid_cnt = 0;
                        $stb_cnt         = 1;
                        $htm_fo = '';
                        $htm_rr = '';
                        $htm_bw = '';

                        $form_data_arr = array();
                        unset($_SESSION['form_data_arr']);
                        foreach ($member_form_array as $memberformrow) {
                        //rm: inhalte der memberformrow werden aus der Liste ID65 Kontakdaten Startbuch erzeugt
                        //    und hier in ein array "form_data_arr" mit startbuchnr als key gepackt  
                        //print_r($memberformrow);echo' :DEBUG::memberformrow<br>';
                          $data     = array();
                          $pdf_data = array();
                          foreach ($memberformrow as $membercol => $membercont) {
                            if($membercol == 1) $pdf_data['VEREIN'] = $membercont;                            
                            if($membercol == 6) $key = $membercont;                            
                            if($membercol == 7) $pdf_data['STARTKLASSE'] = $membercont;                            
                            if($membercol == 9) $pdf_data['TEAMNAME'] = $membercont;                            
                            if($membercol >= 10){
                              $data[] = $membercont;
                            }
                            if($membercol == 10) $pdf_data['NAME_01'] = $membercont;
                            if($membercol == 11) $pdf_data['GEBURTSDATUM_01'] = $membercont;                                                        
                            if($membercol == 12) $pdf_data['NAME_02'] = $membercont;
                            if($membercol == 13) $pdf_data['GEBURTSDATUM_02'] = $membercont;                                                        
                            if($membercol == 14) $pdf_data['NAME_03'] = $membercont;
                            if($membercol == 15) $pdf_data['GEBURTSDATUM_03'] = $membercont;                                                        
                            if($membercol == 16) $pdf_data['NAME_04'] = $membercont;
                            if($membercol == 17) $pdf_data['GEBURTSDATUM_04'] = $membercont;                                                        
                            if($membercol == 18) $pdf_data['NAME_05'] = $membercont;
                            if($membercol == 19) $pdf_data['GEBURTSDATUM_05'] = $membercont;                                                        
                            if($membercol == 20) $pdf_data['NAME_06'] = $membercont;
                            if($membercol == 21) $pdf_data['GEBURTSDATUM_06'] = $membercont;                                                        
                            if($membercol == 22) $pdf_data['NAME_07'] = $membercont;
                            if($membercol == 23) $pdf_data['GEBURTSDATUM_07'] = $membercont;                                                        
                            if($membercol == 24) $pdf_data['NAME_08'] = $membercont;
                            if($membercol == 25) $pdf_data['GEBURTSDATUM_08'] = $membercont;                                                        
                            if($membercol == 26) $pdf_data['NAME_09'] = $membercont;
                            if($membercol == 27) $pdf_data['GEBURTSDATUM_09'] = $membercont;                                                        
                            if($membercol == 28) $pdf_data['NAME_10'] = $membercont;
                            if($membercol == 29) $pdf_data['GEBURTSDATUM_10'] = $membercont;                                                        
                            if($membercol == 30) $pdf_data['NAME_11'] = $membercont;
                            if($membercol == 31) $pdf_data['GEBURTSDATUM_11'] = $membercont;                                                        
                            if($membercol == 32) $pdf_data['NAME_12'] = $membercont;
                            if($membercol == 33) $pdf_data['GEBURTSDATUM_12'] = $membercont;                                                        
                            if($membercol == 34) $pdf_data['NAME_13'] = $membercont;
                            if($membercol == 35) $pdf_data['GEBURTSDATUM_13'] = $membercont;                                                        
                            if($membercol == 36) $pdf_data['NAME_14'] = $membercont;
                            if($membercol == 37) $pdf_data['GEBURTSDATUM_14'] = $membercont;                                                        
                            if($membercol == 38) $pdf_data['NAME_15'] = $membercont;
                            if($membercol == 39) $pdf_data['GEBURTSDATUM_15'] = $membercont;                                                        
                            if($membercol == 40) $pdf_data['NAME_16'] = $membercont;
                            if($membercol == 41) $pdf_data['GEBURTSDATUM_16'] = $membercont;                                                        
                            if($membercol == 42) $pdf_data['NAME_E01'] = $membercont;
                            if($membercol == 43) $pdf_data['GEBURTSDATUM_E01'] = $membercont;                                                        
                            if($membercol == 44) $pdf_data['NAME_E02'] = $membercont;
                            if($membercol == 45) $pdf_data['GEBURTSDATUM_E02'] = $membercont;                                                        
                            if($membercol == 46) $pdf_data['NAME_E03'] = $membercont;
                            if($membercol == 47) $pdf_data['GEBURTSDATUM_E03'] = $membercont;                                                        
                            if($membercol == 48) $pdf_data['NAME_E04'] = $membercont;
                            if($membercol == 49) $pdf_data['GEBURTSDATUM_E04'] = $membercont;                                                        
                            if($membercol == 50) $pdf_data['ANZAHL_AKTIVE'] = $membercont;                                                        
                          }  
                          $form_data_arr[$key] = $data;
                          $form_pdf_arr[$key]  = $pdf_data;
                        }
                        //print_r($form_data_arr);echo' :DEBUG::form_data_arr<br>';
                        $_SESSION['form_data_arr']=$form_pdf_arr;

                        foreach ($member_array as $memberrow) {
                        //rm: inhalte der memberrow werden aus der Liste ID170 Kontakdaten Startbuch erzeugt  
                        //print_r($memberrow);echo' :DEBUG::memberrow<br>';                          
                          
                          foreach ($memberrow as $membercol => $membercont) {
                            //Praeambelturnierstartbuecher 50000+ werden ausgenommen 
                            if($membercol==4 && (substr($memberrow[3],0,1) != '5')){
                              if(substr($membercont,0,1) == 'F'){
                                $htm_fo .='<tr><td align="center">'.$memberrow[3].'</td>';
                                $name   = $memberrow[6];                                
                              } elseif(substr($membercont,0,1) == 'B') {
                                if($memberrow[7]){
                                  $htm_bw .='<tr><td align="center">'.$memberrow[3].'</td>';
                                  $name   = $memberrow[7].' '.$memberrow[8];
                                  $zusatz = '<br><img style="vertical-align: top;" 
                                     src="'.THEME_PATH.'/icons/info.png"
                                     title="'.$memberrow[10].' / '.$memberrow[11].' '.$memberrow[12].'">
                                     <span style="font-size:.8em;"><sup>'.$memberrow[9].'</sup></span>';
                                }
                                if($memberrow[14]){
                                  $htm_bw .='<tr><td align="center">'.$memberrow[3].'</td>';
                                  $name = $memberrow[14].' '.$memberrow[15];
                                  $zusatz = '<br><img style="vertical-align: top;" 
                                     src="'.THEME_PATH.'/icons/info.png"
                                     title="'.$memberrow[17].' / '.$memberrow[18].' '.$memberrow[19].'">
                                     <span style="font-size:.8em;"><sup>'.$memberrow[16].'</sup></span>';
                                }
                              } else {
                                $htm_rr .='<tr><td valign="top" align="center" style="font-size:.9em;">'.$memberrow[3].'</td>';                                
                                //$name = $memberrow[7].' '.$memberrow[8].' & '.$memberrow[14].' '.$memberrow[15];
                              }
                              //Startbuch gültig                              
                              if($memberrow[5] == '1'){
                                $stb_valid = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="g&uuml;ltig" />';
                              } else {
                                $stb_valid = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="ung&uuml;ltig" />';
                                $stb_invalid_cnt = $stb_invalid_cnt + 1;
                              }
                              //Athletiktest Herr & Dame
                              if($memberrow[23] == '1'){
                                $tat_hr = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="bestanden" />';
                              } else {
                                $tat_hr = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="keine Teilnahme oder nicht bestanden" />';
                              }   
                              if($memberrow[24] == '1'){
                                $tat_da = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="bestanden" />';
                              } else {
                                $tat_da = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="keine Teilnahme oder nicht bestanden" />';
                              }   
                              //Qualifikation DC/GPvD & Nachrücker
                              if($memberrow[25] == '1'){
                                $q_dcgp = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="qualifiziert f&uuml;r DC&GPvD" />';
                              } else {
                                $q_dcgp = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="nicht qualifiziert" />';
                              }   
                              if($memberrow[26] == '1'){
                                $q_dcgp    = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok_yellow1.png" alt="Ja" title="qualifiziert als Nachr&uuml;cker f&uuml;r DC&GPvD" />';
                                $q_dcgp_nr = 'NR';
                              } else {
                                $q_dcgp_nr = '';
                              }                                 
                              //Qualifikation DM & Nachrücker
                              if($memberrow[27] == '1'){
                                $q_dm = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok.png" alt="Ja" title="qualifiziert f&uuml;r DM" />';
                              } else {
                                $q_dm = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/no.png" alt="Nein" title="nicht qualifiziert" />';
                              }   
                              if($memberrow[28] == '1'){
                                $q_dm    = '<img style="vertical-align: middle;" src="'.THEME_PATH.'/icons/ok_yellow1.png" alt="Ja" title="qualifiziert als Nachr&uuml;cker f&uuml;r DM" />';                                
                                $q_dm_nr = 'NR';
                              } else {
                                $q_dm_nr = '';
                              }                                
                              //Alterscheck, bis wann in S/J getanzt werden kann
                              $hinweis_alter = '';
                              if($membercont=="RR_S"){
                                $sbisjahr = min(substr($memberrow[9],6,4),substr($memberrow[16],6,4))+14;
                                $hinweis_alter = 'bis '.($sbisjahr);
                              }
                              if($membercont=="RR_J"){
                                $chk_alter_c         = '';
                                $chk_alter_hinweis_c = '';
                                $chk_alter_b         = '';
                                $chk_alter_hinweis_b = '';
                                //
                                $sbisjahr = min(substr($memberrow[9],6,4),substr($memberrow[16],6,4))+17;                   
                                $hinweis_alter = 'bis '.($sbisjahr);
                                //Hinweise bei J bzgl. Alter & Aufstieg
                                //Aufstieg nach C: AktuellesJahr - GebJahr >= 13
                                if(date("Y")-max(substr($memberrow[9],6,4),substr($memberrow[16],6,4)) >= 13){
                                  $chk_alter_c = ' und das Alter'; 
                                } else {
                                  $chk_alter_hinweis_c = 'Der Aufstieg ist aber erst ab 13 Jahren m&ouml;glich.';
                                }
                                //Aufstieg nach B: AktuellesJahr - GebJahr >= 14
                                if(date("Y")-max(substr($memberrow[9],6,4),substr($memberrow[16],6,4)) >= 14){
                                  $chk_alter_b = ' und das Alter'; 
                                } else {
                                  $chk_alter_hinweis_b = 'Der Aufstieg ist aber erst ab 14 Jahren m&ouml;glich.';
                                }                                                                                                      
                              }
                              $aufstiegspunkte = '';
                              $anzahl_turniere = '';
                              list($aufstiegspunkte, $anzahl_turniere) = getAufstiegspunkte($memberrow[21],$memberrow[22],$memberrow[3]);
                              
                              //Header Formationen
                              if(substr($membercont,0,1) == 'F'){
                                $htm_fo .='
                                <td>'.$membercont.'</td>
                                <td>'.$name.'<br><span style="font-size:.8em;">Anzahl Aktiver: '.$memberrow[29].'</span>
                                </td>
                                <td align="center">'.$stb_valid.'</td>
                                <td align="center">'.$anzahl_turniere.'</td>  
                               </tr>
                               <tr>
                                <td align="center">&nbsp;</td>
                                <td align="center">&nbsp;</td>  
                                <td align="left">
                                  <a target="_blank" class="iconLink" 
                                     href="'.$g_root_path.'/adm_program/modules/drbv/startbuchinfo_pdf.php?stbnr='.$memberrow[3].'&mode=pdf">
                                     <img src="'.THEME_PATH.'/icons/pdf.png" alt="Einlassformular drucken" title="Einlassformular drucken" /></a>                                                                                                            
                                  <script>
                                    $(document).ready(function(){
                                      /* jQuery-Code */
                                      $(\'#fade'.$memberrow[3].'\').click(function(){$(\'#ref'.$memberrow[3].'\').toggle(\'slow\');
                                      })
                                    });
                                  </script>    
                                  <a class="admINFOaktive" href="#'.$memberrow[3].'" id="fade'.$memberrow[3].'">
                                     <img style="vertical-align: top;" src="'.THEME_PATH.'/icons/add.png"
                                     title="Aktiven anzeigen!">
                                     <div style="font-size: 10px;display:none;" id="ref'.$memberrow[3].'"><table>';
                                
                                  for ($i=1;$i <= 16; $i++){
                                    if($form_data_arr[$memberrow[3]][2*$i-2]){
                                      $htm_fo .= '<tr><td align="right">'.$i.'</td><td>'
                                        .$form_data_arr[$memberrow[3]][2*$i-2].'</td><td>'
                                        .$form_data_arr[$memberrow[3]][2*$i-1].'</td></tr>';
                                    }
                                  }
                                  
                                $htm_fo .='
                                     </table></div>
                                   </a>                                                                     
                                 </td>
                                 <td align="center">&nbsp;</td>  
                                 <td align="center">&nbsp;</td>                                    
                               </tr>';
                              }
                              //Header Boogie-Woogie
                              if(substr($membercont,0,1) == 'B'){
                                $htm_bw .='
                                <td>'.$membercont.'</td>
                                <td>'.$name.$zusatz.'</td>
                                <td align="center">'.$stb_valid.'</td>
                                <td align="center">'.$anzahl_turniere.'</td>  
                               </tr>';
                              }
                              //Sporttauglichkeit
                              $sprttgl_htm = '';
                              //A keine
                              if($membercont=="RR_A") $sprttgl_htm = '';
                              //B/C alle zwei Jahre
                              if($membercont=="RR_C" || $membercont=="RR_B"){
                                $sprttgl_jahr = 2;
                              }
                              //S/J jedes Jahr
                              if($membercont=="RR_S" || $membercont=="RR_J"){
                                $sprttgl_jahr = 1;                              
                              }
                              if($membercont=="RR_S" || $membercont=="RR_J" || $membercont=="RR_C" || $membercont=="RR_B"){
                                $sprttglhr_htm = $memberrow[13].' ';
                                $sprttglda_htm = $memberrow[20].' ';
                                
                                $ablaufdat_hr  = substr($memberrow[13],0,6).(substr($memberrow[13],6,4)+$sprttgl_jahr);
                                $ablaufdat_da  = substr($memberrow[20],0,6).(substr($memberrow[20],6,4)+$sprttgl_jahr);
                                
                                // 60 Sekunden * 60 Minuten * 24 Stunden = 1 Tag
                                $faktor_tag = 86400;
                                $day2abl_hr = floor((strtotime($ablaufdat_hr)-strtotime("now"))/$faktor_tag);
                                $day2abl_da = floor((strtotime($ablaufdat_da)-strtotime("now"))/$faktor_tag);
                                                                                                
                                if($day2abl_hr < 0){
                                  $sprttglhr_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_rt.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist seit dem '.$ablaufdat_hr.' abgelaufen!">';
                                } elseif ($day2abl_hr < 43){
                                  $sprttglhr_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_gb.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist in '.$day2abl_hr.' Tagen am '.$ablaufdat_hr.' f&auml;llig!">';                                   
                                } else {
                                  $sprttglhr_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_gr.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist am '.$ablaufdat_hr.' f&auml;llig!">';                                                                        
                                }
                                //print_r($day2abl_hr);echo' :DEBUG::day2abl_hr<br>';
                                if($day2abl_da < 0){
                                  $sprttglda_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_rt.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist seit dem '.$ablaufdat_da.' abgelaufen!">';
                                } elseif ($day2abl_da < 43){
                                  $sprttglda_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_gb.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist in '.$day2abl_da.' Tagen am '.$ablaufdat_da.' f&auml;llig!">';                                   
                                } else {
                                  $sprttglda_htm .= '<img style="vertical-align: middle;" 
                                                     src="'.THEME_PATH.'/icons/smilies/smilie_gr.png"
                                                     title="Die Sporttauglichkeitsuntersuchung ist am '.$ablaufdat_da.' f&auml;llig!">';                                                                        
                                }
                                //print_r($day2abl_da);echo' :DEBUG::day2abl_da<br>';                                
                              }                              
                              
                              //Aufstiegspunkte
                              if(substr($membercont,0,1) == 'R'){
                                //S/A haben keine
                                if($membercont=="RR_S" || $membercont=="RR_A"){
                                  $aufpkt_htm      = '';
                                  $aufstiegspunkte = '';                                  
                                }
                                //C/B 200 in jeweils höhere
                                if($membercont=="RR_C" || $membercont=="RR_B"){
                                    $stkl_htm = '';
                                    if($membercont=="RR_C"){
                                      $stkl_htm         = 'B';
                                      $aufstieg_hinweis = 'Die Aufstiegspunkte f&uuml;r die B-Klasse sind erreicht!';
                                    }
                                    if($membercont=="RR_B"){
                                      $stkl_htm = 'A';
                                      $aufstieg_hinweis = 'Die Aufstiegspunkte f&uuml;r die A-Klasse sind erreicht!';                                      
                                    }
                                    $aufpkt_htm = '';
                                    if($aufstiegspunkte < 200){
                                      $aufpkt_htm .= '
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.($aufstiegspunkte/4).'" height="8" alt="'.$aufstiegspunkte.'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg!" />
                                      </div>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_red.gif" width="'.((200-$aufstiegspunkte)/4).'" height="8" alt="'.(200-$aufstiegspunkte).'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg!" />&#8594;'.$stkl_htm.'
                                      </div>';                                                                        
                                    } else {
                                      $aufpkt_htm .= '
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.(200/4).'" height="8" alt="'.$aufstiegspunkte.'" title="'.$aufstieg_hinweis.'" />&#8593;'.$stkl_htm.'
                                      </div>';                                                                                                            
                                    }
                                }
                                //J 100 in C und 200 in B
                                if($membercont=="RR_J"){
                                  $aufpkt_htm = '';
                                    if($aufstiegspunkte < 100){
                                      $aufpkt_htm .= '
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.($aufstiegspunkte/2).'" height="8" alt="'.$aufstiegspunkte.'" title="Es fehlen noch '.(100-$aufstiegspunkte).' Punkte zum Aufstieg in die C-Klasse!" />
                                      </div>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_red.gif" width="'.((100-$aufstiegspunkte)/2).'" height="8" alt="'.(100-$aufstiegspunkte).'" title="Es fehlen noch '.(100-$aufstiegspunkte).' Punkte zum Aufstieg in die C-Klasse!" />&#8594;C
                                      </div>
                                      <br>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.($aufstiegspunkte/4).'" height="8" alt="'.$aufstiegspunkte.'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg in die B-Klasse!" />
                                      </div>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_red.gif" width="'.((200-$aufstiegspunkte)/4).'" height="8" alt="'.(200-$aufstiegspunkte).'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg in die B-Klasse!" />&#8594;B
                                      </div>';                                    
                                    } elseif($aufstiegspunkte < 200){
                                      $aufpkt_htm .= '
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.(100/2).'" height="8" alt="'.$aufstiegspunkte.'" title="Die Aufstiegspunkte'.$chk_alter_c.' f&uuml;r die C-Klasse sind erreicht! '.$chk_alter_hinweis_c.'" />&#8593;C
                                      </div>
                                      <br>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.($aufstiegspunkte/4).'" height="8" alt="'.$aufstiegspunkte.'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg in die B-Klasse!" />
                                      </div>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_red.gif" width="'.((200-$aufstiegspunkte)/4).'" height="8" alt="'.(200-$aufstiegspunkte).'" title="Es fehlen noch '.(200-$aufstiegspunkte).' Punkte zum Aufstieg in die B-Klasse!" />&#8594;B
                                      </div>';                                                                        
                                    } else {
                                      $aufpkt_htm .= '
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.(100/2).'" height="8" alt="'.$aufstiegspunkte.'" title="Die Aufstiegspunkte'.$chk_alter_c.' f&uuml;r die C-Klasse sind erreicht! '.$chk_alter_hinweis_c.'" />&#8593;C
                                      </div>
                                      <br>
                                      <div class="balken">
                                        <img src="'.THEME_PATH.'/icons/balken_gr.gif" width="'.(200/4).'" height="8" alt="'.$aufstiegspunkte.'" title="Die Aufstiegspunkte'.$chk_alter_b.' f&uuml;r die B-Klasse sind erreicht! '.$chk_alter_hinweis_b.'" />&#8593;B
                                      </div>';                                                                                                            
                                    }
                                }
                                //Ranglistenpunkte
                                if($gCurrentUser->isWebmaster()){
                                  $rnglstnpkt = getRngLstnPkt4cup($memberrow[3],$membercont,'Nord_Cup');
                                } else {
                                  $rnglstnpkt = '';
                                }
                                //Header RocknRoll
                                $htm_rr .='
                                <td valign="top" align="center" style="font-size:.8em;">'.$membercont.'<br><span style="font-size:.8em;">'.$hinweis_alter.'</span></td>
                                <td>'.$memberrow[7].' '.$memberrow[8].'<br><img style="vertical-align: top;" 
                                     src="'.THEME_PATH.'/icons/info.png"
                                     title="'.$memberrow[10].' / '.$memberrow[11].' '.$memberrow[12].'">
                                     <span style="font-size:.8em;"><sup>'.$memberrow[9].'</sup></span></td>
                                <td align="center" style="font-size:.8em;">'.$sprttglhr_htm.'</td>
                                <td>'.$tat_hr.'</td>                                                                                                      
                                <td align="center" rowspan="2">'.$stb_valid.'</td>
                                <td align="center" rowspan="2">'.$anzahl_turniere.'</td>';
                                $htm_rr .='
                                    <td align="left" style="font-size:.8em;" >'.$aufpkt_htm.'</td>
                                    <td align="center" rowspan="2" style="font-size:.8em;">'.$q_dcgp.' '.$q_dcgp_nr.' '.$rnglstnpkt.'</td>
                                    <td align="center" rowspan="2" style="font-size:.8em;">'.$q_dm.' '.$q_dm_nr.'</td>
                                  </tr>
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td align="center" valign="top" style="font-size:.6em;"></td>
                                    <td>'.$memberrow[14].' '.$memberrow[15].'<br><img style="vertical-align: top;" 
                                     src="'.THEME_PATH.'/icons/info.png"
                                     title="'.$memberrow[17].' / '.$memberrow[18].' '.$memberrow[19].'">
                                     <span style="font-size:.8em;"><sup>'.$memberrow[16].'</sup></span></td>
                                    <td align="center" style="font-size:.8em;">'.$sprttglda_htm.'</td>
                                    <td>'.$tat_da.'</td>
                                    <td align="center" valign="top" style="font-size:.8em;">'.$aufstiegspunkte.'</td>
                                  </tr>
                                  <tr><td colspan="10">&nbsp;<hr>&nbsp;</td></tr>';
                              }                              
                            }                                                        
                          }
                          $stb_cnt = $stb_cnt + 1;
                        }  
                if($htm_rr != ''){
                  echo'
                  <fieldset>                    
                  <legend>Paarstartbücher </legend>
                    <br />
                    <table border="0" width="100%" align="center">
                      <tr>
                        <th align="center" style="font-size: 60%;">Nummer:</th>
                        <th align="center" style="font-size: 60%;">Klasse:</th>
                        <th align="left"   style="font-size: 60%;">Name:</th>
                        <th align="center" style="font-size: 60%;">Sport-<br>tauglich</th>
                        <th align="center" style="font-size: 60%;">TAT</th>                                          
                        <th align="center" style="font-size: 60%;">Start-<br>marke</th>
                        <th align="center" style="font-size: 60%;">Turniere</th>                    
                        <th align="center" style="font-size: 60%;">Aufstieg:</th>
                        <th align="center" style="font-size: 60%;">Q:GP/DC</th>
                        <th align="center" style="font-size: 60%;">Q:DM</th>
                      </tr>
                      '.$htm_rr.'
                    </table>
                  </fieldset>';
                  }
                if($htm_bw != ''){
                  echo'
                  <br />                    
                  <fieldset>
                  <legend>Boogie-Woogie Startkarten </legend>
                    <br />
                    <table border="0" width="95%" align="center">
                      <tr>
                        <th align="center">Nummer:</th>
                        <th>Klasse:</th>
                        <th>Name:</th>
                        <th align="center" style="font-size: 60%;">Startmarke<br>g&uuml;ltig?</th>
                        <th align="center" style="font-size: 60%;">Turnier-<br>teilnahmen</th>
                      </tr>
                      '.$htm_bw.'
                    </table>
                  </fieldset>';
                  }
                if($htm_fo != ''){
                  echo'
                  <br />                    
                  <fieldset>                    
                  <legend>Formationsstartb&uuml;cher </legend>
                    <br />
                    <table border="0" width="95%" align="center">
                      <tr>
                        <th align="center">Nummer:</th>
                        <th>Klasse:</th>
                        <th>Name:</th>
                        <th align="center" style="font-size: 60%;">Startmarke<br>g&uuml;ltig?</th>
                        <th align="center" style="font-size: 60%;">Turnier-<br>teilnahmen</th>
                      </tr>
                      '.$htm_fo.'
                    </table>
                  </fieldset>';
                  }
                  echo'
                    <br /><i>'.$stb_invalid_cnt.' Startbücher/Startkarten haben aktuell keine Startmarke!</i><br />                    
                  </font>
                  </dl>
               </li>
             </ul>
           </div>
          </div>       
        </div>                      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
  </div>
</div>';  

//$rnglstnpkt = getRngLstnPkt4cup('19576','RR_S','Nord_Cup');
//$rnglstnpkt = getRngLstnPkt4cup('19568','RR_S','Nord_Cup');

  
if($user->getValue('usr_id') != $gCurrentUser->getValue('usr_id'))
{
    echo '
    <ul class="iconTextLinkList">
        <li>
            <span class="iconTextLink">
                <a href="'.$g_root_path.'/adm_program/system/back.php"><img
                src="'.THEME_PATH.'/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
                <a href="'.$g_root_path.'/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
            </span>
        </li>
    </ul>';
}

require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>
