<?php
require_once('common.php');
require_once('drbv_database.php');

define( 'DAYS_CHECKIN_BEFORE_EVENT', 10 );//rmenken: lt. TSO 2014/15 

//rmenken: für Hilfe Icon einsetzbar
//<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DOW_FILE_NAME_RULES&amp;inline=true"><img 
//  onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DOW_FILE_NAME_RULES\',this)" onmouseout="ajax_hideTooltip()"
//  class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>

//rmenken: um auf Felder eines Nutzers zuzugreifen 
//$gCurrentUser->getValue('VEREINSNUMMER')

function getDaysToCheckindeadline( $date, $gPreferences ) {
  $ttermin  = strtotime( $date->getValue( 'dat_begin', $gPreferences['system_date'] ) ) ;
  $taktuell = strtotime( date("d.m.Y",time() ) );
  $tdiff    = ceil( ( $ttermin - $taktuell ) / 86400 );
  $tms      = $tdiff - DAYS_CHECKIN_BEFORE_EVENT;
  return $tms;
}

function isCheckinPossible( $date, $gPreferences ) {
  $daysToDeadline = getDaysToCheckindeadline( $date, $gPreferences );
  if( $daysToDeadline >= 0 ) {
    return true;
  } else {
    return false;
  }
}

// Funktion Startklassenbezeichner
// -------------------------------  
function startklbez($stk_bez){
  if($stk_bez == "F_RR_LF")
    $startklbez = "Lady Formation";
  if($stk_bez == "F_RR_MA")
    $startklbez = "RR Master Formation";
  if($stk_bez == "F_BW_MA")
    $startklbez = "BW Master Formation";   
  if($stk_bez == "F_RR_GF")
    $startklbez = "Girl Formation";     
  if($stk_bez == "F_RR_ST")
    $startklbez = "Showteam Formation";     
  if($stk_bez == "F_RR_J")
    $startklbez = "Jugend Formation";     
  return $startklbez;    
} 

// Funktion Startklassenbezeichnung
// --------------------------------  
function sklbezeichnung($skl_bez){
  if($skl_bez != "RR_A")
    $sklbez = $skl_bez;
  if($skl_bez == "RR_A")
    $sklbez = "A-Klasse";
  if($skl_bez == "RR_B")
    $sklbez = "B-Klasse";
  if($skl_bez == "RR_C")
    $sklbez = "C-Klasse";   
  if($skl_bez == "RR_J")
    $sklbez = "J-Klasse";     
  if($skl_bez == "RR_S")
    $sklbez = "S-Klasse";     
  if($skl_bez == "BW_MA")
    $sklbez = "Main-A";     
  if($skl_bez == "BW_MB")
    $sklbez = "Main-B";     
  if($skl_bez == "BW_SA")
    $sklbez = "Senior-A";     
  if($skl_bez == "BW_SB")
    $sklbez = "Senior-B";     
  if($skl_bez == "BW_JA")
    $sklbez = "BW-Junior";     
  if($skl_bez == "F_RR_M")
    $sklbez = "Formation RR Master";     
  if($skl_bez == "F_BW_M")
    $sklbez = "Formation BW Master";     
  if($skl_bez == "F_RR_J")
    $sklbez = "Formation RR Jugend";     
  if($skl_bez == "F_RR_LF")
    $sklbez = "Formation Lady";     
  if($skl_bez == "F_RR_GF")
    $sklbez = "Formation Girl";     
  if($skl_bez == "F_RR_ST")
    $sklbez = "Formation Showteam";     
  return $sklbez;    
} 
// Funktion Startklassenbezeichnung Kurzform
// -----------------------------------------  
function sklbezeichnung_kurz($skl_bez){
  if($skl_bez != "RR_A")
    $sklbez = $skl_bez;
  if($skl_bez == "RR_A")
    $sklbez = "A";
  if($skl_bez == "RR_B")
    $sklbez = "B";
  if($skl_bez == "RR_C")
    $sklbez = "C";   
  if($skl_bez == "RR_J")
    $sklbez = "J";     
  if($skl_bez == "RR_S")
    $sklbez = "S";     
  if($skl_bez == "BW_MA")
    $sklbez = "MA";     
  if($skl_bez == "BW_MB")
    $sklbez = "MB";     
  if($skl_bez == "BW_SA")
    $sklbez = "SA";     
  if($skl_bez == "BW_SB")
    $sklbez = "SB";     
  if($skl_bez == "BW_JA")
    $sklbez = "BWJ";     
  if($skl_bez == "F_RR_M")
    $sklbez = "FM";     
  if($skl_bez == "F_BW_M")
    $sklbez = "FBM";     
  if($skl_bez == "F_RR_J")
    $sklbez = "FJ";     
  if($skl_bez == "F_RR_LF")
    $sklbez = "FL";     
  if($skl_bez == "F_RR_GF")
    $sklbez = "FG";     
  if($skl_bez == "F_RR_ST")
    $sklbez = "FS";     
  if($skl_bez == "BS_BW_J1")
    $sklbez = "BS";     
  if($skl_bez == "BS_BW_E1")
    $sklbez = "BS";     
  return $sklbez;    
} 

