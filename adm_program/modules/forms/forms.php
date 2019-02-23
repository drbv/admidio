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
require_once('../../system/classes/list_configuration.php');
require_once('../../system/classes/email.php');  
require_once('../../system/classes/table_roles.php');
require_once('../../modules/profile/roles_functions.php');
require_once('../../system/drbv_funktionen.php');
     
// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getFormId = admFuncVariableIsValid($_GET, 'form_id', 'numeric', 0);

// overwrite current user id for webmaster to 1.Dresdner RRC user id for development  
//if($gCurrentUser->isWebmaster()) $getUserId = 14;
//if($gCurrentUser->isWebmaster()) $getUserId = 289;//KWH
if($gCurrentUser->isWebmaster()){ 
  if($gPreferences['drbv_set_verein_id'] == ''){ 
    $getUserId = 14; 
  } else {
    $getUserId = $gPreferences['drbv_set_verein_id'];
  }
} 
  
// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
    
// Turnierleiter aus DB holen:
// z.B. für Turnieranmeldeform
$tleiter_org = array();

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 101";
    
$turnier_leiter = mysqli_query(ADMIDIOdb(), $sqlab);
$x = 1;
while($temp=mysqli_fetch_array($turnier_leiter))
   {
     $t_leiter_id = $temp[0];
     $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
     $ergebnis=mysqli_query(ADMIDIOdb(), $sqlab);
     while($name=mysqli_fetch_array($ergebnis))
           {
            $temp_id = $name[0];
            $temp_name = $name[1];
            if($temp_id == 1)
               $n_name = $temp_name;
            if($temp_id == 2)
               $v_name = $temp_name;
           }   
           
    $tleiter_org[$x] = $n_name . " " . $v_name;
    $x = $x + 1;         
   }
natsort($tleiter_org);

$tleiter = array();
$x = 1;
foreach($tleiter_org as $leiter_drehen)
{
   $ungedreht = explode(" ", $leiter_drehen);
         
   if($ungedreht[2])
      $tleiter[$x] = utf8_encode($ungedreht[1] . " " . $ungedreht[2] . " " . $ungedreht[0]);
   else           
      $tleiter[$x] = utf8_encode($ungedreht[1] . " " . $ungedreht[0]);
   $x = $x + 1; 
} 
  
// Uhrzeit Select Option erzeugen:
// z.B. für Turnieranmeldeform
$uhrzeit_select = '';  
for($hour=0; $hour <= 23;$hour++){
  $min  = "00";
  $uhrzeit_select .= '<option value="';
  if($hour <= '9'){
    $uhrzeit_select .= '0'.$hour.':'.$min.'">0'.$hour.':'.$min.'</option>';
  } else {
    $uhrzeit_select .= $hour.':'.$min.'">'.$hour.':'.$min.'</option>';    
  }  
}

function getAufstiegspunkte($AnfangsPunkte, $PunkteDatum, $StartbuchNr){
    
  // Aufstiegspunkte berechnen:
  // --------------------------  
  // Tanzpaar in TLP Datenbank suchen  
  $sqlab = 'SELECT turniernummer, startklasse, dame, herr, team, platz, punkte, rl_punkte FROM paare WHERE startbuch = ' . $StartbuchNr; 
  $punkteDB        = mysqli_query(DRBVdb(), $sqlab);
  $pkt_summe       = 0;  
  $pkt_ertanzt     = 0;  
  $anzahl_turniere = 0;
  
  while($aufstiegspunkte = mysqli_fetch_array($punkteDB)){
    //print_r($aufstiegspunkte);echo' :DEBUG::aufstiegspunkte<br>';
    $sqlab    = 'SELECT turniername, datum FROM Turnier WHERE turniernummer = ' . $aufstiegspunkte["turniernummer"]; 
    $turnier    = mysqli_query(DRBVdb(), $sqlab);
    $turnierdaten = mysqli_fetch_array($turnier);
    //echo"$sqlab<br>";
    // Nur gefundene Turniere ab Aufstiegspunkte_Datum einbeziehen
    if(strtotime($PunkteDatum) < strtotime($turnierdaten["datum"])){
      $anzahl_turniere++;
      $pkt_ertanzt = $pkt_ertanzt + $aufstiegspunkte["punkte"]; 
    }    
  }
  // Punkte = Anfangswert + ertanzte Punkte ab Datum  
  return ($AnfangsPunkte + $pkt_ertanzt);  
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
    }
    $countShowRoles++;
  }
}  
require('role_members.php');
  
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
         
    <style type="text/css">
    </style>         
         
<script type="text/javascript">
$(function(){
    formSwitch.init();
    })

