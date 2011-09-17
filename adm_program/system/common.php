<?php
/******************************************************************************
 * Basic script for all other Admidio scripts with all the necessary data und
 * variables to run a script in the Admidio environment
 *
 * Copyright    : (c) 2004 - 2011 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if ('common.php' == basename($_SERVER['SCRIPT_FILENAME']))
{
    die('This page may not be called directly !');
}

// embed config and constants file
require_once(substr(__FILE__, 0, strpos(__FILE__, 'adm_program')-1). '/config.php');
require_once(substr(__FILE__, 0, strpos(__FILE__, 'adm_program')-1). '/adm_program/system/constants.php');

// if there is no debug flag in config.php than set debug to false
if(isset($gDebug) == false || $gDebug != 1)
{
    $gDebug = 0;
}

if($gDebug)
{
    // write actual script with parameters in log file
    error_log("--------------------------------------------------------------------------------\n".
              $_SERVER['SCRIPT_FILENAME']. "\n? ". $_SERVER['QUERY_STRING']);
}

 // default prefix is set to 'adm' because of compatibility to old versions
if(strlen($g_tbl_praefix) == 0)
{
    $g_tbl_praefix = 'adm';
}

// includes WITHOUT database connections
require_once(SERVER_PATH. '/adm_program/system/db/database.php');
require_once(SERVER_PATH. '/adm_program/system/function.php');
require_once(SERVER_PATH. '/adm_program/system/string.php');
require_once(SERVER_PATH. '/adm_program/system/classes/datetime_extended.php');
require_once(SERVER_PATH. '/adm_program/system/classes/language.php');
require_once(SERVER_PATH. '/adm_program/system/classes/message.php');
require_once(SERVER_PATH. '/adm_program/system/classes/navigation.php');
require_once(SERVER_PATH. '/adm_program/system/classes/organization.php');
require_once(SERVER_PATH. '/adm_program/system/classes/table_session.php');
require_once(SERVER_PATH. '/adm_program/system/classes/user.php');
require_once(SERVER_PATH. '/adm_program/system/classes/user_fields.php');
require_once(SERVER_PATH. '/adm_program/system/forum/forum.php');

// remove HMTL & PHP-Code from all parameters
$_REQUEST = admStrStripTagsSpecial($_REQUEST);
$_GET     = admStrStripTagsSpecial($_GET);
$_POST    = admStrStripTagsSpecial($_POST);
$_COOKIE  = admStrStripTagsSpecial($_COOKIE);

// escape all quotes so db queries are save
if(get_magic_quotes_gpc() == false)
{
    $_REQUEST = strAddSlashesDeep($_REQUEST);
    $_GET     = strAddSlashesDeep($_GET);
    $_POST    = strAddSlashesDeep($_POST);
    $_COOKIE  = strAddSlashesDeep($_COOKIE);
}

// global parameters
$gValidLogin = false;
$gLayout     = array();

 // Datenbankobjekt anlegen und Verbindung zu Datenbank herstellen
if(!isset($gDbType))
{
    $gDbType = 'mysql';
}
$gDb = Database::createDatabaseObject($gDbType);
$gDbConnection = $gDb->connect($g_adm_srv, $g_adm_usr, $g_adm_pw, $g_adm_db);

// Script fuer das Forum ermitteln und includen, bevor die Session erstellt wird
Forum::includeForumScript($gDb);

// Cookie-Praefix ermitteln und Sonderzeichen entfernen
$gCookiePraefix = 'ADMIDIO_'. $g_organization;
if($gDebug)
{
    $gCookiePraefix .= '_'. ADMIDIO_VERSION. '_'. BETA_VERSION;
}
$gCookiePraefix = strtr($gCookiePraefix, ' .,;:','_____');

// PHP-Session starten
if(headers_sent() == false)
{
    session_name($gCookiePraefix. '_PHP_ID');
    session_start();
}

// Session-ID ermitteln
if(isset($_COOKIE[$gCookiePraefix. '_ID']))
{
    $gSessionId = $_COOKIE[$gCookiePraefix. '_ID'];
}
else
{
    $gSessionId = session_id();
}

// globale Klassen mit Datenbankbezug werden in Sessionvariablen gespeichert, 
// damit die Daten nicht bei jedem Script aus der Datenbank ausgelesen werden muessen
if(isset($_SESSION['gCurrentOrganization']) 
&& isset($_SESSION['gPreferences'])
&& $g_organization == $_SESSION['gCurrentOrganization']->getValue('org_shortname'))
{
    $gCurrentOrganization     =& $_SESSION['gCurrentOrganization'];
    $gCurrentOrganization->db =& $gDb;
    $gPreferences             =& $_SESSION['gPreferences'];
	$gUserFields              =& $_SESSION['gUserFields'];
    $gUserFields->mDb         =& $gDb;
}
else
{
    $gCurrentOrganization = new Organization($gDb, $g_organization);
    
    if($gCurrentOrganization->getValue('org_id') == 0)
    {
        // Organisation wurde nicht gefunden
        die('<div style="color: #CC0000;">Error: The organization of the config.php could not be found in the database!</div>');
    }
    
    // organisationsspezifische Einstellungen aus adm_preferences auslesen
    $gPreferences = $gCurrentOrganization->getPreferences();
	
	// create object with current user field structure
	$gUserFields = new UserFields($gDb, $gCurrentOrganization);
    
    // Daten in Session-Variablen sichern
    $_SESSION['gCurrentOrganization'] =& $gCurrentOrganization;
    $_SESSION['gPreferences']         =& $gPreferences;
    $_SESSION['gUserFields']          =& $gUserFields;
}

// Sprachdateien einlesen
$gL10n = new Language($gPreferences['system_language']);

// Pfad zum gewaehlten Theme zusammensetzen
if(isset($gPreferences['theme']) == false)
{
    $gPreferences['theme'] = 'classic';
}
define('THEME_SERVER_PATH', SERVER_PATH. '/adm_themes/'. $gPreferences['theme']);
define('THEME_PATH', $g_root_path. '/adm_themes/'. $gPreferences['theme']);

// Daten des angemeldeten Users auch in Session speichern
if(isset($_SESSION['gCurrentUser']))
{
    $gCurrentUser =& $_SESSION['gCurrentUser'];
    $gCurrentUser->db =& $gDb;
}
else
{
    $gCurrentUser = new User($gDb, $gUserFields, 0);
    $_SESSION['gCurrentUser'] =& $gCurrentUser;
}

// Nachrichtenklasse anlegen
$gMessage = new Message();

// Objekt fuer die Zuruecknavigation in den Modulen
// hier werden die Urls in einem Stack gespeichert
if(isset($_SESSION['navigation']) == false)
{
    $_SESSION['navigation'] = new Navigation();
}

// pruefen, ob Datenbank-Version zu den Scripten passt
if(isset($gPreferences['db_version']) == false
|| isset($gPreferences['db_version_beta']) == false
|| version_compare($gPreferences['db_version'], ADMIDIO_VERSION) != 0
|| version_compare($gPreferences['db_version_beta'], BETA_VERSION) != 0)
{
    unset($_SESSION['gCurrentOrganization']);
    $gMessage->show($gL10n->get('SYS_DATABASE_INVALID', $gPreferences['db_version'], ADMIDIO_VERSION, '<a href="mailto:'.$gPreferences['email_administrator'].'">', '</a>'));
}

/*********************************************************************************
Session auf Gueltigkeit pruefen bzw. anlegen
/********************************************************************************/

