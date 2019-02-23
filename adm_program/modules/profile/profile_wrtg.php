<?php
/******************************************************************************
 * Profil mit Wertung anzeigen
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
require_once('../../system/login_valid.php');
require_once('../../system/classes/table_roles.php');
require_once('roles_functions.php');

$pkt_datum = "";
$getMode   = "";
$shareRSLT = "";
  
// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getTnrSel = admFuncVariableIsValid($_GET, 'tnrsel', 'numeric', 0);  
$getMode   = admFuncVariableIsValid($_GET, 'mode');  
$shareRSLT = admFuncVariableIsValid($_GET, 'share');  
  
// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
  
unset($_SESSION['profile_request']);
unset($_SESSION['profile_share']);
unset($_SESSION['profile_usrid']);
unset($_SESSION['profile_tnr']);
unset($_SESSION['profile_vmail']);
unset($_SESSION['profile_vname']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);
  
// Funktion Rundenergebnis als Tabellenzeile
// -----------------------------------------
function getRundenErgebnis($runde_name){  
  global $gCurrentUser, $runde, $rd_erg, $wert_richter, $twrnrfix, $paarinfo, $gemeldete_akros, $datum, $datumDM, $datumDMF, $datum12P, $startklasse_mit_akro, $wrtg_bw;
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
     $wrtg_bw = TRUE; 
  }
  if($paarinfo["startklasse"] == "F_RR_GF" || $paarinfo["startklasse"] == "F_RR_LF" ||
     $paarinfo["startklasse"] == "F_RR_ST" || $paarinfo["startklasse"] == "F_RR_J" || 
     $paarinfo["startklasse"] == "F_RR_M"  || $paarinfo["startklasse"] == "F_BW_M"){
     $wrtg_fo = TRUE;
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
      $wertungs_ar4akro = array();
      for($i = 0;$i < 8;$i++){
        $wertungs_ar4akro[] = $wertungs_ar[11+3*$i];
      }
      //if(array_sum(array_slice($wertungs_ar,11,34)) != 0){        
      if(array_sum($wertungs_ar4akro) != 0){        
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
        //Vorrunden/Zwischenrunden/Hoffnungsrunden
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
        if($runde_name == "Endrunde Akrobatik" || $paarinfo["startklasse"] == "F_RR_M" || ($runde_name == "Semifinale" && ($paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "RR_B"))){
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
  if($gCurrentUser->isWebmaster()){  
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
  }           
  return array($tanzpkte_ar, $tanzproz_ar,$akropkte_ar, $akroproz_ar, $t_abzg_text, $t_abzg_pkte, $a_abzg_text, $a_abzg_pkte, $twrnr, $awrnr, $awrname, $twrname);
} //End function getRundenErgebnis 

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
if($getMode == 'printview'){
  $gLayout['header'] .= '<link rel="stylesheet" type="text/css" href="https://drbv.de/adm/adm_themes/classic/css/print.css" />';         
}
  
require(SERVER_PATH. '/adm_program/system/overall_header.php');

// Alle Rollen auswerten, um festzustellen, welche Bereiche ausgegeben werden sollen
// 2014-01-27 - Philipp Loepmeier - philipp@rockcal.de
$role   = new TableRoles($gDb);
$count_show_roles = 0;
$result_role = getRolesFromDatabase($user->getValue('usr_id'));
while($row = $gDb->fetch_array($result_role)) {
    $role->clear();
    $role->setArray($row);
    $role_name = $role->getValue('rol_name');
//    echo"Rollenname = $role_name<br>";  
    if( $role_name == 'Startbuch Formation' ) {
        $isStartbuchFormation = true;
        $isPerson = false;        
        $formular = 1;        
    }
    if( $role_name == 'Startbuch RR-S' ) {
        $class_name = 'Startbuch RR-S';
        $isStartbuchRR = true;
        $isPerson = false;        
        $formular = 1;                 
    }
    if( $role_name == 'Startbuch RR-J' ) {
        $class_name = 'Startbuch RR-J';
        $isStartbuchRR = true;
        $isPerson = false;        
        $formular = 1;                
    }
    if( $role_name == 'Startbuch RR-C' ) {
        $class_name = 'Startbuch RR-C';
        $isStartbuchRR = true;   
        $isPerson = false;        
        $formular = 1;              
    }
    if( $role_name == 'Startbuch RR-C-Int' ) {
        $class_name = 'Startbuch RR-C-Int';
        $isStartbuchRR = true;   
        $isPerson = false;        
        $formular = 1;              
    }
    if( $role_name == 'Startbuch RR-B' ) {
        $class_name = 'Startbuch RR-B';
        $isStartbuchRR = true;
        $isPerson = false;        
        $formular = 1;                 
    }
    if( $role_name == 'Startbuch RR-A' ) {
        $class_name = 'Startbuch RR-A';
        $isStartbuchRR = true; 
        $isPerson = false;        
        $formular = 1;                
    }
    if( $role_name == 'Startbuch Formation' ) {
        $class_name = 'Startbuch Formation';
        $isPerson = false;        
    }
    if( $role_name == 'Startbuch Formation Master RR' ) {
        $class_name = 'Startbuch Formation Master RR';
        $isPerson = false;        
        $formular = 1;         
    }
    if( $role_name == 'Startbuch BW' ) {
        $class_name = 'Startbuch BW';
        $isStartbuchBW = true;
        $isPerson = false;        
        $formular = 1;                
    }                         
}

if($isStartbuchRR){  
  $tanzhead_ar = array("Herr Grundtechnik","Herr Haltungs&Drehtechnik","Dame Grundtechnik","Dame Haltungs&Drehtechnik","Choreografie","Tanzfiguren","Tänzerische Darbietung");
} elseif($isStartbuchFormation) {
  $tanzhead_ar = array("Technik","Tanz","Tanzfiguren","","Bilder","Bildwechsel","F-Figuren/Effekte");
} else {
  $tanzhead_ar = array("Grundschritt","Basic Dancing","Tanzfiguren", "", "Interpretation","Spontane Interpretation","Dance Performance");
}            

$inserthtml   = "";

$sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = '.$getTnrSel;
$turniere     = mysqli_query(DRBVdb(), $sqlab);
$turnierdaten = mysqli_fetch_array($turniere); 
$datum        = new DateTime($turnierdaten["datum"]);
$datumDM      = new DateTime("2016-06-18");//ab hier wurden die Akroteiler B/A angepasst    
$datumDMF     = new DateTime("2016-10-29");//ab hier wurde die Anzahl Aktive bei Showteam korrigiert    
$datum12P     = new DateTime("2018-04-17");//ab hier wurden alle Teiler auf die 12 Punkte Akroregelung angepasst
  
// Wertungsrichter einlesen
$sqlab = 'SELECT * FROM wertungsrichter WHERE turniernummer = '.$getTnrSel;
$temp = mysqli_query(DRBVdb(), $sqlab);

while($wr = mysqli_fetch_array($temp)){
  $i = $wr[1];
  $wert_richter[$i] = array($wr[1],$wr[4]);     
}
  
// Tanzrunden einlesen
$sqlab = 'SELECT * FROM rundentab WHERE turniernummer = '.$getTnrSel;
$temp  = mysqli_query(DRBVdb(), $sqlab);
unset($i);

while($t_runden = mysqli_fetch_array($temp)){
  $i = $i + 1;
  $t_runde[$i] = $t_runden;  
  //echo $t_runde[$i][1] . ' ' . $t_runde[$i][3] . ' ' . $t_runde[$i][4] ;echo"<br>";
}      
      
// Tanzpaar in Datenbank suchen  
$sqlab          = 'SELECT paar_id_tlp,startklasse, dame, herr, team, platz, punkte, rl_punkte, RT_ID_Ausgeschieden, anzahl_taenzer FROM paare 
                   WHERE Turniernummer = '.$getTnrSel.' AND 
                   (startbuch = '.$user->getValue('LAST_NAME').' OR 
                    boogie_sb_herr = '.$user->getValue('LAST_NAME').' OR 
                    boogie_sb_dame = '.$user->getValue('LAST_NAME').')'; 
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

// Tanzpaarpunkteergebnis in Datenbank suchen fuer Endrunde
$sqlab          = 'SELECT * FROM majoritaet WHERE turniernummer = '.$getTnrSel.' AND TP_ID = ' . $paar_id_paare["paar_id_tlp"]  . ' AND RT_ID = ' . $paar_id_paare["RT_ID_Ausgeschieden"];
$ergebnisDB_pkt =  mysqli_query(DRBVdb(), $sqlab);

while($rundenDB_pkt = mysqli_fetch_array($ergebnisDB_pkt)){
  //print_r($rundenDB_pkt);echo' rundenDB_pkt<br>';
  if($rundenDB_pkt[WR1] == 0 && $paarinfo["team"] == ''){
    //Fusstechnikrundenergebnis
    $ergDB_pkt_ft = $rundenDB_pkt[WR2];
    $ergDB_pkt_ta = 0;    
    $ergDB_pkt_ak = 0;        
  } else {
    //Akrobatikrundenergebnis
    $ergDB_pkt_ft = $rundenDB_pkt[WR1];
    $ergDB_pkt_ta = $rundenDB_pkt[WR2];    
    $ergDB_pkt_ak = $rundenDB_pkt[WR3];    
  }
  $ergDB_pkt_bl = $rundenDB_pkt[WR1];
  $ergDB_pkt_bs = $rundenDB_pkt[WR5] + $rundenDB_pkt[WR4];    
  $ergDB_pkt_su = $rundenDB_pkt[WR7];    
}
//if($gCurrentUser->isWebmaster()){
//  print_r($ergDB_pkt_ft);echo' ergDB_pkt_ft<br>';
//  print_r($ergDB_pkt_ta);echo' ergDB_pkt_ta<br>';
//  print_r($ergDB_pkt_ak);echo' ergDB_pkt_ak<br>';
//  print_r($ergDB_pkt_bl);echo' ergDB_pkt_bl<br>';
//  print_r($ergDB_pkt_bs);echo' ergDB_pkt_bs<br>';
//}

// Tanzpaarpunkteergebnis in Datenbank suchen fuer alle Runden ausser ER
$sqlab          = 'SELECT * FROM majoritaet WHERE turniernummer = '.$getTnrSel.' AND TP_ID = ' . $paar_id_paare["paar_id_tlp"]  . ' AND NOT RT_ID = ' . $paar_id_paare["RT_ID_Ausgeschieden"];
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
//if($gCurrentUser->isWebmaster()){
//  print_r($ergDB_pkt_ft_a);echo' ergDB_pkt_ft_a<br>';    
//  print_r($ergDB_pkt_ta_a);echo' ergDB_pkt_ta_a<br>';    
//  print_r($ergDB_pkt_ak_a);echo' ergDB_pkt_ak_a<br>';    
//  print_r($ergDB_pkt_bl_a);echo' ergDB_pkt_bl_a<br>';    
//  print_r($ergDB_pkt_bs_a);echo' ergDB_pkt_bs_a<br>';    
//}

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
  $sqlab           = 'SELECT * FROM paare WHERE turniernummer = '.$getTnrSel.' AND  paar_id_tlp = '.$paarinfo["paar_id"];
  $gemeldet        = mysqli_query(DRBVdb(), $sqlab);
  $gemeldete_akros = mysqli_fetch_array($gemeldet);
    
  // Vorrunde
  $max_pkt          = $max_pkt_vr_a[$paarinfo["startklasse"]];
  $insert_akrovr    = '';
  $insert_akrovrt   = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akrovr   .= '<ul> Vorrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akrovr   .= '<ul><a class="admLink" href="#akrovr" id="fadeakrovr"><img src="'.THEME_PATH. '/icons/info.png" alt="AkroVR" />
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
    $max_pkt        = '70';  
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
  $insert_akrovr   .= '<b>Akrobatischer Vorwert = <i>'.number_format(round($vorwertvr,2),2).' Pkt. (max. '.$max_pkt.') </i></b>';
  $insert_akrovr   .= '</div></ul>';

  // Zwischenrunde  
  $max_pkt          = $max_pkt_vr_a[$paarinfo["startklasse"]];  
  $insert_akrozr    = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akrozr   .= '<ul> Zwischenrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akrozr   .= '<ul><a class="admLink" href="#akrozr" id="fadeakrozr"><img src="'.THEME_PATH. '/icons/info.png" alt="AkroVR" />
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
    $max_pkt        = '70';  
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
  $insert_akrozr   .= '<b>Akrobatischer Vorwert = <i>'.number_format(round($vorwertzr,2),2).' Pkt. (max. '.$max_pkt.')</i></b>';
  $insert_akrozr   .= '</div></ul>';
  
  // Endrunde    
  $max_pkt          = $max_pkt_er_a[$paarinfo["startklasse"]];
  $insert_akroer    = '';
  if($getMode == 'printview' || $shareRSLT == 1){
    $insert_akroer   .= '<ul> Endrunde<div style="font-size: 12px;padding-left:40px;">';
  } else {
    $insert_akroer   .= '<ul><a class="admLink" href="#akroer" id="fadeakroer"><img src="'.THEME_PATH. '/icons/info.png" alt="AkroVR" />
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
    $max_pkt        = '70';
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
  $insert_akroer   .= '<b>Akrobatischer Vorwert = <i>'.number_format(round($vorwerter,2),2).' / Pkt. (max. '.$max_pkt.')</i></b>';
  $insert_akroer   .= '</div></ul>';
}
  
// Wertungen für Tanzpaar in Datenbank suchen
//$sqlab       = 'SELECT * FROM wertungen w JOIN majoritaet m
//                ON w.rund_tab_id = m.RT_ID 
//                WHERE w.turniernummer = '.$getTnrSel. ' AND w.paar_id_tlp = '.$paarinfo["paar_id"].' ORDER BY w.rund_tab_id, w.wert_id'; 
$sqlab       = 'SELECT * FROM wertungen WHERE turniernummer = '.$getTnrSel. ' AND paar_id_tlp = '.$paarinfo["paar_id"].' ORDER BY rund_tab_id, wert_id'; 
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

//hier A20 aus majoritaet holen
$sqlab      = 'SELECT m.RT_ID, m.PA_ID, m.Anmerkung, r.runde, r.rt_id_tlp FROM majoritaet m JOIN rundentab r
                ON m.RT_ID = r.rt_id_tlp
                WHERE m.turniernummer = '.$getTnrSel. ' AND m.TP_ID = '.$paarinfo["paar_id"].'';   
$a20        = mysqli_query(DRBVdb(), $sqlab);
while($loop = mysqli_fetch_array($a20)){
  //print_r($loop);echo"<br>";
  $a20_wrt[$loop[RT_ID]]     = $loop[PA_ID];
  $a20_txt[$loop[RT_ID]]     = $loop[Anmerkung];
  $a20_rnd[$loop[rt_id_tlp]] = $loop[runde];
}
//print_r($a20_wrt);echo" :a20_wrt<br>";  
//print_r($a20_txt);echo" :a20_txt<br>";  
//print_r($a20_rnd);echo" :a20_rnd<br>";  
$a20_runde = array_combine($a20_rnd, $a20_wrt);
$a20_text  = array_combine($a20_rnd, $a20_txt);
//print_r($a20_runde);echo" :a20_runde<br>";  
//print_r($a20_text);echo" :a20_text<br>";  

while($wertungen = mysqli_fetch_array($wertung)){
  //print_r($wertungen);echo" :wertungen<br>";
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
 
  // Für alle Runden Table Rows bestimmen
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
    $t_abzg_info_a20 = $a20_text[$t_runde[$tr][4]];
    $t_abzg_pkte_a20 = 20 * $a20_runde[$t_runde[$tr][4]];
    $t_abzg_text_a20 = '';
    for($y = 0;$y<$a20_runde[$t_runde[$tr][4]];$y++){
      $t_abzg_text_a20 .= 'A20 '; 
    }
    //print_r($t_abzg_info_a20);echo" t_abzg_info_a20<br>";
    //print_r($t_abzg_text_a20);echo" t_abzg_text_a20<br>";
    //print_r($t_abzg_pkte_a20);echo" t_abzg_pkte_a20<br>";
      
    if($isStartbuchBW && ($runde_name == "Vorrunde" || $runde_name == "Endrunde" || $runde_name == "Hoffnungsrunde" || 
       $runde_name == "Langsame Vorrunde" || $runde_name == "Schnelle Vorrunde" ||
       $runde_name == "Langsame Endrunde" || $runde_name == "Schnelle Endrunde")){
      //   
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
                 if($gCurrentUser->isWebmaster() && $twrname[$i] != ''){
                   $html_vrbw .= '
                     <td colspan=2 align=center><b><span data-tooltip="'.$twrname[$i].'" data-tooltip-position="top">WR'.($i+1).'</b></span></td>';
                 
                 } else {
                   $html_vrbw .= '
                     <td colspan=2 align=center><b>WR'.($i+1).'</b></td>';                 
                 }
               }
               $html_vrbw .= '
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>';
               for($i = 0; $i < $twrnr; $i++){
                 $html_vrbw .= '         
                   <td align=center>%</td>
                   <td align=center class="cellcol1">Pkt.</td>';
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
               <td align=right><b>&sum; Tanz&nbsp;</b></td>
               <td align=center colspan='.(2*$twrnr).'><b>'.number_format($tanzpkte_sum,2).'</b></td>
            </tr>';
         $html_vrbw .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>&sum; Abz&uuml;ge&nbsp;</b></td>
               <td align=center colspan='.(2*$twrnr).'><b>'.$t_abzg_text_a20.'</b></td>
             </tr>';
         if($gCurrentUser->isWebmaster()){
           if($runde_name == "Langsame Vorrunde"){
             $compare_html = ' ('.$ergDB_pkt_bl_a[1].')';
           } elseif ($runde_name == "Schnelle Vorrunde"){
             $compare_html = ' ('.number_format(($ergDB_pkt_bs_a[1]/1.1),2).')';
             $compare_html_sum = ' ('.($ergDB_pkt_bs_a[0] + $ergDB_pkt_bs_a[1]).')';
           } elseif ($runde_name == "Langsame Endrunde"){
             $compare_html = ' ('.$ergDB_pkt_bl.')';
           } elseif ($runde_name == "Schnelle Endrunde"){
             $compare_html     = ' ('.number_format(($ergDB_pkt_bs/1.1),2).')';
             $compare_html_sum = ' ('.($ergDB_pkt_su).')';
           } else {
             $compare_html = ' ('.$ergDB_pkt_bs_a[1].')';//Hoffnungsrunde?
           }
         } else {
           $compare_html     = '';
           $compare_html_sum = '';
         }
         $html_vrbw .= '<tr style="background-color: #BCF5A9;">';
         if($tanzpkte_sum-$t_abzg_pkte_a20 > 0){
           $html_vrbw .= '<td align=center colspan='.(1+2*$twrnr).'><b>Ergebnis = '.number_format($tanzpkte_sum-$t_abzg_pkte_a20,2).' Pkt.'.$compare_html.'</b></td>';
         } else {
           $html_vrbw .= '<td align=center colspan='.(1+2*$twrnr).'><b>Ergebnis = 0,00 Pkt.'.$compare_html.'</b></td>';         
         }  
         $html_vrbw .= '</tr>';
         if($runde_name == "Schnelle Vorrunde"){
           $tanzpkte_sum_lasn = $tanzpkte_sum_lv + 1.1*$tanzpkte_sum;
           $html_vrbw .= '
           <tr style="background-color: #BCF5A9;">
             <td align=center colspan='.(1+2*$twrnr).'><b>Endergebnis Langsam(1.0) + Schnell(1.1) = '.number_format($tanzpkte_sum_lasn,2).' Pkt.'.$compare_html_sum.'</b></td>
           </tr>';         
         }
         if($runde_name == "Schnelle Endrunde"){
           $tanzpkte_sum_lasn = $tanzpkte_sum_la + 1.1*$tanzpkte_sum;
           $html_vrbw .= '
           <tr style="background-color: #BCF5A9;">
             <td align=center colspan='.(1+2*$twrnr).'><b>Endergebnis Langsam(1.0) + Schnell(1.1) = '.number_format($tanzpkte_sum_lasn,2).' Pkt.'.$compare_html_sum.'</b></td>
           </tr>';         
         }
         $html_vrbw .= '
          </table>';
         
      if($runde_name == "Langsame Vorrunde"){                 
        $html_lavor         = $html_vrbw;                                  
        $tanzpkte_sum_lv    = $tanzpkte_sum;
        $tanzpkte_sum_ar_lv = $tanzpkte_sum_ar;
        $erg_pkt_su_lv      = $tanzpkte_sum;
        $abz_pkt_a20_lv     = $t_abzg_pkte_a20;
        $teilnahme_lavr     = TRUE;
      } elseif($runde_name == "Schnelle Vorrunde"){
        $html_snvor         = $html_vrbw;                                  
        $tanzpkte_sum_sv    = $tanzpkte_sum;
        $tanzpkte_sum_ar_sv = $tanzpkte_sum_ar;
        $erg_pkt_su_sv      = $tanzpkte_sum;
        $abz_pkt_a20_sv     = $t_abzg_pkte_a20;
        $teilnahme_snvr     = TRUE;
      } elseif($runde_name == "Vorrunde"){
        $html_vor           = $html_vrbw;                                  
        $tanzpkte_sum_vo    = $tanzpkte_sum;
        $tanzpkte_sum_ar_vo = $tanzpkte_sum_ar;
        $erg_pkt_su_vo      = $tanzpkte_sum;
        $abz_pkt_a20_vo     = $t_abzg_pkte_a20;
        $teilnahme_vor      = TRUE;        
      } elseif($runde_name == "Hoffnungsrunde"){
        $html_hoff          = $html_vrbw;                                  
        $tanzpkte_sum_ho    = $tanzpkte_sum;
        $tanzpkte_sum_ar_ho = $tanzpkte_sum_ar;
        $erg_pkt_su_ho      = $tanzpkte_sum;
        $abz_pkt_a20_ho     = $t_abzg_pkte_a20;
        $teilnahme_hoff     = TRUE;        
      } elseif($runde_name == "Langsame Endrunde"){                 
        $html_laend         = $html_vrbw;                                  
        $tanzpkte_sum_la    = $tanzpkte_sum;
        $tanzpkte_sum_ar_la = $tanzpkte_sum_ar;
        $erg_pkt_su_la      = $tanzpkte_sum;
        $abz_pkt_a20_la     = $t_abzg_pkte_a20;
        $teilnahme_laer     = TRUE;
      } elseif($runde_name == "Schnelle Endrunde"){
        $html_snend         = $html_vrbw;                                  
        $tanzpkte_sum_sn    = $tanzpkte_sum;
        $tanzpkte_sum_ar_sn = $tanzpkte_sum_ar;
        $erg_pkt_su_sn      = $tanzpkte_sum;
        $abz_pkt_a20_sn     = $t_abzg_pkte_a20;
        $teilnahme_sner     = TRUE;
      } elseif($runde_name == "Endrunde"){
        $html_end           = $html_vrbw;                                  
        $tanzpkte_sum_er    = $tanzpkte_sum;
        $tanzpkte_sum_ar_er = $tanzpkte_sum_ar;
        $erg_pkt_su_er      = $tanzpkte_sum;
        $abz_pkt_a20_er     = $t_abzg_pkte_a20;
        $teilnahme_end      = TRUE;
      }                     
    }//end "Vorrunde/Endrunde/Langsam/Schnell Boogie-Woogie"
    
    if($runde_name == "Endrunde Fußtechnik"){
      $html_ft  = '';
      $html_ft .= '
          <table class="prfl_wrtg_rgbns">
            <tr style="background-color: orange;">
               <td align=center><b>Endrunde Fußtechnik</b></td>';        
               for($i = 0; $i < 4; $i++){
                if($gCurrentUser->isWebmaster() && $twrname[$i] != ''){
                  $html_ft .= '               
                    <td colspan=2 align=center><b><span data-tooltip="'.$twrname[$i].'" data-tooltip-position="top">TWR'.($i+1).'</span></b></td>';
                } else {
                  $html_ft .= '               
                    <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
                }
              }        
          $html_ft .= '        
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center>&nbsp;</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
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
               <td align=right><b>&sum; Tanz&nbsp;</b></td>
               <td align=center colspan=8><b>'.number_format($tanzpkte_sum,2).'</b></td>
            </tr>';
         $html_ft .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>&sum; Abz&uuml;ge&nbsp;</b></td>';
         if($t_abzg_pkte[0] != 0){
           $html_ft .= '<td align=center colspan=8><b>'.$t_abzg_pkte[0].'</b>&nbsp;('.$t_abzg_text[0].' '.$t_abzg_text_a20.')</td></tr>';         
         } else {
           $html_ft .= '<td align=center colspan=8><b>&nbsp;'.$t_abzg_text_a20.'</b></td></tr>';         
         }
         if($gCurrentUser->isWebmaster()){
           $compare_html = ' ('.$ergDB_pkt_ft.')';
         } else {
           $compare_html = '';
         }
         $html_ft .= '<tr style="background-color: #BCF5A9;">';
         if($tanzpkte_sum <= $t_abzg_pkte[0] + $t_abzg_pkte_a20){
           $html_ft .= '<td align=center colspan=9><b>Ergebnis = 0,00 Pkt.'.$compare_html.'</b></td>';                   
         } else {
           $html_ft .= '<td align=center colspan=9><b>Ergebnis = '.number_format($tanzpkte_sum-$t_abzg_pkte[0]-$t_abzg_pkte_a20,2).' Pkt.'.$compare_html.'</b></td>';                            
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
          <table class="prfl_wrtg_rgbns">
            <tr style="background-color: orange;">';
            if($runde_name == "Endrunde Akrobatik"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Endrunde Akrobatik</b></td>';
            } elseif($runde_name == "Endrunde"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Endrunde</b></td>';
            } elseif($runde_name == "Vorrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_vr;
              $html_ak .= '<td align=center><b>Vorrunde</b></td>';
            } elseif($runde_name == "Hoffnungsrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_vr;
              $html_ak .= '<td align=center><b>Hoffnungsrunde</b></td>';
            } elseif($runde_name == "KO-Runde"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>KO-Runde</b></td>';
            } elseif($runde_name == "Semifinale"){
              $gem_ak_offset = 48;
              $akr_tlr_fm    = $akr_tlr_fm_er;
              $html_ak .= '<td align=center><b>Semifinale</b></td>';
            } elseif($runde_name == "1. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>1. Zwischenrunde</b></td>';
            } elseif($runde_name == "2. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>2. Zwischenrunde</b></td>';
            } elseif($runde_name == "3. Zwischenrunde"){
              $gem_ak_offset = 16;
              $akr_tlr_fm    = $akr_tlr_fm_zr;
              $html_ak .= '<td align=center><b>3. Zwischenrunde</b></td>';
            }
            for($i = 0; $i < 4; $i++){
              if($gCurrentUser->isWebmaster() && $twrname[$i] != ''){
                $html_ak .= '               
                  <td colspan=2 align=center><b><span data-tooltip="'.$twrname[$i].'" data-tooltip-position="top">TWR'.($i+1).'</span></b></td>';
              } else {
                $html_ak .= '               
                  <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
              }
            }
            if($isStartbuchFormation && !$startklasse_mit_akro && $twrnr >= 5){
              for($i = 4; $i < 6; $i++){
                if($gCurrentUser->isWebmaster() && $twrname[$i] != ''){
                  $html_ak .= '
                     <td colspan=2 align=center><b><span data-tooltip="'.$twrname[$i].'" data-tooltip-position="top">TWR'.($i+1).'</span></b></td>';
                } else {
                  $html_ak .= '
                     <td colspan=2 align=center><b>TWR'.($i+1).'</b></td>';
                }
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
               if($akr_tlr_fm == 7){ 
                 $html_ak .= '<td colspan=2 align=center><b>AK7</b></td>';
               }
               if($akr_tlr_fm == 8){
                 $html_ak .= '<td colspan=2 align=center><b>AK7</b></td>';
                 $html_ak .= '<td colspan=2 align=center><b>AK8</b></td>';              
               }
               $html_ak .= '
               <td align=center><b>&sum; Akrobatik</b></td>';}
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
            if($isStartbuchFormation && !$startklasse_mit_akro && $twrnr >= 5){
            $html_ak .= '
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>
               <td align=center>&nbsp;</td>';}                           
            if($startklasse_mit_akro){
            $html_ak .= '        
               <td align=center><b>Akrobatik<br>Vorwert</b></td>               
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
               } elseif(($datum > $datumDM && $runde_name == "Endrunde Akrobatik") || $paarinfo["startklasse"] == "F_RR_M" || ($runde_name == "Semifinale" && ($paarinfo["startklasse"] == "RR_A" || $paarinfo["startklasse"] == "RR_B"))) {
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
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+11]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+12],2).'</span></b></td>               
                       <td align=center>&nbsp;</td>
                       <td align=center><b><span data-tooltip="'.utf8_encode($gemeldete_akros[$gem_ak_offset+13]).'" data-tooltip-position="top">'.number_format($gemeldete_akros[$gem_ak_offset+14],2).'</span></b></td>';               
               }

               if($runde_name == "Endrunde Akrobatik" && $paarinfo["startklasse"] != "RR_S"){
                 $html_ak .= '<td align=center><b>'.round($vorwerter,2).'</b></td></tr>';
               } elseif(($runde_name == "Endrunde" || $runde_name == "KO-Runde" || $runde_name == "Semifinale") && $paarinfo["startklasse"] != "RR_S"){
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
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>';
               if($isStartbuchFormation && !$startklasse_mit_akro && $twrnr >= 5){
               $html_ak .= ' 
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>';               
               }
               if($startklasse_mit_akro){
               $html_ak .= '        
               <td align=center>&nbsp;</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>
               <td align=center>%</td>
               <td align=center class="cellcol1">Pkt.</td>';
               if($akr_tlr_fm == 7){  
                 $html_ak .= '
                 <td align=center>%</td>
                 <td align=center class="cellcol1">Pkt.</td>';
               }  
               if($akr_tlr_fm == 8){  
                 $html_ak .= '
                 <td align=center>%</td>
                 <td align=center class="cellcol1">Pkt.</td>
                 <td align=center>%</td>
                 <td align=center class="cellcol1">Pkt.</td>';
               }  
               $html_ak .= '  
               <td align=center class="cellcol1">Pkt.</td>';
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
                if($gCurrentUser->isWebmaster() && $awrname[$i] != ''){
                  $html_ak .= '<td align=center><b><span data-tooltip="'.$awrname[$i].'" data-tooltip-position="top">AWR'.($i+1).'</span></b></td>';
                } else {
                  $html_ak .= '<td align=center><b>AWR'.($i+1).'</b></td>';                 
                } 
                $akropkte_awrx = 0;
                for($y = 0; $y < count($akroproz_ar)/($awrnr); $y++){                                  
                  $anzahl_akros = count($akropkte_ar)/($awrnr);//nimmt anzahl werte im array durch anzahl wr -> also immer alle 8 moeglichen                 
                  //if($y < 6 || ($y == 6 && $akr_tlr_fm == 7) || ($y == 7 && $akr_tlr_fm == 8)){  
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
                  //}                
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
                <td align=center><b>Abz&uuml;ge</b></td>';
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
         if($paarinfo["startklasse"] == "F_RR_M"){
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
         //print_r($akropkte_sum);echo' akropkte_sum<br>';        
         //print_r($anzaktabzgakro);echo' anzaktabzgakro<br>';        
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
                 
         if($gCurrentUser->isWebmaster()){
           if($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde"){
             $compare_ta_html = ' ('.$ergDB_pkt_ta.')';
             $compare_ak_html = ' ('.$ergDB_pkt_ak.')';           
           } else {
             $compare_ta_html = ' ('.$ergDB_pkt_ta_a[0].'/'.$ergDB_pkt_ta_a[1].')';
             $compare_ak_html = ' ('.$ergDB_pkt_ak_a[0].'/'.$ergDB_pkt_ak_a[1].')';                            
           }      
         } else {
           $compare_ta_html = '';
           $compare_ak_html = '';
         }    
         $html_ak .= '
            </tr>
            <tr style="background-color: orange;">
               <td align=right><b>Wertung Tanz&nbsp;</b></td>';
         if($isStartbuchFormation && !$startklasse_mit_akro && $twrnr >= 5){
           $colspan_value = 12;
         } else {
           $colspan_value = 8;
         }
         if($paarinfo["startklasse"] == "F_RR_M" || $paarinfo["startklasse"] == "F_RR_LF"  || $paarinfo["startklasse"] == "F_RR_GF"  || $paarinfo["startklasse"] == "F_RR_J"){
           $html_ak .= '     
               <td align=center colspan='.$colspan_value.'><b>'.number_format($tanzpkte_sum-$anzaktabzg,2).
               '</b> <span data-tooltip="Abzug Anzahl Aktive" data-tooltip-position="top">(+'.number_format($anzaktabzg,2).')</span></td>';
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
               <td align=left colspan='.(13+$colspan_add).'><b>Wertung Akrobatik&nbsp;</b></td>';
           if($paarinfo["startklasse"] == "F_RR_M"){
             $html_ak .= '     
               <td align=center><b>'.number_format($akropkte_sum-$anzaktabzgakro,2).'
               </b> <span data-tooltip="Abzug Anzahl Aktive" data-tooltip-position="top">(+'.number_format($anzaktabzgakro,2).')</span></td></tr>';
           } else {        
             $html_ak .= '     
               <td align=center><b>'.number_format($akropkte_sum,2).'</b></td></tr>';
           }
           $akropkte_sum = $akropkte_sum-$anzaktabzgakro;      
         } else {
           $html_ak .= '
            </tr>';                  
         }
         if($gCurrentUser->isWebmaster()){
           if($runde_name == "Endrunde Akrobatik" || $runde_name == "Endrunde"){
             $compare_html = ' ('.number_format($ergDB_pkt_ta+$ergDB_pkt_ak,2).')';           
           } else {     
             $compare_html = ' ('.number_format($ergDB_pkt_ta_a[0]+$ergDB_pkt_ak_a[0],2).'/'.number_format($ergDB_pkt_ta_a[1]+$ergDB_pkt_ak_a[1],2).')';
           }      
         } else {
           $compare_html = '';
         }          
         $html_ak .= '
            <tr style="background-color: #F5A9A9;">
               <td align=right><b>&sum; Abz&uuml;ge&nbsp;</b></td>
               <td align=center colspan='.$colspan_value.'><b>'.$t_abzg_text[0].' '.$t_abzg_text_a20.'</b></td>';
         if($startklasse_mit_akro){
           $html_ak .= '<td align=left colspan='.(13+$colspan_add).'><b>&nbsp;</b></td>';
//           if(array_sum($a_abzg_pkte) != 0){
//               $html_ak .= '<td align=center><b>'.number_format(array_sum($a_abzg_pkte)/($awrnr),2).'</b></td>';
           if((array_sum($t_abzg_pkte)+array_sum($a_abzg_pkte)+$t_abzg_pkte_a20) != 0){
               $html_ak .= '<td align=center>-(<b>'.number_format((array_sum($t_abzg_pkte)/($twrnr))+(array_sum($a_abzg_pkte)/($awrnr))+$t_abzg_pkte_a20,2).'</b>)</td>';
           } else {
               $html_ak .= '<td align=center><b>&nbsp;</b></td>';
           }      
         }
         $html_ak .= '    
            </tr>';
//            <tr style="background-color: #BCF5A9;">
//               <td align=right><b>&sum; Tanz&nbsp;</b></td>
//               <td align=center colspan='.$colspan_value.'><b>';          
//         if($tanzpkte_sum <= $t_abzg_pkte[0]){
//           $html_ak .= '0.00'.$compare_ta_html;
//         } else {
//           $html_ak .= number_format($tanzpkte_sum-$t_abzg_pkte[0],2).$compare_ta_html;
//         }
//         $html_ak .= '</b></td>';
//         if($startklasse_mit_akro){
//           if($akropkte_sum <= array_sum($a_abzg_pkte)){
//             $html_ak .= '
//                <td align=left colspan='.(13+$colspan_add).'><b>&sum; Akrobatik&nbsp;</b></td>
//                <td align=center><b>0,00'.$compare_ak_html.'</b></td></tr>';                                   
//           } else {
//             $html_ak .= '
//                <td align=left colspan='.(13+$colspan_add).'><b>&sum; Akrobatik&nbsp;</b></td>
//                <td align=center><b>'.number_format($akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr)),2).$compare_ak_html.'</b></td></tr>';                                   
//           }                       
//         } else {
//           $html_ak .= '
//              </tr>';                           
//         }
         $html_ak .= '
            <tr style="background-color: #BCF5A9;">';
         if($startklasse_mit_akro){                 
           $html_ak .= '<td align=center colspan='.(23+$colspan_add).'><b>Ergebnis = ';         
         } else {
           $html_ak .= '<td align=center colspan='.($colspan_value + 1).'><b>Ergebnis = ';         
         }
               if(($tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr))-$t_abzg_pkte_a20) > 0){
                 $erg_pkt_su = $tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr))-$t_abzg_pkte_a20;
                 $html_ak .= number_format($tanzpkte_sum-$t_abzg_pkte[0]+$akropkte_sum-(array_sum($a_abzg_pkte)/($awrnr))-$t_abzg_pkte_a20,2).' Pkt.'.$compare_html.'</b></td>';                 
               } else {
                 $erg_pkt_su = 0;
                 $html_ak .= '0.00 Pkt.'.$compare_html ;
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
        $akropkte_sum_vr    = $akropkte_sum;         
        $akropkte_sum_ar_vr = $akropkte_sum_ar;         
        $erg_pkt_su_vr      = $erg_pkt_su;
        $abz_pkt_a20_vr     = $t_abzg_pkte_a20;
        $teilnahme_vr       = TRUE;
      } elseif($runde_name == "Hoffnungsrunde"){
        $html_akhr1         = $html_ak;
        $akrovorwert        = $vorwertvr;
        $tanzpkte_sum_h1    = $tanzpkte_sum;
        $tanzpkte_sum_ar_h1 = $tanzpkte_sum_ar;
        $akropkte_sum_h1    = $akropkte_sum;         
        $akropkte_sum_ar_h1 = $akropkte_sum_ar;         
        $erg_pkt_su_h1      = $erg_pkt_su;
        $abz_pkt_a20_h1     = $t_abzg_pkte_a20;
        $teilnahme_h1       = TRUE;
      } elseif($runde_name == "KO-Runde"){
        $html_akko          = $html_ak;
        $akrovorwert        = $vorwerter;
        $tanzpkte_sum_ko    = $tanzpkte_sum;
        $tanzpkte_sum_ar_ko = $tanzpkte_sum_ar;
        $akropkte_sum_ko    = $akropkte_sum;         
        $akropkte_sum_ar_ko = $akropkte_sum_ar;         
        $erg_pkt_su_ko      = $erg_pkt_su;
        $abz_pkt_a20_ko     = $t_abzg_pkte_a20;
        $teilnahme_ko       = TRUE;
      } elseif($runde_name == "Semifinale"){
        $html_akse          = $html_ak;
        $akrovorwert        = $vorwerter;
        $tanzpkte_sum_se    = $tanzpkte_sum;
        $tanzpkte_sum_ar_se = $tanzpkte_sum_ar;
        $akropkte_sum_se    = $akropkte_sum;         
        $akropkte_sum_ar_se = $akropkte_sum_ar;         
        $erg_pkt_su_se      = $erg_pkt_su;
        $abz_pkt_a20_se     = $t_abzg_pkte_a20;
        $teilnahme_se       = TRUE;
      } elseif($runde_name == "1. Zwischenrunde"){
        $html_akzr1         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z1    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z1 = $tanzpkte_sum_ar;
        $akropkte_sum_z1    = $akropkte_sum;         
        $akropkte_sum_ar_z1 = $akropkte_sum_ar;         
        $erg_pkt_su_z1      = $erg_pkt_su;
        $abz_pkt_a20_z1     = $t_abzg_pkte_a20;
        $teilnahme_z1       = TRUE;
      } elseif($runde_name == "2. Zwischenrunde"){
        $html_akzr2         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z2    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z2 = $tanzpkte_sum_ar;
        $akropkte_sum_z2    = $akropkte_sum;         
        $akropkte_sum_ar_z2 = $akropkte_sum_ar;         
        $erg_pkt_su_z2      = $erg_pkt_su;
        $abz_pkt_a20_z2     = $t_abzg_pkte_a20;
        $teilnahme_z2       = TRUE;
      } elseif($runde_name == "3. Zwischenrunde"){
        $html_akzr3         = $html_ak;
        $akrovorwert        = $vorwertzr;
        $tanzpkte_sum_z3    = $tanzpkte_sum;
        $tanzpkte_sum_ar_z3 = $tanzpkte_sum_ar;
        $akropkte_sum_z3    = $akropkte_sum;         
        $akropkte_sum_ar_z3 = $akropkte_sum_ar;         
        $erg_pkt_su_z3      = $erg_pkt_su;
        $abz_pkt_a20_z3     = $t_abzg_pkte_a20;
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
        $akropkte_sum_er    = $akropkte_sum;         
        $akropkte_sum_ar_er = $akropkte_sum_ar;         
        $erg_pkt_su_er      = $erg_pkt_su;
        $abz_pkt_a20_er     = $t_abzg_pkte_a20;
        $teilnahme_er       = TRUE;
      }
    }//end "Endrunde Akrobatik || Endrunde || Vorrunde || Hoffnungsrunde || KO-Runde || Semifinale || Zwischenrunden"          
    
  }//End alle Runden Table Rows
  
if($teilnahme_vr){
  $tanzpkte_sum    = $tanzpkte_sum_vr;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_vr;
  $akropkte_sum    = $akropkte_sum_vr;              
  $akropkte_sum_ar = $akropkte_sum_ar_vr;                 
  $erg_pkt_su      = $erg_pkt_su_vr;
  $abz_pkt_a20     = $abz_pkt_a20_vr;
}
if($teilnahme_h1){
  $tanzpkte_sum    = $tanzpkte_sum_h1;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_h1;
  $akropkte_sum    = $akropkte_sum_h1;              
  $akropkte_sum_ar = $akropkte_sum_ar_h1;                 
  $erg_pkt_su      = $erg_pkt_su_h1;
  $abz_pkt_a20     = $abz_pkt_a20_h1;
}
if($teilnahme_ko){
  $tanzpkte_sum    = $tanzpkte_sum_ko;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_ko;
  $akropkte_sum    = $akropkte_sum_ko;              
  $akropkte_sum_ar = $akropkte_sum_ar_ko;                 
  $erg_pkt_su      = $erg_pkt_su_ko;
  $abz_pkt_a20     = $abz_pkt_a20_ko;
}
if($teilnahme_se){
  $tanzpkte_sum    = $tanzpkte_sum_se;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_se;
  $akropkte_sum    = $akropkte_sum_se;              
  $akropkte_sum_ar = $akropkte_sum_ar_se;                 
  $erg_pkt_su      = $erg_pkt_su_se;
  $abz_pkt_a20     = $abz_pkt_a20_se;
}                 
if($teilnahme_z1){
  $tanzpkte_sum    = $tanzpkte_sum_z1;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z1;
  $akropkte_sum    = $akropkte_sum_z1;              
  $akropkte_sum_ar = $akropkte_sum_ar_z1;                 
  $erg_pkt_su      = $erg_pkt_su_z1;
  $abz_pkt_a20     = $abz_pkt_a20_z1;
}  
if($teilnahme_z2){
  $tanzpkte_sum    = $tanzpkte_sum_z2;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z2;
  $akropkte_sum    = $akropkte_sum_z2;              
  $akropkte_sum_ar = $akropkte_sum_ar_z2;                 
  $erg_pkt_su      = $erg_pkt_su_z2;
  $abz_pkt_a20     = $abz_pkt_a20_z2;
}  
if($teilnahme_z3){
  $tanzpkte_sum    = $tanzpkte_sum_z3;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_z3;
  $akropkte_sum    = $akropkte_sum_z3;              
  $akropkte_sum_ar = $akropkte_sum_ar_z3;                 
  $erg_pkt_su      = $erg_pkt_su_z3;
  $abz_pkt_a20     = $abz_pkt_a20_z3;
}  
if($teilnahme_er){
  $tanzpkte_sum    = $tanzpkte_sum_er;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_er;
  $akropkte_sum    = $akropkte_sum_er;              
  $akropkte_sum_ar = $akropkte_sum_ar_er;  
  $erg_pkt_su      = $erg_pkt_su_er;
  $abz_pkt_a20     = $abz_pkt_a20_er;
}
if($teilnahme_vor){
  $tanzpkte_sum    = $tanzpkte_sum_vo;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_vo;
  $akropkte_sum    = $akropkte_sum_vo;              
  $akropkte_sum_ar = $akropkte_sum_ar_vo;                 
  $erg_pkt_su      = $erg_pkt_su_vo;
  $abz_pkt_a20     = $abz_pkt_a20_vo;
}                 
if($teilnahme_lavor){
  $tanzpkte_sum    = $tanzpkte_sum_lv;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_lv;
  $erg_pkt_su      = $erg_pkt_su_lv;
  $abz_pkt_a20     = $abz_pkt_a20_lv;
}                 
if($teilnahme_snvr){
  $tanzpkte_sum    = $tanzpkte_sum_sv;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_sv;
  $erg_pkt_su      = $erg_pkt_su_sv;
  $abz_pkt_a20     = $abz_pkt_a20_sv;
} 
if($teilnahme_hoff){
  $tanzpkte_sum    = $tanzpkte_sum_ho;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_ho;
  $erg_pkt_su      = $erg_pkt_su_ho;
  $abz_pkt_a20     = $abz_pkt_a20_ho;
}                  
if($teilnahme_laer){
  $tanzpkte_sum    = $tanzpkte_sum_la;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_la;
  $erg_pkt_su      = $erg_pkt_su_la;
  $abz_pkt_a20     = $abz_pkt_a20_la;
}                 
if($teilnahme_sner){
  $tanzpkte_sum    = $tanzpkte_sum_sn;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_sn;
  $erg_pkt_su      = $erg_pkt_su_sn;
  $abz_pkt_a20     = $abz_pkt_a20_sn;
} 
if($teilnahme_end){
  $tanzpkte_sum    = $tanzpkte_sum_er;
  $tanzpkte_sum_ar = $tanzpkte_sum_ar_er;
  $akropkte_sum    = $akropkte_sum_er;              
  $akropkte_sum_ar = $akropkte_sum_ar_er;                 
  $erg_pkt_su      = $erg_pkt_su_er;
  $abz_pkt_a20     = $abz_pkt_a20_er;
} 

$htmlout  = '';                 
$htmlout .= '
<div class="formLayout" id="profile_form" width="100px">
  <div class="formHead">Startbuch: '.$user->getValue('LAST_NAME').' - Wertungen</div>
  <div class="formBody">';
                 
  // *******************************************************************************
  // Userdaten-Block
  // *******************************************************************************
  $htmlout .= '      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Turnierauswertung</div>
      </div>';
  if($getMode != 'printview' && $shareRSLT != 1){               
    $htmlout .= '           
      <div style="text-align: right;">                 
        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_wrtg.php?user_id='.$user->getValue('usr_id').'&tnrsel='.$getTnrSel.'&mode=printview" target="_blank" title="Druckansicht"><img src="'. THEME_PATH. '/icons/print.png" alt="Druckansicht" /></a>
        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_wrtg.php?user_id='.$user->getValue('usr_id').'&tnrsel='.$getTnrSel.'&share=1" title="Versenden"><img src="'. THEME_PATH. '/icons/mail.png" title="Versenden" alt="Mail senden" /></a>               
      </div>';
  }
  if($shareRSLT != 1){               
    $htmlout .= '             
      <div class="groupBoxBody">
      <ul class="formFieldList">
        <li>
          <dl>
            <dt>Turnier:</dt>
            <dd>'.utf8_encode($turnierdaten["turniername"]).'</dd>
          </dl>       
        </li>
        <li>
          <dl>
            <dt>Nummer:</dt>
            <dd>'.$getTnrSel.'</dd>
          </dl>       
        </li>
        <li>
          <dl>
            <dt>Datum:</dt>
            <dd>'.$datum->format("d.m.Y").'</dd>
          </dl>       
        </li>';
        if($isStartbuchFormation){
          $htmlout .= '
            <li>
              <dl>
                <dt>Formation:</dt>
                <dd>'.$paarinfo["team"].'</dd>
              </dl>       
            </li>';
        } else {
          $htmlout .= '
            <li>
              <dl>
                <dt>Paar:</dt>
                <dd>'.$paarinfo["herr"].' / '.$paarinfo["dame"].'</dd>
              </dl>       
            </li>';
        }
        $htmlout .= '
        <li>
          <dl>
            <dt>Platz:</dt>
            <dd>'.$paarinfo["platz"].'</dd>
          </dl>       
        </li>
        <li>
          <dl>
            <dt>Ranglistenpunkte:</dt>
            <dd>'.$paarinfo["rl_punkte"].'</dd>
          </dl>       
        </li>';
        if($class_name == 'Startbuch RR-J' || $class_name == 'Startbuch RR-C' || $class_name == 'Startbuch RR-B'){
          $htmlout .= '
            <li>
              <dl>
                <dt>Aufstiegspunkte:</dt>
                <dd>'.$paarinfo["punkte"].'</dd>
              </dl>       
            </li>';
        }
      $htmlout .= '    
      </ul>
      </div>
    </div>';
    //class="groupBox" end
    } else {
      $htmlout .= '
      <p>&nbsp;<b>'.utf8_encode($turnierdaten["turniername"]).'</b> (Nr.: '.$getTnrSel.')<br><br>
         <b>Datum:</b> '.$datum->format("d.m.Y").'<br>
         <b>Wertung für:</b> '.$paarinfo["team"].$paarinfo["herr"].' / '.$paarinfo["dame"].'<br>
         <b>Platz:</b> '.$paarinfo["platz"].'<br>
         <b>Ranglistenpunkte:</b> '.$paarinfo["rl_punkte"].'<br>
         <b>Aufstiegspunkte:</b> '.$paarinfo["punkte"].'<br>        
      </p>           
      ';
    }
                 
    $html_endergebnis .= '
          <table class="prfl_wrtg_rgbns">
            <tr style="background-color: orange;">
               <td align=center><b>Platz</b></td>
               <td align=center><b>RnglPkt</b></td>';
               if($teilnahme_er && ($paarinfo["startklasse"] == "RR_B"  || $paarinfo["startklasse"] == "RR_A")){
                 $html_endergebnis  .= '<td align=center><b>&sum; FussT</b></td>';
               }
               if($wrtg_bw){
                 if($teilnahme_laer && ($paarinfo["startklasse"] == "BW_MA"  || $paarinfo["startklasse"] == "BW_SA")){
                   $html_endergebnis .= '<td align=center><b>&sum; LaRnd</b></td>';
                 }                 
                 $html_endergebnis .='
                 <td align=center><b>WR1</b></td>
                 <td align=center><b>WR2</b></td>
                 <td align=center><b>WR3</b></td>
                 <td align=center><b>WR4</b></td>
                 <td align=center><b>WR5</b></td>
                 <td align=center><b>WR6</b></td>
                 <td align=center><b>WR7</b></td>';                 
               } else {
                 $html_endergebnis .='
                 <td align=center><b>TWR1</b></td>
                 <td align=center><b>TWR2</b></td>
                 <td align=center><b>TWR3</b></td>
                 <td align=center><b>TWR4</b></td>';
                 if($isStartbuchFormation && !$startklasse_mit_akro && $twrnr >= 5){
                   $html_endergebnis .='
                   <td align=center><b>TWR5</b></td>
                   <td align=center><b>TWR6</b></td>';                 
                 }                   
                 if($startklasse_mit_akro){
                   $html_endergebnis .='
                   <td align=center><b>&sum; Tanz</b></td>
                   <td align=center><b>AKVW</b></td>
                   <td align=center><b>AWR1</b></td>
                   <td align=center><b>AWR2</b></td>
                   <td align=center><b>AWR3</b></td>
                   <td align=center><b>AWR4</b></td>
                   <td align=center><b>&sum; Akrobatik</b></td>';
                 }               
               }  
               $html_endergebnis .='<td align=center><b>Abz&uuml;ge</b></td>';
               if($wrtg_bw && $teilnahme_laer){
                 $html_endergebnis .='<td align=center><b>&sum; SnRnd</b></td>';
               }
               $html_endergebnis .='<td align=center><b>Ergebnis</b></td>
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center><b>'.$paarinfo["platz"].'</b></td>
               <td align=center>'.$paarinfo["rl_punkte"].'</td>';
               if($teilnahme_er && ($paarinfo["startklasse"] == "RR_B"  || $paarinfo["startklasse"] == "RR_A")){
                 $html_endergebnis .='<td align=center><b>'.$ergDB_pkt_ft.'</b></td>';
               }
               if($wrtg_bw){
                 if($teilnahme_laer && ($paarinfo["startklasse"] == "BW_MA"  || $paarinfo["startklasse"] == "BW_SA")){
                   $html_endergebnis  .= '<td align=center><b>'.$ergDB_pkt_bl.'</b></td>';
                 }
                 if($twrnr > 0) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[0],2).'</td>';
                 if($twrnr > 1) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[1],2).'</td>';
                 if($twrnr > 2) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[2],2).'</td>';
                 if($twrnr > 3) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[3],2).'</td>';
                 if($twrnr > 4) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[4],2).'</td>';
                 if($twrnr > 5) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[5],2).'</td>';
                 if($twrnr > 6) $html_endergebnis .='<td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[6],2).'</td>';
                 for($i = 0; $i < 7-$twrnr; $i++){
                   $html_endergebnis .='<td align=center style="color: #41a0fa;"></td>';
                 }
               } else {
                 $html_endergebnis .='
                 <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[0],2).'</td>
                 <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[1],2).'</td>';
                 if($twrnr == 3 || $twrnr == 4 || $twrnr == 5 || $twrnr == 6){                             
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[2],2).'</td>';
                 } else {
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">&nbsp;</td>';               
                 }   
                 if($twrnr == 4 || $twrnr == 5 || $twrnr == 6){                             
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[3],2).'</td>';
                 } else {
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">&nbsp;</td>';               
                 }
                 if($twrnr == 5 || $twrnr == 6){                             
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[4],2).'</td>';
                 }
                 if($twrnr == 6){                             
                   $html_endergebnis .= '
                   <td align=center style="color: #41a0fa;">'.number_format($tanzpkte_sum_ar[5],2).'</td>';
                 }
                 if($startklasse_mit_akro){
                   $html_endergebnis .= '
                   <td align=center><b>'.number_format($tanzpkte_sum,2).'</b></td>
                   <td align=center>'.round($akrovorwert,2).'</td>
                   <td align=center style="color: #41a0fa;">'.number_format($akropkte_sum_ar[0],2).'</td>
                   <td align=center style="color: #41a0fa;">'.number_format($akropkte_sum_ar[1],2).'</td>';               
               
                   if($awrnr == 3 || $awrnr == 4){                             
                     $html_endergebnis .= '
                     <td align=center style="color: #41a0fa;">'.number_format($akropkte_sum_ar[2],2).'</td>';
                   } else {
                     $html_endergebnis .= '<td align=center style="color: #41a0fa;">&nbsp;</td>';  
                   }
                   if($awrnr == 4){                             
                     $html_endergebnis .= '
                     <td align=center style="color: #41a0fa;">'.number_format($akropkte_sum_ar[3],2).'</td>';
                   } else {
                     $html_endergebnis .= '<td align=center style="color: #41a0fa;">&nbsp;</td>';                 
                   }
                   $html_endergebnis .= '<td align=center><b>'.number_format($akropkte_sum,2).'</b></td>';               
                 }                 
               }
               $abz_akro = 0;  
               if($a_abzg_pkte) $abz_akro = array_sum($a_abzg_pkte)/($awrnr);  
               $html_endergebnis .= '
                 <td align=center style="color: #ed595d;">'.number_format($t_abzg_pkte[0]+$abz_akro+$abz_pkt_a20,2).'</td>';
               if($teilnahme_er && ($paarinfo["startklasse"] == "RR_B"  || $paarinfo["startklasse"] == "RR_A")){
                 $html_endergebnis .='<td align=center><b>'.number_format(($erg_pkt_su + $ergDB_pkt_ft),2).'</b></td>';
               } else {
                 if($wrtg_bw && $teilnahme_laer){
                   //$html_endergebnis .='<td align=center><b>'.number_format($erg_pkt_su*1.1+$ergDB_pkt_bl,2).'</b></td>';
                   $html_endergebnis .='<td align=center><b>'.number_format($erg_pkt_su,2).'</b></td>';
                 }
                 if(($erg_pkt_su-$abz_pkt_a20) <= 0){
                   $html_endergebnis .='<td align=center><b>0.00</b></td>';
                 } else {
                   //$html_endergebnis .='<td align=center><b>'.number_format($erg_pkt_su,2).'</b></td>';                 
                   if($wrtg_bw && $teilnahme_laer){
                     $html_endergebnis .='<td align=center><b>'.number_format($erg_pkt_su*1.1+$ergDB_pkt_bl,2).'</b></td>';
                   } else {
                     $html_endergebnis .='<td align=center><b>'.number_format($erg_pkt_su,2).'</b></td>';                 
                   }                 
                 }
               }
            $html_endergebnis .='</tr>
          </table>';
    
}// end: while($paar_id_paare) 

// Gemeldete Akrobatiken  
      if($startklasse_mit_akro){
        if($shareRSLT != 1){         
          $htmlout .= '<div class="groupBox">
               <div class="groupBoxHeadline">
                  <div style="float: left;">Gemeldete Akrobatiken</div>
               </div>
               <div class="groupBoxBody">';
               if($getMode == 'printview'){
                 $htmlout .= '
                 <ul>'.$insert_akrovr.'</ul>
                 <ul>'.$insert_akrozr.'</ul>
                 <ul>'.$insert_akroer.'</ul>';
               } else {
                 $htmlout .= '
                 <ul class="formFieldList">'.$insert_akrovr.'</ul>
                 <ul class="formFieldList">'.$insert_akrozr.'</ul>
                 <ul class="formFieldList">'.$insert_akroer.'</ul>';
               }
               $htmlout .= '  
               </div>
             </div>';
         } else {
           $htmlout .= '<p>
           <p>'.$insert_akrovr.'</p>      
           <p>'.$insert_akrozr.'</p>      
           <p>'.$insert_akroer.'</p>      
           </p>';
         }
       }
// End: Gemeldete Akrobatiken

if($twrnrfix && $gCurrentUser->isWebmaster()){
  $twrnrfix = '<font color="#FF0000"> (! mit AnzahlWR fix von 8 auf 4 bei S-Klasse)</font>';                
}                 
                 
// Wertungen
      if($shareRSLT != 1){
        $htmlout .= '<!-- div class="groupBox">
             </div -->
               <div class="groupBoxHeadline">
                  <div style="float: left;"><br>Endergebnis'.$twrnrfix.'</div>
               </div>
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   <div style="font-size: 12px;">
                     <li><dl>
                       '.$html_endergebnis.'                                                                                         
                     </dl></li>
                   </div>                 
                 </ul>
               </div>                 
               <div class="groupBoxHeadline">
                  <div style="float: left;"><br>Wertung</div>
               </div>';
               if($html_lavor != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_lavor.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_snvor != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_snvor.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                
               if($html_vor != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_vor.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                
               if($html_akvor != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akvor.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akhr1 != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akhr1.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akko != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akko.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akse != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akse.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akzr1 != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akzr1.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akzr2 != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akzr2.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                              
               if($html_akzr3 != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_akzr3.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }
               if($html_hoff != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_hoff.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                
               if($html_laend != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_laend.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                               
               if($html_snend != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_snend.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                               
               if($html_end != ''){
                 $htmlout .=  '
                 <div class="groupBoxBody">
                   <ul class="formFieldList">
                     <div style="font-size: 12px;">
                       <li><dl>
                       '.$html_end.'
                       </dl></li>
                     </div>                 
                   </ul>
                 </div>';                 
               }                                               
               $htmlout .=  '  
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   <div style="font-size: 12px;">
                     <li><dl>
                     '.$html_ft.'
                     </dl></li>
                   </div>                 
                 </ul>
               </div>
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   <div style="font-size: 12px;">
                     <li><dl>
                     '.$html_akend.'
                     </dl></li>
                   </div>                 
                 </ul>
               </div>';
// End: Wertungen
  } else {
    $htmlout .= '<p>'.$html_endergebnis.'</p>';                 
    if($html_lavor != ''){
      $htmlout .= '<p>'.$html_lavor.'</p>';
    }                 
    if($html_snvor != ''){
      $htmlout .= '<p>'.$html_snvor.'</p>';
    }                 
    if($html_vor != ''){
      $htmlout .= '<p>'.$html_vor.'</p>';
    }                 
    if($html_akvor != ''){
      $htmlout .= '<p>'.$html_akvor.'</p>';
    }                 
    if($html_akhr1 != ''){
      $htmlout .= '<p>'.$html_akhr1.'</p>';
    }                 
    if($html_hoff != ''){
      $htmlout .= '<p>'.$html_hoff.'</p>';
    }                 
    if($html_akzr1 != ''){
      $htmlout .= '<p>'.$html_akzr1.'</p>';
    }                 
    if($html_akzr2 != ''){
      $htmlout .= '<p>'.$html_akzr2.'</p>';
    }                 
    if($html_akzr3 != ''){
      $htmlout .= '<p>'.$html_akzr3.'</p>';
    }                 
    if($html_akko != ''){
      $htmlout .= '<p>'.$html_akko.'</p>';
    }                 
    if($html_akse != ''){
      $htmlout .= '<p>'.$html_akse.'</p>';
    }                 
    if($html_laend != ''){
      $htmlout .= '<p>'.$html_laend.'</p>';
    }                 
    if($html_snend != ''){
      $htmlout .= '<p>'.$html_snend.'</p>';
    }                 
    if($html_end != ''){
      $htmlout .= '<p>'.$html_end.'</p>';
    }                 
    if($html_ft != ''){
      $htmlout .= '<p>'.$html_ft.'</p>';
    }                 
    if($html_akend != ''){
      $htmlout .= '<p>'.$html_akend.'</p>';
    }                 
  }  
  $htmlout .=  '
  </div>
</div>';//class="formLayout" end
                
if($shareRSLT == 1){
  echo '        
  <div class="formLayout" id="profile_form" width="100px">
  <div class="formHead">Startbuch: '.$user->getValue('LAST_NAME').' - Wertungen</div>
  <div class="formBody">
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Turnierauswertung</div>
      </div>
      <div class="groupBoxBody">
                 
                 <form name="Formular" action="'.$g_root_path.'/adm_program/modules/profile/profile_wrtg_share.php" method="post" accept-charset="UTF-8">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <fieldset>
                    <legend>An welche Mailadressen soll die Wertung versendet werden?</legend>
                    <table>
                      <tr>
                        <td>Empfänger 1:</td>
                        <td><input type="text" name="Empf1" tabindex="1" size="60"></td>
                      </tr>
                      <tr>
                        <td>Empfänger 2:</td>
                        <td><input type="text" name="Empf2" tabindex="2" size="60"></td>
                      </tr>
                  </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="60" tabindex="3">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  </font>
                  <!-- Ende der Beispielangaben -->
                  <p align="right">
                    <input type="submit" value="Absenden" />
                    <input type="reset"  value="Zurücksetzen" />
                  </p>
              </form>                 
                 
      </div>
    </div>
  </div>
  </div>';
  $_SESSION['profile_share'] = $htmlout;
  $_SESSION['profile_usrid'] = $user->getValue('usr_id');
  $_SESSION['profile_tnr']   = $getTnrSel;
  $_SESSION['profile_vmail'] = $user->getValue('EMAIL');
  $_SESSION['profile_vname'] = $user->getValue('VEREIN');
} else {                                
  echo $htmlout;                 
}
                 
if($getMode != 'printview'){ 
  if($user->getValue('usr_id') != $gCurrentUser->getValue('usr_id'))
  {
    echo '
    <ul class="iconTextLinkList">
        <li>
            <span class="iconTextLink">
                <a href="'.$g_root_path.'/adm_program/system/back.php"><img
                src="'.THEME_PATH.'/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
                <a href="'.$g_root_path.'/adm_program/modules/profile/profile.php?user_id='.$user->getValue('usr_id').'">'.$gL10n->get('SYS_BACK').'</a>
            </span>
        </li>
    </ul>';
  }                
  require(SERVER_PATH. '/adm_program/system/overall_footer.php');
}

?>
