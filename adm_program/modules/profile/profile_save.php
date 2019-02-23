<?php
/******************************************************************************
 * Save profile/registration data
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * user_id    : ID of the user who should be edited
 * new_user   : 0 - Edit user of the user id
 *              1 - Create a new user
 *              2 - Create a registration
 *              3 - assign/accept a registration
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/classes/system_mail.php');
require_once('../../system/classes/user_registration.php');

// Initialize and check the parameters
$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', 0);
$getNewUser = admFuncVariableIsValid($_GET, 'new_user', 'numeric', 0);
$getAkroclass = admFuncVariableIsValid($_GET, 'akroclass', 'numeric', 0);

// if current user has no login then only show registration dialog
if($gValidLogin == false)
{
    $getNewUser = 2;
}

// save form data in session for back navigation
$_SESSION['profile_request'] = $_POST;

if(!isset($_POST['usr_login_name']))
{
    $_POST['usr_login_name'] = '';
}
if(!isset($_POST['reg_org_id']))
{
    $_POST['reg_org_id'] = $gCurrentOrganization->getValue('org_id');
}

// read user data
if($getNewUser == 2 || $getNewUser == 3)
{
    // create user registration object and set requested organization
	$user = new UserRegistration($gDb, $gProfileFields, $getUserId);
	$user->setOrganization($_POST['reg_org_id']);
}
else
{
	$user = new User($gDb, $gProfileFields, $getUserId);
}

// pruefen, ob Modul aufgerufen werden darf
switch($getNewUser)
{
    case 0:
        // prueft, ob der User die notwendigen Rechte hat, das entsprechende Profil zu aendern
        if($gCurrentUser->editProfile($user) == false)
        {
            $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
        }
        break;

    case 1:
        // prueft, ob der User die notwendigen Rechte hat, neue User anzulegen
        if($gCurrentUser->editUsers() == false)
        {
            $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
        }
        break;

    case 2:
    case 3:
        // Registrierung deaktiviert, also auch diesen Modus sperren
        if($gPreferences['registration_mode'] == 0)
        {
            $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
        }
        break;
}

/*------------------------------------------------------------*/
// Feldinhalte pruefen der User-Klasse zuordnen
/*------------------------------------------------------------*/

// bei Registrierung muss Loginname und Pw geprueft werden
if($getNewUser == 2)
{
    if(strlen($_POST['usr_login_name']) == 0)
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('SYS_USERNAME')));
    }

    // Passwort sollte laenger als 6 Zeichen sein
    if(strlen($_POST['usr_password']) < 6)
    {
        $gMessage->show($gL10n->get('PRO_PASSWORD_LENGTH'));
    }

    // beide Passwortfelder muessen identisch sein
    if ($_POST['usr_password'] != $_POST['password2'])
    {
        $gMessage->show($gL10n->get('PRO_PASSWORDS_NOT_EQUAL'));
    }

    if(strlen($_POST['usr_password']) == 0)
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('SYS_PASSWORD')));
    }
}

// Disable Akrobatik Checks for GS (immer) und Webmaster (wenn in den globalen Einstellungen 
// gesetzt) oder keine Akrobatikklasse vorhanden ist!  
if($getAkroclass == 0 || hasRole("Geschäftsstelle") || ($gCurrentUser->isWebmaster() && $gPreferences['drbv_disable_akro_check'] == 1)){
  $akro_check_groups_active = FALSE;
  $akro_check_tso_active    = FALSE;
} else {
  $akro_check_groups_active = TRUE;
  $akro_check_tso_active    = TRUE;
}
  