// Funktion Rundenbezeichnung
// --------------------------  
function rundenbezeichnung($r_bez){
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
  if(utf8_encode($r_bez) == "End_r_Fuß" || $r_bez == "End_r_Fuß")
    $rundenbez = "Endrunde Fußtechnik";
  if($r_bez == "End_r_Akro")
    $rundenbez = "Endrunde Akrobatik"; 
  if($r_bez == "Vor_r_lang")
    $rundenbez = "Langsame Vorrunde";    
  if($r_bez == "Vor_r_schnell")
    $rundenbez = "Schnelle Vorrunde";       
  if($r_bez == "End_r_lang")
    $rundenbez = "Langsame Endrunde";
  if($r_bez == "End_r_schnell")
      $rundenbez = "Schnelle Endrunde";
  if($r_bez == "Hoff_r")
    $rundenbez = "Hoffnungsrunde";
  if($r_bez == "KO_r")
    $rundenbez = "KO-Runde";
  if($r_bez == "Semi")
    $rundenbez = "Semifinale";  
  return $rundenbez;    
} 

// Funktion GetBundeslandKurzform
// ------------------------------  
function getBndLandKurz($listvalue){
  if($listvalue == "1") $bndland = "BW";
  if($listvalue == "2") $bndland = "BY";
  if($listvalue == "3") $bndland = "BE";
  if($listvalue == "4") $bndland = "BB";
  if($listvalue == "5") $bndland = "HB";
  if($listvalue == "6") $bndland = "HH";
  if($listvalue == "7") $bndland = "HE";
  if($listvalue == "8") $bndland = "MV"; 
  if($listvalue == "9") $bndland = "NI ";
  if($listvalue == "10") $bndland = "NW";
  if($listvalue == "11") $bndland = "RP";
  if($listvalue == "12") $bndland = "SL";
  if($listvalue == "13") $bndland = "SN";
  if($listvalue == "14") $bndland = "ST";
  if($listvalue == "15") $bndland = "SH";
  if($listvalue == "16") $bndland = "TH";
  if($listvalue == "17") $bndland = "GR";
  return $bndland;
}  

// Funktion GetBundesland
// ----------------------
function getBndLand($listvalue){
  if($listvalue == "1") $bndland = "Baden-Württemberg";
  if($listvalue == "2") $bndland = "Bayern";
  if($listvalue == "3") $bndland = "Berlin";
  if($listvalue == "4") $bndland = "Brandenburg";
  if($listvalue == "5") $bndland = "Bremen";
  if($listvalue == "6") $bndland = "Hamburg";
  if($listvalue == "7") $bndland = "Hessen";
  if($listvalue == "8") $bndland = "Mecklenburg-Vorpommern"; 
  if($listvalue == "9") $bndland = "Niedersachsen";
  if($listvalue == "10") $bndland = "Nordrhein-Westfalen";
  if($listvalue == "11") $bndland = "Rheinland-Pfalz";
  if($listvalue == "12") $bndland = "Saarland";
  if($listvalue == "13") $bndland = "Sachsen";
  if($listvalue == "14") $bndland = "Sachsen-Anhalt";
  if($listvalue == "15") $bndland = "Schleswig-Holstein";
  if($listvalue == "16") $bndland = "Thüringen";
  if($listvalue == "17") $bndland = "Grenzverkehr";
  return $bndland;
}  

// Funktion GetStartklasse
// -----------------------
function getStartklasse($listvalue){
  if($listvalue == "1") $stkl = "RR_S";
  if($listvalue == "2") $stkl = "RR_J";
  if($listvalue == "3") $stkl = "RR_C";
  if($listvalue == "4") $stkl = "RR_B";
  if($listvalue == "5") $stkl = "RR_A";
  if($listvalue == "6") $stkl = "F_RR_M";
  if($listvalue == "7") $stkl = "F_RR_J";
  if($listvalue == "8") $stkl = "F_RR_LF";
  if($listvalue == "9") $stkl = "F_RR_GF";
  if($listvalue == "10") $stkl = "F_RR_ST";
  if($listvalue == "11") $stkl = "F_BW_M";
  if($listvalue == "12") $stkl = "BW_A";
  if($listvalue == "13") $stkl = "BW_B";  
  return $stkl;
}  