$gCurrentSession = new TableSession($gDb, $gSessionId);

// erst einmal pruefen, ob evtl. frueher ein Autologin-Cookie gesetzt wurde
// dann diese Session wiederherstellen

$autoLogin = false;
if($gPreferences['enable_auto_login'] == 1 && isset($_COOKIE[$gCookiePraefix. '_DATA']))
{
    $admidio_data = explode(';', $_COOKIE[$gCookiePraefix. '_DATA']);
    
    if($admidio_data[0] == true         // autologin
    && is_numeric($admidio_data[1]))    // user_id 
    {   
        if($gCurrentUser->getValue('usr_id') != $admidio_data[1])
        {
            // User aus der Autologin-Session wiederherstellen
            require_once(SERVER_PATH. '/adm_program/system/classes/table_auto_login.php');
            $auto_login = new TableAutoLogin($gDb, $gSessionId);
            
            // User nur herstellen, wenn Cookie-User-Id == gespeicherte DB-User-Id
            if($auto_login->getValue('atl_usr_id') == $admidio_data[1])
            {
                $gCurrentUser->readData($auto_login->getValue('atl_usr_id'));
                // Logins zaehlen und aktuelles Login-Datum aktualisieren
                $gCurrentUser->updateLoginData();            
                $autoLogin = true;
            }
            else
            {
                // irgendwas stimmt nicht -> sicherheitshalber den Auto-Login-Eintrag loeschen
                $auto_login->delete();
            }
        }
        else
        {
            $autoLogin = true;
        }
        
        if($gCurrentSession->getValue('ses_id') == 0)
        {
            $gCurrentSession->setValue('ses_session_id', $gSessionId);
            $gCurrentSession->setValue('ses_usr_id',  $gCurrentUser->getValue('usr_id'));
            $gCurrentSession->save();
        }
    }
}