// nun alle Profilfelder pruefen
$akro_dual_cnt_vr  = 0;
$akro_dual_cnt_zr  = 0;
$akro_dual_cnt_er  = 0;
$akro_form_cnt_vr  = 0;
$akro_form_cnt_zr  = 0;
$akro_form_cnt_er  = 0;    
$akro_combi_cnt_vr = 0;
$akro_combi_cnt_zr = 0;
$akro_combi_cnt_er = 0;
$akro_rot_cnt_vr   = 0;
$akro_rot_cnt_zr   = 0;
$akro_rot_cnt_er   = 0;
$akro_vw_cnt_vr    = 0;
$akro_rw_cnt_vr    = 0;
$akro_dive_cnt_vr  = 0;  
$akro_vw_cnt_zr    = 0;
$akro_rw_cnt_zr    = 0;
$akro_dive_cnt_zr  = 0;  
$akro_vw_cnt_er    = 0;
$akro_rw_cnt_er    = 0;
$akro_dive_cnt_er  = 0;  
$akro_grpid        = '';
$defsub            = 1;
unset ($akro_vr_arr);
unset ($akro_zr_arr);
unset ($akro_er_arr);  
foreach($gProfileFields->mProfileFields as $field)
{
    $post_id = 'usf-'. $field->getValue('usf_id');    
    
	// check and save only fields that aren't disabled
	if($gCurrentUser->editUsers() == true || $field->getValue('usf_disabled') == 0 || ($field->getValue('usf_disabled') == 1 && $getNewUser > 0))
	{
		if(isset($_POST[$post_id])) 
		{
			// Pflichtfelder muessen gefuellt sein
			// E-Mail bei Registrierung immer !!!
			if(($field->getValue('usf_mandatory') == 1 && strlen($_POST[$post_id]) == 0)
			|| ($getNewUser == 2 && $field->getValue('usf_name_intern') == 'EMAIL' && strlen($_POST[$post_id]) == 0))
			{
				$gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $field->getValue('usf_name')));
			}
			
			// if social network then extract username from url
			if($field->getValue('usf_name_intern') == 'FACEBOOK'
			|| $field->getValue('usf_name_intern') == 'GOOGLE_PLUS'
			|| $field->getValue('usf_name_intern') == 'TWITTER'
			|| $field->getValue('usf_name_intern') == 'XING')
			{
				if(strValidCharacters($_POST[$post_id], 'url')
    			&& strpos($_POST[$post_id], '/') !== false)
				{
					if(strrpos($_POST[$post_id], '/profile.php?id=') > 0)
					{
						// extract facebook id (not facebook unique name) from url
						$_POST[$post_id] = substr($_POST[$post_id], strrpos($_POST[$post_id], '/profile.php?id=') + 16);
					}
					else
					{
						if(strrpos($_POST[$post_id], '/posts') > 0)
						{
							$_POST[$post_id] = substr($_POST[$post_id], 0, strrpos($_POST[$post_id], '/posts'));
						}
						
						$_POST[$post_id] = substr($_POST[$post_id], strrpos($_POST[$post_id], '/') + 1);
						if(strrpos($_POST[$post_id], '?') > 0)
						{
						   $_POST[$post_id] = substr($_POST[$post_id], 0, strrpos($_POST[$post_id], '?'));
						}
					}
				}
			}
      // rmenken: Akro pruefen
      // Vorrunde:
      // =========================================
      // Vorrunde: Akro 1..8 check Anzahl Kombinationen
      // Vorrunde: Akro 1..8 check Anzahl Rotationen
      // Vorrunde: Akro 1..8 check auf Doppelte
      // Vorrunde: Akro 1..8, check auf max. 70 Punkte Formation
      // Akro 6-8, E1 und E2 akro string modifizieren, um grpid rauszufiltern            

      //Akro1
      if ($post_id == 'usf-43') {        
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          unset($akro_pkt);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid = trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]);
          $remind_class = substr($_POST[$post_id],0,1);
          // define substr to check; in case Formation there are 2 inital letters
          if($remind_class == 'F'){
            $defsub = 2;
          }  
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 1. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
      }
      //Akro2
      if ($post_id == 'usf-44') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 2. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
      }
      //Akro3
      if ($post_id == 'usf-45') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 3. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'J' && $akro_check_tso_active){
          if ($akro_combi_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
        }
        if ($akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
        }                        
      }
      //Akro4
      if ($post_id == 'usf-46') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          //if ($akro_check_tso_active && (substr($_POST[$post_id],0,1) == 'A' || substr($_POST[$post_id],0,1) == 'B' || substr($_POST[$post_id],0,1) == 'C' || substr($_POST[$post_id],0,1) == 'F')) {
          if (($remind_class == 'C' || $remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active){
            $gMessage->show('Die 4. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'C' && $akro_check_tso_active){
          if ($akro_combi_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
        }        
        if ($akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
        }                
      }
      //Akro5
      if ($post_id == 'usf-47') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active){
            $gMessage->show('Die 5. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'A' || substr($_POST[$post_id],0,1) == 'B') && $akro_check_groups_active){
          if ($akro_combi_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_vw_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
          if ($akro_rw_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }        
          if ($akro_dive_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
        }                         
        if ($akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
        }                
      }
      //Akro6
      if ($post_id == 'usf-48') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'F') && $akro_check_tso_active){
            $gMessage->show('Die 6. Akrobatik in der Vorrunde ist nicht belegt!', 'Vorrunden Akrobatik');
          }
        }        
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,6)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,6)).' Pkt.', 'Vorrunden Akrobatik');          
          }                    
        }                
      }
      //Akro7
      if ($post_id == 'usf-75') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,7)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,7)).' Pkt.', 'Vorrunden Akrobatik');          
          }                    
        }                        
      }
      //Akro8
      if ($post_id == 'usf-76') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_vr = $akro_dual_cnt_vr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_vr = $akro_form_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_vr = $akro_combi_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_vr = $akro_rot_cnt_vr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_vr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_vr = 1;
        }
        if ($remind_class == 'F' && $akro_check_groups_active){
          if ($akro_combi_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_vw_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
          if ($akro_rw_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
          if ($akro_rot_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }        
          if ($akro_dive_cnt_vr == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Vorrunden Akrobatik');
          }
          if ($akro_dive_cnt_vr >= 4) {
            $gMessage->show('Die max. Anzahl an Kopfüberelementen ist 3 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_dual_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl an Dualakrobatiken ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
          if ($akro_form_cnt_vr >= 3) {
            $gMessage->show('Die max. Anzahl an Formationsspezifischen Akrobatiken ist 2 und wurde überschritten!', 'Vorrunden Akrobatik');
          }
        }                         
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_vr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_vr_arr) == count(array_unique($akro_vr_arr)) || $akro_vr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_vr_arr) - count(array_unique($akro_vr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_vr_arr).' : '.$cnt_grpid.' : ', 'Vorrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_vr_arr, array_unique($akro_vr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_vr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Vorrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,8)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,8)).' Pkt.', 'Vorrunden Akrobatik');          
          }                    
        }                        
      }
      //AkroE1
      if ($post_id == 'usf-68') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      //AkroE2
      if ($post_id == 'usf-149') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      // ZwRunde:
      // =========================================
      // ZwRunde: Akro 1..8, check Anzahl Kombinationen
      // ZwRunde: Akro 1..8, check Anzahl Rotationen
      // ZwRunde: Akro 1..8, check auf Doppelte
      // ZwRunde: Akro 1..8, check auf max. 70 Punkte Formation
      // E1 und E2 akro string modifizieren, um grpid rauszufiltern
      //Akro1
      if ($post_id == 'usf-50') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          unset($akro_pkt);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid = trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 1. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
      }
      //Akro2
      if ($post_id == 'usf-55') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 2. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
      }
      //Akro3
      if ($post_id == 'usf-56') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 3. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'J' && $akro_check_tso_active){
          if ($akro_combi_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rot_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
        }
        if ($akro_check_groups_active){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
        }        
      }
      //Akro4
      if ($post_id == 'usf-57') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'C' || $remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 4. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'C' && $akro_check_tso_active){
          if ($akro_combi_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rot_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
        }
        if ($akro_check_groups_active){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
        }                
      }
      //Akro5
      if ($post_id == 'usf-58') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 5. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'A' || substr($_POST[$post_id],0,1) == 'B') && $akro_check_groups_active){
          if ($akro_combi_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          } 
          if ($akro_rot_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_vw_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rw_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rot_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }        
          if ($akro_dive_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
        }                
        if ($akro_check_groups_active){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
        }
      }
      //Akro6
      if ($post_id == 'usf-59') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 6. Akrobatik in der Zwischenrunde ist nicht belegt!', 'Zwischenrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,6)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,6)).' Pkt.', 'Zwischenrunden Akrobatik');          
          }                    
        }        
      }
      //Akro7
      if ($post_id == 'usf-77') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } 
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,7)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,7)).' Pkt.', 'Zwischenrunden Akrobatik');          
          }                    
        }        
      }
      //Akro8
      if ($post_id == 'usf-78') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }  
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_zr = $akro_dual_cnt_zr+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_zr = $akro_form_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_zr = $akro_combi_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_zr = $akro_rot_cnt_zr+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_zr = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_zr = 1;
        }
        if ($remind_class == 'F' && $akro_check_groups_active){
          if ($akro_combi_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          } 
          if ($akro_rot_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_vw_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rw_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_rot_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }        
          if ($akro_dive_cnt_zr == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_dive_cnt_zr >= 4) {
            $gMessage->show('Die max. Anzahl an Kopfüberelementen ist 3 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_dual_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl an Dualakrobatiken ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
          if ($akro_form_cnt_zr >= 3) {
            $gMessage->show('Die max. Anzahl an Formationsspezifischen Akrobatiken ist 2 und wurde überschritten!', 'Zwischenrunden Akrobatik');
          }
        }                
        if ((substr(substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active)){
          $akro_zr_arr = explode(" ",trim($akro_grpid));
          if (count($akro_zr_arr) == count(array_unique($akro_zr_arr)) || $akro_zr_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_zr_arr) - count(array_unique($akro_zr_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_zr_arr).' : '.$cnt_grpid.' : ', 'Zwischenrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_zr_arr, array_unique($akro_zr_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_zr_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Zwischenrunden Akrobatik');            
          }
          if (array_sum(array_slice($akro_pkt,0,8)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,8)).' Pkt.', 'Zwischenrunden Akrobatik');          
          }                    
        }        
      }
      //AkroE1
      if ($post_id == 'usf-69') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      //AkroE2
      if ($post_id == 'usf-150') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      // Endrunde:
      // =========================================
      // Endrunde: Akro 1..8, check Anzahl Kombinationen
      // Endrunde: Akro 1..8, check Anzahl Rotationen
      // Endrunde: Akro 1..8, check Anzahl Dives
      // Endrunde: Akro 1..8, check auf Doppelte
      // Endrunde: Akro 1..6, check auf max. 70 Punkte Paare
      // Endrunde: Akro 1..8, check Formation - 2 DUAL Akros
      // Endrunde: Akro 1..8, check Formation - 2 FORM Akros      
      // Endrunde: Akro 1..8, check auf max. 70 Punkte Formation
      // Akro E1 und E2 akro string modifizieren, um grpid rauszufiltern

      //Akro1
      if ($post_id == 'usf-51') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          unset($akro_pkt);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid = trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 1. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
      }
      //Akro2
      if ($post_id == 'usf-60') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 2. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
      }
      //Akro3
      if ($post_id == 'usf-61') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if ($akro_check_tso_active) {
            $gMessage->show('Die 3. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'J' && $akro_check_tso_active){
          if ($akro_combi_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_rot_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
        }
        if ($akro_check_groups_active){
          $akro_er_arr = explode(" ",trim($akro_grpid));
          if (count($akro_er_arr) == count(array_unique($akro_er_arr)) || $akro_er_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_er_arr) - count(array_unique($akro_er_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_er_arr).' : '.$cnt_grpid.' : ', 'Endrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_er_arr, array_unique($akro_er_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_er_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Endrunden Akrobatik');            
          }      
        }        
      }
      //Akro4
      if ($post_id == 'usf-62') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'C' || $remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 4. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
        if (substr($_POST[$post_id],0,1) == 'C' && $akro_check_tso_active){
          if ($akro_combi_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_rot_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
        }
        if ($akro_check_groups_active){
          $akro_er_arr = explode(" ",trim($akro_grpid));
          if (count($akro_er_arr) == count(array_unique($akro_er_arr)) || $akro_er_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_er_arr) - count(array_unique($akro_er_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_er_arr).' : '.$cnt_grpid.' : ', 'Endrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_er_arr, array_unique($akro_er_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_er_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Endrunden Akrobatik');            
          }      
        }        
      }
      //Akro5
      if ($post_id == 'usf-63') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 5. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8'|| substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
      }
      //Akro6
      if ($post_id == 'usf-64') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid .= ' '.trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        } else {
          if (($remind_class == 'B' || $remind_class == 'A' || $remind_class == 'F') && $akro_check_tso_active) {
            $gMessage->show('Die 6. Akrobatik in der Endrunde ist nicht belegt!', 'Endrunden Akrobatik');
          }
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
        if ((substr($_POST[$post_id],0,1) == 'A' || substr($_POST[$post_id],0,1) == 'B') && $akro_check_groups_active){
          if ($akro_combi_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          } 
          if ($akro_rot_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_vw_cnt_er == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if ($akro_rw_cnt_er == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if ($akro_rot_cnt_er == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Endrunden Akrobatik');
          }        
          if ($akro_dive_cnt_er == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if (array_sum(array_slice($akro_pkt,0,6)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,6)).' Pkt.', 'Endrunden Akrobatik');          
          }          
        }                
        if ($akro_check_groups_active){
          $akro_er_arr = explode(" ",trim($akro_grpid));
          if (count($akro_er_arr) == count(array_unique($akro_er_arr)) || $akro_er_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_er_arr) - count(array_unique($akro_er_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_er_arr).' : '.$cnt_grpid.' : ', 'Endrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_er_arr, array_unique($akro_er_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_er_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Endrunden Akrobatik');            
          }      
        }
      }
      //Akro7
      if ($post_id == 'usf-79') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid = trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }        
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_er_arr = explode(" ",trim($akro_grpid));
          if (array_sum(array_slice($akro_pkt,0,7)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,7)).' Pkt.', 'Endrunden Akrobatik');          
          }                          
          if (count($akro_er_arr) == count(array_unique($akro_er_arr)) || $akro_er_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_er_arr) - count(array_unique($akro_er_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_er_arr).' : '.$cnt_grpid.' : ', 'Endrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_er_arr, array_unique($akro_er_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_er_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Endrunden Akrobatik');            
          }      
        }        
      }
      //Akro8
      if ($post_id == 'usf-80') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $akro_pkt[] = trim($akro_str[3]);
          $akro_grpid = trim($akro_str[2]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }
        if (substr($_POST[$post_id],0,2) == 'FD'){
          $akro_dual_cnt_er = $akro_dual_cnt_er+1;
        }
        if (substr($_POST[$post_id],0,2) == 'FF'){
          $akro_form_cnt_er = $akro_form_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '8' || substr($_POST[$post_id],$defsub,1) == '9'){
          $akro_combi_cnt_er = $akro_combi_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) === '0'){
          $akro_rot_cnt_er = $akro_rot_cnt_er+1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '3'){
          $akro_vw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '4'){
          $akro_rw_cnt_er = 1;
        }
        if (substr($_POST[$post_id],$defsub,1) == '5'){
          $akro_dive_cnt_er = 1;
        }
        if ($remind_class == 'F' && $akro_check_groups_active){
          if ($akro_combi_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Kombinationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          } 
          if ($akro_rot_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl der erlaubten Rotationen ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_vw_cnt_er == 0) {
            $gMessage->show('Die Kategorie Vorwärtselement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if ($akro_rw_cnt_er == 0) {
            $gMessage->show('Die Kategorie Rückwärtselement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if ($akro_rot_cnt_er == 0) {
            $gMessage->show('Die Kategorie Rotationen ist nicht belegt worden!', 'Endrunden Akrobatik');
          }        
          if ($akro_dive_cnt_er == 0) {
            $gMessage->show('Die Kategorie Kopfüberelement ist nicht belegt worden!', 'Endrunden Akrobatik');
          }
          if ($akro_dive_cnt_er >= 4) {
            $gMessage->show('Die max. Anzahl an Kopfüberelementen ist 3 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_dual_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl an Dualakrobatiken ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if ($akro_form_cnt_er >= 3) {
            $gMessage->show('Die max. Anzahl an Formationsspezifischen Akrobatiken ist 2 und wurde überschritten!', 'Endrunden Akrobatik');
          }
          if (array_sum(array_slice($akro_pkt,0,8)) > 70) {
            $gMessage->show('Der max. zulässige akrobatische Vorwert wurde überschritten!<br>Erlaubt 70,00 Pkt. Aktuell = '.array_sum(array_slice($akro_pkt,0,8)).' Pkt.', 'Endrunden Akrobatik');          
          }                          
        }                
        if ((substr($_POST[$post_id],0,1) == 'F') && $akro_check_groups_active){
          $akro_er_arr = explode(" ",trim($akro_grpid));
          if (count($akro_er_arr) == count(array_unique($akro_er_arr)) || $akro_er_arr[0] == '') {
            //keine doppelten Elemente
            //$cnt_grpid   = count($akro_er_arr) - count(array_unique($akro_er_arr));
            //$gMessage->show('Gefundene Gruppen-IDs!: '.$akro_grpid.'! : '.var_dump($akro_er_arr).' : '.$cnt_grpid.' : ', 'Endrunden Akrobatik');        
          } else {
            $akro_double = array_diff_key($akro_er_arr, array_unique($akro_er_arr));
            $gMessage->show('Es wurden Akrobatikwiederholungen gefunden: '.implode("/",$akro_er_arr).' >> GruppenID: <b>'.implode(" & ",$akro_double).'</b> kommt mehrfach vor!', 'Endrunden Akrobatik');            
          }      
        }        
      }
      //AkroE1
      if ($post_id == 'usf-70') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      //AkroE2
      if ($post_id == 'usf-151') {
        if ($_POST[$post_id] != '') {
          $akro_str = explode(":", $_POST[$post_id]);
          $_POST[$post_id] = trim($akro_str[0]).' : '.trim($akro_str[1]); 
        }        
      }
      // end: Akro pruefen

			// Wert aus Feld in das User-Klassenobjekt schreiben
			$returnCode = $user->setValue($field->getValue('usf_name_intern'), $_POST[$post_id]);
			
			// Ausgabe der Fehlermeldung je nach Datentyp
			if($returnCode == false)
			{
				if($field->getValue('usf_type') == 'CHECKBOX')
				{
					$gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
				}
				elseif($field->getValue('usf_type') == 'DATE')
				{
					$gMessage->show($gL10n->get('SYS_DATE_INVALID', $field->getValue('usf_name'), $gPreferences['system_date']));
				}
				elseif($field->getValue('usf_type') == 'EMAIL')
				{
					$gMessage->show($gL10n->get('SYS_EMAIL_INVALID', $field->getValue('usf_name')));
				}
				elseif($field->getValue('usf_type') == 'NUMERIC')
				{
					$gMessage->show($gL10n->get('PRO_FIELD_NUMERIC', $field->getValue('usf_name')));
				}
				elseif($field->getValue('usf_type') == 'URL')
				{
					$gMessage->show($gL10n->get('SYS_URL_INVALID_CHAR', $field->getValue('usf_name')));
				}
			}
		}
		else
		{
			// Checkboxen uebergeben bei 0 keinen Wert, deshalb diesen hier setzen
			if($field->getValue('usf_type') == 'CHECKBOX')
			{
				$user->setValue($field->getValue('usf_name_intern'), '0');
			}
			elseif($field->getValue('usf_mandatory') == 1)
			{
				$gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $field->getValue('usf_name')));
			}
		}
	}
}