// Funktion GetRegion
// ------------------
function getRegion($listvalue){
  if($listvalue == "1") $region = "nord";
  if($listvalue == "2") $region = "sued";
  if($listvalue == "3") $region = "grenzverkehr";
  return $region;
}   
  
// Funktion aktuelles Alter berechnen
// ----------------------------------  
function alter($datum)
{
  $geburtstag = new DateTime($datum);
  $heute      = new DateTime(date('d-m-Y'));
  $differenz  = $geburtstag->diff($heute);
  
  return $differenz->format('%y');
}

// Funktion Einlassformular Formationen
// ------------------------------------
function getEinlassFormPDF($input)
{  
  //print_r($input);echo" ::DEBUG:input<br>";
  
  $pdf_output  = '';
  $pdf_output .= '                
  <style>
    .right      {text-align: right;position: absolute;right: 2em;top: 2em;}
    .grey       {color: #999;}
    .layout_b   {font-size: 12px;}    
  </style>    
  ';
  
  $pdf_output .= '<div class="right"><img src="'.THEME_PATH.'/images/DRBV_DTV_Logo.png" width="300" height="66"></div>';
  $pdf_output .= '<b>Einlassformular Formationen</b><br><br>';
  
  $pdf_output .= '                
  <table class="layout_a" width="600px">
    <tr>  
      <td width="200px"><b>Turniername:</b></td>
      <td class="grey">____________________________________</td>
    </tr>  
    <tr>  
      <td><b>Ausrichter/Ort:</b></td>
      <td class="grey">____________________________________</td>
    </tr>  
    <tr>  
      <td><b>Veranstaltungstag:</b></td>
      <td class="grey">____________________________________</td>
    </tr>    
    <tr>  
      <td colspan="2"><br><hr></td>    
    </tr> 
  </table>
  <br>
  <table class="layout_a" width="600px">     
    <tr>  
      <td width="200px"><b>Startklasse:</b></td>
      <td>'.startklbez($input[STARTKLASSE]).'</td>
    </tr>  
    <tr>  
      <td><b>Formationsname:</b></td>
      <td>'.$input[TEAMNAME].'</td>
    </tr>  
    <tr>  
      <td><b>Vereinsname:</b></td>
      <td>'.$input[VEREIN].'</td>
    </tr>  
    <tr>  
      <td><b>Anzahl Aktive:</b></td>
      <td>'.$input[ANZAHL_AKTIVE].'</td>
    </tr>  
    <tr>  
      <td>Betreuer 1:</td>
      <td class="grey">____________________________________</td>
    </tr>  
    <tr>  
      <td>&nbsp;&nbsp;&nbsp;Telefonnummer:</td>
      <td class="grey">____________________________________</td>
    </tr>  
    <tr>  
      <td>Betreuer 2:</td>
      <td class="grey">____________________________________</td>
    </tr>  
    <tr>  
      <td>&nbsp;&nbsp;&nbsp;Telefonnummer:</td>
      <td class="grey">____________________________________</td>
    </tr>           
    <tr>  
      <td colspan="2"><br><hr></td>    
    </tr> 
  </table>                                                                    
  <br>
                                  
  <table class="layout_b" width="600px"> 
    <tbody>                             
    <tr>
      <th align="center">Nr.</th>
      <th>Name des Aktiven</th>
      <th align="center">Geburtstag</th>
      <th align="center">Alter</th>
      <th align="center">Anw.</th>
      <th>Anmerkung</th>
    </tr>';
  
    for ($i=1;$i <= 16; $i++){
      if($i < 10){
        $nstr = 'NAME_0'.$i;
        $gstr = 'GEBURTSDATUM_0'.$i;
      } else {
        $nstr = 'NAME_'.$i;
        $gstr = 'GEBURTSDATUM_'.$i;        
      }  
      if($input[$nstr]){
        $pdf_output .= '  
        <tr>
          <td align="center">'.$i.'</td>
          <td>'.$input[$nstr].'</td>
          <td align="center">'.$input[$gstr].'</td>
          <td align="center">'.alter($input[$gstr]).'</td>
          <td align="center"><img src="'.THEME_PATH.'/icons/box32x32x2.png" width="16" height="16"></td>
          <td>&nbsp;</td>
        </tr>';
      }
    }
  $pdf_output .= '
    <tr>  
      <td colspan="6">&nbsp;</td>    
    </tr>';
      
    for ($i=1;$i <= 4; $i++){
        $nstr = 'NAME_E0'.$i;
        $gstr = 'GEBURTSDATUM_E0'.$i;
      if($input[$nstr]){
        $pdf_output .= '  
        <tr>
          <td align="center">E'.$i.'</td>
          <td>'.$input[$nstr].'</td>
          <td align="center">'.$input[$gstr].'</td>
          <td align="center">'.alter($input[$gstr]).'</td>
          <td align="center"><img src="'.THEME_PATH.'/icons/box32x32x2.png" width="16" height="16"></td>
          <td>&nbsp;</td>
        </tr>';
      }
    }
      
  $pdf_output .= '
    </tbody>
    <tfoot><tr>  
      <td colspan="6">&nbsp;</td>    
    </tr></tfoot>
  </table>';    
  //echo $pdf_output;
  return $pdf_output;
}

// Funktion Daten aus Startbuch holen
// ----------------------------------  
function getStartbuchData($startbuch)
{
  unset($startbuchdata);
  global $gPreferences;  

  $sqlab       = "SELECT * FROM adm_user_data WHERE usd_value = '" . $startbuch . "' AND usd_usf_id=1";
  $suchbegriff = mysqli_query(ADMIDIOdb(), $sqlab);

  while($temp = mysqli_fetch_array($suchbegriff)){
    // Daten zum Datensatz finden
    $benutzer_id = $temp[1]; 
    $sqlab = "SELECT * FROM adm_user_data WHERE usd_usr_id = '" . $benutzer_id . "' ";    
    $daten = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($werte = mysqli_fetch_array($daten)){
      $wert   = $werte[2];
      $inhalt = $werte[3];
      if($wert == "29")  $startbuchdata['startkl']    = $inhalt;
      if($wert == "31")  $startbuchdata['nameherr']   = $inhalt;
      if($wert == "32")  $startbuchdata['vornherr']   = $inhalt;
      if($wert == "37")  $startbuchdata['namedame']   = $inhalt;
      if($wert == "38")  $startbuchdata['vorndame']   = $inhalt;
      if($wert == "49")  $startbuchdata['bndland']    = $inhalt;
      if($wert == "53")  $startbuchdata['vereinsnr']  = $inhalt;
      if($wert == "65")  $startbuchdata['startmarke'] = $inhalt;
      if($wert == "157") $startbuchdata['zugehor']    = $inhalt;
      if($wert == "199") $startbuchdata['tatherr']    = $inhalt;
      if($wert == "200") $startbuchdata['tatdame']    = $inhalt;
      //$link = '<a href="../adm_program/modules/profile/profile.php?user_id=' . $benutzer_id . '">' . $benutzer_id . '</a>';
    }//end while($werte = mysql_fetch_array($daten))
  }//end while($temp = mysql_fetch_array($suchbegriff))
  //print_r($startbuchdata);echo' :DEBUG::startbuchdata:<br>';
  return $startbuchdata;
}//end getStartbuchData 

// Funktion Daten eines Vereins holen
// ----------------------------------  
function getVereinData($verein_nr,$uid)
{
  unset($vereindata);
  global $gPreferences;  

  $sqlab       = "SELECT * FROM adm_user_data WHERE usd_value = '" . $verein_nr . "'";
  $suchbegriff = mysqli_query(ADMIDIOdb(), $sqlab);
  
  while($temp = mysqli_fetch_array($suchbegriff)){
    // Daten zum Datensatz finden
    $sqlab = "SELECT * FROM adm_user_data WHERE usd_usr_id = '" . $uid . "' ";    
    $daten = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($werte = mysqli_fetch_array($daten)){
      //print_r($werte);echo' :DEBUG::werte:<br>';
      $wert   = $werte[2];
      $inhalt = $werte[3];                  
      if($wert == "28"  ) $vereindata['name']        = $inhalt;
      if($wert == "12"  ) $vereindata['mail']        = $inhalt;      
      if($wert == "49"  ) $vereindata['bndland']     = getBndLand($inhalt);
      if($wert == "53"  ) $vereindata['nummer']      = $inhalt;
      if($wert == "130" ) $vereindata['website']     = $inhalt;
      if($wert == "208" ) $vereindata['gsiegel_sts'] = $inhalt;
      if($wert == "209" ) $vereindata['gsiegel_dat'] = $inhalt;
      //$remind = false;
    }//end while($werte = mysql_fetch_array($daten))
  }//end while($temp = mysql_fetch_array($suchbegriff))
  //print_r($vereindata);echo' :DEBUG::vereindata:<br>';
  return $vereindata;
}//end getVereinData 

// Funktion Gütesiegel Vereine holen
// ----------------------------------  
function getGsiegelVereine()
{
  unset($gsiegelvereine);

  $sqlab       = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = 208 AND usd_value = 1";
  $vereine = mysqli_query(ADMIDIOdb(), $sqlab);
    
  while($row = mysqli_fetch_array($vereine)){    
    $sqlab = "SELECT * FROM adm_user_data WHERE usd_usr_id = '" . $row[0] . "' ";    
    $daten = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($werte = mysqli_fetch_array($daten)){
      //print_r($werte);echo' :DEBUG::werte:<br>';
      $wert   = $werte[2];
      $inhalt = $werte[3];                  
      if($wert == "28"  ) $vereindata['name']        = $inhalt;
      if($wert == "12"  ) $vereindata['mail']        = $inhalt;      
      if($wert == "49"  ) $vereindata['bndland']     = getBndLand($inhalt);
      if($wert == "53"  ) $vereindata['nummer']      = $inhalt;
      if($wert == "130" ) $vereindata['website']     = $inhalt;
      if($wert == "208" ) $vereindata['gsiegel_sts'] = $inhalt;
      if($wert == "209" ) $vereindata['gsiegel_dat'] = $inhalt;
      //$remind = false;
    }//end while($werte = mysql_fetch_array($daten))
    $gsiegelvereine[$row[0]] = $vereindata;
  }//end while($temp = mysql_fetch_array($vereine))
  //print_r($gsiegelvereine);echo' :DEBUG::gsiegelvereine:<br>';
  return $gsiegelvereine;
}//end getGsiegelVerein
  
function getGetanzteTurniere($StartbuchNr){
    
  // Aufstiegspunkte berechnen:
  // --------------------------  
  // Tanzpaar in TLP Datenbank suchen  
  $sqlab = 'SELECT turniernummer, startklasse, dame, herr, team, platz, punkte, rl_punkte 
            FROM paare WHERE 
            startbuch       = '.$StartbuchNr.' OR
            boogie_sb_herr  = '.$StartbuchNr.' OR 
            boogie_sb_dame  = '.$StartbuchNr; 
  $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
  $trnnummern      = array();
  
  while($aufstiegspunkte = mysqli_fetch_array($punkteDB)){
    //print_r($aufstiegspunkte);echo' :DEBUG::aufstiegspunkte<br>';
    $sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $aufstiegspunkte["turniernummer"]; 
    $turnier      = mysqli_query(DRBVdb(), $sqlab);
    $turnierdaten = mysqli_fetch_array($turnier);
    $trnnummern[] = $aufstiegspunkte["turniernummer"];
  }
  return $trnnummern;
}  
  
// Funktion Teilnehmer aus Turnier holen
// -------------------------------------  
function getTurnierteilnehmer($turniernummer,$full){  
  
  global $gPreferences;  

  // Turnier finden
  $sqlab   = 'SELECT dat_rol_id FROM adm_dates WHERE dat_turniernummer = "'.$turniernummer.'"';
  $turnier = mysqli_query(ADMIDIOdb(), $sqlab);

  while($row = mysqli_fetch_array($turnier)){    
    $turnier_rol_id = $row[0];
  }  
  
  if($full){
    $sqlab  = "SELECT mem_leader, usr_id, 
    row03id66.usd_value, row04id32.usd_value, row05id31.usd_value, row06id38.usd_value, row07id37.usd_value, row08id54.usd_value, 
    row09id29.usd_value, row10id157.usd_value, row11id28.usd_value, row12id53.usd_value, row13id49.usd_value, row14id188.usd_value, 
    row15id33.usd_value, row16id189.usd_value, row17id39.usd_value, row18id191.usd_value, row19id179.usd_value        
    FROM adm_roles, adm_categories, adm_members, adm_users 
    LEFT JOIN adm_user_data row03id66 ON row03id66.usd_usr_id = usr_id AND row03id66.usd_usf_id = 66 
    LEFT JOIN adm_user_data row04id32 ON row04id32.usd_usr_id = usr_id AND row04id32.usd_usf_id = 32 
    LEFT JOIN adm_user_data row05id31 ON row05id31.usd_usr_id = usr_id AND row05id31.usd_usf_id = 31 
    LEFT JOIN adm_user_data row06id38 ON row06id38.usd_usr_id = usr_id AND row06id38.usd_usf_id = 38 
    LEFT JOIN adm_user_data row07id37 ON row07id37.usd_usr_id = usr_id AND row07id37.usd_usf_id = 37     
    LEFT JOIN adm_user_data row08id54 ON row08id54.usd_usr_id = usr_id AND row08id54.usd_usf_id = 54
    LEFT JOIN adm_user_data row09id29 ON row09id29.usd_usr_id = usr_id AND row09id29 .usd_usf_id = 29
    LEFT JOIN adm_user_data row10id157 ON row10id157.usd_usr_id = usr_id AND row10id157.usd_usf_id = 157
    LEFT JOIN adm_user_data row11id28 ON row11id28.usd_usr_id = usr_id AND row11id28.usd_usf_id = 28
    LEFT JOIN adm_user_data row12id53 ON row12id53.usd_usr_id = usr_id AND row12id53.usd_usf_id = 53
    LEFT JOIN adm_user_data row13id49 ON row13id49.usd_usr_id = usr_id AND row13id49.usd_usf_id = 49
    LEFT JOIN adm_user_data row14id188 ON row14id188.usd_usr_id = usr_id AND row14id188.usd_usf_id = 188
    LEFT JOIN adm_user_data row15id33 ON row15id33.usd_usr_id = usr_id AND row15id33.usd_usf_id = 33
    LEFT JOIN adm_user_data row16id189 ON row16id189.usd_usr_id = usr_id AND row16id189.usd_usf_id = 189
    LEFT JOIN adm_user_data row17id39 ON row17id39.usd_usr_id = usr_id  AND row17id39.usd_usf_id = 39
    LEFT JOIN adm_user_data row18id191 ON row18id191.usd_usr_id = usr_id AND row18id191.usd_usf_id = 191
    LEFT JOIN adm_user_data row19id179 ON row19id179.usd_usr_id = usr_id AND row19id179.usd_usf_id = 179
    WHERE rol_id IN (".$turnier_rol_id.")         
    AND rol_cat_id = cat_id 
    AND ( cat_org_id = 1 OR cat_org_id IS NULL ) 
    AND mem_rol_id = rol_id 
    AND mem_begin <= '2018-08-29' 
    AND mem_end >= '2018-08-29' 
    AND mem_usr_id = usr_id 
    AND usr_valid = 1 
    AND mem_leader = 0";
  } else {  
    $sqlab  = "SELECT mem_leader, usr_id, 
    row3id66.usd_value, row4id32.usd_value, row5id31.usd_value, row6id38.usd_value, row7id37.usd_value 
    FROM adm_roles, adm_categories, adm_members, adm_users 
    LEFT JOIN adm_user_data row3id66 ON row3id66.usd_usr_id = usr_id AND row3id66.usd_usf_id = 66 
    LEFT JOIN adm_user_data row4id32 ON row4id32.usd_usr_id = usr_id AND row4id32.usd_usf_id = 32 
    LEFT JOIN adm_user_data row5id31 ON row5id31.usd_usr_id = usr_id AND row5id31.usd_usf_id = 31 
    LEFT JOIN adm_user_data row6id38 ON row6id38.usd_usr_id = usr_id AND row6id38.usd_usf_id = 38 
    LEFT JOIN adm_user_data row7id37 ON row7id37.usd_usr_id = usr_id AND row7id37.usd_usf_id = 37 
    WHERE rol_id IN (".$turnier_rol_id.") 
    AND rol_cat_id = cat_id 
    AND ( cat_org_id = 1 OR cat_org_id IS NULL ) 
    AND mem_rol_id = rol_id 
    AND mem_begin <= '2018-08-29' 
    AND mem_end >= '2018-08-29' 
    AND mem_usr_id = usr_id 
    AND usr_valid = 1 
    AND mem_leader = 0";
  }

  $result = mysqli_query(ADMIDIOdb(), $sqlab);
  while ($row = mysqli_fetch_array($result)){
    $resultlist[$row['usr_id']] = $row[2];
    $resultlist_stb[$row[2]]    = array('usr_id'=>$row['usr_id'],
                                        'vnherr'=>utf8_encode($row[3]),
                                        'nnherr'=>utf8_encode($row[4]),
                                        'vndame'=>utf8_encode($row[5]),
                                        'nndame'=>utf8_encode($row[6]),
                                        'teamname'=>utf8_encode($row[7]),
                                        'startklasse'=>getStartklasse($row[8]),
                                        'region'=>getRegion($row[9]),
                                        'vereinname'=>utf8_encode($row[10]),
                                        'vereinnr'=>$row[11],
                                        'bland'=>getBndLand($row[12]),
                                        'rfidhr'=>$row[13],
                                        'gebhr'=>$row[14],
                                        'rfidda'=>$row[15],
                                        'gebda'=>$row[16]);
  }
    
  if($full){  
    return $resultlist_stb;
  } else {
    return $resultlist;  
  }
}//end getTurnierteilnehmer

// Funktion Teilnehmer aus Turnier holen
// -------------------------------------  
function getTurnierInfoADM($turniernummer){  
  
  global $gPreferences;  

  // Turnier finden
  $sqlab   = 'SELECT dat_location,dat_headline,dat_begin FROM adm_dates WHERE dat_turniernummer = "'.$turniernummer.'"';
  $turnier = mysqli_query(ADMIDIOdb(), $sqlab);

  while($row = mysqli_fetch_array($turnier)){    
    $turnier_info = array('dat_location'=>utf8_encode($row[0]),'dat_headline'=>utf8_encode($row[1]),'dat_begin'=>$row[2],);
  }
  
  return $turnier_info;
}//end function getTurnierInfoADM  

// Funktion Turnierleiter holen
// ----------------------------
function getTurnierleiter(){  
  // Datum festlegen
  $datum = date("Y-m-d", (time() + 864000));
  
  $tleiter = array();
  $sqlab          = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 101 AND mem_end > '".$datum."'";    
  $turnier_leiter = mysqli_query(ADMIDIOdb(), $sqlab);
  //print_r($turnier_leiter);echo' :DEBUG::turnier_leiter<br>';
  
  while($temp=mysqli_fetch_array($turnier_leiter)){
    $tleiter_id = $temp[0];
    $sqlab      = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $tleiter_id";
    $ergebnis   = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($name=mysqli_fetch_array($ergebnis)){
      $temp_id   = $name[0];
      $temp_name = $name[1];
      if($temp_id == 1)   $n_name = $temp_name; //   1=Nachnahme
      if($temp_id == 2)   $v_name = $temp_name; //   2=Vorname
      if($temp_id == 53)  $verein = $temp_name; //  53=Vereinsnummer
      if($temp_id == 158) $tlflag = $temp_name; // 158=Turnierleiter Flag        
      if($temp_id == 170) $tl_liz = $temp_name; // 170=Turnierleiter Lizenz        
    }   
    //Rolle enthält alle aktiven und ehemaligen TL, nur die mit TL-Flag selektieren           
    if($tlflag==1) $tleiter[$tl_liz] = array('nname'=>utf8_encode($n_name),'vname'=>utf8_encode($v_name),'verein'=>$verein);     
  }
  // sortieren nach nachname
  foreach ($tleiter as $key => $row) {
    $nname[$key] = $row['nname'];
  } 
  array_multisort (array_column($tleiter, 'nname'), SORT_ASC, $tleiter);
  //print_r($tleiter);echo' :DEBUG::tleiter'.count($tleiter).'<br>';
  
  return $tleiter;
}  

// Funktion Wertungsrichter holen
// ------------------------------
function getWertungsrichter(){  
  // Datum festlegen
  $datum = date("Y-m-d", (time() + 864000));
  
  $wrichter = array();
  $sqlab            = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 124 AND mem_end > '".$datum."'";    
  $wertungs_richter = mysqli_query(ADMIDIOdb(), $sqlab);
  
  while($temp=mysqli_fetch_array($wertungs_richter)){
    $wrichter_id = $temp[0];
    $sqlab       = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $wrichter_id";
    $ergebnis    = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($name=mysqli_fetch_array($ergebnis)){
      $temp_id   = $name[0];
      $temp_name = $name[1];
      if($temp_id == 1)   $n_name   = $temp_name; //   1=Nachnahme
      if($temp_id == 2)   $v_name   = $temp_name; //   2=Vorname
      if($temp_id == 53)  $verein   = $temp_name; //  53=Vereinsnummer
      if($temp_id == 160) $wrrrflag = $temp_name; // 160=Wertungsrichter RR Flag        
      if($temp_id == 162) $wrbwflag = $temp_name; // 162=Wertungsrichter BW Flag        
      if($temp_id == 171) $wrrr_liz = $temp_name; // 171=Wertungsrichter RR Lizenz        
      if($temp_id == 172) $wrbw_liz = $temp_name; // 172=Wertungsrichter BW Lizenz        
    }   
    //Rolle enthält alle aktiven und ehemaligen WR, nur die mit WR-Flag selektieren           
    if($wrrrflag==1) $wrichter[$wrrr_liz] = array('nname'=>utf8_encode($n_name),'vname'=>utf8_encode($v_name),'verein'=>$verein);     
    if($wrbwflag==1) $wrichter[$wrbw_liz] = array('nname'=>utf8_encode($n_name),'vname'=>utf8_encode($v_name),'verein'=>$verein);     
  }
  // sortieren nach nachname
  foreach ($wrichter as $key => $row) {
    $nname[$key] = $row['nname'];
  } 
  array_multisort (array_column($wrichter, 'nname'), SORT_ASC, $wrichter);
  //print_r($wrichter);echo' :DEBUG::wrichter'.count($wrichter).'<br>';
  
  return $wrichter;
}  

// Funktion Trainer/Kursleiter holen
// ---------------------------------
function getTrainer(){  
  // Datum festlegen
  $datum = date("Y-m-d", (time() + 864000));
  
  $trainer = array();
  //rol-id 420 KursleiterRR
  //rol-id 421 KursleiterBW
  //rol-id  11 TrainerA RR
  //rol-id 347 TrainerB RR
  //rol-id 366 TrainerB BW
  //rol-id 367 TrainerC RR
  //rol-id 369 TrainerC/BRE RR
  //rol-id 368 TrainerC BW
  //rol-id 370 TrainerC/BRE BW  
  $sqlab           = "SELECT mem_usr_id FROM adm_members 
                      WHERE mem_rol_id in(420,421,11,347,366,367,369,368,370) 
                      AND mem_end > '".$datum."'";    
  $trainer_kleiter = mysqli_query(ADMIDIOdb(), $sqlab);
  
  while($temp=mysqli_fetch_array($trainer_kleiter)){
    $trainer_id = $temp[0];
    $sqlab       = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $trainer_id";
    $ergebnis    = mysqli_query(ADMIDIOdb(), $sqlab);    
    
    while($name=mysqli_fetch_array($ergebnis)){
      $temp_id   = $name[0];
      $temp_name = $name[1];
      if($temp_id == 1)   $n_name   = $temp_name; //   1=Nachnahme
      if($temp_id == 2)   $v_name   = $temp_name; //   2=Vorname
      if($temp_id == 53)  $verein   = $temp_name; //  53=Vereinsnummer
    }   
    //Rolle enthält alle aktiven Trainer und Kursleiter
    $trainer[] = array('nname'=>utf8_encode($n_name),'vname'=>utf8_encode($v_name),'verein'=>$verein);     
  }
  // sortieren nach nachname
  foreach ($trainer as $key => $row) {
    $nname[$key] = $row['nname'];
  } 
  array_multisort (array_column($trainer, 'nname'), SORT_ASC, $trainer);
  //print_r($trainer);echo' :DEBUG::trainer'.count($trainer).'<br>';
  
  return $trainer;
}  
 
// Funktion ausgerichtete Turniere holen
// -------------------------------------
function getEigeneTurniere($vereinsnummer){
  unset($turnierdata);

  $sqlab       = "SELECT dat_turniernummer,dat_tform,dat_begin,dat_headline 
                  FROM adm_dates WHERE dat_vereinsnummer = '".$vereinsnummer."'";
  $suchbegriff = mysqli_query(ADMIDIOdb(), $sqlab);

  while($row = mysqli_fetch_array($suchbegriff)){
    //print_r($row);echo' :DEBUG::$row:<br>';
    $turnierdata[$row['dat_turniernummer']] = array('dat_tform'   =>$row['dat_tform'],
                                                    'dat_begin'   =>$row['dat_begin'],
                                                    'dat_headline'=>$row['dat_headline']);
  }    
  //print_r($turnierdata);echo' :DEBUG::turnierdata:<br>';
  return $turnierdata;  
} 

// CSV Datei 2 Array
// -------------------------------------
function csv_to_array($filename='', $delimiter=';'){
  if(!file_exists($filename) || !is_readable($filename)) return FALSE;
  
  $header = NULL;
  $data = array();
  if (($handle = fopen($filename, 'r')) !== FALSE) {
     while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
       if(!$header){
         $header = $row;
       } else {
         $data[] = array_combine($header, $row);
       }
     }//end while
     fclose($handle);
  }
  return $data;
}//end function csv_to_array()
 
// Profiling/Debugging
// -------------------------------------
function prof($str)
{
    global $prof_timing, $prof_names;
    $prof_timing[] = microtime(true);
    $prof_names[] = $str;
}

//Call this when you're done and want to see the results
function prof_print()
{
    global $prof_timing, $prof_names;
    $size = count($prof_timing);
    for($i=0;$i<$size - 1; $i++)
    {
        echo "<b>{$prof_names[$i]}</b><br>";
        echo sprintf("&nbsp;&nbsp;&nbsp;%f<br>", $prof_timing[$i+1]-$prof_timing[$i]);
    }
    echo "<b>{$prof_names[$size-1]}</b><br>";
}  
  
?>