if($gCurrentSession->getValue('ses_id') > 0)
{
    // erst einmal pruefen, ob Organisation- oder Userobjekt neu eingelesen werden muessen,
    // da die Daten evtl. von anderen Usern in der DB geaendert wurden
    if($gCurrentSession->getValue('ses_renew') == 1 || $gCurrentSession->getValue('ses_renew') == 3)
    {
        // Feldstruktur entfernen und Userobjekt neu einlesen
        $gCurrentUser->userFieldData = array();
        $gCurrentUser->readData($gCurrentUser->getValue('usr_id'));
        $gCurrentSession->setValue('ses_renew', 0);
    }
    if($gCurrentSession->getValue('ses_renew') == 2 || $gCurrentSession->getValue('ses_renew') == 3)
    {
        // Organisationsobjekt neu einlesen
        $gCurrentOrganization->readData($g_organization);
        $gPreferences = $gCurrentOrganization->getPreferences();
        $gCurrentSession->setValue('ses_renew', 0);
    }

    // nun die Session pruefen
    if($gCurrentSession->getValue('ses_usr_id') > 0)
    {
        if($gCurrentSession->getValue('ses_usr_id') == $gCurrentUser->getValue('usr_id'))
        {
            // Session gehoert zu einem eingeloggten User -> pruefen, ob der User noch eingeloggt sein darf
            $time_gap = time() - strtotime($gCurrentSession->getValue('ses_timestamp', 'Y-m-d H:i:s'));
            
            // wenn laenger nichts gemacht wurde, als in Orga-Prefs eingestellt ist, dann ausloggen
            if($time_gap < $gPreferences['logout_minutes'] * 60 || $autoLogin == true) 
            {
                // bei Autologin ggf. den Beginn aktualisieren, wenn die Luecke zu gross geworden ist
                if($time_gap > $gPreferences['logout_minutes'] * 60 && $autoLogin == true)
                {
                    $gCurrentSession->setValue('ses_begin', DATETIME_NOW);
                }

                // User-Login ist gueltig
                $gValidLogin = true;
                $gCurrentSession->setValue('ses_timestamp', DATETIME_NOW);
            }
            else
            {
                // User war zu lange inaktiv -> User aus Session entfernen
                $gCurrentUser->clear();
                $gCurrentSession->setValue('ses_usr_id', '');
            }
        }
        else
        {
            // irgendwas stimmt nicht, also alles zuruecksetzen
            $gCurrentUser->clear();
            $gCurrentSession->setValue('ses_usr_id', '');
        }
    }
    
    // Update auf Sessionsatz machen (u.a. timestamp aktualisieren)
    $gCurrentSession->save();
}
else
{
    // Session existierte noch nicht, dann neu anlegen
    $gCurrentUser->clear();
    $gCurrentSession->setValue('ses_session_id', $gSessionId);
    $gCurrentSession->save();
    
    // Alle alten Session loeschen
    $gCurrentSession->tableCleanup($gPreferences['logout_minutes']);
}

// Homepageseite festlegen
if($gValidLogin)
{
    $gHomepage = $g_root_path. '/'. $gPreferences['homepage_login'];
}
else
{
    $gHomepage = $g_root_path. '/'. $gPreferences['homepage_logout'];
}

/*********************************************************************************
Verbindung zur Forum-Datenbank herstellen und die Funktionen, sowie Routinen des Forums laden.
/********************************************************************************/

if($gPreferences['enable_forum_interface']) 
{
    // Admidio-Zugangsdaten nehmen, wenn entsprechende Einstellung gesetzt ist
    if($gPreferences['forum_sqldata_from_admidio']) 
    {
        $gPreferences['forum_srv'] = $gDb->server;
        $gPreferences['forum_usr'] = $gDb->user;
        $gPreferences['forum_pw']  = $gDb->password;
        $gPreferences['forum_db']  = $gDb->dbname;
    }
    
    // globale Klassen mit Datenbankbezug werden in Sessionvariablen gespeichert, 
    // damit die Daten nicht bei jedem Script aus der Datenbank ausgelesen werden muessen
    if(isset($_SESSION['gForum']))
    {
        $gForum =& $_SESSION['gForum'];
    }
    else
    {
        $gForum = Forum::createForumObject($gPreferences['forum_version']);
        $_SESSION['gForum'] =& $gForum;
    }
        
    if(!$gForum->connect($gPreferences['forum_srv'], $gPreferences['forum_usr'], $gPreferences['forum_pw'], $gPreferences['forum_db'], $gDb))
    {
        // Verbindungsprobleme, deshalb Forum deaktivieren, damit Admidio ggf. noch funktioniert
        $gPreferences['enable_forum_interface'] = 0;
    }
    else
    {
        // Einstellungen des Forums einlesen
        if(!$gForum->initialize(session_id(), $gPreferences['forum_praefix'], $gPreferences['forum_export_user'], $gPreferences['forum_link_intern'], $gCurrentUser->getValue('usr_login_name')))
        {
            echo '<div style="color: #CC0000;">Verbindungsfehler zum Forum !<br />
                Das eingegebene Forumpräfix <strong>'. $gPreferences['forum_praefix'].'</strong> ist nicht korrekt oder<br />
                es wurde die falsche Datenbankverbindung zum Forum angegeben.</div>';
        }
    }    
}

?>