$login_name_changed = false;
$forum_old_username = '';

if($gCurrentUser->isWebmaster() || $getNewUser > 0)
{
    // Loginname darf nur vom Webmaster bzw. bei Neuanlage geaendert werden    
    if($_POST['usr_login_name'] != $user->getValue('usr_login_name'))
    {
        if(strlen($_POST['usr_login_name']) > 0)
        {
            // pruefen, ob der Benutzername bereits vergeben ist
            $sql = 'SELECT usr_id FROM '. TBL_USERS. '
                     WHERE usr_login_name LIKE \''. $_POST['usr_login_name']. '\'';
            $gDb->query($sql);

            if($gDb->num_rows() > 0)
            {
                $row = $gDb->fetch_array();

                if(strcmp($row['usr_id'], $getUserId) != 0)
                {
                    $gMessage->show($gL10n->get('PRO_LOGIN_NAME_EXIST'));
                }
            }

            // pruefen, ob der Benutzername bereits im Forum vergeben ist, 
            // Benutzernamenswechsel und diese Dinge
            if($gPreferences['enable_forum_interface'])
            {
                // pruefen, ob der Benutzername bereits im Forum vergeben ist
                if($gForum->userExists($_POST['usr_login_name']))
                {
                    $gMessage->show($gL10n->get('SYS_FORUM_USER_EXIST'));
                }
                
                // bisherigen Loginnamen merken, damit dieser spaeter im Forum geaendert werden kann
                $forum_old_username = '';
                if(strlen($user->getValue('usr_login_name')) > 0)
                {
                    $forum_old_username = $user->getValue('usr_login_name');
                }
            }
        }

        $login_name_changed = true;
        if(!$user->setValue('usr_login_name', $_POST['usr_login_name']))
		{
			$gMessage->show($gL10n->get('SYS_FIELD_INVALID_CHAR', $gL10n->get('SYS_USERNAME')));
		}
    }    
}

