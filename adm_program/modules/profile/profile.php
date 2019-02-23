<?php

/******************************************************************************
 * Profil anzeigen
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

//$startzeit = microtime(true);

require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/table_roles.php');
require_once('../../system/drbv_funktionen.php');
require_once('roles_functions.php');
require_once('../../../../vendor/autoload.php');

$pkt_datum = "";
$getMode   = "";
$pdf_html  = array();

// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getMode   = admFuncVariableIsValid($_GET, 'mode');

// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// diese Funktion gibt den Html-Code fuer ein Feld mit Beschreibung wieder
// dabei wird der Inhalt richtig formatiert
function getFieldCode($fieldNameIntern, $user, $akro_class='', $raw='')
{
  global $gPreferences, $g_root_path, $gCurrentUser, $gProfileFields, $gL10n, $pkt_datum;
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

	// Icons der Messenger anzeigen
	if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'ICQ')
	{
		if(strlen($user->getValue($fieldNameIntern)) > 0)
		{
			// Sonderzeichen aus der ICQ-Nummer entfernen (damit kommt www.icq.com nicht zurecht)
			preg_match_all('/\d+/', $user->getValue($fieldNameIntern), $matches);
			$icq_number = implode("", reset($matches));

			// ICQ Onlinestatus anzeigen
			$value = '
      <a class="iconLink" href="https://www.icq.com/people/cmd.php?uin='.$icq_number.'&amp;action=add"><img
        src="https://status.icq.com/online.gif?icq='.$icq_number.'&amp;img=5"
				alt="'.$gL10n->get('PRO_TO_ADD', $user->getValue($fieldNameIntern), $gProfileFields->getProperty($fieldNameIntern, 'usf_name')).'"
				title="'.$gL10n->get('PRO_TO_ADD', $user->getValue($fieldNameIntern), $gProfileFields->getProperty($fieldNameIntern, 'usf_name')).'" /></a> '.$value;
		}
	}
	elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'SKYPE')
	{
		if(strlen($user->getValue($fieldNameIntern)) > 0)
		{
			// Skype Onlinestatus anzeigen
      $value = '<script type="text/javascript" src="https://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
			<a class="iconLink" href="skype:'.$user->getValue($fieldNameIntern).'?add"><img
        src="https://mystatus.skype.com/smallicon/'.$user->getValue($fieldNameIntern).'"
				title="'.$gL10n->get('PRO_TO_ADD', $user->getValue($fieldNameIntern), $gProfileFields->getProperty($fieldNameIntern, 'usf_name')).'"
				alt="'.$gL10n->get('PRO_TO_ADD', $user->getValue($fieldNameIntern), $gProfileFields->getProperty($fieldNameIntern, 'usf_name')).'" /></a> '.$value;
		}
	}
	elseif(strlen($gProfileFields->getProperty($fieldNameIntern, 'usf_icon')) > 0)
	{
		$value = $gProfileFields->getProperty($fieldNameIntern, 'usf_icon').'&nbsp;&nbsp;'. $value;
	}

	// show html of field, if user has a value for that field or it's a checkbox field
    if(strlen($user->getValue($fieldNameIntern)) > 0 || $gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'CHECKBOX')
    {
    if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_FUSSTECHNIK' ||
       $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_AKROBATIK' || 
       $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_STELLPROBE' || 
       $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_TANZMUSIK' || 
       $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_ERSATZMUSIK')
    {
       $html = '<li>
                  <dl>
                  <table style="table-layout:fixed">
                    <td style="vertical-align:middle;width:26%">'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</td>
                    <td style="vertical-align:middle;width:28%">'. $value. '</td>
                    <td style="vertical-align:middle">
                      <audio controls>
                        <source src="https://www.drbv.de/turniermusik/index.php?file='. $value. '.mp3" type="audio/mpeg" />
                        <em>Sorry, your browser does not support HTML5 audio!</em>
                      </audio>
                      <a rel="colorboxPWContent"><img style="vertical-align:40%" src="http://drbv.de/adm/adm_themes/classic/icons/info.png" alt="Information" title="Wird keine Musik abgespielt, ist evtl. eine falsche Musik-ID eingetragen!" /></a>     
                    </td>
                  </table>
                  </dl>       
                </li>';    
    } elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'AUFSTIEGSPUNKTE_DATUM') {
      $pkt_datum = $value;
        $html = '<li>
                    <dl>
                        <dt>'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</dt>
                        <dd>'. $value. '&nbsp;</dd>
                    </dl>
                </li>';
    } elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'AUFSTIEGSPUNKTE') {
      // Aufstiegspunkte berechnen:
      // --------------------------  
      // Tanzpaar in TLP Datenbank suchen  
      $sqlab = 'SELECT turniernummer, startklasse, dame, herr, team, platz, punkte, rl_punkte FROM paare WHERE startbuch = ' . $user->getValue('LAST_NAME'); 
      //echo"$sqlab<br>"; 
      $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
      $pkt_summe       = 0;  
      $pkt_ertanzt     = 0;  
      $anzahl_turniere = 0;
      
      while($aufstiegspunkte = mysqli_fetch_array($punkteDB)){
        //print_r($aufstiegspunkte);echo"<br>";
        $sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $aufstiegspunkte["turniernummer"]; 
        $turnier      = mysqli_query(DRBVdb(), $sqlab);
        $turnierdaten = mysqli_fetch_array($turnier);
        //echo"$sqlab<br>";
        // Nur gefundene Turniere ab Aufstiegspunkte_Datum einbeziehen
        if(strtotime($pkt_datum) < strtotime($turnierdaten["datum"])){
          $anzahl_turniere++;
          $pkt_ertanzt = $pkt_ertanzt + $aufstiegspunkte["punkte"]; 
        }           
      }
      // Punkte = Anfangswert + ertanzte Punkte ab Datum  
      $pkt_summe = $value + $pkt_ertanzt;
      $html = '<li>
                 <dl>
                   <dt>'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</dt>
                   <dd>Startwert: '. $value. ' + ertanzt: '.$pkt_ertanzt.' = <b>'.$pkt_summe.'</b> Aufstiegspunkte (Turniere: '.$anzahl_turniere.')</dd>
                 </dl>       
               </li>';    
    } else {
      $html = '<li>
                 <dl>
                   <dt>'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':&nbsp;</dt>
                   <dd>'. $value. '&nbsp;</dd>
                 </dl>       
               </li>';    
    }
  }
  if($raw){
    return $value;
  } else {
    return $html;
}
} // end function getFieldCode

unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

if($getMode == ''){  
// Html-Kopf ausgeben
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = $gL10n->get('PRO_MY_PROFILE');
}
else
{
    $gLayout['title'] = $gL10n->get('PRO_PROFILE_FROM', $user->getValue('FIRST_NAME'), $user->getValue('LAST_NAME'));
}
$gLayout['header'] = '
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
	<script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/modules/profile/profile.js"></script>
    <script type="text/javascript">
    <!--
        var profileJS = new profileJSClass();
            profileJS.deleteRole_ConfirmText 	= \''.$gL10n->get('ROL_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.deleteFRole_ConfirmText 	= \''.$gL10n->get('ROL_LINK_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.changeRoleDates_ErrorText = \''.$gL10n->get('ROL_CHANGE_ROLE_DATES_ERROR').'\';
            profileJS.setBy_Text				= \''.$gL10n->get('SYS_SET_BY').'\';
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
    </script>';

require(SERVER_PATH. '/adm_program/system/overall_header.php');
}//end if mode=''

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
    if( $role_name == 'Mitglied' ) {
        $isPerson = true;        
    }  
    if( $role_name == 'Vereine' ) {
        $isVerein = true;        
        $isPerson = false;        
    }
    if( substr($role_name,0,7) == 'Trainer' ) {
        $isLizenzler = true;        
        $isPerson = false;        
    }  
    if( substr($role_name,0,13) == 'Landestrainer' ) {
        $isLizenzler = true;        
        $isPerson = false;        
    }  
    if( substr($role_name,0,10) == 'Kursleiter' ) {
        $isLizenzler = true;        
        $isPerson = false;        
    }  
    if( substr($role_name,0,13) == 'Turnierleiter' ) {
        $isLizenzler = true;        
        $isPerson = false;        
    }  
    if( substr($role_name,0,15) == 'Wertungsrichter' ) {
        $isLizenzler = true;        
        $isPerson = false;        
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
        $isStartbuchFormation = true;
        $isPerson = false;        
        $formular = 1;        
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

$profile_htm  = '';  
$profile_htm .= '
<div class="formLayout" id="profile_form">
    <div class="formHead">'. $gLayout['title']. '</div>
    <div class="formBody">
        <div>';
                
            if($formular == 1){
            // Hinweis für Startbücher 
            $profile_htm .= '<div class="groupBox">
                  <div class="groupBoxHeadline">
                     <div style="float: left;">Hinweis!</div>
                  </div>
                  <div class="groupBoxBody">
                     Eine Stammdaten&auml;nderung im Startbuch kann nur &uuml;ber diese <a href="'.$g_root_path.'/adm_program/modules/forms/forms.php">Online-Formulare</a> beantragt werden.
                  </div>
                 </div>';           
            }
                      
            // *******************************************************************************
            // Userdaten-Block
            // *******************************************************************************

            $profile_htm .= '
            <div style="width: 58%; float: left;">
                <div id="admProfileMasterData" class="groupBox">

                    <div class="groupBoxHeadline">
                        <div style="float: left;">'. $user->getValue('FIRST_NAME'). ' '. $user->getValue('LAST_NAME');

                            // Icon des Geschlechts anzeigen, wenn noetigen Rechte vorhanden
                            if(strlen($user->getValue('GENDER')) > 0
                            && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('GENDER', 'usf_hidden') == 0 ))
                            {
                                $profile_htm .= ' '.$user->getValue('GENDER');
                            }
                        $profile_htm .= '</div>
                        <div style="text-align: right;">
                            <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_function.php?mode=1&amp;user_id='. $user->getValue('usr_id'). '"><img
                                src="'. THEME_PATH. '/icons/vcard.png"
                                alt="'.$gL10n->get('PRO_EXPORT_VCARD_FROM_VAR', $user->getValue('FIRST_NAME'). ' '. $user->getValue('LAST_NAME')).'"
                                title="'.$gL10n->get('PRO_EXPORT_VCARD_FROM_VAR', $user->getValue('FIRST_NAME'). ' '. $user->getValue('LAST_NAME')).'" /></a>';

                            // Nur berechtigte User duerfen das Passwort editieren
                            if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id') || $gCurrentUser->isWebmaster())
                            {
                                $profile_htm .= '
                                <a rel="colorboxPWContent" href="password.php?usr_id='. $user->getValue('usr_id'). '&amp;inline=1"><img
                                    src="'. THEME_PATH. '/icons/key.png" alt="'.$gL10n->get('SYS_CHANGE_PASSWORD').'" title="'.$gL10n->get('SYS_CHANGE_PASSWORD').'" /></a>';
                            }
							
							if($gPreferences['profile_log_edit_fields'] == 1)
							{
								// show link to view profile field change history
                                $profile_htm .= '
                                <a class="iconLink" href="'. $g_root_path. '/adm_program/administration/members/profile_field_history.php?usr_id='. $user->getValue('usr_id'). '"><img
                                    src="'. THEME_PATH. '/icons/clock.png" alt="'.$gL10n->get('MEM_CHANGE_HISTORY').'" title="'.$gL10n->get('MEM_CHANGE_HISTORY').'" /></a>';
							}
							
                            // Nur berechtigte User duerfen ein Profil editieren
							if($gCurrentUser->editProfile($user))
                            {
                                $profile_htm .= '
                                <a class="iconLink" href="'. $g_root_path. '/adm_program/modules/profile/profile_new.php?user_id='. $user->getValue('usr_id'). '"><img
                                    src="'. THEME_PATH. '/icons/edit.png" alt="'.$gL10n->get('PRO_EDIT_PROFILE').'" title="'.$gL10n->get('PRO_EDIT_PROFILE').'" /></a>';
                            }
                        $profile_htm .= '</div>
                    </div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">
                            <li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_USERNAME').':</dt>
                                    <dd><i>';
                                    if(strlen($user->getValue('usr_login_name')) > 0)
                                    {
                                        $profile_htm .= $user->getValue('usr_login_name');
                                    }
                                    else
                                    {
                                        $profile_htm .= $gL10n->get('SYS_NOT_REGISTERED');
                                    }
                                    $profile_htm .= '&nbsp;</i></dd>
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
                                        case 'LAST_NAME':
                                        case 'FIRST_NAME':
                                        case 'GENDER':
											// don't show these fields in default profile list
                                            break;

                                        case 'ADDRESS':
                                        case 'POSTCODE':
                                        case 'CITY':
                                        case 'COUNTRY':
                                            if($bAddressOutput == false // output of address only once
											&& (  strlen($user->getValue('ADDRESS')) > 0 || strlen($user->getValue('POSTCODE')) > 0 
											   || strlen($user->getValue('CITY')) > 0 || strlen($user->getValue('COUNTRY')) > 0 ))   
                                            {
                                                $bAddressOutput = true;
                        $profile_htm .= '<li>
                                                    <dl>
                                                        <dt>'.$gL10n->get('SYS_ADDRESS').':</dt>
                                                        <dd>';
                                                            $address = '';
                                                            $map_url = 'https://maps.google.com/?q=';
                                                            $route_url = 'https://maps.google.com/?f=d&amp;saddr='.
                                                                urlencode($gCurrentUser->getValue('ADDRESS')).
                                                                ',%20'. urlencode($gCurrentUser->getValue('POSTCODE')).
                                                                ',%20'. urlencode($gCurrentUser->getValue('CITY')).
                                                                ',%20'. urlencode($gCurrentUser->getValue('COUNTRY')).
                                                                '&amp;daddr=';

                                                            if(strlen($user->getValue('ADDRESS')) > 0
                                                            && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('ADDRESS', 'usf_hidden') == 0))
                                                            {
                                                                $address   .= '<div>'.$user->getValue('ADDRESS'). '</div>';
                                                                $map_url   .= urlencode($user->getValue('ADDRESS'));
                                                                $route_url .= urlencode($user->getValue('ADDRESS'));
                                                            }

                                                            if(strlen($user->getValue('POSTCODE')) > 0
                                                            && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('POSTCODE', 'usf_hidden') == 0))
                                                            {
                                                                $address   .= '<div>'.$user->getValue('POSTCODE');
                                                                $map_url   .= ',%20'. urlencode($user->getValue('POSTCODE'));
                                                                $route_url .= ',%20'. urlencode($user->getValue('POSTCODE'));

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
                                                                $map_url   .= ',%20'. urlencode($user->getValue('CITY'));
                                                                $route_url .= ',%20'. urlencode($user->getValue('CITY'));
                                                            }

                                                            if(strlen($user->getValue('COUNTRY')) > 0
                                                            && ($gCurrentUser->editProfile($user) == true || $gProfileFields->getProperty('COUNTRY', 'usf_hidden') == 0))
                                                            {
																$country    = $user->getValue('COUNTRY');
                                                                $address   .= '<div>'.$country. '</div>';
                                                                $map_url   .= ',%20'. urlencode($country);
                                                                $route_url .= ',%20'. urlencode($country);
                                                            }

                                                            $profile_htm .= $address;

															// show route or address link if function is enabled and user has filled address or city
                                                            if($gPreferences['profile_show_map_link'] && strlen($user->getValue('ADDRESS')) > 0 
															&& (strlen($user->getValue('POSTCODE')) > 0 || strlen($user->getValue('CITY')) > 0))
                                                            {
                                                                $profile_htm .= '<span class="iconTextLink">
                                                                    <a href="'. $map_url. '" target="_blank"><img
                                                                        src="'. THEME_PATH. '/icons/map.png" alt="'.$gL10n->get('SYS_MAP').'" /></a>
                                                                    <a href="'. $map_url. '" target="_blank">'.$gL10n->get('SYS_MAP').'</a>
                                                                </span>';

																// show route link if its not the profile of CurrentUser
                                                                if($gCurrentUser->getValue('usr_id') != $user->getValue('usr_id'))
                                                                {
                                                                    $profile_htm .= ' - <a href="'.$route_url.'" target="_blank">'.$gL10n->get('SYS_SHOW_ROUTE').'</a>';
                                                                }
                                                            }
                                                        $profile_htm .= '</dd>
                                                    </dl>
                                                </li>';
                                            }
                                            break;

                                        default:
                                            $profile_htm .= getFieldCode($field->getValue('usf_name_intern'), $user);
                                            $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user,FALSE,TRUE);
                                            break;
                                    }
                                }
                            }
                        $profile_htm .= '</ul>
                    </div>
                </div>
            </div>';

            $profile_htm .= '<div style="width: 38%; float: right">';

                // *******************************************************************************
                // Profile photo
                // *******************************************************************************
                if($isVerein || $isStartbuchFormation){
                  //show no photo in case of 'Verein' or 'Formation'
                } elseif($isStartbuchRR) {
                  //show two photos in case of RR Startbuch
                $profile_htm .= '
                <div id="admProfilePhoto" class="groupBox">
                    <div class="groupBoxBody" style="text-align: center;">
                        <table width="100%" summary="Profilfoto" border="1" style="border:0px;" cellpadding="0" cellspacing="0" rules="none">
                            <tr>
                                <td>
                                  <img id="profile_picture" src="profile_photo_show.php?usr_id='.$user->getValue('usr_id').'" alt="'.$gL10n->get('PRO_CURRENT_PICTURE').' Herr" />
                                </td>
                                <!-- rmenken: added next 2 td -->
                                <td>&nbsp;
                                </td>
                                <td>
                                  <img id="profile_picture" src="profile_photo_showa.php?usr_id='.$user->getValue('usr_id').'" alt="'.$gL10n->get('PRO_CURRENT_PICTURE').' Dame" />
                                </td>                
                            </tr>';
                            // Nur berechtigte User duerfen das Profilfoto editieren
                            // rmenken: Vereine sollen keine Profilbilder aendern
                            if($gCurrentUser->editProfile($user) == true && !hasRole("Vereine"))
                            {
                                $profile_htm .= '
                                <tr>
                                    <td align="center">
                                        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_photo_edit.php?usr_id='.$user->getValue('usr_id').'"><img
                                            src="'.THEME_PATH.'/icons/photo_upload.png" alt="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').' Herr" title="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').' Herr" /></a>';
                                    //Dass Bild kann natürlich nur gelöscht werden, wenn entsprechende Rechte bestehen
                                    if((strlen($user->getValue('usr_photo')) > 0 && $gPreferences['profile_photo_storage'] == 0)
                                      || file_exists(SERVER_PATH. '/adm_my_files/user_profile_photos/'.$user->getValue('usr_id').'.jpg') && $gPreferences['profile_photo_storage'] == 1 )
                                    {
                                        $profile_htm .= '<a class="iconLink" rel="lnkDelete" href="'.$g_root_path.'/adm_program/system/popup_message.php?type=pro_pho&amp;element_id=no_element'.
                                            '&amp;database_id='.$user->getValue('usr_id').'"><img src="'. THEME_PATH. '/icons/delete.png"
                                            alt="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').' Herr" title="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').' Herr" /></a>';
                                    }
                                $profile_htm .= '</td>
                                <td>&nbsp;
                                </td>
                                    <td align="center">
                                        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_photo_edita.php?usr_id='.$user->getValue('usr_id').'"><img
                                            src="'.THEME_PATH.'/icons/photo_upload.png" alt="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').' Dame" title="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').' Dame" /></a>';
                                    //Dass Bild kann natürlich nur gelöscht werden, wenn entsprechende Rechte bestehen
                                    if((strlen($user->getValue('usr_photo')) > 0 && $gPreferences['profile_photo_storage'] == 0)
                                      || file_exists(SERVER_PATH. '/adm_my_files/user_profile_photos/'.$user->getValue('usr_id').'a.jpg') && $gPreferences['profile_photo_storage'] == 1 )
                                    {
                                        $profile_htm .= '<a class="iconLink" rel="lnkDelete" href="'.$g_root_path.'/adm_program/system/popup_message.php?type=pro_pho&amp;element_id=no_element'.
                                            '&amp;database_id='.$user->getValue('usr_id').'"><img src="'. THEME_PATH. '/icons/delete.png"
                                            alt="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').' Dame" title="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').' Dame" /></a>';
                                    }                
                                $profile_htm .= '</tr>';
                            }
                        $profile_htm .= '</table>
                    </div>
                </div>';    
    }
                else {
    //show normal = one photo in any other case
                $profile_htm .= '
                <div id="admProfilePhoto" class="groupBox">
                    <div class="groupBoxBody" style="text-align: center;">
                        <table width="100%" summary="Profilfoto" border="0" style="border:0px;" cellpadding="0" cellspacing="0" rules="none">
                            <tr>
                                <td>
                                	<img id="profile_picture" src="profile_photo_show.php?usr_id='.$user->getValue('usr_id').'" alt="'.$gL10n->get('PRO_CURRENT_PICTURE').'" />
                                </td>
                            </tr>';
                             // Nur berechtigte User duerfen das Profilfoto editieren
                            if($gCurrentUser->editProfile($user) == true && !hasRole("Vereine"))
                            {
                                $profile_htm .= '
                                <tr>
                                    <td align="center">
                                        <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_photo_edit.php?usr_id='.$user->getValue('usr_id').'"><img
                                            src="'.THEME_PATH.'/icons/photo_upload.png" alt="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').'" title="'.$gL10n->get('PRO_CHANGE_PROFILE_PICTURE').'" /></a>';
                                    //Dass Bild kann natürlich nur gelöscht werden, wenn entsprechende Rechte bestehen
                                    if((strlen($user->getValue('usr_photo')) > 0 && $gPreferences['profile_photo_storage'] == 0)
                                    	|| file_exists(SERVER_PATH. '/adm_my_files/user_profile_photos/'.$user->getValue('usr_id').'.jpg') && $gPreferences['profile_photo_storage'] == 1 )
                                    {
                                        $profile_htm .= '<a class="iconLink" rel="lnkDelete" href="'.$g_root_path.'/adm_program/system/popup_message.php?type=pro_pho&amp;element_id=no_element'.
                                            '&amp;database_id='.$user->getValue('usr_id').'"><img src="'. THEME_PATH. '/icons/delete.png" 
                                            alt="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').'" title="'.$gL10n->get('PRO_DELETE_PROFILE_PICTURE').'" /></a>';
                                    }
                                $profile_htm .= '</td>
                                </tr>';
                            }
                        $profile_htm .= '</table>
                </div>
                </div>';
     }
        $profile_htm .= '
            </div>
        </div>

        <div style="clear: left; font-size: 1pt;">&nbsp;</div>';

        // *******************************************************************************
        // Schleife ueber alle Kategorien und Felder ausser den Stammdaten
        // *******************************************************************************

        $category = '';
        $FieldCodeArray = array();
        foreach($gProfileFields->mProfileFields as $field){
                
        //if($gCurrentUser->isWebmaster()){print_r($field->getValue('cat_name'));echo" ::DEBUG:usf_cat_name<br>";}
                                
        if($isVerein &&
          ($field->getValue('cat_name') == ('Stammdaten Startbuch')  ||
           $field->getValue('cat_name') == ('Stammdaten Lizenzen')   ||
           $field->getValue('cat_name') == ('Stammdaten Herr')       ||
           $field->getValue('cat_name') == ('Stammdaten Dame')       ||
          ($field->getValue('cat_name') == ('Stammdaten Gütesiegel') && !($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle"))) || 
           $field->getValue('cat_name') == ('Stammdaten Boogie-Woogie')))
        { //do not show 'Stammdaten Startbuch' and 'Stammdaten Boogie-Woogie' in case of Verein
        }
        else if(!$isStartbuchBW && $field->getValue('cat_name') == ('Stammdaten Boogie-Woogie'))
        { //do not show 'Stammdaten Boogie-Woogie' and 'Stammdaten Lizenzen' in case of Startbuch 
        }
        else if($isStartbuchRR && 
               ($field->getValue('cat_name') == ('Stammdaten Lizenzen') ||
                $field->getValue('cat_name') == ('Stammdaten Formation') ||
                $field->getValue('cat_name') == ('Stammdaten Gütesiegel') ||
                $field->getValue('cat_name') == ('Akrobatiklisten')))
        { //do not show 'Stammdaten Lizenzen' 'Stammdaten Formation' 'Akrobatiklisten' in case of RR-Startbuch 
        }
        else if($isStartbuchFormation && 
               ($field->getValue('cat_name') == ('Stammdaten Lizenzen') ||
                $field->getValue('cat_name') == ('Stammdaten Herr') || 
                $field->getValue('cat_name') == ('Stammdaten Dame') || 
                $field->getValue('cat_name') == ('Stammdaten Gütesiegel') ||
                $field->getValue('cat_name') == ('Akrobatiklisten')))
        { //do not show 'Stammdaten Lizenzen' in case of Formation-Startbuch 
        }
        else if($isStartbuchBW && 
               ($field->getValue('cat_name') == ('Stammdaten Lizenzen') ||
                $field->getValue('cat_name') == ('Stammdaten Formation') ||
                $field->getValue('cat_name') == ('Stammdaten Gütesiegel') ||
                $field->getValue('cat_name') == ('Akrobatiklisten')))
        { //do not show 'Stammdaten Lizenzen' in case of BW-Startbuch 
        }
        else if($isPerson && 
               ($field->getValue('cat_name') == ('Stammdaten Startbuch') ||
                $field->getValue('cat_name') == ('Stammdaten Lizenzen') ||
                $field->getValue('cat_name') == ('Stammdaten Herr') || 
                $field->getValue('cat_name') == ('Stammdaten Dame') || 
                $field->getValue('cat_name') == ('Stammdaten Gütesiegel')))
        { //do not show 'Stammdaten Lizenzen' in case of Person(Mitglied) 
        }
        else if($isLizenzler && 
                 ($field->getValue('cat_name') == ('Stammdaten Startbuch') ||
                  $field->getValue('cat_name') == ('Stammdaten Herr') || 
                  $field->getValue('cat_name') == ('Stammdaten Dame') || 
                  $field->getValue('cat_name') == ('Stammdaten Gütesiegel')))
        { //do not show 'Stammdaten Lizenzen' in case of Person(Mitglied) 
        }
  
        else
        {  
        {
            // Felder der Kategorie Stammdaten wurde schon angezeigt, nun alle anderen anzeigen
            // versteckte Felder nur anzeigen, wenn man das Recht hat, dieses Profil zu editieren
            if($field->getValue('cat_name_intern') != 'MASTER_DATA'
            && (  $gCurrentUser->editProfile($user) == true
               || ($gCurrentUser->editProfile($user) == false && $field->getValue('usf_hidden') == 0 )))
            {
                // show new category header if new category and field has value or is a checkbox field
            // drbv: or is category Akrobatikmeldung without Startbuch-S    
            if($category != $field->getValue('cat_name') && 
                 (strlen($user->getValue($field->getValue('usf_name_intern'))) > 0 || 
                 ($field->getValue('usf_type') == 'CHECKBOX') || 
                 ($field->getValue('cat_name') == 'Akrobatikmeldung' && 
                 ($class_name == 'Startbuch RR-J' || $class_name == 'Startbuch RR-C' || 
                  $class_name == 'Startbuch RR-B' || $class_name == 'Startbuch RR-A')))){
      
              if(strlen($category) > 0){
                        // div-Container groupBoxBody und groupBox schliessen
                $profile_htm .= '</ul></div></div>';
                    }
              
                    $category = $field->getValue('cat_name');

              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-S') {
                $akro_class = 'AkromeldungS';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-J') {
                $akro_class = 'AkromeldungJ';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-C') {
                $akro_class = 'AkromeldungC';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-C-Int') {
                $akro_class = 'AkromeldungCInt';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-B') {
                $akro_class = 'AkromeldungB';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch RR-A') {
                $akro_class = 'AkromeldungA';
              }
              if ($category == 'Akrobatikmeldung' && $class_name == 'Startbuch Formation Master RR') {
                $akro_class = 'AkromeldungF';
              }
                
              //$cat_header = '';
              $profile_htm .= '<div class="groupBox">
                        <div class="groupBoxHeadline">
                            <div style="float: left;">'.$field->getValue('cat_name').'</div>';
                            // Nur berechtigte User duerfen ein Profil editieren
              if($gCurrentUser->editProfile($user) == true){
                $profile_htm .= '
                <div style="text-align: right;">';
                if($category == 'Stammdaten Formation'){
                  $profile_htm .= '
                    <a target="_blank" class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile.php?user_id='.$user->getValue('usr_id').'&mode=pdf"><img
                    src="'.THEME_PATH.'/icons/pdf.png" alt="Einlassformular drucken" title="Einlassformular drucken" /></a>';
                }                               
                $profile_htm .= '
                                    <a class="iconLink" href="'.$g_root_path.'/adm_program/modules/profile/profile_new.php?user_id='.$user->getValue('usr_id').'#cat-'.$field->getValue('cat_id').'"><img
                                        src="'.THEME_PATH.'/icons/edit.png" alt="'.$gL10n->get('SYS_EDIT_VAR',$field->getValue('cat_name')).'" title="'.$gL10n->get('SYS_EDIT_VAR',$field->getValue('cat_name')).'" /></a>
                                </div>';
                            }
                                
//            if($category == 'Akrobatikmeldung'){
//              echo '<div style="text-align: center; font-size: 10pt;">Aktueller Hinweis!<br>Wenn die Akrobatikfelder leer sind bzw. Zahlen anzeigen, dann m&uuml;ssen diese neu zugeordnet werden.</div>';
//            }               

              $profile_htm .= '</div>';              
              $profile_htm .= '<div class="groupBoxBody">
                            <ul class="formFieldList">';
            }//debug <ul class="formFieldList" opsa="'.$akro_class.'" opsb="'.$category.'" opsc="'.$isStartbuchRR.'" opsd="'.$class_name.'">';

            //rmenken: get access to AkroValues:
            if ($akro_class == 'AkromeldungJ'){
              $AkroListValues = $gProfileFields->getProperty('AKROBATIKLISTE-J', 'usf_value_list');
            }
            if ($akro_class == 'AkromeldungC'){
              $AkroListValues = $gProfileFields->getProperty('AKROBATIKLISTE-C', 'usf_value_list');
                }
            if ($akro_class == 'AkromeldungB'){
              $AkroListValues = $gProfileFields->getProperty('AKROBATIKLISTE-B', 'usf_value_list');
            }                
            if ($akro_class == 'AkromeldungA'){
              $AkroListValues = $gProfileFields->getProperty('AKROBATIKLISTE-A', 'usf_value_list');
            }
            if ($akro_class == 'AkromeldungF'){
              $AkroListValues = $gProfileFields->getProperty('AKROBATIKLISTE-F', 'usf_value_list');
            }
            //print_r($AkroListValues);echo" ::DEBUG:AkroListValues<br>";

            // show html of field, if user has a value for that field or it's a checkbox field or
            // in case of acrobatic step through the complete list 
            if(strlen($user->getValue($field->getValue('usf_name_intern'))) > 0 || 
                      $field->getValue('usf_type') == 'CHECKBOX' || 
                      $category == 'Akrobatikmeldung')
                {
              //if($gCurrentUser->isWebmaster()){print_r($field->getValue('usf_name_intern'));echo" ::DEBUG:usf_name_intern<br>";}
      
              //STARTKLASSE              
              //-----------
              if($field->getValue('usf_name_intern') == 'STARTKLASSE'){
                
                $herr_geburt = getFieldCode('GEBURTSTAG_HERR', $user, $akro_class, TRUE);
                $pdf_html['GEBURTSTAG_HERR'] = getFieldCode('GEBURTSTAG_HERR', $user, $akro_class, TRUE);
                $dame_geburt = getFieldCode('GEBURTSTAG_DAME', $user, $akro_class, TRUE);
                $pdf_html['GEBURTSTAG_DAME'] = getFieldCode('GEBURTSTAG_DAME', $user, $akro_class, TRUE);
                
                if(getFieldCode('STARTKLASSE', $user, $akro_class, TRUE) == 'RR_S'){
                  $sbisjahr = min(substr($herr_geburt,6,4),substr($dame_geburt,6,4))+14;
                  $profile_htm .= '<li>
                     <dl>
                       <dt>Startklasse:</dt>
                       <dd>RR_S (bis Ende&nbsp;'.$sbisjahr.' m&ouml;glich)</dd>
                     </dl>       
                   </li>';
                } elseif(getFieldCode('STARTKLASSE', $user, $akro_class, TRUE) == 'RR_J'){
                  $sbisjahr = min(substr($herr_geburt,6,4),substr($dame_geburt,6,4))+17;
                  $profile_htm .= '<li>
                     <dl>
                       <dt>Startklasse:</dt>
                       <dd>RR_J (bis Ende&nbsp;'.$sbisjahr.' m&ouml;glich)</dd>
                     </dl>       
                   </li>';
                } else {
                  $profile_htm .= getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class);
                  $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
                }
              //AKROBATIK VR1              
              //-------------
              } elseif($field->getValue('usf_name_intern') == 'AKROBATIK_1_-_VORRUNDE'){
                  $akroset = FALSE;
                  $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
                  
                  $akroid    = explode(":", getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE));
                  $akrosuche = preg_grep("/$akroid[0]/i",$AkroListValues);
                  //$akropktvr = array(1 => explode(":",array_shift($akrosuche))); 
                  $akropkt   = explode(":",array_shift($akrosuche)); 
                  $akrosum   = 0;
                  unset($akropktvr);
                  unset($akropktzw);
                  unset($akropkter);
                  $akropktvr = array(1 => str_replace(',','.',$akropkt[1]));
                  
                  if(getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE)){
                    $akroset = TRUE;  
                    $profile_htm .= '
                      <table width=100% border=0>
                        <tr>
                          <td width=30%>'.$field->getValue('usf_name').'</td>
                          <td width=60%>'.getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE).'</td>
                          <td width=10% align="right">'.$akropkt[1].'</td>
                        </tr>';
                }
              //AKROBATIK VR2 bis ERE              
              //---------------------
              } elseif($field->getValue('usf_name_intern') == 'AKROBATIK_2_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_3_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_4_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_5_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_6_-_VORRUNDE'  ||                      
                       $field->getValue('usf_name_intern') == 'AKROBATIK_7_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_8_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_E_-_VORRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_E2_-_VORRUNDE' ||                      
                       $field->getValue('usf_name_intern') == 'AKROBATIK_2_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_3_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_4_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_5_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_6_-_ZWRUNDE'   ||                      
                       $field->getValue('usf_name_intern') == 'AKROBATIK_7_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_8_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_E_-_ZWRUNDE'   ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_E2_-_ZWRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_2_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_3_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_4_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_5_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_6_-_ENDRUNDE'  ||                      
                       $field->getValue('usf_name_intern') == 'AKROBATIK_7_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_8_-_ENDRUNDE'  ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_E_-_ENDRUNDE'){
                  $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
                  $akroid    = explode(":", getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE));
                  $akrosuche = preg_grep("/$akroid[0]/i",$AkroListValues);
                  $akropkt   = explode(":",array_shift($akrosuche));
                       
                  if(substr($field->getValue('usf_name_intern'),14,3) == 'VOR'){       
                    $akropktvr[substr($field->getValue('usf_name_intern'),10,1)] = str_replace(',','.',$akropkt[1]);                    
                  }
                  if(substr($field->getValue('usf_name_intern'),14,3) == 'ZWR'){       
                    $akropktzw[substr($field->getValue('usf_name_intern'),10,1)] = str_replace(',','.',$akropkt[1]);
                  }
                  if(substr($field->getValue('usf_name_intern'),14,3) == 'END'){       
                    $akropkter[substr($field->getValue('usf_name_intern'),10,1)] = str_replace(',','.',$akropkt[1]);
                  }
                         
                  if($field->getValue('usf_name_intern') != 'AKROBATIK_E_-_VORRUNDE' &&
                     $field->getValue('usf_name_intern') != 'AKROBATIK_E1_-_VORRUNDE' &&
                     $field->getValue('usf_name_intern') != 'AKROBATIK_E_-_ZWRUNDE' &&
                     $field->getValue('usf_name_intern') != 'AKROBATIK_E1_-_ZWRUNDE' &&
                     $field->getValue('usf_name_intern') != 'AKROBATIK_E_-_ENDRUNDE'){       
                  }
                  if(getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE)){       
                    $profile_htm .= '
                      <tr>
                        <td width=30%>'.$field->getValue('usf_name').'</td>
                        <td width=60%>'.getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE).'</td>
                        <td width=10% align="right">'.$akropkt[1].'</td>
                      </tr>';
            }
              //AKROBATIK ZW1 und ER1              
              //---------------------
              } elseif($field->getValue('usf_name_intern') == 'AKROBATIK_1_-_ZWRUNDE' ||
                       $field->getValue('usf_name_intern') == 'AKROBATIK_1_-_ENDRUNDE'){
                  $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
                  $akroid    = explode(":", getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE));
                  $akrosuche = preg_grep("/$akroid[0]/i",$AkroListValues);
                  $akropkt   = explode(":",array_shift($akrosuche));

                  if(substr($field->getValue('usf_name_intern'),14,3) == 'ZWR'){       
                    $akropktzw[substr($field->getValue('usf_name_intern'),10,1)] = str_replace(',','.',$akropkt[1]);
                    $akrosum = 0;
                    if($akro_class == 'AkromeldungJ') $akrosum = array_sum(array_slice($akropktvr,0,3));
                    if($akro_class == 'AkromeldungC') $akrosum = array_sum(array_slice($akropktvr,0,4));
                    if($akro_class == 'AkromeldungB') $akrosum = array_sum(array_slice($akropktvr,0,5));
                    if($akro_class == 'AkromeldungA') $akrosum = array_sum(array_slice($akropktvr,0,5));
                    if($akro_class == 'AkromeldungF') $akrosum = array_sum(array_slice($akropktvr,0,8));                    
                  }
                  if(substr($field->getValue('usf_name_intern'),14,3) == 'END'){       
                    $akropkter[substr($field->getValue('usf_name_intern'),10,1)] = str_replace(',','.',$akropkt[1]);
                    $akrosum = 0;
                    if($akro_class == 'AkromeldungJ') $akrosum = array_sum(array_slice($akropktzw,0,3));
                    if($akro_class == 'AkromeldungC') $akrosum = array_sum(array_slice($akropktzw,0,4));
                    if($akro_class == 'AkromeldungB') $akrosum = array_sum(array_slice($akropktzw,0,5));
                    if($akro_class == 'AkromeldungA') $akrosum = array_sum(array_slice($akropktzw,0,5));
                    if($akro_class == 'AkromeldungF') $akrosum = array_sum(array_slice($akropktzw,0,8));                    
                  }
                                       
                  if(getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE)){       
                    $profile_htm .= '
                      <tr><td></td><td align="right"><i>Akrobatischer Vorwert:</i><td align="right"><b><i>'.number_format($akrosum,2).'</i></b></td></tr>
                      <tr>
                        <td width=30%>'.$field->getValue('usf_name').'</td>
                        <td width=60%>'.getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE).'</td>
                        <td width=10% align="right">'.$akropkt[1].'</td>
                      </tr>';
        }
              //AKROBATIK ERE2              
              //--------------
              } elseif($field->getValue('usf_name_intern') == 'AKROBATIK_E2_-_ENDRUNDE'){
                  $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
                  $akroid    = explode(":", getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE));
                  $akrosuche = preg_grep("/$akroid[0]/i",$AkroListValues);
                  $akropkt   = explode(":",array_shift($akrosuche));                    

                  if(getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE)){
                    $profile_htm .= '                    
                      <tr>
                        <td width=30%>'.$field->getValue('usf_name').'</td>
                        <td width=60%>'.getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE).'</td>
                        <td width=10% align="right">'.$akropkt[1].'</td>
                      </tr>';
                  }
                  $akrosum = 0;
                  if($akro_class == 'AkromeldungJ') $akrosum = array_sum(array_slice($akropkter,0,3));
                  if($akro_class == 'AkromeldungC') $akrosum = array_sum(array_slice($akropkter,0,4));
                  if($akro_class == 'AkromeldungB') $akrosum = array_sum(array_slice($akropkter,0,6));
                  if($akro_class == 'AkromeldungA') $akrosum = array_sum(array_slice($akropkter,0,6));
                  if($akro_class == 'AkromeldungF') $akrosum = array_sum(array_slice($akropkter,0,8));
                  //if($gCurrentUser->isWebmaster()){print_r($akropkter);echo" ::DEBUG:akropkter<br>";}
                                
                  $profile_htm .= '<tr><td></td><td align="right">';
                  if($akroset == TRUE) $profile_htm .= '<i>Akrobatischer Vorwert:</i></td><td align="right"><b><i>'.number_format($akrosum,2).'</i></b>';
                  $profile_htm .= '</td></tr></table><br>';
              //Alles Andere              
              //------------
              } else {
                $profile_htm .= getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class);
                $pdf_html[$field->getValue('usf_name_intern')] = getFieldCode($field->getValue('usf_name_intern'), $user, $akro_class, TRUE);
              }              
            }                                                
          }
        }
  }
}//end foreach: Schleife ueber alle Kategorien und Felder ausser den Stammdaten

if(strlen($category) > 0){
            // div-Container groupBoxBody und groupBox schliessen
  $profile_htm .= '</ul></div></div>';
}
  
// Wertungen:
// ----------
$sqlab      = 'SELECT * 
               FROM paare m JOIN Turnier f
               ON m.turniernummer = f.turniernummer
               WHERE m.startbuch = '.$user->getValue('LAST_NAME').' OR 
               m.boogie_sb_herr  = '.$user->getValue('LAST_NAME').' OR 
               m.boogie_sb_dame  = '.$user->getValue('LAST_NAME').' ORDER BY f.datum DESC'; 
$platz      =  mysqli_query(DRBVdb(), $sqlab);
$inserthtml = "";
$id         = '';

if(!$isVerein){ // if(!$isVerein)
while($platzierungen = mysqli_fetch_array($platz)){
  $id++;
  $sqlab        = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $platzierungen["turniernummer"]; 
  $turnier      = mysqli_query(DRBVdb(), $sqlab);
  
  $sqlab        = 'SELECT * FROM majoritaet WHERE turniernummer = ' . $platzierungen["turniernummer"] . ' AND TP_ID = ' . $platzierungen["paar_id_tlp"]  . ' AND RT_ID = ' . $platzierungen["RT_ID_Ausgeschieden"];
  $ergebnis_pkt = mysqli_query(DRBVdb(), $sqlab);
  //if($gCurrentUser->isWebmaster()){print_r($ergebnis_pkt);echo' ergebnis_pkt<br>';}
  
  unset($runden_pkt_html);
  while($runden_pkt = mysqli_fetch_array($ergebnis_pkt)){
    //if($gCurrentUser->isWebmaster()){print_r($runden_pkt);echo' runden_pkt<br>';}
    if($runden_pkt[WR1] == 0){
      //Fusstechnikrundenergebnis
      $erg_pkt_ft = $runden_pkt[WR2];
      $erg_pkt_ta = 0;    
      $erg_pkt_ak = 0;    
      $erg_pkt_su = $runden_pkt[WR7];    
    } else {
      //Akrobatikrundenergebnis
      $erg_pkt_ft = $runden_pkt[WR1];
      $erg_pkt_ta = $runden_pkt[WR2];    
      $erg_pkt_ak = $runden_pkt[WR3];    
      $erg_pkt_su = $runden_pkt[WR7];    
    }
  }
  //if($gCurrentUser->isWebmaster()){
  //  print_r($erg_pkt_ft);echo' erg_pkt_ft<br>';
  //  print_r($erg_pkt_ta);echo' erg_pkt_ta<br>';
  //  print_r($erg_pkt_ak);echo' erg_pkt_ak<br>';
  //  print_r($erg_pkt_su);echo' erg_pkt_su<br>';
  //}
  
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
  
  $inserthtml  .= '
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fade'.$t_nummer.$id.'\').click(function(){$(\'#ref'.$t_nummer.$id.'\').toggle(\'slow\');
      })
      });
    </script>    
    <li><dl>
        <dt>'.$datum->format("d.m.Y").'</dt>
        <dd>
           <a class="admLinkWtgErgKrz" href="#'.$t_nummer.$id.'" id="fade'.$t_nummer.$id.'"><img src="'. THEME_PATH. '/icons/add.png" alt="WrtErgKrz" />
           </a>'.utf8_encode($turnierdaten["turniername"]).' / <b>Platz '.$platzierungen["platz"].'</b>
        </dd>
    </dl></li>
    <div style="font-size: 12px;display:none;" id="ref'.$t_nummer.$id.'">
       <li><dl>
          <table border=0>
            <tr style="background-color: orange;">
               <td align=center><b>Platz</b></td>
               <td align=center><b>RnglPkt</b></td>';
               if($platzierungen["startklasse"] == "RR_B"  || $platzierungen["startklasse"] == "RR_A"){
                 $inserthtml  .= '<td align=center><b>&sum; FussT</b></td>';
               }
               if($isBoogieWoogie){
                 $inserthtml .= '
                 <td align=center><b>WR1</b></td>
                 <td align=center><b>WR2</b></td>
                 <td align=center><b>WR3</b></td>
                 <td align=center><b>WR4</b></td>
                 <td align=center><b>WR5</b></td>
                 <td align=center><b>WR6</b></td>
                 <td align=center><b>WR7</b></td>';               
               } else {
                 $inserthtml .= '
                 <td align=center><b>TWR1</b></td>
                 <td align=center><b>TWR2</b></td>
                 <td align=center><b>TWR3</b></td>
                 <td align=center><b>TWR4</b></td>';
                 if($isFormation && !$isAkrobatik){
                   $inserthtml .= '
                   <td align=center><b>TWR5</b></td>
                   <td align=center><b>TWR6</b></td>';
                 }
                 $inserthtml .= '  
                 <td align=center><b>&sum; Tanz</b></td>';
                 if($isAkrobatik){
                   $inserthtml .= '                 
                   <td align=center><b>AKVW</b></td>
                   <td align=center><b>AWR1</b></td>
                   <td align=center><b>AWR2</b></td>
                   <td align=center><b>AWR3</b></td>
                   <td align=center><b>AWR4</b></td>
                   <td align=center><b>&sum; Akrobatik</b></td>';
                 }               
               }
               $inserthtml .= '                                  
               <td align=center><b>Abz&uuml;ge</b></td>
               <td align=center><b>Ergebnis</b></td>
               <td align=center><b>Details</b></td>
            </tr>
            <tr style="background-color: #eaeaea;">
               <td align=center><b>'.$platzierungen["platz"].'</b></td>
               <td align=center>'.$platzierungen["rl_punkte"].'</td>';
               if($platzierungen["startklasse"] == "RR_B"  || $platzierungen["startklasse"] == "RR_A"){
                 $inserthtml .='<td align=center><b>'.$erg_pkt_ft.'</b></td>';
               }
               if($isBoogieWoogie){
                 $inserthtml .='
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>';                
               } else {
                 $inserthtml .='
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>
                 <td align=center style="color: #41a0fa;">?</td>';
                 if($isFormation && !$isAkrobatik){
                   $inserthtml .= '
                   <td align=center style="color: #41a0fa;">?</td>
                   <td align=center style="color: #41a0fa;">?</td>';
                 }                 
                 $inserthtml .='  
                 <td align=center><b>'.$erg_pkt_ta.'</b></td>';
                 if($isAkrobatik){                 
                   $inserthtml .='  
                   <td align=center>?</td>
                   <td align=center style="color: #41a0fa;">?</td>
                   <td align=center style="color: #41a0fa;">?</td>
                   <td align=center style="color: #41a0fa;">?</td>
                   <td align=center style="color: #41a0fa;">?</td>
                   <td align=center><b>'.$erg_pkt_ak.'</b></td>';
                 }               
               }
               $inserthtml .='  
               <td align=center style="color: #ed595d;">?</td>
               <td align=center><b>'.$erg_pkt_su.'</b></td>
               <td align=center><a class="admLinkWtgErgKrz" href="'.$g_root_path.'/adm_program/modules/profile/profile_wrtg.php?user_id='.$user->getValue('usr_id').'&tnrsel='.$t_nummer.'">link</a></td>
            </tr>
          </table>                                                                                       
       </dl></li>
    </div>';
}// end while ($platzierungen = mysqli_fetch_array($platz))                 
}// end if (!isVerein)
  
if ($inserthtml == ""){
  $inserthtml = '<li><dl><dt>'.date("d.m.Y",time()).'</dt><dd><b><i>Es liegen noch keine Turnierergebnisse vor.</i></b></dd></dl></li>'; 
}   

if(($gCurrentUser->isWebmaster() || hasRole("Vereine")) && ($isStartbuchRR || $isStartbuchBW || $isStartbuchFormation))  
{
        $profile_htm .= '<div class="groupBox">
               <div class="groupBoxHeadline">
                  <div style="float: left;">Wertungen</div>
               </div>
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   '.$inserthtml.'
                 </ul>
               </div>
             </div>';
}  
// -------------
// WertungenEnde

// WertungenWR
// -----------
if($isLizenzler && ($gCurrentUser->isWebmaster() || hasRole("Wertungsrichter") || hasRole("Trainer-A") || hasRole("Trainer-B")))  
//if($gCurrentUser->isWebmaster())  
{
  $inserthtml      = '';
  $anzahl_turniere = 0;
  unset($_SESSION['profile_user_liznr']);

  if(hasRole("Wertungsrichter")){
    if($user->getValue('WERTUNGSRICHTER_RR_LIZENZ-NR.')){
      $user_liznr = $user->getValue('WERTUNGSRICHTER_RR_LIZENZ-NR.');
    } else {
      $user_liznr = $user->getValue('WERTUNGSRICHTER_BW_LIZENZ-NR.');
    }
  } else {
    $user_liznr = '7'.$user->getValue('TRAINERLIZENZ_1_LIZENZ-NR.');
  }  
  //print_r($user_liznr);echo' user_liznr<br>';
  $_SESSION['profile_user_liznr'] = $user_liznr;
  
  // Gewertete Turniere auswählen
  // Webmaster kann WR Lizenz zum debuggen in den globalen Einstellungen setzen
  if($gCurrentUser->isWebmaster() && $gPreferences['drbv_set_wr_lizenz_nr'] != ''){
    $sqlab = 'SELECT turniernummer,lizenznummer FROM wertungsrichter WHERE lizenznummer = '.$gPreferences['drbv_set_wr_lizenz_nr'].' ORDER BY turniernummer DESC';
  } else {
    $sqlab = 'SELECT turniernummer,lizenznummer FROM wertungsrichter WHERE lizenznummer = "'.$user_liznr.'" ORDER BY turniernummer DESC';
  }    
  $turnier_gewertet = mysqli_query(DRBVdb(), $sqlab);
  //print_r($turnier_gewertet);echo' turnier_gewertet<br>';
  
  while($trn_info = mysqli_fetch_array($turnier_gewertet)){
    //print_r($trn_info);echo' trn_info<br>';
    $sqlab               = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$trn_info["0"].''; 
    $turnier             = mysqli_query(DRBVdb(), $sqlab);
    $getTurniereGewertet = mysqli_fetch_row($turnier);    
    //print_r($getTurniereGewertet);echo' getTurniereGewertet<br>';
       
    $sqlab = 'SELECT wr_id_tlp FROM wertungsrichter WHERE turniernummer = '.$getTurniereGewertet[0].' AND lizenznummer = "'.$user_liznr.'"';
    $wr_id = mysqli_query(DRBVdb(), $sqlab);
    $wr_id_tlp = mysqli_fetch_row($wr_id);
    //print_r($wr_id_tlp);echo' wr_id_tlp<br>';
    
    $anzahl_turniere = $anzahl_turniere +1;
    $datum           = new DateTime($getTurniereGewertet[2]);
    
    $inserthtml .= '
      <li><dl>
        <dt></b>'.$datum->format("d.m.Y").'</dt>
        <dd>
          <a href="'.$g_root_path.'/adm_program/modules/profile/profile_wrtg4wr.php?user_id='.$user->getValue('usr_id').'&tnrsel='.$getTurniereGewertet[0].'">'.utf8_encode($getTurniereGewertet[1]).'</a>
        </dd>
        </dl>
      </li>';    
  }
  
  if($anzahl_turniere == 0){
    $inserthtml  = '<li><dl><dt>'.date("d.m.Y",time()).'</dt><dd><b><i>Es liegen noch keine Turnierergebnisse vor.</i></b></dd></dl></li>';   
  } else {
    $inserthtml .= '
      <li><dl>
        <dt></dt>
        <dd>
          Es wurde';
      if($anzahl_turniere > 1) $inserthtml .= 'n';    
    $inserthtml .= ' '.$anzahl_turniere.' Turnier';
      if($anzahl_turniere > 1) $inserthtml .= 'e'; 
    $inserthtml .= ' gewertet!
        </dd>
        </dl>
      </li>';        
  } 
   
  $profile_htm .= '<div class="groupBox">
         <div class="groupBoxHeadline">
           <div style="float: left;">Gewertete Turniere</div>
         </div>
         <div class="groupBoxBody">
           <ul class="formFieldList">
             '.$inserthtml.'
           </ul>
         </div>
       </div>';
        }
// ------------------
// WertungenWREnde

  
  

        if($gPreferences['profile_show_roles'] == 1)
        {
            // *******************************************************************************
            // Berechtigungen-Block
            // *******************************************************************************

            //Array mit allen Berechtigungen
            $authorizations = Array('rol_assign_roles','rol_approve_users','rol_edit_user',
                                    'rol_mail_to_all','rol_profile','rol_announcements',
                                    'rol_dates','rol_photo','rol_download','rol_guestbook',
                                    'rol_guestbook_comments','rol_weblinks', 'rol_all_lists_view');

            //Abfragen der aktiven Rollen mit Berechtigung und Schreiben in ein Array
            foreach($authorizations as $authorization_db_name)
            {
                $sql = 'SELECT rol_name
                          FROM '. TBL_MEMBERS. ', '. TBL_ROLES. ', '. TBL_CATEGORIES.'
                         WHERE mem_rol_id = rol_id
                           AND mem_begin <= \''.DATE_NOW.'\'
                           AND mem_end    > \''.DATE_NOW.'\'
                           AND mem_usr_id = '.$user->getValue('usr_id').'
                           AND rol_valid  = 1
                           AND rol_cat_id = cat_id
                           AND (  cat_org_id = '. $gCurrentOrganization->getValue('org_id'). '
                               OR cat_org_id IS NULL )
                           AND '.$authorization_db_name.' = 1
                         ORDER BY cat_org_id, cat_sequence, rol_name';
                $result_role = $gDb->query($sql);
                $berechtigungs_Herkunft[$authorization_db_name] = NULL;

                while($row = $gDb->fetch_array($result_role))
                {
                    $berechtigungs_Herkunft[$authorization_db_name] = $berechtigungs_Herkunft[$authorization_db_name].', '.$row['rol_name'];
                }
            }

            $profile_htm .= '<div class="groupBox" id="profile_authorizations_box">
                     <div class="groupBoxHeadline">
                        <div style="float: left;">'.$gL10n->get('SYS_AUTHORIZATION').'&nbsp;</div>
                     </div>
                     <div class="groupBoxBody" onmouseout="profileJS.deleteShowInfo()">';
            //checkRolesRight($right)
              if($user->checkRolesRight('rol_assign_roles') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_assign_roles'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/roles.png"
                  alt="'.$gL10n->get('ROL_RIGHT_ASSIGN_ROLES').'" title="'.$gL10n->get('ROL_RIGHT_ASSIGN_ROLES').'" />';
              }
              if($user->checkRolesRight('rol_approve_users') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_approve_users'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/new_registrations.png"
                  alt="'.$gL10n->get('ROL_RIGHT_APPROVE_USERS').'" title="'.$gL10n->get('ROL_RIGHT_APPROVE_USERS').'" />';
              }
              if($user->checkRolesRight('rol_edit_user') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_edit_user'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/group.png"
                  alt="'.$gL10n->get('ROL_RIGHT_EDIT_USER').'" title="'.$gL10n->get('ROL_RIGHT_EDIT_USER').'" />';
              }

              if($user->checkRolesRight('rol_mail_to_all') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_mail_to_all'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/email.png"
                  alt="'.$gL10n->get('ROL_RIGHT_MAIL_TO_ALL').'" title="'.$gL10n->get('ROL_RIGHT_MAIL_TO_ALL').'" />';
              }
              if($user->checkRolesRight('rol_profile') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_profile'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/profile.png"
                  alt="'.$gL10n->get('ROL_RIGHT_PROFILE').'" title="'.$gL10n->get('ROL_RIGHT_PROFILE').'" />';
              }
              if($user->checkRolesRight('rol_announcements') == 1 && $gPreferences['enable_announcements_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_announcements'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/announcements.png"
                  alt="'.$gL10n->get('ROL_RIGHT_ANNOUNCEMENTS').'" title="'.$gL10n->get('ROL_RIGHT_ANNOUNCEMENTS').'" />';
              }
              if($user->checkRolesRight('rol_dates') == 1 && $gPreferences['enable_dates_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_dates'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/dates.png"
                  alt="'.$gL10n->get('ROL_RIGHT_DATES').'" title="'.$gL10n->get('ROL_RIGHT_DATES').'" />';
              }
              if($user->checkRolesRight('rol_photo') == 1 && $gPreferences['enable_photo_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_photo'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/photo.png"
                  alt="'.$gL10n->get('ROL_RIGHT_PHOTO').'" title="'.$gL10n->get('ROL_RIGHT_PHOTO').'" />';
              }
              if($user->checkRolesRight('rol_download') == 1 && $gPreferences['enable_download_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_download'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/download.png"
                  alt="'.$gL10n->get('ROL_RIGHT_DOWNLOAD').'" title="'.$gL10n->get('ROL_RIGHT_DOWNLOAD').'" />';
              }
              if($user->checkRolesRight('rol_guestbook') == 1 && $gPreferences['enable_guestbook_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_guestbook'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/guestbook.png"
                  alt="'.$gL10n->get('ROL_RIGHT_GUESTBOOK').'" title="'.$gL10n->get('ROL_RIGHT_GUESTBOOK').'" />';
              }
              if($user->checkRolesRight('rol_guestbook_comments') == 1 && $gPreferences['enable_guestbook_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_guestbook_comments'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/comments.png"
                  alt="'.$gL10n->get('ROL_RIGHT_GUESTBOOK_COMMENTS').'" title="'.$gL10n->get('ROL_RIGHT_GUESTBOOK_COMMENTS').'" />';
              }
              if($user->checkRolesRight('rol_weblinks') == 1 && $gPreferences['enable_weblinks_module'] > 0)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_weblinks'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/weblinks.png"
                  alt="'.$gL10n->get('ROL_RIGHT_WEBLINKS').'" title="'.$gL10n->get('ROL_RIGHT_WEBLINKS').'" />';
              }
              if($user->checkRolesRight('rol_all_lists_view') == 1)
              {
                  $profile_htm .= '<img onmouseover="profileJS.showInfo(\''.substr($berechtigungs_Herkunft['rol_all_lists_view'],2).'\')" class="iconInformation" src="'.THEME_PATH.'/icons/lists.png"
                  alt="'.$gL10n->get('ROL_RIGHT_ALL_LISTS_VIEW').'" title="'.$gL10n->get('ROL_RIGHT_ALL_LISTS_VIEW').'" />';
              }
              $profile_htm .= '</div><div><p id="anzeige">'.$gL10n->get('SYS_SET_BY').':</p></div>
              </div>';

            // *******************************************************************************
            // Rollen-Block
            // *******************************************************************************

            // Alle Rollen auflisten, die dem Mitglied zugeordnet sind
            $count_show_roles = 0;
            $result_role = getRolesFromDatabase($user->getValue('usr_id'));
            $count_role  = $gDb->num_rows($result_role);

            //Ausgabe
            $profile_htm .= '<div class="groupBox profileRolesBox" id="profile_roles_box">
                <div class="groupBoxHeadline">
                    <div style="float: left;">'.$gL10n->get('ROL_ROLE_MEMBERSHIPS').'&nbsp;</div>';
                        // Moderatoren & Gruppenleiter duerfen neue Rollen zuordnen
                        if($gCurrentUser->onlyAssignRoles())
                        {
                            $profile_htm .= '
                            <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
                            <script type="text/javascript">
                                    var calPopup = new CalendarPopup("calendardiv");
                                    calPopup.setCssPrefix("calendar");
                            </script>
                            <div style="text-align: right;">
                                <a rel="colorboxRoles" href="'.$g_root_path.'/adm_program/modules/profile/roles.php?usr_id='.$user->getValue('usr_id').'&amp;inline=1" title="'.$gL10n->get('ROL_ROLE_MEMBERSHIPS_CHANGE').'">
                                    <img src="'.THEME_PATH.'/icons/edit.png" title="'.$gL10n->get('ROL_ROLE_MEMBERSHIPS_CHANGE').'" alt="'.$gL10n->get('ROL_ROLE_MEMBERSHIPS_CHANGE').'" />
                                </a>
                            </div>';
                        }
                $profile_htm .= '</div>
				<div id="profile_roles_box_body" class="groupBoxBody">
					'.getRoleMemberships('role_list', $user, $result_role, $count_role, false).'
				</div>
			</div>';
			
            // *******************************************************************************
            // block with future memberships
            // *******************************************************************************

            $count_show_roles = 0;
            $result_role = getFutureRolesFromDatabase($user->getValue('usr_id'));
            $count_role  = $gDb->num_rows($result_role);
            $visible     = "";

            if($count_role == 0)
            {
                $visible = ' style="display: none;" ';
            }
            else
            {
                $profile_htm .= '<script type="text/javascript">profileJS.futureRoleCount="'.$count_role.'";</script>';  
            }
            $profile_htm .= '<div class="groupBox profileRolesBox" id="profile_future_roles_box" '.$visible.'>
                <div class="groupBoxHeadline">'.$gL10n->get('PRO_FUTURE_ROLE_MEMBERSHIP').'&nbsp;</div>
                <div id="profile_future_roles_box_body" class="groupBoxBody">
                    '.getRoleMemberships('future_role_list',$user,$result_role,$count_role,false).'
				</div>
			</div>';
        }

        if($gPreferences['profile_show_former_roles'] == 1)
        {
            // *******************************************************************************
            // Ehemalige Rollen Block
            // *******************************************************************************

            // Alle Rollen auflisten, die dem Mitglied zugeordnet waren

            $count_show_roles = 0;
            $result_role = getFormerRolesFromDatabase($user->getValue('usr_id'));
            $count_role  = $gDb->num_rows($result_role);
            $visible     = "";

            if($count_role == 0)
            {
                $visible = ' style="display: none;" ';
            }
            else
            {
                $profile_htm .= '<script type="text/javascript">profileJS.formerRoleCount="'.$count_role.'";</script>';  
            }
            $profile_htm .= '<div class="groupBox profileRolesBox" id="profile_former_roles_box" '.$visible.'>
				<div class="groupBoxHeadline">'.$gL10n->get('PRO_FORMER_ROLE_MEMBERSHIP').'&nbsp;</div>
                <div id="profile_former_roles_box_body" class="groupBoxBody">
                    '.getRoleMemberships('former_role_list',$user,$result_role,$count_role,false).'
				</div>
			</div>';
        }

        if($gPreferences['profile_show_extern_roles'] == 1
        && (  $gCurrentOrganization->getValue('org_org_id_parent') > 0
           || $gCurrentOrganization->hasChildOrganizations() ))
        {
            // *******************************************************************************
            // Rollen-Block anderer Organisationen
            // *******************************************************************************

            // list all roles where the viewed user has an active membership
            $sql = 'SELECT *
                      FROM '. TBL_MEMBERS. ', '. TBL_ROLES. ', '. TBL_CATEGORIES. ', '. TBL_ORGANIZATIONS. '
                     WHERE mem_rol_id = rol_id
                       AND mem_begin <= \''.DATE_NOW.'\'
                       AND mem_end   >= \''.DATE_NOW.'\'
                       AND mem_usr_id = '.$user->getValue('usr_id').'
                       AND rol_valid   = 1
					   AND rol_visible = 1
                       AND rol_cat_id  = cat_id
                       AND cat_org_id  = org_id
                       AND org_id    <> '. $gCurrentOrganization->getValue('org_id'). '
                     ORDER BY org_shortname, cat_sequence, rol_name';
            $result_role = $gDb->query($sql);

            if($gDb->num_rows($result_role) > 0)
            {
				$showRolesOtherOrganizations = false;
				$actualOrganization = 0;
				$role = new TableRoles($gDb);
				
				while($row = $gDb->fetch_array($result_role))
				{
					// if roles of new organization than read the rights of this organization
					if($actualOrganization != $row['org_id'])
					{
						$gCurrentUser->setOrganization($row['org_id']);
						$actualOrganization = $row['org_id'];
					}

					// check if current user has right to view the role of that organization
					if($gCurrentUser->viewRole($row['rol_id']))
					{
						$role->clear();
						$role->setArray($row);
						
						if($showRolesOtherOrganizations == false)
						{
              $profile_htm .= '<div class="groupBox profileRolesBox" id="profile_roles_box_other_orga">
								<div class="groupBoxHeadline">'.$gL10n->get('PRO_ROLE_MEMBERSHIP_OTHER_ORG').'&nbsp;
									<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=PRO_VIEW_ROLES_OTHER_ORGAS&amp;inline=true"><img 
										onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=PRO_VIEW_ROLES_OTHER_ORGAS\',this)" onmouseout="ajax_hideTooltip()"
										class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
								</div>
								<div class="groupBoxBody">
									<ul class="formFieldList">';
							$showRolesOtherOrganizations = true;
						}
						
						$startDate = new DateTimeExtended($row['mem_begin'], 'Y-m-d', 'date');
						// jede einzelne Rolle anzeigen
            $profile_htm .= '
						<li>
							<dl>
								<dt>
									'. $row['org_shortname']. ' - '.
										$role->getValue('cat_name'). ' - '. $role->getValue('rol_name');
										if($row['mem_leader'] == 1)
										{
                      $profile_htm .= ' - '.$gL10n->get('SYS_LEADER');
										}
                  $profile_htm .= '&nbsp;
								</dt>
								<dd>'.$gL10n->get('SYS_SINCE',$startDate->format($gPreferences['system_date'])).'</dd>
							</dl>
						</li>';
					}
				}
				
				$gCurrentUser->setOrganization($gCurrentOrganization->getValue('org_id'));
				
				if($showRolesOtherOrganizations == true)
				{
              $profile_htm .= '</ul>
						</div>
					</div>';
				}
            }
        }

        // show informations about user who creates the recordset and changed it
        $profile_htm .= admFuncShowCreateChangeInfoById($user->getValue('usr_usr_id_create'), $user->getValue('usr_timestamp_create'), $user->getValue('usr_usr_id_change'), $user->getValue('usr_timestamp_change')).'
    </div>
</div>';

if($user->getValue('usr_id') != $gCurrentUser->getValue('usr_id'))
{
    $profile_htm .= '
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

if($getMode == 'pdf'){
  //if($gCurrentUser->isWebmaster()){
  //  print_r($pdf_html);echo" ::DEBUG:pdf_html<br>";
  //}
  
  $pdf_output = getEinlassFormPDF($pdf_html);    
  //echo $pdf_output;
  
  $dateiname = 'Einlassformular_'.$pdf_html[TEAMNAME].'.pdf';            
  $mpdf = new \Mpdf\Mpdf();
  $mpdf->WriteHTML($pdf_output);
  $mpdf->Output($dateiname,'I');        
  die;
} else {   
  echo $profile_htm;
require(SERVER_PATH. '/adm_program/system/overall_footer.php');
}
//$stopzeit = microtime(true);

//$laufzeit = ($stopzeit - $startzeit)*1000;
//$laufzeit = substr($laufzeit, 0,5);
//$sekunden = ($stopzeit - $startzeit);
//$sekunden = substr($sekunden, 0,5);
//if($gCurrentUser->isWebmaster()) echo'Scriptlaufzeit: ' . $laufzeit . ' Millisekunden = ' . $sekunden . ' Sekunden';
?>