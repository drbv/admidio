<?php
/******************************************************************************
 * Liste aller Module und Administrationsseiten von Admidio
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

// wenn noch nicht installiert, dann Install-Dialog anzeigen
if(!file_exists('../config.php'))
{
    $location = 'Location: ../adm_install/index.php';
    header($location);
    exit();
}

require_once('system/common.php');
require_once('system/classes/table_members.php');
require_once('system/classes/table_roles.php');
require_once('system/drbv_funktionen.php');
  
// get all memberships where the user is assigned
function getRolesFromDatabase($user_id)
{
  global $gDb, $gCurrentOrganization;

    $sql = 'SELECT *
              FROM '. TBL_MEMBERS. ', '. TBL_ROLES. ', '. TBL_CATEGORIES. '
             WHERE mem_rol_id  = rol_id
               AND mem_begin  <= \''.DATE_NOW.'\'
               AND mem_end    >= \''.DATE_NOW.'\'
               AND mem_usr_id  = '.$user_id.'
               AND rol_valid   = 1
               AND rol_visible = 1
               AND rol_cat_id  = cat_id
               AND (  cat_org_id  = '. $gCurrentOrganization->getValue('org_id'). '
                   OR cat_org_id IS NULL )
             ORDER BY cat_org_id, cat_sequence, rol_name';
    return $gDb->query($sql);  
}    
// get memberships id for Verein
function getRoleMembershipVereine($htmlListId, $user, $result_role){
  global $gDb, $gCurrentUser ;    
  $member = new TableMembers($gDb);
  $role   = new TableRoles($gDb);

  while($row = $gDb->fetch_array($result_role)){
    if($gCurrentUser->viewRole($row['mem_rol_id']) && $row['rol_visible']==1){
      $member->clear();
      $member->setArray($row);
      $role->clear();
      $role->setArray($row);          
      if($role->getValue('cat_name') == 'Vereine'){
        $roleId4Vereine = $member->getValue('mem_rol_id');
      }
    }
  }    
  return $roleId4Vereine;  
}

$result_role  = getRolesFromDatabase($gCurrentUser->getValue('usr_id'));
$roleIdVerein = getRoleMembershipVereine('role_list', $gCurrentUser->getValue('usr_id'), $result_role);  

if($gCurrentUser->isWebmaster())
{
    // der Installationsordner darf aus Sicherheitsgruenden nicht existieren
    if($gDebug == 0 && file_exists('../adm_install'))
    {
        $gMessage->show($gL10n->get('SYS_INSTALL_FOLDER_EXIST'));
    }
}

// Url-Stack loeschen
$gNavigation->clear();
$gNavigation->addUrl(CURRENT_URL);

// Html-Kopf ausgeben
$gLayout['title']  = 'AktivenPORTAL '.$gL10n->get('SYS_OVERVIEW');
$gLayout['header'] = '<link rel="stylesheet" href="'. THEME_PATH. '/css/overview_modules.css" type="text/css" />';

require(SERVER_PATH. '/adm_program/system/overall_header.php');

// Html des Modules ausgeben
echo '
<h1 class="moduleHeadline">AktivenPORTAL</h1>

<ul class="iconTextLinkList">';
    if($gValidLogin == 1)
    {
        echo '<li>
            <span class="iconTextLink">
                <a href="'.$g_root_path.'/adm_program/system/logout.php"><img
                src="'.THEME_PATH.'/icons/door_in.png" alt="'.$gL10n->get('SYS_LOGOUT').'" /></a>
                <a href="'.$g_root_path.'/adm_program/system/logout.php">'.$gL10n->get('SYS_LOGOUT').'</a>
            </span>
        </li>';
    }
    else
    {
        echo '<li>
            <span class="iconTextLink">
                <a href="'.$g_root_path.'/adm_program/system/login.php"><img
                src="'.THEME_PATH.'/icons/key.png" alt="'.$gL10n->get('SYS_LOGIN').'" /></a>
                <a href="'.$g_root_path.'/adm_program/system/login.php">'.$gL10n->get('SYS_LOGIN').'</a>
            </span>
        </li>';

        if($gPreferences['registration_mode'] > 0)
        {
            echo '<li>
                <span class="iconTextLink">
                    <a href="'.$g_root_path.'/adm_program/system/registration.php"><img
                    src="'. THEME_PATH. '/icons/new_registrations.png" alt="'.$gL10n->get('SYS_REGISTRATION').'" /></a>
                    <a href="'.$g_root_path.'/adm_program/system/registration.php">'.$gL10n->get('SYS_REGISTRATION').'</a>
                </span>
            </li>';
        }
    }
echo '</ul>';

//rmenken: Announcements werden als 'Nur-Anzeige' Modul dargestellt. Die Adminfunktion ist
//         im normalen announcement.php, welches im Admin-Teil geladen wird.
if($gValidLogin){  
  require(SERVER_PATH. '/adm_program/modules/announcements/announcements_only.php');
}

  $moduleMenu = new Menu('modules', $gL10n->get('SYS_MODULES'));

//rmenken: wegen Nutzung 'Nur-Anzeige' hier auskommentiert
//
//if( $gPreferences['enable_announcements_module'] == 1
//|| ($gPreferences['enable_announcements_module'] == 2 && $gValidLogin))
//{
//  $moduleMenu->addItem('announcements', '/adm_program/modules/announcements/announcements.php',
//            $gL10n->get('ANN_ANNOUNCEMENTS'), '/icons/announcements_big.png',
//            $gL10n->get('ANN_ANNOUNCEMENTS_DESC'));
//}

if($gPreferences['enable_photo_module'] == 1 
|| ($gPreferences['enable_photo_module'] == 2 && $gValidLogin))
{
	$moduleMenu->addItem('photo', '/adm_program/modules/photos/photos.php',
						$gL10n->get('PHO_PHOTOS'), '/icons/photo_big.png',
						$gL10n->get('PHO_PHOTOS_DESC'));
}
if( $gPreferences['enable_guestbook_module'] == 1
|| ($gPreferences['enable_guestbook_module'] == 2 && $gValidLogin))
{
	$moduleMenu->addItem('guestbk', '/adm_program/modules/guestbook/guestbook.php',
						$gL10n->get('GBO_GUESTBOOK'), '/icons/guestbook_big.png',
						$gL10n->get('GBO_GUESTBOOK_DESC'));
}
$moduleMenu->addItem('profile', '/adm_program/modules/profile/profile.php',
          $gL10n->get('PRO_MY_PROFILE'), '/icons/profile_big.png',
          $gL10n->get('PRO_MY_PROFILE_DESC'));
$moduleMenu->addSubItem('profile', 'editprof', '/adm_program/modules/profile/profile_new.php?user_id='.$gCurrentUser->getValue('usr_id'),
            $gL10n->get('PRO_EDIT_MY_PROFILE'),'');

//rmenken direkter Link zur Liste der Aktiven eines Vereins  
if($gCurrentUser->isWebmaster() || hasRole("Vereine")){
  $moduleMenu->addItem('aktive', '/adm_program/modules/lists/lists_show.php?mode=html&rol_id='.$roleIdVerein,
          'Meine Aktiven', '/icons/user_administration_big.png',
          'Anzeige aller Aktiven: Paare, Startkarten und Formationen.');      
}
//if($gCurrentUser->isWebmaster()){
//enable DRRC(14)  
//if($gCurrentUser->isWebmaster() || $gCurrentUser->getValue('usr_id')==14){
if($gCurrentUser->isWebmaster() || hasRole("Vereine")){
  //rmenken Anzeige der Aktiven eines Vereins: detailliert (BETA-TEST)
  //$moduleMenu->addSubItem('aktive', 'Vereine', '/adm_program/modules/drbv/startbuchinfo.php',
  //             'Detaillierte Ansicht', '/icons/beta32x32.png');  
  $moduleMenu->addSubItem('aktive', 'Vereine', '/adm_program/modules/drbv/startbuchinfo.php',
               'Detaillierte Ansicht','');
}
if($gCurrentUser->isWebmaster() || $gCurrentUser->getValue('usr_id')==14){
  $moduleMenu->addSubItem('aktive', 'VereineAdd', '/adm_program/modules/drbv/addaktive.php',
               'Aktive zufügen','/icons/beta32x32.png');
}

//rmenken Anzeige der Wertungsergebnisse für Paare ohne AktivenPORTAL Zugang 
$moduleMenu->addItem('results', '/adm_program/modules/results/results.php',
          'Wertungen', '/icons/wrtg_bigk.png',
          'Anzeige von Wertungen für Nutzer mit Wertungsfreigabe vom Verein.');
if($gCurrentUser->isWebmaster() || hasRole("Wertungsrichterdozent RR") || hasRole("Wertungsrichterdozent BW")){
  //rmenken Anzeige der Wertungsergebnisse für Wertungsrichterdozenten
  $moduleMenu->addSubItem('results', 'Wertungsrichter', '/adm_program/modules/results/results4wr.php',
               'Aller Wertungsrichter für Dozenten','');
}
if(hasRole("Bundestrainer")){
//rmenken Anzeige der Wertungsergebnisse für Bundestrainer
$moduleMenu->addSubItem('results', 'Wertungsrichter', '/adm_program/modules/results/results4wr.php',
             'Alle Wertungsergebnisse für Bundestrainer','');
}  

//rmenken WR&TL Einsatztabelle Webmaster and GS
if($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle"))  
{
$moduleMenu->addItem('wrtltab', '/adm_program/modules/results/einsatztab4wrtl.php',
                     'Einsatztabelle WR/TL', '/icons/tabelle_big.png',
                     'Zur Lizenzprüfung werden hier die Einsätze aller WR/TL aufgezeigt.');  
}
  
//rmenken Anzeige der Turnierergebnisse
$moduleMenu->addItem('ergebnis', '/adm_program/modules/results/turnierergebnisse.php','Ergebnislisten', 
                     '/icons/ldboard.png',
                     'Turnierergebnisse der DRBV Turniere.'); 
//rmenken Anzeige der Ranglisten
$moduleMenu->addSubItem('ergebnis', 'Ranglisten', '/adm_program/modules/results/ranglisten.php',
             'Ranglisten',''); 
  
//rmenken direkter Link zu 
//enable DRRC(14),KWH(289),Hans-Werner.Kirz(678),Hans-Peter.Wandera(685)
//if($gCurrentUser->getValue('usr_id')==14
//   || $gCurrentUser->getValue('usr_id')==289
//   || $gCurrentUser->getValue('usr_id')==678   
//   || $gCurrentUser->getValue('usr_id')==685){
//}  
  
//rmenken formulare module for Webmaster, GS, and Clubs
if($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle") || hasRole("Vereine"))  
//if($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle"))  
{
$moduleMenu->addItem('forms', '/adm_program/modules/forms/forms.php',
          $gL10n->get('PRO_MY_FORMS'), '/icons/forms_big.png',
          $gL10n->get('PRO_MY_FORMS_DESC'));  
}
if($gValidLogin){  
$moduleMenu->addItem('lists', '/adm_program/modules/lists/lists.php',
					$gL10n->get('LST_LISTS'), '/icons/lists_big.png',
					$gL10n->get('LST_LISTS_DESC'));
}
// rmenken: allow own list for specific users
if($gCurrentUser->isWebmaster()  || hasRole("Geschäftsstelle")
   || hasRole("Hauptausschuss")
   || hasRole("Jugendausschuss")
   || hasRole("Präsidium")
   || hasRole("Sportausschuss")
   || hasRole("ErweiterteRechteEigenelisten") )
{          
$moduleMenu->addSubItem('lists', 'mylist', '/adm_program/modules/lists/mylist.php',
            $gL10n->get('LST_MY_LIST'),'');
}
// rmenken: disable inactive roles for normal users
if($gCurrentUser->isWebmaster())
{          
$moduleMenu->addSubItem('lists', 'rolinac', '/adm_program/modules/lists/lists.php?active_role=0',
            $gL10n->get('ROL_INACTIV_ROLE'),'');
}
  
if( $gPreferences['enable_dates_module'] == 1
|| ($gPreferences['enable_dates_module'] == 2 && $gValidLogin))
{
	$moduleMenu->addItem('dates', $g_root_path.'/adm_program/modules/dates/dates.php',
						$gL10n->get('DAT_DATES'), '/icons/dates_big.png',
						$gL10n->get('DAT_DATES_DESC'));
	$moduleMenu->addSubItem('dates', 'olddates', '/adm_program/modules/dates/dates.php?mode=old',
            $gL10n->get('DAT_PREVIOUS_DATES', $gL10n->get('DAT_DATES')), '');
}
//rmenken download module currently only for Webmaster, GS, Turnierleiter & Musikzertifizierung
//if($gPreferences['enable_download_module'] == 1 && ($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle") || hasRole("Turnierleiter") || hasRole("Musikzertifizierung")))  
if($gPreferences['enable_download_module'] == 1 && $gValidLogin)
{
  $moduleMenu->addItem('download', '/adm_program/modules/downloads/downloads.php',
            $gL10n->get('DOW_DOWNLOADS'), '/icons/download_big.png',
            $gL10n->get('DOW_DOWNLOADS_DESC'));
}
  
//rmenken MusikDB module for Webmaster and Turnierleiter
//if($gCurrentUser->isWebmaster())
// enable Christoph Otto  
//if($gCurrentUser->isWebmaster() || $gCurrentUser->getValue('usr_id')==677)  
if($gCurrentUser->isWebmaster() || hasRole("Turnierleiter"))  
{
$moduleMenu->addItem('musikdb', '/adm_program/modules/drbv/musikdb.php',
          $gL10n->get('PRO_MY_MUSIKDB'), '/icons/musikdb_big.png',
          $gL10n->get('PRO_MY_MUSIKDB_DESC'));  
}  
if($gCurrentUser->isWebmaster())
{          
$moduleMenu->addSubItem('musikdb', 'adminmusikdb', '/adm_program/modules/drbv/musikdb_admin.php',
            $gL10n->get('PRO_ADMIN_MUSIKDB'),'');
}  
//rmenken mail module currently only for webmaster
if($gPreferences['enable_mail_module'] == 1 && $gCurrentUser->isWebmaster())
//if($gPreferences['enable_mail_module'] == 1)
{
  $moduleMenu->addItem('email', '/adm_program/modules/mail/mail.php',
            $gL10n->get('SYS_EMAIL'), '/icons/email_big.png',
            $gL10n->get('MAI_EMAIL_DESC'));
}
if( $gPreferences['enable_weblinks_module'] == 1
|| ($gPreferences['enable_weblinks_module'] == 2 && $gValidLogin))
{
	$moduleMenu->addItem('links', $g_root_path.'/adm_program/modules/links/links.php',
						$gL10n->get('LNK_WEBLINKS'), '/icons/weblinks_big.png',
						$gL10n->get('LNK_WEBLINKS_DESC'));
}
// Wenn das Forum aktiv ist, dieses auch in der Uebersicht anzeigen.
if($gPreferences['enable_forum_interface'])
{
	if($gForum->session_valid)
	{
		$forumstext = $gL10n->get('SYS_FORUM_LOGIN_DESC', $gForum->user, $gForum->sitename, $gForum->getUserPM($gCurrentUser->getValue('usr_login_name')));
	}
	else
	{
		$forumstext = $gL10n->get('SYS_FORUM_DESC');
	}
	$moduleMenu->addItem('forum', $gForum->url,
						$gL10n->get('SYS_FORUM'), '/icons/forum_big.png',
						$forumstext);
}
$moduleMenu->show('long');

if($gCurrentUser->isWebmaster() || $gCurrentUser->assignRoles() || $gCurrentUser->approveUsers() || $gCurrentUser->editUsers())
{
	$adminMenu = new Menu('administration', $gL10n->get('SYS_ADMINISTRATION'));
	if($gCurrentUser->approveUsers() && $gPreferences['registration_mode'] > 0)
	{
		$adminMenu->addItem('newreg', '/adm_program/administration/new_user/new_user.php',
							$gL10n->get('NWU_NEW_REGISTRATIONS'), '/icons/new_registrations_big.png',
							$gL10n->get('NWU_MANAGE_NEW_REGISTRATIONS'));
	}
  if($gCurrentUser->isWebmaster() && $gPreferences['enable_announcements_module'] > 0)
  {
    $adminMenu->addItem('announcements', '/adm_program/modules/announcements/announcements.php',
              $gL10n->get('ANN_ANNOUNCEMENTS'), '/icons/announcements_big.png',
              $gL10n->get('ANN_ANNOUNCEMENTS_DESC'));
  }    
	if($gCurrentUser->editUsers())
	{
		$adminMenu->addItem('usrmgt', '/adm_program/administration/members/members.php',
							$gL10n->get('MEM_USER_MANAGEMENT'), '/icons/user_administration_big.png',
							$gL10n->get('MEM_USER_MANAGEMENT_DESC'));
	}

	if($gCurrentUser->assignRoles())
	{
		$adminMenu->addItem('roladm', '/adm_program/administration/roles/roles.php',
							$gL10n->get('ROL_ROLE_ADMINISTRATION'), '/icons/roles_big.png',
							$gL10n->get('ROL_ROLE_ADMINISTRATION_DESC'));
	}
	if($gCurrentUser->isWebmaster())
	{
  
		$adminMenu->addItem('dbback', '/adm_program/administration/backup/backup.php',
							$gL10n->get('BAC_DATABASE_BACKUP'), '/icons/backup_big.png',
							$gL10n->get('BAC_DATABASE_BACKUP_DESC'));
		$adminMenu->addItem('orgprop', '/adm_program/administration/organization/organization.php',
							$gL10n->get('ORG_ORGANIZATION_PROPERTIES'), '/icons/options_big.png',
							$gL10n->get('ORG_ORGANIZATION_PROPERTIES_DESC'));
	}
	$adminMenu->show('long');
}

// Suche in Terminen
if($gCurrentUser->isWebmaster() || hasRole("Geschäftsstelle") || hasRole("Präsident"))
   {
   
    //connect_drbv();
   
    echo"<p><h3>Suche in Terminen</h3><br>";
    
    echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';
echo'<center><table>
<tr><td width=100><b>Suche in</b></td><td><b>Suche nach</b></td><td> </td><td><b>Sortieren nach</b></td></tr>
<tr><td>';
if(!$_SESSION["suche_nach"] || $_POST["suche_nach"] != $_SESSION["suche_nach"] )
    $_SESSION["suche_nach"] = $_POST["suche_nach"];

echo'<select name="suche_nach" size="1"  onchange="submit();">';

if($_SESSION["suche_nach"] == "dat_turniernummer")
   echo'<option value="dat_turniernummer"  selected>Turniernummer</option>';
else
echo'<option value="dat_turniernummer"> Turniernummer </option>';

if($_SESSION["suche_nach"] == "dat_verein")
   echo'<option value="dat_verein"  selected>Verein</option>';
else
echo'<option value="dat_verein">Verein</option>';

if($_SESSION["suche_nach"] == "dat_vereinsnummer")
   echo'<option value="dat_vereinsnummer" selected>Vereinsnummer</option>';
else
   echo'<option value="dat_vereinsnummer">Vereinsnummer</option>';

if($_SESSION["suche_nach"] == "dat_begin")
   echo'<option value="dat_begin" selected>Datum (JJJJ-DD-TT)</option>';
else
   echo'<option value="dat_begin">Datum (JJJJ-DD-TT)</option>';

if($_SESSION["suche_nach"] == "dat_location")
   echo'<option value="dat_location" selected>Ort</option>';
else
   echo'<option value="dat_location">Ort</option>';

if($_SESSION["suche_nach"] == "dat_headline")
   echo'<option value="dat_headline" selected>Titel</option>';
else
   echo'<option value="dat_headline">Titel</option>';
echo'</select>';
echo'<td><input type=text name="suche_wert" value="' . $_POST["suche_wert"] .'" size=25 maxlength=25></td>
<td><input type=submit name="absenden" value="Absenden"></td>
<td>';


if(!$_SESSION["sortieren"] || $_POST["sortieren"] != $_SESSION["sortieren"] )
    $_SESSION["sortieren"] = $_POST["sortieren"];

echo'<select name="sortieren" size="1"  onchange="submit();">';
if($_SESSION["sortieren"] == "dat_turniernummer")
   echo'<option value="dat_turniernummer"  selected>Turniernummer</option>';
else
echo'<option value="dat_turniernummer">Turniernummer</option>';

if($_SESSION["sortieren"] == "dat_verein")
   echo'<option value="dat_verein"  selected>Verein</option>';
else
echo'<option value="dat_verein">Verein</option>';

if($_SESSION["sortieren"] == "dat_vereinsnummer")
   echo'<option value="dat_vereinsnummer" selected>Vereinsnummer</option>';
else
   echo'<option value="dat_vereinsnummer">Vereinsnummer</option>';

if($_SESSION["sortieren"] == "dat_begin")
   echo'<option value="dat_begin" selected>Datum (JJJJ-DD-TT)</option>';
else
   echo'<option value="dat_begin">Datum (JJJJ-DD-TT)</option>';

if($_SESSION["sortieren"] == "dat_location")
   echo'<option value="dat_location" selected>Ort</option>';
else
   echo'<option value="dat_location">Ort</option>';

if($_SESSION["sortieren"] == "dat_headline")
   echo'<option value="dat_headline" selected>Titel</option>';
else
   echo'<option value="dat_headline">Titel</option>';
echo'</select>';

echo"</td><td>";

if(!$_SESSION["vwrw"] || $_POST["vwrw"] != $_SESSION["vwrw"] )
    $_SESSION["vwrw"] = $_POST["vwrw"];
    
echo'<select name="vwrw" size="1"  onchange="submit();"1>';

if($_SESSION["vwrw"] == "ASC")
   echo'<option value="ASC"  selected>Aufsteigend</option>';
else
echo'<option value="ASC">Aufsteigend</option>';

if($_SESSION["vwrw"] == "DESC")
   echo'<option value="DESC"  selected>Absteigend</option>';
else
echo'<option value="DESC">Absteigend</option>';

echo'</select></td></tr>
</table>';


if(!$_SESSION["vwrw"] || $_POST["vwrw"] != $_SESSION["vwrw"] )
    $_SESSION["vwrw"] = $_POST["vwrw"];
if(!$_SESSION["suche_nach"] || $_POST["suche_nach"] != $_SESSION["suche_nach"] )
    $_SESSION["vwrw"] = $_POST["vwrw"];

if($_POST["suche_nach"] && $_POST["suche_wert"])
{
echo'<table border="1">';
echo"<tr><th width=80>Turniernr.</th><th width=80>Vereinsnr.</th><th width=200>Verein</th><th width=80>Datum</th><th width=470>Ort</th><th width=350>Titel</th></tr>";

// Turnier finden
$sqlab = "SELECT * FROM adm_dates WHERE " . $_POST["suche_nach"] . " LIKE  '%" . $_POST["suche_wert"] . "%' " . "ORDER BY " . $_POST["sortieren"] . " " . $_SESSION["vwrw"];
$turniere = $gDb->query($sqlab);
  
//$turniere = mysqli_query($ADMIDIOdb, $sqlab);
// $anzahl = mysqli_affected_rows($turniere);
// print_r($turniere);
while($temp = mysqli_fetch_array($turniere))
 {
  $termin_id = $temp[0];
  $beginn = $temp[5];
  $anfang = substr($beginn,8,2) . "." . substr($beginn,5,2) . "." . substr($beginn,0,4);
  $anmeldung = $temp[9];
  $verein = $temp[38];
  if(!$verein)
     $verein = "&nbsp";
  $vereinsnummer = $temp[39];
  $turniernummer = $temp[45];
  $ort = $temp[10];
  if(!$ort)
     $ort =  "&nbsp";
  $titel = $temp[12];

  $link = '<a href="../adm_program/modules/dates/dates_new.php?dat_id=' . $termin_id . '">' . $titel . '</a>';

  $anzahl = $anzahl + 1;
echo"<tr><td align=center>$turniernummer</td><td align=center>$vereinsnummer</td><td>$verein</td><td align=center>$anfang</td><td>$ort </td><td>$link </td></tr>";

 }

echo"</table>";
echo"<br><center><b>Es wurden $anzahl Turniere gefunden.</b></center>";
}
echo'</form>'; 
// Suche in Daten
    echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';
    
    echo"<p><h3>Suche in Daten</h3><br>";

    echo'<table>
     <tr><td><b>Suchbegriff</b></td> <td>&nbsp;</td></tr>
     <tr><td><input type=text name="suche_daten" value="' . $_POST["suche_daten"] . '" size=25 maxlength=25></td>
     <td><input type=submit name="suchen" value="Absenden"></td></tr>
    </table>';

if($_POST["suchen"] && $_POST["suche_daten"])
{

echo'<table border="1">';
echo"<tr><th width=80>Vereinsnr.</th><th width=80>Nachname</th><th width=300>Vorname</th><th width=300>Tänzer/Team</th><th width=120>Telefon</th><th width=250>E-Mail</th><th width=60>Admidio</th></tr>";

// Suchbegriff finden
$sqlab       = "SELECT * FROM adm_user_data WHERE usd_value LIKE  '%" . $_POST["suche_daten"] . "%' ";
$suchbegriff = $gDb->query($sqlab);
  
while($temp = mysqli_fetch_array($suchbegriff))
 {
    // Daten zum Datensatz finden
   $benutzer_id = $temp[1]; 
   $sqlab = "SELECT * FROM adm_user_data WHERE usd_usr_id = '" . $benutzer_id . "' ";
   $daten = $gDb->query($sqlab);
   
   while($werte = mysqli_fetch_array($daten))
     {
      $wert = $werte[2];
      $inhalt = $werte[3];

      if($wert == "1")
         $nachname = $inhalt;
      if($wert == "2")
         $vorname = $inhalt;
      if($wert == "8")
         $telefon = $inhalt;
      if($wert == "12")
         $mail = $inhalt;
      if($wert == "31")
         $paar = $inhalt;
      if($wert == "32")
         $paar .= " $inhalt, ";
      if($wert == "37")
         $paar .= $inhalt;
      if($wert == "38")
         $paar .= " $inhalt";
      if($wert == "53")
         $ve_nr = $inhalt;
      if($wert == "54")
         $paar = $inhalt;

      if(!$nachname)
          $nachname = "&nbsp";
      if(!$vorname)
          $vorname = "&nbsp";
      if(!$telefon)
          $telefon = "&nbsp";
      if(!$mail)
          $mail = "&nbsp";
      if(!$paar)
          $paar  = "&nbsp";

      $link = '<a href="../adm_program/modules/profile/profile.php?user_id=' . $benutzer_id . '">' . $benutzer_id . '</a>';

     }

echo"<tr><td align=center>$ve_nr</td><td>$nachname</td><td>$vorname</td><td>$paar</td><td align=center>$telefon</td><td>$mail</td><td align=center>$link<br></tr>";

  unset($nachname);
  unset($vorname);
  unset($telefon);
  unset($mail);
  unset($paar);
  unset($ve_nr);
  unset($link);

 }

echo"</table>";

}
echo'</form>';    
    
   } 


echo'
<div style="text-align: center; margin: 15px;">  
    <span class="veryBigFontSize">
      <a href="http://www.drbv.de/cms/images/PDF/2017/AktivenPORTAL_Anleitung.pdf" target="_blank">Anleitung</a>
    </span>
    <br>
    <span class="smallFontSize">AktivenPORTAL Anleitung zum Vereinsprofil, zur Akrobatikauswahl und Anmeldung zu Turnieren.</span>
</div>';
  
require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>