// falls Registrierung, dann die entsprechenden Felder noch besetzen
if($getNewUser == 2)
{
    $user->setValue('usr_password', $_POST['usr_password']);
}


// Falls der User sich registrieren wollte, aber ein Captcha geschaltet ist,
// muss natuerlich der Code ueberprueft werden
if ($getNewUser == 2 && $gPreferences['enable_registration_captcha'] == 1)
{
    if ( !isset($_SESSION['captchacode']) || admStrToUpper($_SESSION['captchacode']) != admStrToUpper($_POST['captcha']) )
    {
		if($gPreferences['captcha_type']=='pic') {$gMessage->show($gL10n->get('SYS_CAPTCHA_CODE_INVALID'));}
		else if($gPreferences['captcha_type']=='calc') {$gMessage->show($gL10n->get('SYS_CAPTCHA_CALC_CODE_INVALID'));}
    }
}

/*------------------------------------------------------------*/
// Benutzerdaten in Datenbank schreiben
/*------------------------------------------------------------*/
$gDb->startTransaction();

try
{
    // save changes; if it's a new registration than caught exception if email couldn't send

    if($user->getValue('usr_id') == 0)
    {
        // der User wird gerade angelegt und die ID kann erst danach in das Create-Feld gesetzt werden
        $user->save();
    
        if($getNewUser == 1)
        {
            $user->setValue('usr_usr_id_create', $gCurrentUser->getValue('usr_id'));
        }
        else
        {
            $user->setValue('usr_usr_id_create', $user->getValue('usr_id'));
        }
    }

    $ret_code = $user->save();
}
catch(AdmException $e)
{
    unset($_SESSION['profile_request']);
    $gMessage->setForwardUrl($gNavigation->getPreviousUrl());
	$e->showHtml();
}

