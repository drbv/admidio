<?php
/******************************************************************************
 * Show role members list
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * mode   : Ausgabeart   (html, print, csv-ms, csv-oo)
 * lst_id : ID der Listenkonfiguration, die angezeigt werden soll
 *          Wird keine ID uebergeben, wird die Default-Konfiguration angezeigt
 * rol_id : Rolle, fuer die die Funktion dargestellt werden soll
 * start  : Position of query recordset where the visual output should start
 * show_members : 0 - (Default) show active members of role
 *                1 - show former members of role
 *                2 - show active and former members of role
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/classes/list_configuration.php');
require_once('../../system/classes/table_roles.php');

// Initialize and check the parameters
$getMode        = admFuncVariableIsValid($_GET, 'mode', 'string', null, true, array('csv-ms', 'csv-oo', 'html', 'print'));
$getListId      = admFuncVariableIsValid($_GET, 'lst_id', 'numeric', 0);
$getRoleId      = admFuncVariableIsValid($_GET, 'rol_id', 'numeric', 0);
$getStart       = admFuncVariableIsValid($_GET, 'start', 'numeric', 0);
$getShowMembers = admFuncVariableIsValid($_GET, 'show_members', 'numeric', 0);
$getLeaderOff   = admFuncVariableIsValid($_GET, 'ldoff', 'numeric', 0);


//rmenken
//switch off leaders in list views
$leader_off = $getLeaderOff;

// Initialize the content of this parameter (otherwise some servers will keep the content)
unset($role_ids);

if($getRoleId > 0)
{
    $role_ids[] = $getRoleId;
}
else
{
    $role_ids = $_SESSION['role_ids'];
    $getRoleId = $role_ids[0];
}

// Rollenobjekt erzeugen
$role = new TableRoles($gDb, $getRoleId);

//Testen ob Recht zur Listeneinsicht besteht
if($role->viewRole() == false)
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// if no list parameter is set then load role default list configuration or system default list configuration
if($getListId == 0)
{
	// set role default list configuration
    $getListId = $role->getDefaultList();
	
	if($getListId == 0)
	{
	   $gMessage->show($gL10n->get('LST_DEFAULT_LIST_NOT_SET_UP'));
	}
}

$separator   = ',';    // fuer CSV-Dateien
$valueQuotes = '';
$charset     = '';
switch ($getMode)
{
	// Microsoft Excel 2007 und neuer braucht ein Semicolon    
	case 'csv-ms':
		$separator   = ';'; 
        $valueQuotes = '"';
        $getMode     = 'csv';
        $charset     = 'iso-8859-1';
		break;
    case 'csv-oo':
        $separator   = ',';   // fuer CSV-Dateien
        $valueQuotes = '"';   // Werte muessen mit Anfuehrungszeichen eingeschlossen sein
        $getMode     = 'csv';
        $charset     = 'utf-8';
        break;
    case 'html':
        $class_table           = 'tableList';
        $class_sub_header      = 'tableSubHeader';
        $class_sub_header_font = 'tableSubHeaderFont';
        break;
    case 'print':
        $class_table           = 'tableListPrint';
        $class_sub_header      = 'tableSubHeaderPrint';
        $class_sub_header_font = 'tableSubHeaderFontPrint';
		break;
}


// Array um den Namen der Tabellen sinnvolle Texte zuzuweisen
$arr_col_name = array('usr_login_name' => $gL10n->get('SYS_USERNAME'),
                      'usr_photo'      => $gL10n->get('PHO_PHOTO'),
                      'mem_begin'      => $gL10n->get('SYS_START'),
                      'mem_end'        => $gL10n->get('SYS_END'),
                      'mem_leader'     => $gL10n->get('SYS_LEADER')
                      );

$mainSql      = '';   // enthaelt das Haupt-Sql-Statement fuer die Liste
$str_csv      = '';   // enthaelt die komplette CSV-Datei als String
$leiter       = 0;    // Gruppe besitzt Leiter
$memberStatus = '';

try
{
	// create list configuration object and create a sql statement out of it
	$list = new ListConfiguration($gDb, $getListId);
	$mainSql = $list->getSQL($role_ids, $getShowMembers);
	//echo $mainSql; exit();
}
catch(AdmException $e)
{
	$e->showHtml();
}

// SQL-Statement der Liste ausfuehren und pruefen ob Daten vorhanden sind
$resultList = $gDb->query($mainSql);

$numMembers = $gDb->num_rows($resultList);

if($numMembers == 0)
{
    // Es sind keine Daten vorhanden !
    $gMessage->show($gL10n->get('LST_NO_USER_FOUND'));
}

if($numMembers < $getStart)
{
    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

if($getMode == 'html' && $getStart == 0)
{
    // Url fuer die Zuruecknavigation merken, aber nur in der Html-Ansicht
    $gNavigation->addUrl(CURRENT_URL);
}

if($getMode != 'csv')
{
    // Html-Kopf wird geschrieben
    if($getMode == 'print')
    {
    	header('Content-type: text/html; charset=utf-8');
        echo '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
        <head>
            <!-- (c) 2004 - 2013 The Admidio Team - http://www.admidio.org -->
            
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        
            <title>'. $gCurrentOrganization->getValue('org_longname'). ' - Liste - '. $role->getValue('rol_name'). '</title>
            
            <link rel="stylesheet" type="text/css" href="'. THEME_PATH. '/css/print.css" />
            <script type="text/javascript" src="'. $g_root_path. '/adm_program/system/js/common_functions.js"></script>

            <style type="text/css">
                @page { size:landscape; }
            </style>
        </head>
        <body class="bodyPrint">';
    }
    else
    {
        $gLayout['title']    = $gL10n->get('LST_LIST').' - '. $role->getValue('rol_name');
        $gLayout['includes'] = false;
        $gLayout['header']   = '
            <style type="text/css">
                body {
                    margin: 20px;
                }
            </style>

            <script type="text/javascript"><!--
				$(document).ready(function() {
					$("#admSelectExportMode").change(function () {
						if($(this).val().length > 1) {
							self.location.href = "'. $g_root_path. '/adm_program/modules/lists/lists_show.php?" +
                "lst_id='. $getListId. '&rol_id='. $getRoleId. '&ldoff='. $getLeaderOff. '&mode=" + $(this).val() + "&show_members='.$getShowMembers.'";
						}
					})
				});
            //--></script>';
        require(SERVER_PATH. '/adm_program/system/overall_header.php');
    }

    if($getShowMembers == 0)
    {
    	$memberStatus = $gL10n->get('LST_ACTIVE_MEMBERS');
    }
    elseif($getShowMembers == 1)
    {
    	$memberStatus = $gL10n->get('LST_FORMER_MEMBERS');
    }
    elseif($getShowMembers == 2)
    {
    	$memberStatus = $gL10n->get('LST_ACTIVE_FORMER_MEMBERS');
    }

    echo '<h1 class="moduleHeadline">'. $role->getValue('rol_name'). ' &#40;'.$role->getValue('cat_name').'&#41;</h1>
    <h3>';

    //Beschreibung der Rolle einblenden
    if(strlen($role->getValue('rol_description')) > 0)
    {
        echo $role->getValue('rol_description'). ' - ';
    }
    
    echo $memberStatus.'</h3>';

    if($getMode == 'html')
    {
        echo '<ul class="iconTextLinkList">
            <li>
                <span class="iconTextLink">';
                // Navigationspunkt zum uebergeordneten Punkt dieser Liste
                if(strpos($gNavigation->getPreviousUrl(), 'mylist') === false)
                {
                    // wenn nicht aus Listenuebersicht aufgerufen, dann wird hier die Listenuebersicht ohne Parameter aufgerufen
                    if(strpos($gNavigation->getPreviousUrl(), 'lists.php') === false)
                    {
                        $url = $g_root_path.'/adm_program/modules/lists/lists.php';
                    }
                    else
                    {
                        $url = $g_root_path.'/adm_program/system/back.php';
                    }
                    echo '
                    <a href="'.$url.'"><img
                    src="'. THEME_PATH. '/icons/application_view_list.png" alt="'.$gL10n->get('LST_LIST_VIEW').'" title="'.$gL10n->get('LST_LIST_VIEW').'" /></a>
                    <a href="'.$url.'">'.$gL10n->get('LST_LIST_VIEW').'</a>';
                }
                else
                {
                    echo '
                    <a href="'.$g_root_path.'/adm_program/modules/lists/mylist.php?lst_id='. $getListId. '&rol_id='. $getRoleId. '&show_members='.$getShowMembers.'"><img
                    src="'. THEME_PATH. '/icons/application_form.png" alt="'.$gL10n->get('LST_KONFIGURATION_OWN_LIST').'" title="'.$gL10n->get('LST_KONFIGURATION_OWN_LIST').'" /></a>
                    <a href="'.$g_root_path.'/adm_program/modules/lists/mylist.php?lst_id='. $getListId. '&rol_id='. $getRoleId. '&show_members='.$getShowMembers.'">'.$gL10n->get('LST_KONFIGURATION_OWN_LIST').'</a>';
                }
            echo '</span>
            </li>';

			// link to mail module with this role and members status
            if($gCurrentUser->mailRole($role->getValue('rol_id')) && (hasRole("Webmaster") or hasRole("Turnierleiter")) && $gPreferences['enable_mail_module'] == 1)
            {
                echo '<li>
                    <span class="iconTextLink">
                        <a href="'.$g_root_path.'/adm_program/modules/mail/mail.php?rol_id='.$getRoleId.'&show_members='.$getShowMembers.'"><img
                        src="'. THEME_PATH. '/icons/email.png" alt="'.$gL10n->get('LST_EMAIL_TO_MEMBERS').'"  title="'.$gL10n->get('LST_EMAIL_TO_MEMBERS').'" /></a>
                        <a href="'.$g_root_path.'/adm_program/modules/mail/mail.php?rol_id='.$getRoleId.'&show_members='.$getShowMembers.'">'.$gL10n->get('LST_EMAIL_TO_MEMBERS').'</a>
                    </span>
                </li>';
            }

			// link to assign or remove members if you are allowed to do it
			if($role->allowedToAssignMembers($gCurrentUser))
			{
          //rmenken: only for webmaster otherwise link2 members4dates will not be taken into account here
          if(hasRole("Webmaster") or hasRole("Turnierleiter")){
				echo '<li>
					<span class="iconTextLink">
						<a href="'.$g_root_path.'/adm_program/modules/lists/members.php?rol_id='. $role->getValue('rol_id'). '"><img 
							src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('SYS_ASSIGN_MEMBERS').'" title="'.$gL10n->get('SYS_ASSIGN_MEMBERS').'" /></a>
						<a href="'.$g_root_path.'/adm_program/modules/lists/members.php?rol_id='. $role->getValue('rol_id'). '">'.$gL10n->get('SYS_ASSIGN_MEMBERS').'</a>
					</span>
				</li>';
          }
			}

            echo '<li>
                <span class="iconTextLink">
                    <a href="#" onclick="window.open(\''.$g_root_path.'/adm_program/modules/lists/lists_show.php?lst_id='.$getListId.'&amp;mode=print&amp;ldoff='.$getLeaderOff.'&amp;rol_id='.$getRoleId.'&amp;show_members='.$getShowMembers.'\', \'_blank\')"><img
                    src="'. THEME_PATH. '/icons/print.png" alt="'.$gL10n->get('LST_PRINT_PREVIEW').'" title="'.$gL10n->get('LST_PRINT_PREVIEW').'" /></a>
                    <a href="#" onclick="window.open(\''.$g_root_path.'/adm_program/modules/lists/lists_show.php?lst_id='.$getListId.'&amp;mode=print&amp;ldoff='.$getLeaderOff.'&amp;rol_id='.$getRoleId.'&amp;show_members='.$getShowMembers.'\', \'_blank\')">'.$gL10n->get('LST_PRINT_PREVIEW').'</a>
                </span>
            </li>
            <li>
                <span class="iconTextLink">
                    <img src="'. THEME_PATH. '/icons/database_out.png" alt="'.$gL10n->get('LST_EXPORT_TO').'" />
                    <select id="admSelectExportMode" size="1">
                        <option value="" selected="selected">'.$gL10n->get('LST_EXPORT_TO').' ...</option>
                        <option value="csv-ms">'.$gL10n->get('LST_MICROSOFT_EXCEL').' ('.$gL10n->get('SYS_ISO_8859_1').')</option>
                        <option value="csv-oo">'.$gL10n->get('LST_CSV_FILE').' ('.$gL10n->get('SYS_UTF8').')</option>
                    </select>
                </span>
            </li>   
        </ul>';
    }

    // write table header
    echo '<table class="'.$class_table.'" style="width: 100%;" cellspacing="0">
        <thead><tr>';
}

// headlines for columns
for($column_number = 1; $column_number <= $list->countColumns(); $column_number++)
{
    $column = $list->getColumnObject($column_number);
    $align = 'left';

    // den Namen des Feldes ermitteln
    if($column->getValue('lsc_usf_id') > 0)
    {
        // benutzerdefiniertes Feld
        $usf_id = $column->getValue('lsc_usf_id');
        $col_name = $gProfileFields->getPropertyById($usf_id, 'usf_name');

        if($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'CHECKBOX'
        || $gProfileFields->getPropertyById($usf_id, 'usf_name_intern') == 'GENDER')
        {
            $align = 'center';
        }
        elseif($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'NUMERIC')
        {
            $align = 'center';
        }
    }
    else
    {
        $usf_id = 0;
        $col_name = $arr_col_name[$column->getValue('lsc_special_field')];
    }

    // versteckte Felder duerfen nur von Leuten mit entsprechenden Rechten gesehen werden
    if($usf_id == 0
    || $gCurrentUser->editUsers()
    || $gProfileFields->getPropertyById($usf_id, 'usf_hidden') == 0)
    {
        if($getMode == 'csv')
        {
            if($column_number == 1)
            {
        if($getListId != 24) {
                // die Laufende Nummer noch davorsetzen
                $str_csv = $str_csv. $valueQuotes. $gL10n->get('SYS_ABR_NO'). $valueQuotes;
              } else {
                // setze den Header direkt und nur einmal, ohne weiteres überschreiben bei
                // columns grösser 1
                $str_csv = $str_csv. $valueQuotes. 'Startklasse'. $valueQuotes.  
                     $separator. $valueQuotes. 'Startnr'. $valueQuotes.  
                     $separator. $valueQuotes. 'Da_Vorname'. $valueQuotes.  
                     $separator. $valueQuotes. 'Da_Nachname'. $valueQuotes.  
                     $separator. $valueQuotes. 'He_Vorname'. $valueQuotes.  
                     $separator. $valueQuotes. 'He_Nachname'. $valueQuotes.  
                     $separator. $valueQuotes. 'Vereinsnr'. $valueQuotes.  
                     $separator. $valueQuotes. 'Vereinsname'. $valueQuotes.  
                     $separator. $valueQuotes. 'Teamname'. $valueQuotes.  
                     $separator. $valueQuotes. 'Startbuch'. $valueQuotes.  
                     $separator. $valueQuotes. 'Boogie_Startkarte_H'. $valueQuotes.  
                     $separator. $valueQuotes. 'Boogie_Startkarte_D'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz1_VR'. $valueQuotes.                     
                     $separator. $valueQuotes. 'Wert1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro2_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz2_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert2_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID2_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro3_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz3_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert3_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID3_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro4_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz4_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert4_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID4_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro5_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz5_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert5_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID5_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro6_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz6_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert6_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID6_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro7_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz7_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert7_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID7_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro8_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz8_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert8_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID8_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE1_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-IDE1_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE2_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE2_VR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE2_VR'. $valueQuotes.                   
                     $separator. $valueQuotes. 'W-IDE2_VR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz1_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro2_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz2_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert2_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID2_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro3_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz3_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert3_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID3_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro4_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz4_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert4_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID4_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro5_ZR'. $valueQuotes.                        
                     $separator. $valueQuotes. 'Kurz5_ZR'. $valueQuotes.                      
                     $separator. $valueQuotes. 'Wert5_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID5_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro6_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz6_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert6_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID6_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro7_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz7_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert7_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID7_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro8_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz8_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert8_ZR'. $valueQuotes.
                     $separator. $valueQuotes. 'W-ID8_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE1_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-IDE1_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE2_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE2_ZR'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE2_ZR'. $valueQuotes.                   
                     $separator. $valueQuotes. 'W-IDE2_ZR'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro1_ER'. $valueQuotes.
                     $separator. $valueQuotes. 'Kurz1_ER'. $valueQuotes.                     
                     $separator. $valueQuotes. 'Wert1_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID1_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro2_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz2_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert2_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID2_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro3_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz3_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert3_ER'. $valueQuotes.
                     $separator. $valueQuotes. 'W-ID3_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro4_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz4_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert4_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID4_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro5_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz5_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert5_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID5_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro6_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz6_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert6_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID6_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro7_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz7_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'Wert7_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-ID7_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Akro8_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'Kurz8_ER'. $valueQuotes.            
                     $separator. $valueQuotes. 'Wert8_ER'. $valueQuotes.            
                     $separator. $valueQuotes. 'W-ID8_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE1_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE1_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE1_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'W-IDE1_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'AkroE2_ER'. $valueQuotes.  
                     $separator. $valueQuotes. 'KurzE2_ER'. $valueQuotes.                       
                     $separator. $valueQuotes. 'WertE2_ER'. $valueQuotes.                   
                     $separator. $valueQuotes. 'W-IDE2_ER'. $valueQuotes; 
        }
            }
      if($getListId != 24){
            $str_csv = $str_csv. $separator. $valueQuotes. $col_name. $valueQuotes;
      }            
        }
        else
        {                
            if($column_number == 1)
            {
                // die Laufende Nummer noch davorsetzen
                echo '<th style="text-align: '.$align.';">'.$gL10n->get('SYS_ABR_NO').'</th>';
            }
            echo '<th style="text-align: '.$align.';">'.$col_name.'</th>';
        }
    }
    
}  // End-For

if($getMode == 'csv')
{
    $str_csv = $str_csv. "\n";
}
else
{
    echo '</tr></thead><tbody>';
}

//rmenken
$amountLeader = $role->countLeaders();

// set number of first member of this page (leaders are counted separately)
if($getStart > $role->countLeaders())
{
	$listRowNumber = $getStart - $role->countLeaders() + 1;
}
else
{
	$listRowNumber = $getStart + 1;	
}

$lastGroupHead = -1;             // Merker um Wechsel zwischen Leiter und Mitglieder zu merken

if($getMode == 'html' && $gPreferences['lists_members_per_page'] > 0)
{
    $members_per_page = $gPreferences['lists_members_per_page'];     // Anzahl der Mitglieder, die auf einer Seite angezeigt werden
  //rmenken
  if($leader_off == 1){
    $members_per_page = 1000;
  }
}
else
{
    $members_per_page = $numMembers;
}

// jetzt erst einmal zu dem ersten relevanten Datensatz springen
if(!$gDb->data_seek($resultList, $getStart))
{
    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

for($j = 0; $j < $members_per_page && $j + $getStart < $numMembers; $j++)
{
    $html_out3 = '';
    if($row = $gDb->fetch_array($resultList))
    {
        if($getMode != 'csv')
        {
            // erst einmal pruefen, ob es ein Leiter ist, falls es Leiter in der Gruppe gibt, 
            // dann muss noch jeweils ein Gruppenkopf eingefuegt werden
            if($lastGroupHead != $row['mem_leader']
            && ($row['mem_leader'] != 0 || $lastGroupHead != -1))
            {
                if($row['mem_leader'] == 1)
                {
                    $title = $gL10n->get('SYS_LEADER');
                }
                else
                {
					// if list has leaders then initialize row number for members
					$listRowNumber = 1;
                    $title = $gL10n->get('SYS_PARTICIPANTS');
                }
          if($leader_off == 1){
            if($row['mem_leader'] == 0){
                echo '<tr>
                    <td class="'.$class_sub_header.'" colspan="'. ($list->countColumns() + 1). '">
                        <div class="'.$class_sub_header_font.'" style="float: left;">&nbsp;'.$title.'</div>
                    </td>
                </tr>';
            }
              } else {
                    echo '<tr>
                      <td class="'.$class_sub_header.'" colspan="'. ($list->countColumns() + 1). '">
                        <div class="'.$class_sub_header_font.'" style="float: left;">&nbsp;'.$title.'</div>
                      </td>
                    </tr>';                
        }
                $lastGroupHead = $row['mem_leader'];
        }
        }

        // Felder zu Datensatz
        for($column_number = 1; $column_number <= $list->countColumns(); $column_number++)
        {
            $column = $list->getColumnObject($column_number);

            // da im SQL noch mem_leader und usr_id vor die eigentlichen Spalten kommen,
            // muss der Index auf row direkt mit 2 anfangen
            $sql_column_number = $column_number + 1;

            if($column->getValue('lsc_usf_id') > 0)
            {
                // pruefen, ob ein benutzerdefiniertes Feld und Kennzeichen merken
                $b_user_field = true;
                $usf_id = $column->getValue('lsc_usf_id');
            }
            else
            {
                $b_user_field = false;
                $usf_id = 0;
            }

            // versteckte Felder duerfen nur von Leuten mit entsprechenden Rechten gesehen werden
            if($usf_id == 0
            || $gCurrentUser->editUsers()
            || $gProfileFields->getPropertyById($usf_id, 'usf_hidden') == 0)
            {
                if($getMode != 'csv')
                {
                    $align = 'left';
                    if($b_user_field == true)
                    {
                        if($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'CHECKBOX'
                        || $gProfileFields->getPropertyById($usf_id, 'usf_name_intern') == 'GENDER')
                        {
                            $align = 'center';
                        }
                        elseif($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'NUMERIC')
                        {
                            $align = 'center';
                        }
                    }
                    //rmenken
                    if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){
                    if($column_number == 1)
                    {
                        // die Laufende Nummer noch davorsetzen
                        $html_out3 = '<td style="text-align: '.$align.';">'.$listRowNumber.'</td>';
                      }
                      $html_out3 .= '<td style="text-align: '.$align.';">';
                    }
                }
                else
                {
                  if($column_number == 1 && $getListId != 24)
                    {
                        // erste Spalte zeigt lfd. Nummer an
                  // rmenken
                    if($leader_off == 1){
                      if($row['mem_leader'] == 0){
                        $mylistRowNumber = $listRowNumber - $role->countLeaders();
                        $str_csv = $str_csv. $valueQuotes. $mylistRowNumber. $valueQuotes. $separator;
                      }      
                    } else {
                       $str_csv = $str_csv. $valueQuotes. $listRowNumber. $valueQuotes. $separator;
                    }    
                    }
                }
    
                $content  = '';

				/*****************************************************************/
                // create field content for each field type and output format
				/*****************************************************************/
				if($usf_id == $gProfileFields->getProperty('COUNTRY', 'usf_id') && $usf_id!=0)
				{
					$content = $gL10n->getCountryByCode($row[$sql_column_number]);
				}
                elseif($column->getValue('lsc_special_field') == 'usr_photo')
                {
                    // show user photo
                    if($getMode == 'html' || $getMode == 'print')
                    {
                if(file_exists(SERVER_PATH. '/adm_my_files/user_profile_photos/'.$row['usr_id'].'a.jpg'))
                {
                  $content = '<img src="'.$g_root_path.'/adm_program/modules/profile/profile_photo_show.php?usr_id='.$row['usr_id'].'" style="vertical-align: middle;" alt="'.$gL10n->get('LST_USER_PHOTO').'" />
                              <img src="'.$g_root_path.'/adm_program/modules/profile/profile_photo_showa.php?usr_id='.$row['usr_id'].'" style="vertical-align: middle;" alt="'.$gL10n->get('LST_USER_PHOTO').'" />';
                } else {
                        $content = '<img src="'.$g_root_path.'/adm_program/modules/profile/profile_photo_show.php?usr_id='.$row['usr_id'].'" style="vertical-align: middle;" alt="'.$gL10n->get('LST_USER_PHOTO').'" />';
                    }
            }
            //if ($getMode == 'csv' && $row[$sql_column_number] != NULL)
            if ($getMode == 'csv')
                    {
                //$content = $gL10n->get('LST_USER_PHOTO');
                $content = $g_root_path.'/adm_program/modules/profile/profile_photo_show.php?usr_id='.$row['usr_id'].'_unscaled.jpg';
                    }
                }
				elseif($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'DATE'
				||     $column->getValue('lsc_special_field') == 'mem_begin'
				||     $column->getValue('lsc_special_field') == 'mem_end') 
				{
					if(strlen($row[$sql_column_number]) > 0)
					{
						// date must be formated
						$date = new DateTimeExtended($row[$sql_column_number], 'Y-m-d', 'date');
						$content = $date->format($gPreferences['system_date']);
					}
				}
				elseif($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'DROPDOWN'
				||     $gProfileFields->getPropertyById($usf_id, 'usf_type') == 'RADIO_BUTTON') 
				{
					if(strlen($row[$sql_column_number]) > 0)
					{
						// show selected text of optionfield or combobox
						if($getMode != 'csv')
						{
							$arrListValues = $gProfileFields->getPropertyById($usf_id, 'usf_value_list', 'html');
						}
						else
						{
							$arrListValues = $gProfileFields->getPropertyById($usf_id, 'usf_value_list', 'text');
						}
            if (in_array($gProfileFields->getPropertyById($usf_id, 'usf_name_intern'),
              array("AKROBATIK_1_-_VORRUNDE","AKROBATIK_2_-_VORRUNDE","AKROBATIK_3_-_VORRUNDE","AKROBATIK_4_-_VORRUNDE","AKROBATIK_5_-_VORRUNDE",
                    "AKROBATIK_6_-_VORRUNDE","AKROBATIK_7_-_VORRUNDE","AKROBATIK_8_-_VORRUNDE","AKROBATIK_E_-_VORRUNDE","AKROBATIK_E2_-_VORRUNDE",
                    "AKROBATIK_1_-_ZWRUNDE","AKROBATIK_2_-_ZWRUNDE","AKROBATIK_3_-_ZWRUNDE","AKROBATIK_4_-_ZWRUNDE","AKROBATIK_5_-_ZWRUNDE",
                    "AKROBATIK_6_-_ZWRUNDE","AKROBATIK_7_-_ZWRUNDE","AKROBATIK_8_-_ZWRUNDE","AKROBATIK_E_-_ZWRUNDE","AKROBATIK_E2_-_ZWRUNDE", 
                    "AKROBATIK_1_-_ENDRUNDE","AKROBATIK_2_-_ENDRUNDE","AKROBATIK_3_-_ENDRUNDE","AKROBATIK_4_-_ENDRUNDE","AKROBATIK_5_-_ENDRUNDE",
                    "AKROBATIK_6_-_ENDRUNDE","AKROBATIK_7_-_ENDRUNDE","AKROBATIK_8_-_ENDRUNDE","AKROBATIK_E_-_ENDRUNDE","AKROBATIK_E2_-_ENDRUNDE"))){
              $content = $row[$sql_column_number];
              
              if($getMode == 'html'){
                $AkroList             = 'AKROBATIKLISTE-'.substr($content,0,1);                      
                $arrListValues4search = $gProfileFields->getProperty($AkroList, 'usf_value_list');
  
                $akro_content = explode(":", $content);
                // suchen in $arrListValues4search
                foreach($arrListValues4search as $key => $valueList4search){
                  $valueList4search_content = explode(":", $valueList4search);
                    if (trim($valueList4search_content[0]) == trim($akro_content[0])) {
                      $myLangname = trim($valueList4search_content[2]);
                      $myKurzname = trim($valueList4search_content[3]);
                      $myWert     = trim($valueList4search_content[1]);
                      $myID       = trim($valueList4search_content[4]);
                    }    
                 }                                    
                 $content = '<a class="textTooltipTLP" title="'.$myLangname.' / ID='.$myID.' / Pkt='.$myWert.'">'.$content.'</a>';                 
               }              
            } else {
						$content = $arrListValues[$row[$sql_column_number]];
					}
				}
        }
				else 
				{
					$content = $row[$sql_column_number];
				}

				// format value for csv export
                if($getMode == 'csv')
                {
          //rmenken
          $myLangname = '';
          $myKurzname = '';
          $myWert     = '';
          $myID       = '';
          if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){
            if($gProfileFields->getPropertyById($usf_id, 'usf_name_intern') == 'STARTKLASSE') 
            {
              $startklasse = $gProfileFields->getHtmlValue($gProfileFields->getPropertyById($usf_id, 'usf_name_intern'), $content, $row['usr_id']);
              if ($startklasse == 'RR_S'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-S', 'usf_value_list');
                    }
              if ($startklasse == 'RR_J'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-J', 'usf_value_list');
              }
              if ($startklasse == 'RR_C'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-C', 'usf_value_list');
              }
              if ($startklasse == 'RR_B'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-B', 'usf_value_list');
              }
              if ($startklasse == 'RR_A'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-A', 'usf_value_list');
              }
              if ($startklasse == 'RR_F'){
                      $arrListValues4search = $gProfileFields->getProperty('AKROBATIKLISTE-F', 'usf_value_list');
              }
            }
      
            //Inhalt Feld pro column
            //Nur bis column 9 normal, dann folgen Akrobatikstrings
            if($getListId == 24){
              if($column_number < 10) {
                $str_csv = $str_csv. $valueQuotes. $content. $valueQuotes. $separator;      
              } else {
                //Akrostring Beispiel: "AK01 : Kurzname"
                //                      [0]    [1]
                $akro_content = explode(":", $content);

                // suchen in $arrListValues4search
                foreach($arrListValues4search as $key => $valueList4search){
                  $valueList4search_content = explode(":", $valueList4search);
                    if (trim($valueList4search_content[0]) == trim($akro_content[0])) {
                      $myLangname = trim($valueList4search_content[2]);
                      $myKurzname = trim($valueList4search_content[3]);
                      $myWert     = trim($valueList4search_content[1]);
                      $myID       = trim($valueList4search_content[4]);
                    }    
                 }

                // liste 24(7) alt:
                // ----------------
                // csv spalten: Akro1_VR / Kurz1_VR / Wert1_VR
                //              Langname   Kurzname   Wert
                //$str_csv = $str_csv. $valueQuotes. trim($akro_content[0]). $valueQuotes. $separator. $valueQuotes. trim($akro_content[1]). $valueQuotes. $separator;
                // liste 24(7) neu:
                // ----------------
                // csv spalten: Akro1_VR / Kurz1_VR / Wert1_VR
                //              Nummer  Kurzname   Wert
                //$str_csv = $str_csv. $valueQuotes. trim($akro_content[0]). $valueQuotes. $separator. $valueQuotes. trim($akro_content[1]). $valueQuotes. $separator. $valueQuotes. '0,00'. $valueQuotes. $separator;
                $str_csv = $str_csv. $valueQuotes. $myLangname. $valueQuotes. $separator. $valueQuotes. $myKurzname. $valueQuotes. $separator. $valueQuotes. $myWert. $valueQuotes. $separator. $valueQuotes. $myID. $valueQuotes. $separator;
              }
              //Nach erster column (Startklasse) lfd. Nummer (Startnummer) hinzufügen
              if($column_number == 1){
                // erste Spalte zeigt lfd. Nummer an
                // rmenken
                if($leader_off == 1){
                  if($row['mem_leader'] == 0){
                    $mylistRowNumber = $listRowNumber - $role->countLeaders();
                    $str_csv = $str_csv. $valueQuotes. $mylistRowNumber. $valueQuotes. $separator;
                  }   
                } else {
                   $str_csv = $str_csv. $valueQuotes. $listRowNumber. $valueQuotes. $separator;
                }     
              }
              // rmenken
              // Leere Felder einfügen für Boogie_Startkarte_H und Boogie_Startkarte_D
              if($column_number == 9){
              $str_csv = $str_csv. $valueQuotes. ''. $valueQuotes. $separator. $valueQuotes. ''. $valueQuotes. $separator;
              }      
            } else {
              $str_csv = $str_csv. $valueQuotes. $content. $valueQuotes. $separator;
            }           
          }
                }
				// create output in html layout
				else
				{
          //rmenken
          if($gProfileFields->getPropertyById($usf_id, 'usf_name_intern') == 'VEREINSNUMMER') {
            $vereinsnr_list = $gProfileFields->getHtmlValue($gProfileFields->getPropertyById($usf_id, 'usf_name_intern'), $content, $row['usr_id']);
          }
          if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){      
            $html_out3 .=  $content.'</td>';
          }
        }
      }
    } // End-For Felder zu Datensatz
        
    if($getMode == 'html')
    {
      //rmenken
      if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){
      $html_out1 = '<tr class="tableMouseOver" style="cursor: pointer" ';
      
      $vereinsnr_user = $gCurrentUser->getValue('VEREINSNUMMER');
        
      if($gCurrentUser->isWebmaster() || ($vereinsnr_list == $vereinsnr_user)){
        $html_out2 = 'onclick="window.location.href=\''. $g_root_path. '/adm_program/modules/profile/profile.php?user_id='. $row['usr_id']. '\'">';
      } else {
        $html_out2 = '>';
      }
				}
            }
    else if($getMode == 'print')
    {
      echo '<tr>';
        }

    echo $html_out1 . $html_out2 . $html_out3;      

        if($getMode == 'csv')
        {
        //rmenken
          if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){
            $str_csv = $str_csv. "\n";
        }
        }
        else
        {
          if($row['mem_leader'] == 0 or ($row['mem_leader'] == 1 and ($leader_off == 0))){
            echo '</tr>';
        }
        }

        $listRowNumber++;
    } // End-($row = $gDb->fetch_array($resultList))
}  // End-While (jeder gefundene User)

