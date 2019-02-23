<?php
/******************************************************************************
 * Create or edit a user profile
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
 * lastname   : (Optional) Lastname could be set and will than be preassigned for new users
 * firstname  : (Optional) First name could be set and will than be preassigned for new users
 * remove_url : 1 - Removes the last url from navigation cache
 *
 *****************************************************************************/

//error_reporting (E_ALL);

require_once('../../system/common.php');
require_once('../../system/drbv_database.php');
require_once('../../system/classes/form_elements.php');
require_once('roles_functions.php');

// Initialize and check the parameters
$getUserId    = admFuncVariableIsValid($_GET, 'user_id', 'numeric', 0);
$getNewUser   = admFuncVariableIsValid($_GET, 'new_user', 'numeric', 0);
$getLastname  = admFuncVariableIsValid($_GET, 'lastname', 'string', '');
$getFirstname = admFuncVariableIsValid($_GET, 'firstname', 'string', '');
$getRemoveUrl = admFuncVariableIsValid($_GET, 'remove_url', 'boolean', 0);

$registrationOrgId = $gCurrentOrganization->getValue('org_id');

// if current user has no login then only show registration dialog
if($gValidLogin == false)
{
    $getNewUser = 2;
}

// if new_user isn't set and no user id is set then show dialog to create a user
if($getUserId == 0 && $getNewUser == 0)
{
	$getNewUser = 1;
}

if($getRemoveUrl == 1)
{
    $gNavigation->deleteLastUrl();
}

// Falls das Catpcha in den Orgaeinstellungen aktiviert wurde und die Ausgabe als
// Rechenaufgabe eingestellt wurde, muss die Klasse f\ufffdr neue Registrierungen geladen werden
if ($getNewUser == 2 && $gPreferences['enable_registration_captcha'] == 1 && $gPreferences['captcha_type']=='calc')
{
	require_once('../../system/classes/captcha.php');
}