// wurde der Loginname vergeben oder geaendert, so muss ein Forumaccount gepflegt werden
// bei einer Bestaetigung der Registrierung muss der Account aktiviert werden
if($gPreferences['enable_forum_interface'] && ($login_name_changed || $getNewUser == 3))
{
    $set_admin = false;
    if($gPreferences['forum_set_admin'] == 1 && $user->isWebmaster())
    {
        $set_admin = true;
    }
    $gForum->userSave($user->getValue('usr_login_name'), $user->getValue('usr_password'), $user->getValue('EMAIL'), $forum_old_username, $getNewUser, $set_admin);
}

$gDb->endTransaction();

// wenn Daten des eingeloggten Users geaendert werden, dann Session-Variablen aktualisieren
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gCurrentUser = $user;
    //rmenken: add mail
    $strMailtext = "Die Profildaten von ".$user->getValue('usr_login_name')." wurden bearbeitet!";    
    mail('webmaster@drbv.de','OnlineStartbuch: Profilbearbeitung',$strMailtext,'From: webmaster@drbv.de');
//    mail('geschaeftsstelle@drbv.de','Aktivenportal: Profilbearbeitung',$strMailtext,'From: webmaster@drbv.de');      
}

unset($_SESSION['profile_request']);
$gNavigation->deleteLastUrl();