var formSwitch = {
    formId: \'#formSwitcher\', // ID des Formulars
    formValWrap: \'#wechsler\', //die ID des Select elements
    pasteId: \'#pasteMe\', // die ID der Div in die später der Formularinhalt eingegeben wird
    idSel: null, // brauchen wir später um festzustellen wlcher Inhalt eingebunden werden soll
    init: function(){
        var self = this;
        // die check funktion wird gestartet sobald sich der Wert der Liste ändert
        $(this.formValWrap).change(function(){
            self.idSel= $(this).val(); // gewählte Wert wird gespeichert
            self.check();
        })
            
    },
    check: function(){
    // da der Wert sich öffters ändern kann, wenn der user mehrmals das Selectfeld benutzt prüfen wir erst ob es bereits    
        if($(this.pasteId).children().length>0){
            $(this.pasteId).html(\'\'); // wenn Elelemente vorhanden dann löschen 
            }
        this.paste(); // aufruf der Funktion zum klonen der Inhalte und dem Sichtbarschalten
    },
    paste:function(){
        $(\'#\'+this.idSel).clone().appendTo(this.pasteId);
        $(this.pasteId).children().css(\'display\',\'block\');
    }
}
</script>         

<style type="text/css">
  .hideMe{display:none;}
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
                          // only show these fields in default profile list
                          case 'VEREIN':
                          case 'VEREINSNUMMER':
                          case 'VERANTWORTLICHER_NAME':  
                          case 'VERANTWORTLICHER_VORNAME':
                          case 'BUNDESLAND':
                            echo getFieldCode($field->getValue('usf_name_intern'), $user);                          
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
                                    <b>Eine Formularbestätigung wird versendet an:</b>
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
           <div class="groupBoxHeadline">
             <div style="float: left;">Verfügbare Formulare:</div><br />
             <p style="text-align: justify;">Das Formular zur Änderung des Vereinsnamens ist als PDF verfügbar und muss unterschrieben an die, 
                Geschäftsstelle gesendet werden. Alle anderen Formulare stehen online zur Verfügung.</p>
               <ul>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=1">Änderung des Vereinsnamens (PDF)</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=2">Änderung der Vereinsmail</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=3">Änderung der Geodaten</a></li>
               </ul>
               <ul>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=13">Turnier Reservierung</a></li>                                            
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=4">Turnier Bewerbung (Nur Ranglisten-/Qualifikationsturniere und Deutsche Meisterschaften)</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=5">Turnier Anmeldung</a></li>
               </ul>
               <ul>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=6">Startbuch/Startkarten Erstbestellung</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=8">Startbuch/Startkarten Adressänderung</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=12">Startbuch/Startkarten Startklassenwechsel (Aufstiegsantrag)</a></li>                      
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=14">Startbuch/Startkarten Vereinswechsel</a></li>                      
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=9">Startbuch/Startkarten Ungültigkeitsantrag</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=10">Startmarkenbestellung</a></li>
               </ul>
               <ul>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=11">Sporttauglichkeitsnachweis nachreichen</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=7">RFID-Karten Folgebestellung (nach Verlust)</a></li>
                 <li><a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=15">Breitensportstartbuch Bestellung (Digital, für Präambelturniere)</a></li>
               </ul>';
               echo '
                     <ul>
                       <li>';
                         if(date("n") > 6){
                           echo '<a href="'.$g_root_path.'/adm_program/modules/forms/forms.php?form_id=16">DRBV Gütesiegel für Vereine beantragen</a> 
                                    <a target="_top" href="https://www.drbv.de/cms/index.php/guetesiegelinfo">&raquo;Information zum Gütesiegel&laquo;</a>';
                         } else {
                           echo '
                             <span style="color: #808080;"><i>DRBV Gütesiegel für Vereine beantragen (nur Juli bis Dezember)</i></span>
                             <a target="_top" href="https://www.drbv.de/cms/index.php/guetesiegelinfo">&raquo;Information zum Gütesiegel&laquo;</a>';
                         }
                         echo '
                         <span id="admInfoGsiegel" class="iconTextLink" style="display: inline;">
	                   <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_INFO_GSIEGEL&amp;message_var1=&amp;inline=true"><img 
	                   onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_INFO_GSIEGEL&amp;message_var1=\',this)" onmouseout="ajax_hideTooltip()"
	                   class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>	    				   
	                 </span>                          
                       </li>
                     </ul>';
       echo '</div>
         </div>          
       </div>            
       <div style="width: 100%; float: left;">
         <div id="admProfileMasterData" class="groupBox">                   
           <div class="groupBoxBody">
             <ul class="formFieldList">
               <li>
                 <dl>';
                   if ($getFormId=='1') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f1_vereinsname.form.php');
                   } elseif ($getFormId=='2') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f2_vereinsmail.form.php');
                   } elseif ($getFormId=='3') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f3_vereinsgeodaten.form.php');
                   } elseif ($getFormId=='4') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f4_turnierbewerbung.form.php');
                   } elseif ($getFormId=='5') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f5_turnieranmeldung.form.php');                     
                   } elseif ($getFormId=='6') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f6_startbuchneu.form.php');                     
                   } elseif ($getFormId=='7') {                     
                     require(SERVER_PATH. '/adm_program/modules/forms/f7_rfidfolge.form.php');
                   } elseif ($getFormId=='8') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f8_startbuchaddr.form.php');
                   } elseif ($getFormId=='9') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f9_startbuch_ungueltig.form.php');
                   } elseif ($getFormId=='10') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f10_startmarken.form.php');                     
                   } elseif ($getFormId=='11') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f11_sporttauglichkeit.form.php');
                   } elseif ($getFormId=='12') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f12_startbuchaufstieg.form.php');                     
                   } elseif ($getFormId=='13') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f13_turnierreservierung.form.php');                     
                   } elseif ($getFormId=='14') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f14_vereinswechsel.form.php');
                   } elseif ($getFormId=='15') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f15_bspstartbuch.form.php');                     
                   } elseif ($getFormId=='16') {
                     require(SERVER_PATH. '/adm_program/modules/forms/f16_guetesiegel.form.php');                     
                   } else {
                     echo '<i>Noch kein Formular ausgewählt!</i>';                   
                     //if($gCurrentUser->isWebmaster()){
                       //require(SERVER_PATH. '/adm_program/modules/forms/f12_startbuchaufstieg.form.dbg.php');
                     //} else {
                       //require(SERVER_PATH. '/adm_program/modules/forms/f12_startbuchaufstieg.form.php');
                     //}
                   }
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
