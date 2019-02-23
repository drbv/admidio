<?php
/******************************************************************************
 * Musik Datenbank / Turnierleiter
 *
 * Copyright    : (c) 2018 DRBV Webteam
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
//require('role_members.php');
  
// Html-Kopf ausgeben
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = $gL10n->get('PRO_MY_MUSIKDB').' Administration';
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
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>         
    <script type="text/javascript">
      function stopEnterKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
      }
      document.onkeypress = stopEnterKey;
    </script>
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
                 <div style="float: left;">Musik-DB Administration:'; 
                  //$user->getValue('FIRST_NAME')
                  //$user->getValue('LAST_NAME')  
                  echo '<p><a href="'.$g_root_path.'/eigene_scripts/musikdatenbank/admin/index.php"target="_blank">zur Turniermusik-Administration</a></p>';  
                  echo '</div>
               </div>                                        
               <div class="groupBoxBody">
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