/*------------------------------------------------------------*/
// je nach Aufrufmodus auf die richtige Seite weiterleiten
/*------------------------------------------------------------*/

if($getNewUser == 1 || $getNewUser == 3)
{
	// assign a registration or create a new user

	if($getNewUser == 3)
	{
        try
        {
    		// accept a registration, assign neccessary roles and send a notification email
    		$user->acceptRegistration();
    		$messageId = 'PRO_ASSIGN_REGISTRATION_SUCCESSFUL';
        }
        catch(AdmException $e)
        {
            $gMessage->setForwardUrl($gNavigation->getPreviousUrl());
        	$e->showHtml();
        }
	}
	else
	{
		// a new user is created with the user management module
		// then the user must get the neccessary roles
		$user->assignDefaultRoles();
		$messageId = 'SYS_SAVE_DATA';
	}
	
	// if current user has the right to assign roles then show roles dialog
	// otherwise go to previous url (default roles are assigned automatically)
	if($gCurrentUser->onlyAssignRoles())
	{
		header('Location: roles.php?usr_id='. $user->getValue('usr_id'). '&new_user='.$getNewUser);
		exit();
	}
	else
	{
		$gMessage->setForwardUrl($gNavigation->getPreviousUrl(), 2000);
		$gMessage->show($gL10n->get($messageId));
	}
}
elseif($getNewUser == 2)
{
    // registration was successful then go to homepage
    $gMessage->setForwardUrl($gHomepage);
    $gMessage->show($gL10n->get('SYS_REGISTRATION_SAVED'));
}
elseif($getNewUser == 0 && $user->getValue('usr_valid') == 0)
{
    // a registration was edited then go back to profile view
    $gMessage->setForwardUrl($gNavigation->getPreviousUrl(), 2000);
    $gMessage->show($gL10n->get('SYS_SAVE_DATA'));
}
else
{
    // go back to profile view
    $gMessage->setForwardUrl($gNavigation->getUrl(), 2000);
    $gMessage->show($gL10n->get('SYS_SAVE_DATA'));
}
?>