// User-ID nur uebernehmen, wenn ein vorhandener Benutzer auch bearbeitet wird
if($getUserId > 0 && $getNewUser != 0 && $getNewUser != 3)
{
    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

  // read user data
$user = new User($gDb, $gProfileFields, $getUserId);

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
        
        // wurde Nachname und Vorname uebergeben, dann diese bereits vorbelegen
        $user->setValue('LAST_NAME', $getLastname);
        $user->setValue('FIRST_NAME', $getFirstname);
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

$gNavigation->addUrl(CURRENT_URL);

// Formular wurde ueber "Zurueck"-Funktion aufgerufen, also alle Felder mit den vorherigen Werten fuellen
if(isset($_SESSION['profile_request']))
{
    $user->noValueCheck();

    foreach($gProfileFields->mProfileFields as $field)
    {
        $field_name = 'usf-'. $field->getValue('usf_id');
        if(isset($_SESSION['profile_request'][$field_name]))
        {
            $user->setValue($field->getValue('usf_name_intern'), $_SESSION['profile_request'][$field_name]);
        }
    }

    if(isset($_SESSION['profile_request']['usr_login_name']))
    {
    $user->setArray(array('usr_login_name' => $_SESSION['profile_request']['usr_login_name']));
    }
    if(isset($_SESSION['profile_request']['reg_org_id']))
    {
        $registrationOrgId = $_SESSION['profile_request']['reg_org_id'];
    }
    
    unset($_SESSION['profile_request']);
}          

// Alle Rollen auswerten, um festzustellen, welche Bereiche ausgegeben werden sollen
// 2014-01-27 - Philipp Loepmeier - philipp@rockcal.de
$role   = new TableRoles($gDb);
$count_show_roles = 0;
$class_name = '';
$akroclass  = 0; 
$aktiventag = '';
$result_role = getRolesFromDatabase($user->getValue('usr_id'));
while($row = $gDb->fetch_array($result_role)) {
    $role->clear();
    $role->setArray($row);
    $role_name = $role->getValue('rol_name');    

    if( $role_name == 'Mitglied' ) {
        $isPerson = true;
        $hiddenCategories = array("Stammdaten Lizenzen", "Stammdaten Wertungen", "Stammdaten Startbuch", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }   
    if( $role_name == 'Wertungsrichter' ) {
        $isPerson = false;
        $isLizenzler = true;
        $hiddenCategories = array("Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Turnierleiter' ) {
        $isPerson = false;
        $isLizenzler = true;
        $hiddenCategories = array("Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( substr($role_name,0,7) == 'Trainer' ) {
        $isPerson = false;
        $isLizenzler = true;
        $hiddenCategories = array("Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( substr($role_name,0,10) == 'Kursleiter' ) {
        $isPerson = false;
        $isLizenzler = true;
        $hiddenCategories = array("Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( substr($role_name,0,13) == 'Landestrainer' ) {
        $isPerson = false;
        $isLizenzler = true;
        $hiddenCategories = array("Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Vereine' ) {
        $isVerein = true;
        $isPerson = false;
        if($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle")){
          $hiddenCategories = array("Stammdaten Lizenzen", "Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation");
        } else {
          $hiddenCategories = array("Stammdaten Lizenzen", "Stammdaten Startbuch", "Stammdaten Wertungen", "Stammdaten Boogie-Woogie", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");        
        }  
    }      
    if( $role_name == 'Startbuch RR-S' ) {
        $class_name = 'Startbuch RR-S';
        $isStartbuch = true;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Akrobatikmeldung", "Stammdaten Boogie-Woogie", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch RR-J' ) {
        $class_name = 'Startbuch RR-J';
        $isStartbuch = true;
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Boogie-Woogie", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch RR-C' ) {
        $class_name = 'Startbuch RR-C';
        $isStartbuch = true;
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Boogie-Woogie", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch RR-C-Int' ) {
        $class_name = 'Startbuch RR-C-Int';
        $isStartbuch = true;
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Stammdaten Boogie-Woogie", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch RR-B' ) {
        $class_name = 'Startbuch RR-B';
        $isStartbuch = true;        
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Stammdaten Boogie-Woogie", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch RR-A' ) {
        $class_name = 'Startbuch RR-A';
        $isStartbuch = true;
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Stammdaten Boogie-Woogie", "Musikmeldung Formation", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch Formation' ) {
        $class_name = 'Startbuch Formation';
        $aktiventag = '8bis12';
        $isStartbuch = true;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Stammdaten Boogie-Woogie", "Musikmeldung B&A Klasse", "Stammdaten Herr", "Stammdaten Dame", "Akrobatikmeldung", "Akrobatiklisten", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch Formation Lady RR' ) {
        $aktiventag = '8bis16';
    }  
    if( $role_name == 'Startbuch Formation Show RR' ) {
        $aktiventag = '4bis16';
    }  
    if( $role_name == 'Startbuch Formation Master RR' ) {
        $class_name = 'Startbuch Formation Master RR';
        $isStartbuch = true;
        $akroclass = 1;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Stammdaten Boogie-Woogie", "Musikmeldung B&A Klasse", "Stammdaten Herr", "Stammdaten Dame", "Akrobatiklisten", "Stammdaten Gütesiegel");
    }
    if( $role_name == 'Startbuch BW' ) {
        $class_name = 'Startbuch BW';
        $isStartbuch = true;
        $isPerson = false;
        $hiddenCategories = array("Stammdaten Lizenzen", "Akrobatiklisten", "Musikmeldung B&A Klasse", "Musikmeldung Formation", "Akrobatikmeldung", "Stammdaten Formation", "Stammdaten Gütesiegel");
    }
             
}

if( substr($role_name,0,9) != 'Startbuch' && !$isStartbuch) { 
  $isVerein = true;  
  $isPerson = false;
}

if($isPerson) {
  $isVerein = false;
  $isStartbuch = false;
}

/** This function creates the html code for one profile field that is set in the parameters.
 *  The html output will consider the configuration of the profile field and creates the 
 *  neccessary html element. Also the data will be filled and the correct format will be set.
 *  @param $fieldNameIntern	Internal name of the profile field for which the html should be generated e.g. @b LAST_NAME or @b EMAIL
 *  @param $user			An object of the @b User class of the user that should be edited
 *  @param $getNewUser		The parameter @b new_user of the script @b profile_new.php
 *  @return Returns a string with the html of the profile field to add it to a html form
 */

function getFieldCode($fieldNameIntern, $user, $getNewUser, $akro_class, $verein_grundaten)
{
    global $gPreferences, $g_root_path, $gCurrentUser, $gL10n, $gProfileFields, $startbuchnr, $class_name, $aktiventag;
    $value    = '';
    
	// disable field if this is configured in profile field configuration
    $disabled = '';
    if($gProfileFields->getProperty($fieldNameIntern, 'usf_disabled') == 1 && $gCurrentUser->editUsers() == false && $getNewUser == 0)
    {
		$disabled = ' disabled="disabled" ';
    }

    // code for different field types
    
    if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'COUNTRY')
    {
		// create selectbox with all countries
        $value = '
		<select size="1" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" '.$disabled.'>
			<option value="" ';
                if(strlen($gPreferences['default_country']) == 0
                && strlen($user->getValue($fieldNameIntern)) == 0)
                {
                    $value = $value. ' selected="selected" ';
                }
			$value = $value. '>- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
			
			// first add default country to selectbox, so this country is very prominent to the user
            if(strlen($gPreferences['default_country']) > 0)
            {
                $value = $value. ' <option value="">--------------------------------</option>
				<option value="'. $gPreferences['default_country']. '">'. $gL10n->getCountryByCode($gPreferences['default_country']). '</option>
                <option value="">--------------------------------</option>';
            }
			
			// add all countries to selectbox and select the assigned or default country
			foreach($gL10n->getCountries() as $key => $country_name)
			{
				$value = $value. '<option value="'.$key.'" ';
				if($user->getValue('usr_id') == 0 && $key == $gPreferences['default_country'])
				{
					$value = $value. ' selected="selected" ';
				}
				if($user->getValue('usr_id') > 0 && $country_name == $user->getValue($fieldNameIntern))
				{
					$value = $value. ' selected="selected" ';
				}
				$value = $value. '>'.$country_name.'</option>';
			}
		$value = $value. '</select>';
    }
    // add all Music-IDs to selectbox
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_FUSSTECHNIK')
    {     
      $musikID_list = '';
      if($class_name == 'Startbuch RR-B'){
        $musiktitel_path  = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Fusstechnik/';
      } else {
        $musiktitel_path  = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Fusstechnik/';      
      }
      $musiktitel_path .= $startbuchnr.'*.mp3';
      foreach (glob($musiktitel_path) as $filename) {
        $filenamenopath = explode("/", $filename);
        $filenameonly   = explode(".", $filenamenopath[9]);//[Number] needs to match slashes of _path
        $musikID_list .= '<option value="'.$filenameonly[0].'">'.$filenameonly[0].'</option>';                       
      }  
      // create selectbox with all Musik-IDs
      $value = '
        <select size="1" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '
        " name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" '.$disabled.'>';
      if($musikID_list == ''){
        $value .= '<option value="" selected="selected">Keine zertifizierten Musiktitel gefunden!</option>';
      } else {
        $value .= '<option value="'.$user->getValue($fieldNameIntern).'" selected="selected" >'.$user->getValue($fieldNameIntern).'</option>';
        $value .= '<option value="" >----</option>';
      }                  
      $value .= $musikID_list.'</select>';
    }
    // add all Music-IDs to selectbox
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_AKROBATIK')
    {     
      $musikID_list = '';
      if($class_name == 'Startbuch RR-B'){
        $musiktitel_path  = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Akrobatik/';
      } else {
        $musiktitel_path  = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Akrobatik/';      
      }
      $musiktitel_path .= $startbuchnr.'*.mp3';
      foreach (glob($musiktitel_path) as $filename) {
        $filenamenopath = explode("/", $filename);
        $filenameonly   = explode(".", $filenamenopath[9]);//[Number] needs to match slashes of _path
        $musikID_list .= '<option value="'.$filenameonly[0].'">'.$filenameonly[0].'</option>';                       
      }  
      // create selectbox with all Musik-IDs
      $value = '
        <select size="1" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '
        " name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" '.$disabled.'>';
      if($musikID_list == ''){
        $value .= '<option value="" selected="selected">Keine zertifizierten Musiktitel gefunden!</option>';
      } else {
        $value .= '<option value="'.$user->getValue($fieldNameIntern).'" selected="selected" >'.$user->getValue($fieldNameIntern).'</option>';
        $value .= '<option value="" >----</option>';
      }                  
      $value .= $musikID_list.'<ops='.$class_name.'></select>';
    }
    // add all Music-IDs to selectbox
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_STELLPROBE' ||
           $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_TANZMUSIK'  ||
           $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'MUSIKTITEL_ERSATZMUSIK' )
    {     
      $musikID_list = '';
      $musiktitel_path  = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/Formationen/';
      $musiktitel_path .= $startbuchnr.'*.mp3';
      foreach (glob($musiktitel_path) as $filename) {
        $filenamenopath = explode("/", $filename);
        $filenameonly   = explode(".", $filenamenopath[8]);//[Number] needs to match slashes of _path
        $musikID_list .= '<option value="'.$filenameonly[0].'">'.$filenameonly[0].'</option>';                       
      }  
      // create selectbox with all Musik-IDs
      $value = '
        <select size="1" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '
        " name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" '.$disabled.'>';
      if($musikID_list == ''){
        $value .= '<option value="" selected="selected">Keine zertifizierten Musiktitel gefunden!</option>';
      } else {
        $value .= '<option value="'.$user->getValue($fieldNameIntern).'" selected="selected" >'.$user->getValue($fieldNameIntern).'</option>';
        $value .= '<option value="" >----</option>';
      }                  
      $value .= $musikID_list.'</select>';
    }    
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'CHECKBOX')
    {
        $mode = '';
        if($user->getValue($fieldNameIntern) == 1)
        {
            $mode = ' checked="checked" ';
        }
        $value = '<input type="checkbox" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" '.$mode.' '.$disabled.' value="1" />';
    }
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'DROPDOWN')
    {
		$arrListValues = $gProfileFields->getProperty($fieldNameIntern, 'usf_value_list');
		$position = 1;
		$text     = '';
		
		$value = '<select size="1" name="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'" id="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'" '.$disabled.'>
			<option value="" ';
                if(strlen($user->getValue($fieldNameIntern)) == 0)
                {
                    $value .= ' selected="selected" ';
                }
                if($gProfileFields->getProperty($fieldNameIntern, 'usf_mandatory') == 1)
                {
                    $text  .= '- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -';
                }
			$value .= '>'.$text.'</option>';

			// fuer jeden Feldtypen einen Eintrag in der Combobox anlegen
      // rmenken: modify selection list in case of Akrobatikmeldung 
      // if akrobatikmeldung
      $isAkromeldung = '';
      $arrVRZW = array(
        'AKROBATIK_1_-_VORRUNDE','AKROBATIK_2_-_VORRUNDE','AKROBATIK_3_-_VORRUNDE',
        'AKROBATIK_4_-_VORRUNDE','AKROBATIK_5_-_VORRUNDE','AKROBATIK_6_-_VORRUNDE',
        'AKROBATIK_7_-_VORRUNDE','AKROBATIK_8_-_VORRUNDE',
        'AKROBATIK_E_-_VORRUNDE','AKROBATIK_E2_-_VORRUNDE',  
        'AKROBATIK_1_-_ZWRUNDE','AKROBATIK_2_-_ZWRUNDE','AKROBATIK_3_-_ZWRUNDE',
        'AKROBATIK_4_-_ZWRUNDE','AKROBATIK_5_-_ZWRUNDE','AKROBATIK_6_-_ZWRUNDE',
        'AKROBATIK_7_-_ZWRUNDE','AKROBATIK_8_-_ZWRUNDE',  
        'AKROBATIK_E_-_ZWRUNDE','AKROBATIK_E2_-_ZWRUNDE');
      $arrER = array(
        'AKROBATIK_1_-_ENDRUNDE','AKROBATIK_2_-_ENDRUNDE','AKROBATIK_3_-_ENDRUNDE','AKROBATIK_4_-_ENDRUNDE',
        'AKROBATIK_5_-_ENDRUNDE','AKROBATIK_6_-_ENDRUNDE','AKROBATIK_7_-_ENDRUNDE','AKROBATIK_8_-_ENDRUNDE',
        'AKROBATIK_E_-_ENDRUNDE','AKROBATIK_E2_-_ENDRUNDE');           
      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern'), $arrVRZW))
      { 
        if ($akro_class == 'AkromeldungS'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-S', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungJ'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-J', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungC'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-C', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungCInt'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-C-INT', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungB'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-B-VR', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungA'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-A-VR', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungF'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-F-VR', 'usf_value_list');
          $isAkromeldung = 'true';
        }    
      }
      elseif(in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern'), $arrER))
      {        
        if ($akro_class == 'AkromeldungS'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-S', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungJ'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-J', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungC'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-C', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungCInt'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-C-INT', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungB'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-B-ER', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungA'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-A-ER', 'usf_value_list');
          $isAkromeldung = 'true';
        }
        if ($akro_class == 'AkromeldungF'){
          $arrListValues = $gProfileFields->getProperty('AKROBATIKLISTE-F', 'usf_value_list');
          $isAkromeldung = 'true';
        }    
      }
      
      //arrListValues entsprechend den Startklassen anpassen um sinnige Anzahl Aktiver zu forcieren
      if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'ANZAHL_AKTIVE'){
        //print_r($arrListValues);
        if($aktiventag == '8bis16'){
          $arrListValues = [8,9,10,11,12,13,14,15,16];
          $position      = 5;     
        } elseif($aktiventag == '4bis16'){
          $arrListValues = [4,5,6,7,8,9,10,11,12,13,14,15,16];
        } else {
          $arrListValues = [8,9,10,11,12];//MaBW,MaRR,Girl,Jgd        
          $position      = 5;     
        }
      }                
      
			foreach($arrListValues as $key => $valueList)
			{
        if ($isAkromeldung) {
          //Akrostring Beispiel: "A001 : 10,00 : Akroname : Kurzname : GruppenID"
          //                      [0]    [1]     [2]        [3]         [4]
          $valueList_content = explode(":", $valueList);
          //$valueList  = trim($valueList_content[0]) . ' : ' . trim($valueList_content[3]);
          $valueList  = trim($valueList_content[0]) . ' : ' . trim($valueList_content[3]) . ' : ' . trim($valueList_content[1] . 'Pkt.');
          $valueList3 = trim($valueList_content[0]) . ' : ' . trim($valueList_content[3]);
          //$valueList2 = trim($valueList_content[0]) . ' : ' . trim($valueList_content[3]) . ' : ' . trim($valueList_content[4]);
          $valueList2 = trim($valueList_content[0]) . ' : ' . trim($valueList_content[3]) . ' : ' . trim($valueList_content[4]) . ' : ' . trim($valueList_content[1]);
          $value .= '<option value="'.$valueList2.'" ';
          if($user->getValue($fieldNameIntern) == $valueList3)
          {
            $value .= ' selected="selected"';
          }               
        } else {
				$value .= '<option value="'.$position.'" '; 
				if($user->getValue($fieldNameIntern) == $valueList) 
				{
					$value .= ' selected="selected"';
				}
        }        
				$value .= '>'.$valueList.'</option>';
				$position++;
			}
      $isAkromeldung = '';           
		$value .= '</select>';
	}
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'RADIO_BUTTON')
    {
		$arrListValues = $gProfileFields->getProperty($fieldNameIntern, 'usf_value_list');
		$position = 1;
		$value = '';

        if($gProfileFields->getProperty($fieldNameIntern, 'usf_mandatory') == 0)
        {
	        $htmlChecked = '';
	        if(strlen($user->getValue($fieldNameIntern)) == 0)
	        {
	            $htmlChecked = ' checked="checked" ';
	        }
	        $value .= '<input type="radio" id="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'-0" name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" value="" '.$htmlChecked.' '.$disabled.' />
	            <label for="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id').'-0">---</label>&nbsp;&nbsp;';
        }

		// fuer jeden Feldtypen einen Eintrag in der Combobox anlegen
		foreach($arrListValues as $key => $valueList)
		{
	        $htmlChecked = '';
	        if($user->getValue($fieldNameIntern) == $valueList)
	        {
	            $htmlChecked = ' checked="checked" ';
	        }
	        
	        $value .= '<input type="radio" id="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'-'.$position.'" name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" value="'.$position.'" '.$htmlChecked.' '.$disabled.' />
	            <label for="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id').'-'.$position.'">'.$valueList.'</label>&nbsp;&nbsp;';
			$position++;
		}
		
    }
    elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'TEXT_BIG')
    {
        $usfId = 'usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id');
        $value = '<script type="text/javascript">
                    $(document).ready(function(){
                        $(\'#'.$usfId.'\').NobleCount(\'#'.$usfId.'_counter\',{
                            max_chars: 255,
                            on_negative: \'systeminfoBad\',
                            block_negative: true
                        });
                    });
                 </script>         
        <textarea  name="'.$usfId.'" id="'.$usfId.'" '.$disabled.' style="width: 300px;" rows="2" cols="40">'. $user->getValue($fieldNameIntern).'</textarea>
        (<span id="'.$usfId.'_counter" class="">255</span>)';
    }
    else
    {
        if($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'DATE')
        {
            $width = '80px';
            $maxlength = '10';
        }
        elseif($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'EMAIL' || $gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'URL')
        {
            $width     = '300px';
            $maxlength = '255';
        }
        elseif($gProfileFields->getProperty($fieldNameIntern, 'cat_name_intern') == 'SOCIAL_NETWORKS')
        {
            $width = '200px';
            $maxlength = '255';
        }
        else
        {
            $width = '200px';
            $maxlength = '50';
        }
        if($gProfileFields->getProperty($fieldNameIntern, 'usf_type') == 'DATE')
        {
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern') == 'BIRTHDAY')
            {
                $value = '<script type="text/javascript">
                            var calBirthday = new CalendarPopup("calendardiv");
                            calBirthday.setCssPrefix("calendar");
                            calBirthday.showNavigationDropdowns();
                            calBirthday.setYearSelectStartOffset(90);
                            calBirthday.setYearSelectEndOffset(0);
                        </script>';
                $calObject = 'calBirthday';
            }
            else
            {
                $value = '<script type="text/javascript">
                            var calDate = new CalendarPopup("calendardiv");
                            calDate.setCssPrefix("calendar");
                            calDate.showNavigationDropdowns();
                            calDate.setYearSelectStartOffset(50);
                            calDate.setYearSelectEndOffset(10);
                        </script>';
                $calObject = 'calDate';
            }
            $value .= '
                    <input type="text" id="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" name="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" style="width: '.$width.';" 
                        maxlength="'.$maxlength.'" '.$disabled.' value="'. $user->getValue($fieldNameIntern, $gPreferences['system_date']). '" '.$disabled.' />
                    <a class="iconLink" id="anchor_'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '" href="javascript:'.$calObject.'.select(document.getElementById(\'usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '\'),\'anchor_'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '\',\''.$gPreferences['system_date'].'\');"><img 
                    	src="'. THEME_PATH. '/icons/calendar.png" alt="'.$gL10n->get('SYS_SHOW_CALENDAR').'" title="'.$gL10n->get('SYS_SHOW_CALENDAR').'" /></a>
                    <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>';
        }
        else
        {
          //Hier bei Startbüchern die Hauptdaten vom Verein beziehen:
          // echo"Grunddaten: "; print_r($verein_grundaten);echo"<br>"; 
          $sqlab = "SELECT usd_value FROM adm_user_data WHERE usd_usr_id = '" . $_GET["user_id"] . "' AND usd_usf_id = '66' ";
          $ist_startbuch  = mysqli_query(ADMIDIOdb(), $sqlab);
          $startbuch_ja   = mysqli_fetch_row($ist_startbuch);
          $modified_value = $user->getValue($fieldNameIntern);
          
          if($startbuch_ja){
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Verantwrtl. Name"){
              $modified_value = $verein_grundaten[verantw_name];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Verantwrtl. Vorname"){
              $modified_value = $verein_grundaten[verantw_vorname];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Straße"){
              $modified_value = $verein_grundaten[straße];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "PLZ"){
              $modified_value = $verein_grundaten[plz];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Ort"){
              $modified_value = $verein_grundaten[ort];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Telefon Geschäftlich"){
              $modified_value = $verein_grundaten[verein_telefon_geschaeft];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Telefon"){
              $modified_value = $verein_grundaten[telefon];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Handy"){
              $modified_value = $verein_grundaten[handy];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "E-Mail"){
              $modified_value = $verein_grundaten[mail];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Webseite"){
              $modified_value = $verein_grundaten[website];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Vorstand Nachname"){
              $modified_value = $verein_grundaten[vorstand_name];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Vorstand Vorname"){
              $modified_value = $verein_grundaten[vorstand_vorname];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Vorstand Telefon"){
              $modified_value = $verein_grundaten[vorstand_telefon];
            }                       
            if($gProfileFields->getProperty($fieldNameIntern, 'usf_name') == "Vorstand seit"){
              $modified_value = $verein_grundaten[vorstand_seit];
            }                       
          }          
          $value = '<input type="text" id="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'" name="usf-'.$gProfileFields->getProperty($fieldNameIntern, 'usf_id').'" style="width: '.$width.';" maxlength="'.$maxlength.'" '.$disabled.' value="'.$modified_value.'" '.$disabled.' />';          
        }
    }
    
    // display icon of field
    $icon = '';
    if(strlen($gProfileFields->getProperty($fieldNameIntern, 'usf_icon')) > 0)
    {
        $icon = $gProfileFields->getProperty($fieldNameIntern, 'usf_icon').'&nbsp;';
    }
        
    // Kennzeichen fuer Pflichtfeld setzen
    $mandatory = '';
    if($gProfileFields->getProperty($fieldNameIntern, 'usf_mandatory') == 1)
    {
        $mandatory = '<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>';
    }
    
    // Fragezeichen mit Feldbeschreibung anzeigen, wenn diese hinterlegt ist
    $description = '';
    if(strlen($gProfileFields->getProperty($fieldNameIntern, 'usf_description')) > 0)
    {
        $description = '<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=user_field_description&amp;message_var1='. $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern'). '&amp;inline=true"><img 
            onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=user_field_description&amp;message_var1='. $gProfileFields->getProperty($fieldNameIntern, 'usf_name_intern'). '\',this)" onmouseout="ajax_hideTooltip()"
            class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="'.$gL10n->get('SYS_HELP').'" title="" /></a>';
    }
    
    $html = '<li>
                <dl>
                    <dt><label for="usf-'. $gProfileFields->getProperty($fieldNameIntern, 'usf_id'). '">'. $icon. $gProfileFields->getProperty($fieldNameIntern, 'usf_name'). ':</label></dt>
                    <dd>'. $myops . $value. $mandatory. $description. '</dd>
                </dl>
            </li>';
             
    // rmenken: get startbuchnummer for musik-ids
    if ($gProfileFields->getProperty($fieldNameIntern, 'usf_id') == 66) {
      $startbuchnr = $user->getValue($fieldNameIntern);
}

    // nun den Html-Code ausblenden fuer nicht belegte Felder bei der Akromeldung
    if($akro_class == 'AkromeldungA') {
      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_id'),array("48", "59", "75", "76", "77", "78", "79", "80"))){
        $html = '';
        }
        }
    if($akro_class == 'AkromeldungB') {
      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_id'),array("48", "75", "76", "149", "59", "77", "78", "150", "79", "80", "151"))){
        $html = '';
        }
}
    if($akro_class == 'AkromeldungC' || $akro_class == 'AkromeldungCInt' ) {
      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_id'),array("47", "48", "75", "76", "149", "58", "59", "77", "78", "150", "63", "64", "79", "80", "151"))){
        $html = '';
        }
    }
    if($akro_class == 'AkromeldungJ') {
      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_id'),array("46", "47", "48", "75", "76", "149", "57", "58", "59", "77", "78", "150", "62", "63", "64", "79", "80", "151"))){
        $html = '';
    }
}
    // rmenken: 18.01.2017 Ersatzakro2 fuer Formationen ist nun moeglich
    //    if($akro_class == 'AkromeldungF') {
    //      if (in_array($gProfileFields->getProperty($fieldNameIntern, 'usf_id'),array("149", "150", "151"))){
    //        $html = '';
    //      }
    //    }               

  return $html;
}//end function getFieldCode 

