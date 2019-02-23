<?php
/******************************************************************************
 * Formulare anzeigen
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
require_once('../../system/classes/table_roles.php');

// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));

// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
    
// diese Funktion gibt den Html-Code fuer ein Feld mit Beschreibung wieder
// dabei wird der Inhalt richtig formatiert
function getFieldCode($fieldNameIntern, $user, $akro_class='')
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
                   <dt>'. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</dt>
                   <dd>'. $value. '&nbsp;</dd>
                 </dl>       
               </li>';    
    }
    return $html;
}
  
unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

// Html-Kopf ausgeben
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = $gL10n->get('PRO_MY_FORMS');
}
else
{
    $gLayout['title'] = $gL10n->get('PRO_PROFILE_FROM', $user->getValue('FIRST_NAME'), $user->getValue('LAST_NAME'));
}
$gLayout['header'] = '
    <meta http-equiv="refresh" content="8; URL='.$g_root_path.'/adm_program/modules/forms/forms.php">    
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>
    <script type="text/javascript">
      function stopEnterKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
      }
      document.onkeypress = stopEnterKey;
    </script>';

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
            <div id="admProfileMasterData" class="groupBox">
               <div class="groupBoxHeadline">
                 <div style="float: left;">Formular wird erstellt für:'; 
                  //$user->getValue('FIRST_NAME')
                  //$user->getValue('LAST_NAME')                            
                  echo '</div>
               </div>
               <div class="groupBoxBody">
                 <ul class="formFieldList">
                   <li>
                     <dl>
                       <dt>'.$gL10n->get('SYS_USERNAME').':</dt>
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
                          // don't show these fields in default profile list
                          case 'LAST_NAME':
                          case 'FIRST_NAME':
                          case 'PHONE':  
                          case 'MOBILE':
                          case 'E-MAIL(ALTERNATIV)':                          
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
                          // define special html view for this fields 
                          case 'EMAIL':
                          if((strlen($user->getValue('EMAIL')) > 0 ))  
                          {
                            echo '
                            <li><dl><dt><dd>&nbsp;</dd></dt></dl></li>  
                            <li>
                              <dl>
                                <dt>
                                  <div class="groupBoxHeadline">
                                    <b>Formularbestätigung per Mail an:</b>
                                  </div>
                                </dt>
                                <dd>&nbsp;</dd>
                              </dl>
                            </li>';
                            echo '
                            <li>
                              <dl>
                              <dt>
                              <i><font color="blue">';
                                  echo $user->getValue('EMAIL').'
                                </font></i>
                              </dt>
                              <dd>&nbsp;</dd>
                              </dl>
                            </li>';
                          }
                          break;  
                          default:
                            echo getFieldCode($field->getValue('usf_name_intern'), $user);
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
           <div class="groupBoxHeadline">
             <div style="float: left;">Formulare:</div>
           </div>
           <div class="groupBoxBody">
             <ul class="formFieldList">
               <li>
                 <dl>Das Formular wurde übermittelt und wird bearbeitet.</ br>
                      Eine Kopie der Formularanfrage wurde an die Vereinsmail: <font color="blue"><i>'.
                      $user->getValue('EMAIL').'</i></font> versendet.';                      
             echo '</dl>
               </li>
             </ul>
           </div>
          </div>       
        </div>                      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
  </div>
</div>';  

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