if($getMode == 'csv')
{
    // nun die erstellte CSV-Datei an den User schicken
    $filename = $gCurrentOrganization->getValue('org_shortname'). '-'. str_replace('.', '', $role->getValue('rol_name')). '.csv';
    
    // for IE the filename must have special chars in hexadecimal 
    if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
    {
        $filename = urlencode($filename);
    }
    
    header('Content-Type: text/comma-separated-values; charset='.$charset);
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    
    // neccessary for IE, because without it the download with SSL has problems
	header('Cache-Control: private');
	header('Pragma: public');

	if($charset == 'iso-8859-1')
	{
		echo utf8_decode($str_csv);
	}
	else
	{
    echo utf8_decode($str_csv);
	}
}
else
{
    echo '</tbody></table>';

    if($getMode != 'print')
    {
        // If neccessary show links to navigate to next and previous recordsets of the query
        $base_url = $g_root_path. '/adm_program/modules/lists/lists_show.php?lst_id='.$getListId.'&mode='.$getMode.'&ldoff='.$getLeaderOff.'&rol_id='.$getRoleId.'&show_members='.$getShowMembers;
        echo admFuncGeneratePagination($base_url, $numMembers, $members_per_page, $getStart, TRUE);
    }

    //INFOBOX zur Gruppe
    //nur anzeigen wenn zusatzfelder gefüllt sind
    if(strlen($role->getValue('rol_start_date')) > 0
    || $role->getValue('rol_weekday') > 0
    || strlen($role->getValue('rol_start_time')) > 0
    || strlen($role->getValue('rol_location')) > 0
    || strlen($role->getValue('rol_cost')) > 0
    || strlen($role->getValue('rol_max_members')) > 0)
    {
        echo '
        <div class="groupBox" id="infoboxListsBox">
            <div class="groupBoxHeadline">Infobox: '. $role->getValue('rol_name'). '</div>
            <div class="groupBoxBody">
                <ul class="formFieldList">
                    <li>';
                        //Kategorie
                        echo '
                        <dl>
                            <dt>'.$gL10n->get('SYS_CATEGORY').':</dt>
                            <dd>'.$role->getValue('cat_name').'</dd>
                        </dl>
                    </li>';

                        //Beschreibung
                        if(strlen($role->getValue('rol_description')) > 0)
                        {
                            echo'<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_DESCRIPTION').':</dt>
                                    <dd>'.$role->getValue('rol_description').'</dd>
                                </dl>
                            </li>';
                        }

                        //Zeitraum
                        if(strlen($role->getValue('rol_start_date')) > 0)
                        {
                            echo'<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_PERIOD').':</dt>
                                    <dd>'.$gL10n->get('SYS_DATE_FROM_TO', $role->getValue('rol_start_date', $gPreferences['system_date']), $role->getValue('rol_end_date', $gPreferences['system_date'])).'</dd>
                                </dl>
                            </li>';
                        }

                        //Termin
                        if($role->getValue('rol_weekday') > 0 || strlen($role->getValue('rol_start_time')) > 0)
                        {
                            echo '<li>
                                <dl>
                                    <dt>'.$gL10n->get('DAT_DATE').': </dt>
                                    <dd>'; 
                                        if($role->getValue('rol_weekday') > 0)
                                        {
                                            echo DateTimeExtended::getWeekdays($role->getValue('rol_weekday')).' ';
                                        }
                                        if(strlen($role->getValue('rol_start_time')) > 0)
                                        {
                                            echo $gL10n->get('LST_FROM_TO', $role->getValue('rol_start_time', $gPreferences['system_time']), $role->getValue('rol_end_time', $gPreferences['system_time']));
                                        }

                                    echo '</dd>
                                </dl>
                            </li>';
                        }

                        //Treffpunkt
                        if(strlen($role->getValue('rol_location')) > 0)
                        {
                            echo '<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_LOCATION').':</dt>
                                    <dd>'.$role->getValue('rol_location').'</dd>
                                </dl>
                            </li>';
                        }

                        //Beitrag
                        if(strlen($role->getValue('rol_cost')) > 0)
                        {
                            echo '<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_CONTRIBUTION').':</dt>
                                    <dd>'. $role->getValue('rol_cost'). ' '.$gPreferences['system_currency'].'</dd>
                                </dl>
                            </li>';
                        }

						//Beitragszeitraum
                        if(strlen($role->getValue('rol_cost_period')) > 0 && $role->getValue('rol_cost_period') != 0)
                        {
                            echo '<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_CONTRIBUTION_PERIOD').':</dt>
                                    <dd>'.$role->getCostPeriods($role->getValue('rol_cost_period')).'</dd>
                                </dl>
                            </li>';
                        }

                        //maximale Teilnehmerzahl
                        if(strlen($role->getValue('rol_max_members')) > 0)
                        {
                            echo'<li>
                                <dl>
                                    <dt>'.$gL10n->get('SYS_MAX_PARTICIPANTS').':</dt>
                                    <dd>'. $role->getValue('rol_max_members'). '</dd>
                                </dl>
                            </li>';
                        }
                echo'</ul>
            </div>
        </div>';
    } // Ende Infobox
    
    if($getMode == 'print')
    {
        echo '</body></html>';
    }
    else
    {    
        echo '
        <ul class="iconTextLinkList">
            <li>
                <span class="iconTextLink">
                    <a href="'.$g_root_path.'/adm_program/system/back.php"><img 
                    src="'. THEME_PATH. '/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
                    <a href="'.$g_root_path.'/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
                </span>
            </li>
        </ul>';
    
        require(SERVER_PATH. '/adm_program/system/overall_footer.php');
    }
}

?>

