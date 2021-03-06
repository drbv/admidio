<?php
/******************************************************************************
 * Installation and configuration of Admidio database and config file
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * mode     = 1 : (Default) Choose language
 *            2 : Welcome to installation
 *            3 : Enter database access information
 *            4 : Creating organization
 *            5 : Creating administrator
 *            6 : Creating configuration file
 *            7 : Download configuration file
 *            8 : Start installation
 *
 *****************************************************************************/

session_name('admidio_php_session_id');
session_start();

if(isset($_SESSION['prefix']))
{
    $g_tbl_praefix = $_SESSION['prefix'];
}
else
{
	// default praefix is "adm" because of compatibility to older versions
    $g_tbl_praefix = 'adm';
}
 
// embed constants file
require_once(substr(__FILE__, 0, strpos(__FILE__, 'adm_install')-1). '/adm_program/system/constants.php');

// check PHP version and show notice if version is too low
if(version_compare(phpversion(), MIN_PHP_VERSION) == -1)
{
    die('<div style="color: #CC0000;">Error: Your PHP version '.phpversion().' does not fulfill 
		the minimum requirements for this Admidio version. You need at least PHP '.MIN_PHP_VERSION.' or more highly.</div>');
}

require_once('install_functions.php');
require_once(SERVER_PATH. '/adm_program/system/string.php');
require_once(SERVER_PATH. '/adm_program/system/function.php');
require_once(SERVER_PATH. '/adm_program/system/classes/datetime_extended.php');
require_once(SERVER_PATH. '/adm_program/system/classes/form_elements.php');
require_once(SERVER_PATH. '/adm_program/system/classes/language.php');
require_once(SERVER_PATH. '/adm_program/system/classes/language_data.php');
require_once(SERVER_PATH. '/adm_program/system/classes/list_configuration.php');
require_once(SERVER_PATH. '/adm_program/system/classes/organization.php');
require_once(SERVER_PATH. '/adm_program/system/classes/profile_fields.php');
require_once(SERVER_PATH. '/adm_program/system/classes/table_members.php');
require_once(SERVER_PATH. '/adm_program/system/classes/table_roles.php');
require_once(SERVER_PATH. '/adm_program/system/classes/table_text.php');
require_once(SERVER_PATH. '/adm_program/system/classes/user.php');
require_once(SERVER_PATH. '/adm_program/system/db/database.php');

// Initialize and check the parameters

$getMode = admFuncVariableIsValid($_GET, 'mode', 'numeric', 1);
$message = '';

// default database type is always MySQL and must be set because of old config files
if(!isset($gDbType))
{
    $gDbType = 'mysql';
}

// create language and language data object to handle translations
if(isset($_SESSION['language']))
{
    $language = $_SESSION['language'];
}
else
{
    $language = 'en';
}
$gL10n = new Language();
$gLanguageData = new LanguageData($language);
$gL10n->addLanguageData($gLanguageData);

if($getMode == 1)  // (Default) Choose language
{
    session_destroy();

    $message = '<div class="groupBox">
                    <div class="groupBoxHeadline">'.$gL10n->get('INS_CHOOSE_LANGUAGE').'</div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">
                            <li>
                                <dl>
                                    <dt><label for="system_language">'.$gL10n->get('SYS_LANGUAGE').':</label></dt>
                                    <dd>'. FormElements::generateXMLSelectBox(SERVER_PATH.'/adm_program/languages/languages.xml', 'ISOCODE', 'NAME', 'system_language').'</dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
                <br />';
    showPage($message, 'installation.php?mode=2', 'forward.png', $gL10n->get('SYS_NEXT'));
}
elseif($getMode == 2)  // Welcome to installation
{   
    // Pruefen ob Sprache uebergeben wurde
    if(isset($_POST['system_language']) == false || strlen($_POST['system_language']) == 0)
    {
        showPage($gL10n->get('INS_LANGUAGE_NOT_CHOOSEN'), 'installation.php?mode=1', 'back.png', $gL10n->get('SYS_BACK'));
    }
    else
    {
        $_SESSION['language'] = $_POST['system_language'];
        $gL10n->setLanguage($_SESSION['language']);
    }
    
    $message = '<strong>'.$gL10n->get('INS_WELCOME_TO_INSTALLATION').'</strong><br /><br />'.$gL10n->get('INS_WELCOME_TEXT');

    // falls dies eine Betaversion ist, dann Hinweis ausgeben
    if(BETA_VERSION > 0)
    {
        $message .= '<br /><br /><img style="vertical-align: top;" src="layout/warning.png" alt="'.$gL10n->get('SYS_WARNING').'" />'.$gL10n->get('INS_WARNING_BETA_VERSION');
    }

    if(ini_get('safe_mode') == 1)
    {    
        $message .= '<br /><br /><img style="vertical-align: top;" src="layout/warning.png" alt="'.$gL10n->get('SYS_WARNING').'" />'.$gL10n->get('INS_WARNING_SAFE_MODE');
    }
    showPage($message, 'installation.php?mode=3', 'forward.png', $gL10n->get('INS_DATABASE_LOGIN'));
}
elseif($getMode == 3)  // Enter database access information
{
    // initialize form data
    if(isset($_SESSION['server']))
    {
        $dbType   = $_SESSION['db_type'];
        $server   = $_SESSION['server'];
        $user     = $_SESSION['user'];
        $database = $_SESSION['database'];
        $prefix   = $_SESSION['prefix'];
    }
    else
    {
		$dbType   = 'mysql';
        $server   = '';
        $user     = '';
        $database = '';
        $prefix   = 'adm';
    }

    $message = '<strong>'.$gL10n->get('INS_ENTER_LOGIN_TO_DATABASE').'</strong><br /><br />'.$gL10n->get('INS_DATABASE_LOGIN_DESC').'
                <div class="groupBox">
                    <div class="groupBoxHeadline">'.$gL10n->get('INS_DATABASE_LOGIN').'</div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">
                            <li>
                                <dl>
                                    <dt><label for="db_type">'.$gL10n->get('INS_DATABASE_SYSTEM').':</label></dt>
                                    <dd>'. FormElements::generateXMLSelectBox(SERVER_PATH.'/adm_program/system/db/databases.xml', 'IDENTIFIER', 'NAME', 'db_type', $dbType).'</dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="server">'.$gL10n->get('SYS_SERVER').':</label></dt>
                                    <dd><input type="text" name="server" id="server" style="width: 250px;" maxlength="50" value="'. $server. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user">'.$gL10n->get('SYS_USERNAME').':</label></dt>
                                    <dd><input type="text" name="user" id="user" style="width: 250px;" maxlength="50" value="'. $user. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="password">'.$gL10n->get('SYS_PASSWORD').':</label></dt>
                                    <dd><input type="password" name="password" id="password" style="width: 250px;" maxlength="50" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="database">'.$gL10n->get('SYS_DATABASE').':</label></dt>
                                    <dd><input type="text" name="database" id="database" style="width: 250px;" maxlength="50" value="'. $database. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="prefix">'.$gL10n->get('INS_TABLE_PREFIX').':</label></dt>
                                    <dd><input type="text" name="prefix" id="prefix" style="width: 80px;" maxlength="10" value="'. $prefix. '" /></dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
                <br />
                <img src="layout/warning.png" alt="'.$gL10n->get('SYS_WARNING').'" />&nbsp;'.$gL10n->get('INS_TABLE_PREFIX_OVERRIDE_DATA').'<br />';
    showPage($message, 'installation.php?mode=4', 'forward.png', $gL10n->get('INS_SET_ORGANIZATION'));
}
elseif($getMode == 4)  // Creating organization
{
    if(isset($_POST['server']))
    {
        if(strlen($_POST['prefix']) == 0)
        {
            $_POST['prefix'] = 'adm';
        }
        else
        {
            // wenn letztes Zeichen ein _ dann abschneiden
            if(strrpos($_POST['prefix'], '_')+1 == strlen($_POST['prefix']))
            {
                $_POST['prefix'] = substr($_POST['prefix'], 0, strlen($_POST['prefix'])-1);
            }

            // nur gueltige Zeichen zulassen
            $anz = strspn($_POST['prefix'], 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_');

            if($anz != strlen($_POST['prefix']))
            {
                showPage($gL10n->get('INS_TABLE_PREFIX_INVALID'), 'installation.php?mode=3', 'back.png', $gL10n->get('SYS_BACK'));
            }
        }

        // Zugangsdaten der DB in Sessionvariablen gefiltert speichern
        $_SESSION['db_type']  = strStripTags($_POST['db_type']);
        $_SESSION['server']   = strStripTags($_POST['server']);
        $_SESSION['user']     = strStripTags($_POST['user']);
        $_SESSION['password'] = strStripTags($_POST['password']);
        $_SESSION['database'] = strStripTags($_POST['database']);
        $_SESSION['prefix']   = strStripTags($_POST['prefix']);

        if(strlen($_SESSION['db_type'])  == 0
		|| strlen($_SESSION['server'])   == 0
        || strlen($_SESSION['user'])     == 0
        || strlen($_SESSION['database']) == 0 )
        {
            showPage($gL10n->get('INS_MYSQL_LOGIN_NOT_COMPLETELY'), 'installation.php?mode=3', 'back.png', $gL10n->get('SYS_BACK'));
        }

        // pruefen, ob eine Verbindung zur Datenbank erstellt werden kann
        $db = Database::createDatabaseObject($_SESSION['db_type']);
        if($db->connect($_SESSION['server'], $_SESSION['user'], $_SESSION['password'], $_SESSION['database']) == false)
        {
            showPage($gL10n->get('INS_DATABASE_NO_LOGIN'), 'installation.php?mode=3', 'back.png', $gL10n->get('SYS_BACK'));
        }

        //Datenbank- und PHP-Version prüfen
        if(checkVersions($db, $message) == false)
        {
            showPage($message, 'installation.php?mode=3', 'back.png', $gL10n->get('SYS_BACK'));
        }
    }

    // initialize form data
    if(isset($_SESSION['orgaShortName']))
    {
        $orgaShortName = $_SESSION['orgaShortName'];
        $orgaLongName  = $_SESSION['orgaLongName'];
    }
    else
    {
        $orgaShortName = '';
        $orgaLongName  = '';
    }

    $message = $message.'<strong>'.$gL10n->get('INS_SET_ORGANIZATION').'</strong><br /><br />
                '.$gL10n->get('INS_NAME_OF_ORGANIZATION_DESC').'
                <div class="groupBox">
                    <div class="groupBoxHeadline">'.$gL10n->get('INS_NAME_OF_ORGANIZATION').'</div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">
                            <li>
                                <dl>
                                    <dt><label for="orgaShortName">'.$gL10n->get('SYS_NAME_ABBREVIATION').':</label></dt>
                                    <dd><input type="text" name="orgaShortName" id="orgaShortName" style="width: 80px;" maxlength="10" value="'. $orgaShortName. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="orgaLongName">'.$gL10n->get('SYS_NAME').':</label></dt>
                                    <dd><input type="text" name="orgaLongName" id="orgaLongName" style="width: 250px;" maxlength="60" value="'. $orgaLongName. '" /></dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
                <br />';
    showPage($message, 'installation.php?mode=5', 'forward.png', $gL10n->get('INS_CREATE_ADMINISTRATOR'));
}
elseif($getMode == 5)  // Creating addministrator
{
    if(isset($_POST['orgaShortName']))
    {
        // Zugangsdaten der DB in Sessionvariablen gefiltert speichern
        $_SESSION['orgaShortName'] = strStripTags($_POST['orgaShortName']);
        $_SESSION['orgaLongName']  = strStripTags($_POST['orgaLongName']);

        if(strlen($_SESSION['orgaShortName']) == 0
        || strlen($_SESSION['orgaLongName']) == 0 )
        {
            showPage($gL10n->get('INS_ORGANIZATION_NAME_NOT_COMPLETELY'), 'installation.php?mode=4', 'back.png', $gL10n->get('SYS_BACK'));
        }
    }

    // initialize form data
    if(isset($_SESSION['user_last_name']))
    {
        $user_last_name  = $_SESSION['user_last_name'];
        $user_first_name = $_SESSION['user_first_name'];
        $user_email      = $_SESSION['user_email'];
        $user_login      = $_SESSION['user_login'];
    }
    else
    {
        $user_last_name  = '';
        $user_first_name = '';
        $user_email      = '';
        $user_login      = '';
    }
    $message = '<strong>'.$gL10n->get('INS_CREATE_ADMINISTRATOR').'</strong><br /><br />
                '.$gL10n->get('INS_DATA_OF_ADMINISTRATOR_DESC').'
                <div class="groupBox">
                    <div class="groupBoxHeadline">'.$gL10n->get('INS_DATA_OF_ADMINISTRATOR').'</div>
                    <div class="groupBoxBody">
                        <ul class="formFieldList">
                            <li>
                                <dl>
                                    <dt><label for="user_last_name">'.$gL10n->get('SYS_LASTNAME').':</label></dt>
                                    <dd><input type="text" name="user_last_name" id="user_last_name" style="width: 250px;" maxlength="50" value="'. $user_last_name. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user_first_name">'.$gL10n->get('SYS_FIRSTNAME').':</label></dt>
                                    <dd><input type="text" name="user_first_name" id="user_first_name" style="width: 250px;" maxlength="50" value="'. $user_first_name. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user_email">'.$gL10n->get('SYS_EMAIL').':</label></dt>
                                    <dd><input type="text" name="user_email" id="user_email" style="width: 250px;" maxlength="50" value="'. $user_email. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user_login">'.$gL10n->get('SYS_USERNAME').':</label></dt>
                                    <dd><input type="text" name="user_login" id="user_login" style="width: 250px;" maxlength="35" value="'. $user_login. '" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user_password">'.$gL10n->get('SYS_PASSWORD').':</label></dt>
                                    <dd><input type="password" name="user_password" id="user_password" style="width: 150px;" maxlength="20" /></dd>
                                </dl>
                            </li>
                            <li>
                                <dl>
                                    <dt><label for="user_password_confirm">'.$gL10n->get('SYS_CONFIRM_PASSWORD').':</label></dt>
                                    <dd><input type="password" name="user_password_confirm" id="user_password_confirm" style="width: 150px;" maxlength="20" /></dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
                <br />';
    showPage($message, 'installation.php?mode=6', 'forward.png', $gL10n->get('INS_CREATE_CONFIGURATION_FILE'));
}
elseif($getMode == 6)  // Creating configuration file
{
    if(isset($_POST['user_last_name']))
    {
        // Daten des Administrators in Sessionvariablen gefiltert speichern
        $_SESSION['user_last_name']  = strStripTags($_POST['user_last_name']);
        $_SESSION['user_first_name'] = strStripTags($_POST['user_first_name']);
        $_SESSION['user_email']      = strStripTags($_POST['user_email']);
        $_SESSION['user_login']      = strStripTags($_POST['user_login']);
        $_SESSION['user_password']   = strStripTags($_POST['user_password']);
        $_SESSION['user_password_confirm'] = strStripTags($_POST['user_password_confirm']);

        if(strlen($_SESSION['user_last_name'])  == 0
        || strlen($_SESSION['user_first_name']) == 0
        || strlen($_SESSION['user_email'])     == 0
        || strlen($_SESSION['user_login'])      == 0
        || strlen($_SESSION['user_password'])   == 0 )
        {
            showPage($gL10n->get('INS_ADMINISTRATOR_DATA_NOT_COMPLETELY'), 'installation.php?mode=5', 'back.png', $gL10n->get('SYS_BACK'));
        }

        $_SESSION['user_email'] = admStrToLower($_SESSION['user_email']);
        if(!strValidCharacters($_SESSION['user_email'], 'email'))
        {
            showPage($gL10n->get('SYS_EMAIL_INVALID', $gL10n->get('SYS_EMAIL')), 'installation.php?mode=5', 'back.png', $gL10n->get('SYS_BACK'));
        }

        if($_SESSION['user_password'] != $_SESSION['user_password_confirm'])
        {
            showPage($gL10n->get('INS_PASSWORDS_NOT_EQUAL'), 'installation.php?mode=5', 'back.png', $gL10n->get('SYS_BACK'));
        }
    }

    $message = '<strong>'.$gL10n->get('INS_CREATE_CONFIGURATION_FILE').'</strong><br /><br />
                '.$gL10n->get('INS_DOWNLOAD_CONFIGURATION_FILE', 'config.php', 'config_example.php').'<br /><br />

                <span class="iconTextLink">
                    <a href="installation.php?mode=7"><img
                    src="layout/page_white_download.png" alt="'.$gL10n->get('INS_DOWNLOAD', 'config.php').'" /></a>
                    <a href="installation.php?mode=7">'.$gL10n->get('INS_DOWNLOAD', 'config.php').'</a>
                </span>
                <br />';
    showPage($message, 'installation.php?mode=8', 'database_in.png', $gL10n->get('INS_INSTALL_ADMIDIO'));
}
elseif($getMode == 7) // Download configuration file
{
    // MySQL-Zugangsdaten in config.php schreiben
    // Datei auslesen
    $filename     = 'config.php';
    $config_file  = fopen($filename, 'r');
    $file_content = fread($config_file, filesize($filename));
    fclose($config_file);

    // den Root-Pfad ermitteln
    $root_path = $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
    $root_path = substr($root_path, 0, strpos($root_path, '/adm_install'));
    if(!strpos($root_path, 'http://'))
    {
        $root_path = 'http://'. $root_path;
    }

    $file_content = str_replace('%PREFIX%',  $_SESSION['prefix'],  $file_content);
    $file_content = str_replace('%DB_TYPE%', $_SESSION['db_type'], $file_content);
    $file_content = str_replace('%SERVER%',  $_SESSION['server'],  $file_content);
    $file_content = str_replace('%USER%',    $_SESSION['user'],    $file_content);
    $file_content = str_replace('%PASSWORD%',$_SESSION['password'],$file_content);
    $file_content = str_replace('%DATABASE%',$_SESSION['database'],$file_content);
    $file_content = str_replace('%ROOT_PATH%', $root_path, $file_content);
    $file_content = str_replace('%ORGANIZATION%', $_SESSION['orgaShortName'], $file_content);

    // die erstellte Config-Datei an den User schicken
    $file_name   = 'config.php';
    $file_length = strlen($file_content);

    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Length: '.$file_length);
    header('Content-Disposition: attachment; filename="'.$file_name.'"');
    echo $file_content;
    exit();
}
elseif($getMode == 8)	// Start installation
{
    if(file_exists('../config.php') == false)
    {
        showPage($gL10n->get('INS_CONFIGURATION_FILE_NOT_FOUND', 'config.php'), 'installation.php?mode=6', 'back.png', $gL10n->get('SYS_BACK'));
    }

    // setzt die Ausfuehrungszeit des Scripts auf 2 Min., da hier teilweise sehr viel gemacht wird
    // allerdings darf hier keine Fehlermeldung wg. dem safe_mode kommen
    @set_time_limit(300);

    // Verbindung zu Datenbank herstellen
    require_once(SERVER_PATH. '/config.php');

    if($g_tbl_praefix != $_SESSION['prefix']
    || $gDbType     != $_SESSION['db_type']
    || $g_adm_srv     != $_SESSION['server']
    || $g_adm_usr     != $_SESSION['user']
    || $g_adm_pw      != $_SESSION['password']
    || $g_adm_db      != $_SESSION['database']
    || $g_organization!= $_SESSION['orgaShortName'])
    {
        showPage($gL10n->get('INS_DATA_DO_NOT_MATCH', 'config.php'), 'installation.php?mode=6', 'back.png', $gL10n->get('SYS_BACK'));
    }
    $db = Database::createDatabaseObject($gDbType);
    $connection = $db->connect($g_adm_srv, $g_adm_usr, $g_adm_pw, $g_adm_db);

    $filename = 'db_scripts/db.sql';
    $file     = fopen($filename, 'r')
                or showPage($gL10n->get('INS_DATABASE_FILE_NOT_FOUND', 'db.sql', 'adm_install/db_scripts'), 'installation.php?mode=6', 'back.png', $gL10n->get('SYS_BACK'));
    $content  = fread($file, filesize($filename));
    $sql_arr  = explode(';', $content);
    fclose($file);

    foreach($sql_arr as $sql)
    {
        if(strlen(trim($sql)) > 0)
        {
            // Prefix fuer die Tabellen einsetzen und SQL-Statement ausfuehren
            $sql = str_replace('%PREFIX%', $g_tbl_praefix, $sql);
            $db->query($sql);
        }
    }

    // create default data

    // create a hidden system user for internal use
    // all recordsets created by installation will get the create id of the system user
    $gCurrentUser = new TableUsers($db);
    $gCurrentUser->setValue('usr_login_name', $gL10n->get('SYS_SYSTEM'));
    $gCurrentUser->setValue('usr_valid', '0');
    $gCurrentUser->setValue('usr_timestamp_create', DATETIME_NOW);
    $gCurrentUser->save(false); // no registered user -> UserIdCreate couldn't be filled
	$systemUserId = $gCurrentUser->getValue('usr_id');

    // Orga-Uebergreifende Kategorien anlegen
    $sql = 'INSERT INTO '. TBL_CATEGORIES. ' (cat_org_id, cat_type, cat_name_intern, cat_name, cat_hidden, cat_system, cat_sequence, cat_usr_id_create, cat_timestamp_create)
                                      VALUES (NULL, \'USF\', \'MASTER_DATA\', \'SYS_MASTER_DATA\', 0, 1, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\') ';
    $db->query($sql);
    $cat_id_master_data = $db->insert_id();

    $sql = 'INSERT INTO '. TBL_CATEGORIES. ' (cat_org_id, cat_type, cat_name_intern, cat_name, cat_hidden, cat_system, cat_sequence, cat_usr_id_create, cat_timestamp_create)
                                      VALUES (NULL, \'USF\', \'SOCIAL_NETWORKS\', \'SYS_SOCIAL_NETWORKS\', 0, 0, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\') ';
    $db->query($sql);
    $cat_id_messenger = $db->insert_id();

    // Stammdatenfelder anlegen
    $sql = 'INSERT INTO '. TBL_USER_FIELDS. ' (usf_cat_id, usf_type, usf_name_intern, usf_name, usf_description, usf_value_list, usf_system, usf_disabled, usf_mandatory, usf_sequence, usf_usr_id_create, usf_timestamp_create)
                                       VALUES ('.$cat_id_master_data.', \'TEXT\', \'LAST_NAME\', \'SYS_LASTNAME\', NULL, NULL, 1, 1, 1, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'FIRST_NAME\',\'SYS_FIRSTNAME\', NULL, NULL, 1, 1, 1, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'ADDRESS\',   \'SYS_ADDRESS\', NULL, NULL, 0, 0, 0, 3, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'POSTCODE\',  \'SYS_POSTCODE\', NULL, NULL, 0, 0, 0, 4, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'CITY\',      \'SYS_CITY\', NULL, NULL, 0, 0, 0, 5, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'COUNTRY\',   \'SYS_COUNTRY\', NULL, NULL, 0, 0, 0, 6, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'PHONE\',     \'SYS_PHONE\', NULL, NULL, 0, 0, 0, 7, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'MOBILE\',    \'SYS_MOBILE\', NULL, NULL, 0, 0, 0, 8, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'TEXT\', \'FAX\',       \'SYS_FAX\', NULL, NULL, 0, 0, 0, 9, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'DATE\', \'BIRTHDAY\',  \'SYS_BIRTHDAY\', NULL, NULL, 0, 0, 0, 10, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'RADIO_BUTTON\', \'GENDER\', \'SYS_GENDER\', NULL, \'male.png|SYS_MALE\r\nfemale.png|SYS_FEMALE\', 0, 0, 0, 11, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'EMAIL\', \'EMAIL\',    \'SYS_EMAIL\', NULL, NULL, 1, 0, 1, 12, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_master_data.', \'URL\',  \'WEBSITE\',   \'SYS_WEBSITE\', NULL, NULL, 0, 0, 0, 13, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\') ';
    $db->query($sql);
    $usf_id_homepage = $db->insert_id();

    // Messenger anlegen
    $sql = 'INSERT INTO '. TBL_USER_FIELDS. ' (usf_cat_id, usf_type, usf_name_intern, usf_name, usf_description, usf_icon, usf_url, usf_system, usf_sequence, usf_usr_id_create, usf_timestamp_create)
                                       VALUES ('.$cat_id_messenger.', \'TEXT\', \'AOL_INSTANT_MESSENGER\', \'INS_AOL_INSTANT_MESSENGER\', NULL, \'aim.png\', NULL, 0, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'FACEBOOK\',       \'INS_FACEBOOK\', \''.$gL10n->get('INS_FACEBOOK_DESC').'\', \'facebook.png\', \'http://www.facebook.com/%user_content%\', 0, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'GOOGLE_PLUS\',    \'INS_GOOGLE_PLUS\', \''.$gL10n->get('INS_GOOGLE_PLUS_DESC').'\', \'google_plus.png\', NULL, 0, 3, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'ICQ\',            \'INS_ICQ\', \''.$gL10n->get('INS_ICQ_DESC').'\', \'icq.png\', \'http://www.icq.com/people/%user_content%\', 0, 4, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'SKYPE\',          \'INS_SKYPE\', \''.$gL10n->get('INS_SKYPE_DESC').'\', \'skype.png\', NULL, 0, 5, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'TWITTER\',        \'INS_TWITTER\', \''.$gL10n->get('INS_TWITTER_DESC').'\', \'twitter.png\', \'http://twitter.com/#!/%user_content%\', 0, 6, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'XING\',           \'INS_XING\', \''.$gL10n->get('INS_XING_DESC').'\', \'xing.png\', \'https://www.xing.com/profile/%user_content%\', 0, 7, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                            , ('.$cat_id_messenger.', \'TEXT\', \'YAHOO_MESSENGER\',\'INS_YAHOO_MESSENGER\', NULL, \'yahoo.png\', NULL, 0, 8, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\') ';
    $db->query($sql);

    // Organisationsobjekt erstellen
    $sql = 'INSERT INTO '. TBL_ORGANIZATIONS. ' (org_longname, org_shortname, org_homepage) 
	                                     VALUES (\''.$_SESSION['orgaLongName'].'\', \''.$_SESSION['orgaShortName'].'\', \'http://www.admidio.org\')';
    $db->query($sql);

    $gCurrentOrganization = new Organization($db, $_SESSION['orgaShortName']);
    $gCurrentOrganization->setValue('org_homepage', $_SERVER['HTTP_HOST']);
    $gCurrentOrganization->save();

    // alle Einstellungen aus preferences.php in die Tabelle adm_preferences schreiben
    include('db_scripts/preferences.php');

    // die Administrator-Email-Adresse ist erst einmal die vom Installationsuser
    $orga_preferences['email_administrator'] = $_SESSION['user_email'];

    $gCurrentOrganization->setPreferences($orga_preferences, false);
	
	// now set db specific admidio preferences
	$db->setDBSpecificAdmidioProperties();

    // alle Systemmails aus systemmails_texts.php in die Tabelle adm_texts schreiben
    $systemmails_texts = array('SYSMAIL_REGISTRATION_USER' => $gL10n->get('SYS_SYSMAIL_REGISTRATION_USER'),
                               'SYSMAIL_REGISTRATION_WEBMASTER' => $gL10n->get('SYS_SYSMAIL_REGISTRATION_WEBMASTER'),
                               'SYSMAIL_REFUSE_REGISTRATION' => $gL10n->get('SYS_SYSMAIL_REFUSE_REGISTRATION'),
                               'SYSMAIL_NEW_PASSWORD' => $gL10n->get('SYS_SYSMAIL_NEW_PASSWORD'),
                               'SYSMAIL_ACTIVATION_LINK' => $gL10n->get('SYS_SYSMAIL_ACTIVATION_LINK'));
    $text = new TableText($db);

    foreach($systemmails_texts as $key => $value)
    {
        // convert <br /> to a normal line feed
        $value = preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/',chr(13).chr(10),$value);

        $text->clear();
        $text->setValue('txt_name', $key);
        $text->setValue('txt_text', $value);
        $text->save();
    }

    // nun noch die ausgewaehlte Sprache in den Einstellungen speichern
    $sql = 'UPDATE '. TBL_PREFERENCES. ' SET prf_value = \''.$_SESSION['language'].'\'
             WHERE prf_name   = \'system_language\' 
               AND prf_org_id = '. $gCurrentOrganization->getValue('org_id');
    $db->query($sql);

    // Admidio-Versionsnummer schreiben
    $sql = 'INSERT INTO '. TBL_PREFERENCES. ' (prf_org_id, prf_name, prf_value)
                                       VALUES ('. $gCurrentOrganization->getValue('org_id'). ', \'db_version\',      \''. ADMIDIO_VERSION. '\') 
                                            , ('. $gCurrentOrganization->getValue('org_id'). ', \'db_version_beta\', \''. BETA_VERSION. '\')';
    $db->query($sql);

    // Default-Kategorie fuer Rollen und Links eintragen
    $sql = 'INSERT INTO '. TBL_CATEGORIES. ' (cat_org_id, cat_type, cat_name_intern, cat_name, cat_hidden, cat_default, cat_sequence, cat_usr_id_create, cat_timestamp_create)
                                           VALUES ('. $gCurrentOrganization->getValue('org_id'). ', \'ROL\', \'COMMON\', \'SYS_COMMON\', 0, 1, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')';
    $db->query($sql);
    $category_common = $db->insert_id();

    $sql = 'INSERT INTO '. TBL_CATEGORIES.' (cat_org_id, cat_type, cat_name_intern, cat_name, cat_hidden, cat_default, cat_system, cat_sequence, cat_usr_id_create, cat_timestamp_create)
                                     VALUES ('. $gCurrentOrganization->getValue('org_id').', \'ROL\', \'GROUPS\',  \'INS_GROUPS\', 0, 0, 0, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'ROL\', \'COURSES\', \'INS_COURSES\', 0, 0, 0, 3, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'ROL\', \'TEAMS\',   \'INS_TEAMS\', 0, 0, 0, 4, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , (NULL, \'ROL\', \'CONFIRMATION_OF_PARTICIPATION\', \'SYS_CONFIRMATION_OF_PARTICIPATION\', 1, 0, 1, 5, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'LNK\', \'COMMON\',  \'SYS_COMMON\', 0, 1, 0, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'LNK\', \'INTERN\',  \'INS_INTERN\', 1, 0, 0, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'DAT\', \'COMMON\',  \'SYS_COMMON\', 0, 1, 0, 1, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'DAT\', \'TRAINING\',\'INS_TRAINING\', 0, 0, 0, 2, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , ('. $gCurrentOrganization->getValue('org_id').', \'DAT\', \'COURSES\', \'INS_COURSES\', 0, 0, 0, 3, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')
                                          , (NULL, \'USF\', \'ADDIDIONAL_DATA\', \'INS_ADDIDIONAL_DATA\', 0, 0, 0, 3, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\') ';
    $db->query($sql);

    //DefaultOrdner fuer Downloadmodul in der DB anlegen:
    $sql = 'INSERT INTO '. TBL_FOLDERS. ' (fol_org_id, fol_type, fol_name, fol_path,
                                           fol_locked, fol_public, fol_timestamp)
                                    VALUES ('. $gCurrentOrganization->getValue('org_id'). ', \'DOWNLOAD\', \'download\', \'/adm_my_files\',
                                            0,1,\''.DATETIME_NOW.'\')';
    $db->query($sql);

    //Defaultraum fuer Raummodul in der DB anlegen:
    $sql = 'INSERT INTO '. TBL_ROOMS. ' (room_name, room_description, room_capacity, room_usr_id_create, room_timestamp_create)
                                    VALUES (\''.$gL10n->get('INS_CONFERENCE_ROOM').'\', \''.$gL10n->get('INS_DESCRIPTION_CONFERENCE_ROOM').'\', 
                                            15, '.$gCurrentUser->getValue('usr_id').',\''. DATETIME_NOW.'\')';
    $db->query($sql);

    // nun die Default-Rollen anlegen

    // Create role webmaster
    $roleWebmaster = new TableRoles($db);
    $roleWebmaster->setValue('rol_cat_id', $category_common);
    $roleWebmaster->setValue('rol_name', $gL10n->get('SYS_WEBMASTER'));
    $roleWebmaster->setValue('rol_description', $gL10n->get('INS_DESCRIPTION_WEBMASTER'));
    $roleWebmaster->setValue('rol_assign_roles', 1);
    $roleWebmaster->setValue('rol_approve_users', 1);
    $roleWebmaster->setValue('rol_announcements', 1);
    $roleWebmaster->setValue('rol_dates', 1);
    $roleWebmaster->setValue('rol_download', 1);
    $roleWebmaster->setValue('rol_guestbook', 1);
    $roleWebmaster->setValue('rol_guestbook_comments', 1);
    $roleWebmaster->setValue('rol_photo', 1);
    $roleWebmaster->setValue('rol_weblinks', 1);
    $roleWebmaster->setValue('rol_edit_user', 1);
    $roleWebmaster->setValue('rol_mail_to_all', 1);
    $roleWebmaster->setValue('rol_mail_this_role', 3);
    $roleWebmaster->setValue('rol_profile', 1);
    $roleWebmaster->setValue('rol_this_list_view', 1);
    $roleWebmaster->setValue('rol_all_lists_view', 1);
	$roleWebmaster->setValue('rol_webmaster', 1);
    $roleWebmaster->save();

    // Create role member
    $roleMember = new TableRoles($db);
    $roleMember->setValue('rol_cat_id', $category_common);
    $roleMember->setValue('rol_name', $gL10n->get('SYS_MEMBER'));
    $roleMember->setValue('rol_description', $gL10n->get('INS_DESCRIPTION_MEMBER'));
    $roleMember->setValue('rol_mail_this_role', 2);
    $roleMember->setValue('rol_profile', 1);
    $roleMember->setValue('rol_this_list_view', 1);
    $roleMember->setValue('rol_default_registration', 1);
    $roleMember->save();

    // Create role board
    $roleManagement = new TableRoles($db);
    $roleManagement->setValue('rol_cat_id', $category_common);
    $roleManagement->setValue('rol_name', $gL10n->get('INS_BOARD'));
    $roleManagement->setValue('rol_description', $gL10n->get('INS_DESCRIPTION_BOARD'));
    $roleManagement->setValue('rol_announcements', 1);
    $roleManagement->setValue('rol_dates', 1);
    $roleManagement->setValue('rol_weblinks', 1);
    $roleManagement->setValue('rol_edit_user', 1);
    $roleManagement->setValue('rol_mail_to_all', 1);
    $roleManagement->setValue('rol_mail_this_role', 2);
    $roleManagement->setValue('rol_profile', 1);
    $roleManagement->setValue('rol_this_list_view', 1);
    $roleManagement->setValue('rol_all_lists_view', 1);
    $roleManagement->save();
    
    // create user webmaster and assign roles
    $webmaster = new TableUsers($db);
    $webmaster->setValue('usr_login_name', $_SESSION['user_login']);
    $webmaster->setValue('usr_password',   $_SESSION['user_password']);
    $webmaster->setValue('usr_usr_id_create', $gCurrentUser->getValue('usr_id'));
    $webmaster->setValue('usr_timestamp_create', DATETIME_NOW);
    $webmaster->save(false); // no registered user -> UserIdCreate couldn't be filled
    
    // Create membership for current user in role 'Webmaster'
    $member = new TableMembers($db);
    $member->startMembership($roleWebmaster->getValue('rol_id'), $webmaster->getValue('usr_id'));
    $member->startMembership($roleMember->getValue('rol_id'),    $webmaster->getValue('usr_id'));
	
	// create object with current user field structure
	$gProfileFields = new ProfileFields($db, $gCurrentOrganization->getValue('org_id'));

    // first create a user object "current user" with webmaster rights because webmaster
    // is allowed to edit firstname and lastname
    $gCurrentUser = new User($db, $gProfileFields, $webmaster->getValue('usr_id'));
    $gCurrentUser->setValue('LAST_NAME',  $_SESSION['user_last_name']);
    $gCurrentUser->setValue('FIRST_NAME', $_SESSION['user_first_name']);
    $gCurrentUser->setValue('EMAIL',      $_SESSION['user_email']);
    $gCurrentUser->save(false);

    // now create a full user object for system user
    $systemUser = new User($db, $gProfileFields, $systemUserId);
    $systemUser->setValue('LAST_NAME', $gL10n->get('SYS_SYSTEM'));
    $systemUser->save(false); // no registered user -> UserIdCreate couldn't be filled
    
    // now set current user to system user
    $gCurrentUser->readDataById($systemUserId);

    // Default-Listen-Konfigurationen anlegen
    $addressList = new ListConfiguration($db);
    $addressList->setValue('lst_name', $gL10n->get('INS_ADDRESS_LIST'));
    $addressList->setValue('lst_global', 1);
    $addressList->setValue('lst_default', 1);
    $addressList->addColumn(1, $gProfileFields->getProperty('LAST_NAME', 'usf_id'), 'ASC');
    $addressList->addColumn(2, $gProfileFields->getProperty('FIRST_NAME', 'usf_id'), 'ASC');
    $addressList->addColumn(3, $gProfileFields->getProperty('BIRTHDAY', 'usf_id'));
    $addressList->addColumn(4, $gProfileFields->getProperty('ADDRESS', 'usf_id'));
    $addressList->addColumn(5, $gProfileFields->getProperty('POSTCODE', 'usf_id'));
    $addressList->addColumn(6, $gProfileFields->getProperty('CITY', 'usf_id'));
    $addressList->save();

    $phoneList = new ListConfiguration($db);
    $phoneList->setValue('lst_name', $gL10n->get('INS_PHONE_LIST'));
    $phoneList->setValue('lst_global', 1);
    $phoneList->addColumn(1, $gProfileFields->getProperty('LAST_NAME', 'usf_id'), 'ASC');
    $phoneList->addColumn(2, $gProfileFields->getProperty('FIRST_NAME', 'usf_id'), 'ASC');
    $phoneList->addColumn(3, $gProfileFields->getProperty('PHONE', 'usf_id'));
    $phoneList->addColumn(4, $gProfileFields->getProperty('MOBILE', 'usf_id'));
    $phoneList->addColumn(5, $gProfileFields->getProperty('EMAIL', 'usf_id'));
    $phoneList->addColumn(6, $gProfileFields->getProperty('FAX', 'usf_id'));
    $phoneList->save();

    $contactList = new ListConfiguration($db);
    $contactList->setValue('lst_name', $gL10n->get('SYS_CONTACT_DETAILS'));
    $contactList->setValue('lst_global', 1);
    $contactList->addColumn(1, $gProfileFields->getProperty('LAST_NAME', 'usf_id'), 'ASC');
    $contactList->addColumn(2, $gProfileFields->getProperty('FIRST_NAME', 'usf_id'), 'ASC');
    $contactList->addColumn(3, $gProfileFields->getProperty('BIRTHDAY', 'usf_id'));
    $contactList->addColumn(4, $gProfileFields->getProperty('ADDRESS', 'usf_id'));
    $contactList->addColumn(5, $gProfileFields->getProperty('POSTCODE', 'usf_id'));
    $contactList->addColumn(6, $gProfileFields->getProperty('CITY', 'usf_id'));
    $contactList->addColumn(7, $gProfileFields->getProperty('PHONE', 'usf_id'));
    $contactList->addColumn(8, $gProfileFields->getProperty('MOBILE', 'usf_id'));
    $contactList->addColumn(9, $gProfileFields->getProperty('EMAIL', 'usf_id'));
    $contactList->save();

    $formerList = new ListConfiguration($db);
    $formerList->setValue('lst_name', $gL10n->get('INS_MEMBERSHIP'));
    $formerList->setValue('lst_global', 1);
    $formerList->addColumn(1, $gProfileFields->getProperty('LAST_NAME', 'usf_id'));
    $formerList->addColumn(2, $gProfileFields->getProperty('FIRST_NAME', 'usf_id'));
    $formerList->addColumn(3, $gProfileFields->getProperty('BIRTHDAY', 'usf_id'));
    $formerList->addColumn(4, 'mem_begin');
    $formerList->addColumn(5, 'mem_end', 'DESC');
    $formerList->save();

    // nach der Installation zur Sicherheit bei den Sessions das neue Einlesen des Organisations- und Userobjekts erzwingen
    $sql = 'UPDATE '. TBL_SESSIONS. ' SET ses_renew = 1 ';
    $db->query($sql);
    
    // Daten der Session loeschen
    session_unset();

    $message = '<img style="vertical-align: top;" src="layout/ok.png" /> <strong>'.$gL10n->get('INS_INSTALLATION_WAS_SUCCESSFUL').'</strong><br /><br />
               '.$gL10n->get('INS_INSTALLATION_SUCCESSFUL').'<br /><br />
               '.$gL10n->get('INS_SUPPORT_FURTHER_DEVELOPMENT');
    if(is_writeable("../adm_my_files") == false)
    {
        $message = $message. '<br /><br /><img src="layout/warning.png" alt="'.$gL10n->get('SYS_WARNING').'" /> '.$gL10n->get('INS_FOLDER_NOT_WRITABLE', 'adm_my_files');
    }
    showPage($message, 'http://www.admidio.org/index.php?page=donate', 'money.png', $gL10n->get('SYS_DONATE'));
}

?>