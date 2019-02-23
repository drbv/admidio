<?php
/******************************************************************************
 * Aktive als Datei hochladen
 *
 * Copyright    : (c) 2019 The DRBV Software Team
 * Homepage  : https://www.drbv.de
 * License       : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * folder_id : ID des akutellen Ordner
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/table_folder.php');

// Initialize and check the parameters
//$getFolderId = admFuncVariableIsValid($_GET, 'folder_id', 'numeric', null, true);
$getFolderId = 148;//Folder AktivenListen

//nur von eigentlicher OragHompage erreichbar
if($gCurrentOrganization->getValue('org_shortname')!= $g_organization)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_ACCESS_FROM_HOMEPAGE_ONLY', $gHomepage));
}

//maximaler Fileupload fuer das Downloadmodul muss groesser 0 sein
if ($gPreferences['max_file_upload_size'] == 0) {

    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

//pruefen ob in den aktuellen Servereinstellungen ueberhaupt file_uploads auf ON gesetzt ist...
if (ini_get('file_uploads') != '1')
{
    $gMessage->show($gL10n->get('SYS_SERVER_NO_UPLOAD'));
}

$gNavigation->addUrl(CURRENT_URL);

if(isset($_SESSION['download_request']))
{
   $form_values = strStripSlashesDeep($_SESSION['download_request']);
   unset($_SESSION['download_request']);
}
else
{
   $form_values['new_name'] = null;
   $form_values['new_description'] = null;
}

try
{
    // get recordset of current folder from databse
    $folder = new TableFolder($gDb);
    $folder->getFolderForDownload($getFolderId);
}
catch(AdmException $e)
{
	$e->showHtml();
}

$parentFolderName = $folder->getValue('fol_name');


// Html-Kopf ausgeben
$gLayout['title']  = "Aktivenliste für Formationen und Präambelsportpaare hochladen";
$gLayout['header'] = '
    <script type="text/javascript"><!--
        $(document).ready(function() 
        {
            $("#userfile").focus();
        }); 
    //--></script>';
require(SERVER_PATH. '/adm_program/system/overall_header.php');

// Html des Modules ausgeben
echo '
<form action="'.$g_root_path.'/adm_program/modules/downloads/download_function.php?mode=1&amp;folder_id='.$getFolderId.'" method="post" enctype="multipart/form-data">
<div class="formLayout" id="upload_download_form">
    <div class="formHead">'.$gLayout['title'].'</div>
    <div class="formBody">
        <ul class="formFieldList">
            <li>
                <dl>
                    <dt>'.$gL10n->get('DOW_UPLOAD_FILE_TO_FOLDER', $parentFolderName).'</dt>
                    <dd>&nbsp;</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for="userfile">'.$gL10n->get('DOW_CHOOSE_FILE').':</label></dt>
                    <dd>
                        <input type="hidden" name="MAX_FILE_SIZE" value="'.($gPreferences['max_file_upload_size'] * 1024).'" />
                        <input type="file" id="userfile" name="userfile" style="width: 90%;" />
                        <span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt>Dateiname</dt>
                    <dd>
                        <input type="hidden" id="new_name" name="new_name" value="'.$gCurrentUser->getValue('VEREINSNUMMER').'_aktivenliste" style="width: 250px;" maxlength="255" />'
                        .$gCurrentUser->getValue('VEREINSNUMMER').'_aktivenliste
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for="new_description">'.$gL10n->get('SYS_DESCRIPTION').':</label></dt>
                    <dd>Die Datei muss im CSV-Format vorliegen und 
                            sollte pro Aktiven eine Zeile in folgender Form haben:<br><b>Name;Vorname;Geburtsdatum(tt.mm.jjjj)</b>
                            <br><br><i>Beispiel:</i><br>
                            Name;Vorname;Geburtsdatum<br>
                            Max;Mustermann;31.12.2000<br>
                            Eva;Musterfrau;01.01.2001
                    </dd>
                </dl>
            </li>
        </ul>

        <hr />

        <div class="formSubmit">
            <button id="btnUpload" type="submit"><img 
            src="'.THEME_PATH.'/icons/page_white_upload.png" alt="'.$gL10n->get('SYS_UPLOAD').'" />
            &nbsp;'.$gL10n->get('SYS_UPLOAD').'</button>
        </div>
    </div>
</div>
</form>

<ul class="iconTextLinkList">
    <li>
        <span class="iconTextLink">
            <a href="'.$g_root_path.'/adm_program/system/back.php"><img
            src="'.THEME_PATH.'/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
            <a href="'.$g_root_path.'/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
        </span>
    </li>
</ul>';

require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>