// Html-Kopf ausgeben
if($getNewUser == 1)
{
    $gLayout['title'] = $gL10n->get('PRO_ADD_USER');
}
elseif($getNewUser == 2)
{
    $gLayout['title'] = $gL10n->get('SYS_REGISTRATION');
}
elseif($getUserId == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = $gL10n->get('PRO_EDIT_MY_PROFILE');
}
else
{
    $gLayout['title'] = $gL10n->get('PRO_EDIT_PROFILE');
}

$gLayout['header'] = '
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/jquery/jquery.noblecount.min.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
	<script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
	<script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>
	<script type="text/javascript" src="'.$g_root_path.'/adm_program/modules/profile/profile.js"></script>
    <link rel="stylesheet" href="'.THEME_PATH.'/css/calendar.css" type="text/css" />';

$gLayout['header'] .= '
        <script type="text/javascript"><!--
			var profileJS = new profileJSClass();
			$(document).ready(function() 
            {
				profileJS.init();
				';

// setzt den Focus bei Neuanlagen/Registrierung auf das erste Feld im Dialog
if($getNewUser == 1 || $getNewUser == 2)
{
    if($getNewUser == 1)
    {
    	$first_field = reset($gProfileFields->mProfileFields);
        $focusField = 'usf-'.$first_field->getValue('usf_id');
    }
    else
    {
        $focusField = 'usr_login_name';
    }
	$gLayout['header'] .= '$("#'.$focusField.'").focus();';
}
$gLayout['header'] .= '}); 
        //--></script>';
require(SERVER_PATH. '/adm_program/system/overall_header.php');

// Stammdaten auslesen
$verein_grundaten = array();

$sqlab = "SELECT usd_value FROM adm_user_data WHERE usd_usf_id = 53 AND usd_usr_id = '" . $getUserId . "' ";
$verein_nummer = mysqli_query(ADMIDIOdb(), $sqlab);
$vereinsnummer = mysqli_fetch_row($verein_nummer);

$sqlab = "SELECT usd_usr_id, usd_value FROM adm_user_data WHERE usd_value = " . $vereinsnummer[0];
$vereinsdaten = mysqli_query(ADMIDIOdb(), $sqlab);
//echo'Vereinsdaten: '; print_r($vereinsdaten);echo"<br>";


while($verein_verein = mysqli_fetch_array($vereinsdaten))
    {

$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $verein_verein[0] . "' ORDER BY usd_usf_id";
$vereins_id = mysqli_query(ADMIDIOdb(), $sqlab);

while($stammdaten_verein = mysqli_fetch_array($vereins_id))
     {
      if($ausstieg == 1 && $stammdaten_verein[0] == 1)
        break; 
      if($stammdaten_verein[0] == 1  && $stammdaten_verein[1] == "verein")
        {
         // echo$stammdaten_verein[0] . " " . $stammdaten_verein[1] ;
         $ausstieg = 1;
        }

      $stammdaten_verein[1] = str_replace('/', '-', $stammdaten_verein[1]);
      $stammdaten_verein[1] = str_replace(' ', '&#32;', $stammdaten_verein[1]);
      
      if($stammdaten_verein[0] == 7)
         $verein_grundaten[telefon] = $stammdaten_verein[1];   
      if($stammdaten_verein[0] == 8)
         $verein_grundaten[handy] = $stammdaten_verein[1];      
      if($stammdaten_verein[0] == 12)
         $verein_grundaten[mail] = $stammdaten_verein[1];        
      if($stammdaten_verein[0] == 72)
         $verein_grundaten[straße] = utf8_encode($stammdaten_verein[1]);        
      if($stammdaten_verein[0] == 73)
         $verein_grundaten[plz] = $stammdaten_verein[1]; 
      if($stammdaten_verein[0] == 74)
         $verein_grundaten[ort] = utf8_encode($stammdaten_verein[1]);              
      if($stammdaten_verein[0] == 127)
         $verein_grundaten[verantw_name] = utf8_encode($stammdaten_verein[1]);
      if($stammdaten_verein[0] == 128)
         $verein_grundaten[verantw_vorname] = utf8_encode($stammdaten_verein[1]);
      if($stammdaten_verein[0] == 130)
         $verein_grundaten[website] = $stammdaten_verein[1];        
      if($stammdaten_verein[0] == 132)
         $verein_grundaten[vorstand_name] = utf8_encode($stammdaten_verein[1]);
      if($stammdaten_verein[0] == 133)
         $verein_grundaten[vorstand_vorname] = utf8_encode($stammdaten_verein[1]);
      if($stammdaten_verein[0] == 134)
         $verein_grundaten[vorstand_telefon] = $stammdaten_verein[1];         
      if($stammdaten_verein[0] == 135)
         $verein_grundaten[vorstand_seit] = $stammdaten_verein[1];         
      if($stammdaten_verein[0] == 137)
         $verein_grundaten[verein_telefon_geschaeft] = $stammdaten_verein[1];
    
      }             
  
      // print_r($verein_grundaten);echo"<br>";           
    }         
             
echo '<form action="'.$g_root_path.'/adm_program/modules/profile/profile_save.php?user_id='.$getUserId.'&amp;new_user='.$getNewUser.'&amp;akroclass='.$akroclass.'" method="post">
<div class="formLayout" id="edit_profile_form">
    <div class="formHead">'. $gLayout['title']. '</div>
    <div class="formBody">'; 
        // *******************************************************************************
        // Schleife ueber alle Kategorien und Felder ausser den Stammdaten
        // *******************************************************************************

        $category = '';
        
        foreach($gProfileFields->mProfileFields as $field)
        {
            $show_field = false;
            
            // bei schneller Registrierung duerfen nur die Pflichtfelder ausgegeben werden
            // E-Mail ist Ausnahme und muss immer angezeigt werden
            if($getNewUser == 2 
            && $gPreferences['registration_mode'] == 1 
            && ($field->getValue('usf_mandatory') == 1 || $field->getValue('usf_name_intern') == 'EMAIL'))
            {
                $show_field = true;
            }
            elseif($getNewUser == 2
            && $gPreferences['registration_mode'] == 2)
            {
                // bei der vollstaendigen Registrierung alle Felder anzeigen
                $show_field = true;
            }
            elseif($getNewUser != 2 
            && ($getUserId == $gCurrentUser->getValue('usr_id') || $gCurrentUser->editProfile($user)))
            {
                // bei fremden Profilen duerfen versteckte Felder nur berechtigten Personen angezeigt werden
                // Leiter duerfen dies nicht !!!
                $show_field = true;
            }
        
            // Kategorienwechsel den Kategorienheader anzeigen
            // bei schneller Registrierung duerfen nur die Pflichtfelder ausgegeben werden
            if($category != $field->getValue('cat_name')
            && $show_field == true)
            {
                if(strlen($category) > 0)
                {
                    if ($field->getValue('cat_id') == 34 or $field->getValue('cat_id') == 42 ) {
                      $musiktitel_html = '';
                      if ($class_name == 'Startbuch RR-B') {
                        $musiktitel_ftpath = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Fusstechnik/';
                        $musiktitel_akpath = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Akrobatik/';
                      } else if ($class_name == 'Startbuch RR-A') {
                        $musiktitel_ftpath = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Fusstechnik/';
                        $musiktitel_akpath = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Akrobatik/';
                      } else {
                        $musiktitel_formpath = '../../../adm_my_files/download/Turniermusik/Zertifizierte-Musik/Formationen/';
                      }
                      $musiktitel_ftpath   .= $startbuchnr.'*.mp3';
                      $musiktitel_akpath   .= $startbuchnr.'*.mp3';
                      $musiktitel_formpath .= $startbuchnr.'*.mp3';
                      foreach (glob($musiktitel_ftpath) as $filename) {
                        $filenamenopath = explode("/", $filename);
                        $filenameonly = explode(".", $filenamenopath[9]);
                        $musiktitel_html .= $filenameonly[0].'<br>';                       
                      }
                      foreach (glob($musiktitel_akpath) as $filename) {
                        $filenamenopath = explode("/", $filename);
                        $filenameonly = explode(".", $filenamenopath[9]);
                        $musiktitel_html .= $filenameonly[0].'<br>';                       
                      }                      
                      foreach (glob($musiktitel_formpath) as $filename) {
                        $filenamenopath = explode("/", $filename);
                        $filenameonly = explode(".", $filenamenopath[8]);
                        $musiktitel_html .= $filenameonly[0].'<br>';                       
                      }                      
                      if ($musiktitel_html == '') {
                        $musiktitel_html = 'Keine zertifizierten Musiktitel gefunden!';
                      }                    
                      echo '
                        <li>
                          <dl>
                            <dt><label>Zertifizierte Musik-IDs:</label></dt>
                            <dd>'.$musiktitel_html.'</dd>
                          </dl>
                        </li>
                      </ul></div></div>';
                   } else {
                    // div-Container groupBoxBody und groupBox schliessen
                    echo '</ul></div></div>';
                }
                }
              
                $category = $field->getValue('cat_name');

                // Paarbereiche bei Vereinen ausblenden
                // 2014-01-27 - Philipp Loepmeier - philipp@rockcal.de
                $style = '';
                $isHiddenCategory = in_array( $category, $hiddenCategories );
                if( ($isVerein || $isStartbuch || $isPerson) && $isHiddenCategory ) {
                    $style = ' style="display:none;"';
                }
                //
                $akro_class = '';
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
              
                echo '<a name="cat-'. $field->getValue('cat_id'). '"></a>
                <div class="groupBox"'.$style.'>
                    <div class="groupBoxHeadline">'. $field->getValue('cat_name'). '</div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">';
                        
                if($field->getValue('cat_name_intern') == 'MASTER_DATA')
                {
                    // bei den Stammdaten erst einmal Benutzername und Passwort anzeigen
                    if($getUserId > 0 || $getNewUser == 2)
                    {
                        echo '<li>
                            <dl>
                                <dt><label for="usr_login_name">'.$gL10n->get('SYS_USERNAME').':</label></dt>
                                <dd>
                                    <input type="text" id="usr_login_name" name="usr_login_name" style="width: 200px;" maxlength="35" value="'. $user->getValue('usr_login_name'). '" ';
                                    if($gCurrentUser->isWebmaster() == false && $getNewUser == 0)
                                    {
                                        echo ' disabled="disabled" ';
                                    }
                                    echo ' />';

                                    if($getNewUser > 0)
                                    {
                                        echo '<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
                                        <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=PRO_USERNAME_DESCRIPTION&amp;inline=true"><img 
                                            onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=PRO_USERNAME_DESCRIPTION\',this)" onmouseout="ajax_hideTooltip()"
                                            class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="'.$gL10n->get('SYS_HELP').'" title="" /></a>';
                                    }
                                echo '</dd>
                            </dl>
                        </li>';

                        if($getNewUser == 2)
                        {
                            echo '<li>
                                <dl>
                                    <dt><label for="usr_password">'.$gL10n->get('SYS_PASSWORD').':</label></dt>
                                    <dd>
                                        <input type="password" id="usr_password" name="usr_password" style="width: 130px;" maxlength="20" />
                                        <span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
                                        <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=PRO_PASSWORD_DESCRIPTION&amp;inline=true"><img 
                                            onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=PRO_PASSWORD_DESCRIPTION\',this)" onmouseout="ajax_hideTooltip()"
                                            class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="'.$gL10n->get('SYS_HELP').'" title="" /></a>
                                    </dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="password2">'.$gL10n->get('SYS_CONFIRM_PASSWORD').':</label></dt>
                                    <dd>
                                        <input type="password" id="password2" name="password2" style="width: 130px;" maxlength="20" />
                                        <span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
                                    </dd>
                                </dl>
                            </li>';
                			// show selectbox with all organizations of database
                			if($gPreferences['system_organization_select'] == 1)
                			{
                				echo '<li>
                					<dl>
                						<dt><label for="reg_org_id">'.$gL10n->get('SYS_ORGANIZATION').':</label></dt>
                						<dd>'.FormElements::generateOrganizationSelectBox($registrationOrgId, 'reg_org_id').'</dd>
                					</dl>
                				</li>';
                			}
                            
                        }
                        else
                        {
                            // eigenes Passwort aendern, nur Webmaster duerfen Passwoerter von anderen aendern
                            if($gCurrentUser->isWebmaster() || $gCurrentUser->getValue("usr_id") == $getUserId )
                            {
                                echo '<li>
                                    <dl>
                                        <dt><label>'.$gL10n->get('SYS_PASSWORD').':</label></dt>
                                        <dd>
                                            <span class="iconTextLink">
                                                <a rel="colorboxPWContent" href="password.php?usr_id='. $getUserId. '&amp;inline=1"><img 
                                                	src="'. THEME_PATH. '/icons/key.png" alt="'.$gL10n->get('SYS_CHANGE_PASSWORD').'" title="'.$gL10n->get('SYS_CHANGE_PASSWORD').'" /></a>
                                                <a rel="colorboxPWContent" href="password.php?usr_id='. $getUserId. '&amp;inline=1">'.$gL10n->get('SYS_CHANGE_PASSWORD').'</a>
                                            </span>
                                        </dd>
                                    </dl>
                                </li>';
                            }
                        }
                        echo '<li><hr /></li>';
                    }
                }
            }
            // bei schneller Registrierung duerfen nur die Pflichtfelder ausgegeben werden
            if($show_field == true)
            {
                // Html des Feldes ausgeben
              echo getFieldCode($field->getValue('usf_name_intern'), $user, $getNewUser, $akro_class, $verein_grundaten);
            }
        }
        
        // div-Container groupBoxBody und groupBox schliessen
        echo '</ul></div></div>';

        // User, die sich registrieren wollen, bekommen jetzt noch das Captcha praesentiert,
        // falls es in den Orgaeinstellungen aktiviert wurde...
        if ($getNewUser == 2 && $gPreferences['enable_registration_captcha'] == 1)
        {
            echo '
            <ul class="formFieldList">
                <li>
                    <dl>
                        <dt>&nbsp;</dt>
						<dd>
						';
			if($gPreferences['captcha_type']=='pic')
			{
				echo '<img src="'.$g_root_path.'/adm_program/system/classes/captcha.php?id='. time(). '&type=pic" alt="'.$gL10n->get('SYS_CAPTCHA').'" />';
				$captcha_label = $gL10n->get('SYS_CAPTCHA_CONFIRMATION_CODE');
				$captcha_description = 'SYS_CAPTCHA_DESCRIPTION';
			}
			else if($gPreferences['captcha_type']=='calc')
			{
				$captcha = new Captcha();
				$captcha->getCaptchaCalc($gL10n->get('SYS_CAPTCHA_CALC_PART1'),$gL10n->get('SYS_CAPTCHA_CALC_PART2'),$gL10n->get('SYS_CAPTCHA_CALC_PART3_THIRD'),$gL10n->get('SYS_CAPTCHA_CALC_PART3_HALF'),$gL10n->get('SYS_CAPTCHA_CALC_PART4'));
				$captcha_label = $gL10n->get('SYS_CAPTCHA_CALC');
				$captcha_description = 'SYS_CAPTCHA_CALC_DESCRIPTION';
			}
      
			echo '
                    </dd>
					</dl>
                </li>
                <li>
                    <dl>
                        <dt>'.$captcha_label.':</dt>
                        <dd>
                            <input type="text" id="captcha" name="captcha" style="width: 200px;" maxlength="8" value="" />
                            <span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
                            <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id='.$captcha_description.'&amp;inline=true"><img 
					            onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id='.$captcha_description.'\',this)" onmouseout="ajax_hideTooltip()"
					            class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="'.$gL10n->get('SYS_HELP').'" title="" /></a>
                        </dd>
                    </dl>
                </li>
            </ul>
            <hr />';
        }

        // Bild und Text fuer den Speichern-Button
        if($getNewUser == 2)
        {
            // Registrierung
            $btn_image = 'email.png';
            $btn_text  = $gL10n->get('SYS_SEND');
        }
        else
        {
            $btn_image = 'disk.png';
            $btn_text  = $gL10n->get('SYS_SAVE');
        }

        if($getNewUser == 0)
        {
            // show informations about user who creates the recordset and changed it
            echo admFuncShowCreateChangeInfoById($user->getValue('usr_usr_id_create'), $user->getValue('usr_timestamp_create'), $user->getValue('usr_usr_id_change'), $user->getValue('usr_timestamp_change'));
        }

        echo '
        <div class="formSubmit">
            <button id="btnSave" type="submit"><img 
                src="'. THEME_PATH. '/icons/'. $btn_image. '" alt="'. $btn_text. '" />
                &nbsp;'. $btn_text. '</button>
        </div>
    </div>
</div>
</form>

<ul class="iconTextLinkList">
    <li>
        <span class="iconTextLink">
            <a href="'. $g_root_path. '/adm_program/system/back.php"><img 
            src="'. THEME_PATH. '/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
            <a href="'. $g_root_path. '/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
        </span>
    </li>
</ul>';

require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>