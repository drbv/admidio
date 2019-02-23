<?php
/******************************************************************************
 * Downloadfunktionen
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * mode   :  1 - Datei hochladen
 *           2 - Datei loeschen
 *           3 - Ordner erstellen
 *           4 - Datei / Ordner umbenennen
 *           5 - Ordner loeschen
 *           6 - Datei / Ordner zur DB hinzufuegen
 *           7 - Berechtigungen ffür Ordner speichern
 * folder_id :  OrdnerId in der DB
 * file_id   :  FileId in der DB
 * name      : Name des Ordners/Datei die zur DB hinzugefuegt werden soll
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/my_files.php');
require_once('../../system/classes/table_folder.php');
require_once('../../system/classes/table_file.php');
require_once(SERVER_PATH. '/adm_program/system/classes/email.php');

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_download_module'] != 1)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}

// Initialize and check the parameters
$getMode     = admFuncVariableIsValid($_GET, 'mode', 'numeric', null, true);
$getFolderId = admFuncVariableIsValid($_GET, 'folder_id', 'numeric', 0);
$getFileId   = admFuncVariableIsValid($_GET, 'file_id', 'numeric', 0);
$getName     = admFuncVariableIsValid($_GET, 'name', 'string');
$rm_file_exists = false;

// erst pruefen, ob der User auch die entsprechenden Rechte hat
if (!$gCurrentUser->editDownloadRight() && $getFolderId != 148)//rm folder 148 aktivenliste immer die Rechte geben
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

$_SESSION['download_request'] = $_POST;

// Pfad in adm_my_files pruefen und ggf. anlegen
$myFilesDownload = new MyFiles('DOWNLOAD');
if($myFilesDownload->checkSettings() == false)
{
    $gMessage->show($gL10n->get($myFilesDownload->errorText, $myFilesDownload->errorPath, '<a href="mailto:'.$gPreferences['email_administrator'].'">', '</a>'));
}

// Dateien hochladen
if ($getMode == 1)
{
    if ($getFolderId == 0) 
    {
        //FolderId ist zum hochladen erforderlich
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    try
    {
        // get recordset of current folder from databse
        $targetFolder = new TableFolder($gDb);
        $targetFolder->getFolderForDownload($getFolderId);
    }
    catch(AdmException $e)
    {
    	$e->showHtml();
    }

    if (empty($_POST))
    {
        $gMessage->show($gL10n->get('DOW_UPLOAD_POST_EMPTY',ini_get('upload_max_filesize')));
    }

    //Dateigroesse ueberpruefen Servereinstellungen
    if ($_FILES['userfile']['error']==1)
    {
        $gMessage->show($gL10n->get('SYS_FILE_TO_LARGE_SERVER',ini_get('upload_max_filesize')));
    }

    //Dateigroesse ueberpruefen Administratoreinstellungen
    if ($_FILES['userfile']['size']>($gPreferences['max_file_upload_size'])*1024)
    {
        $gMessage->show($gL10n->get('DOW_FILE_TO_LARGE', $gPreferences['max_file_upload_size']));
    }

    // Dateinamen ermitteln
    $file_name = $_FILES['userfile']['name'];

    // wenn neuer Name uebergeben wurde, dann diesen nehmen
    if(strlen($_POST['new_name']) > 0)
    {
        $file_name = admFuncGetFilenameWithoutExtension($_POST['new_name']).admFuncGetFilenameExtension($_FILES['userfile']['name']);
    }

    // pruefen, ob der Dateiname gueltig ist
    $ret_code = isValidFileName($file_name, true);

    if($ret_code < 0)
    {
        if($ret_code == -1)
        {
            $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('DOW_CHOOSE_FILE')));
        }
        elseif($ret_code == -2)
        {
            $gMessage->show($gL10n->get('DOW_FILE_NAME_INVALID'));
        }
        elseif($ret_code == -3)
        {
            $gMessage->show($gL10n->get('DOW_FILE_EXTENSION_INVALID'));
        }
    }

    if (file_exists($targetFolder->getCompletePathOfFolder(). '/'.$file_name)){
      $rm_file_exists = true;
      //rm: in case of folder id=148 AktivenListe overwrite is OK
      if($targetFolder->getValue('fol_id') != 148){
        $gMessage->show($gL10n->get('DOW_FILE_EXIST', $file_name));
    }
    }  

    $file_description = $_POST['new_description'];

    // Datei hochladen
    if(move_uploaded_file($_FILES['userfile']['tmp_name'], $targetFolder->getCompletePathOfFolder(). '/'.$file_name))
    {
        //Neue Datei noch in der DB eintragen
        if(!$rm_file_exists){//rm: doppelten Eintrag vermeiden, bei overwrite
        $newFile = new TableFile($gDb);
        $newFile->setValue('fil_fol_id',$targetFolder->getValue('fol_id'));
        $newFile->setValue('fil_name',$file_name);
        $newFile->setValue('fil_description',$file_description);
        $newFile->setValue('fil_locked',$targetFolder->getValue('fol_locked'));
        $newFile->setValue('fil_counter','0');
        $newFile->save();
	}	
		// Benachrichtigungs-Email für neue Einträge        
        if($file_description!='')
        {
            $message = $gL10n->get('DOW_EMAIL_NOTIFICATION_MESSAGE', $gCurrentOrganization->getValue('org_longname'), $file_name. ' ('.$file_description.')', $gCurrentUser->getValue('FIRST_NAME').' '.$gCurrentUser->getValue('LAST_NAME'), date($gPreferences['system_date'], time()));    
        }
        else
        {
            $message = $gL10n->get('DOW_EMAIL_NOTIFICATION_MESSAGE', $gCurrentOrganization->getValue('org_longname'), $file_name, $gCurrentUser->getValue('FIRST_NAME').' '.$gCurrentUser->getValue('LAST_NAME'), date($gPreferences['system_date'], time()));
        }          
        $notification = new Email();
        $notification->adminNotfication($gL10n->get('DOW_EMAIL_NOTIFICATION_TITLE'), $message, $gCurrentUser->getValue('FIRST_NAME').' '.$gCurrentUser->getValue('LAST_NAME'), $gCurrentUser->getValue('EMAIL'));
        
        $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
        $gMessage->show($gL10n->get('DOW_FILE_UPLOADED', $file_name));
    }
    else
    {
        $gMessage->show($gL10n->get('DOW_FILE_UPLOAD_ERROR',$file_name));
    }
}


//Datei loeschen
elseif ($getMode == 2)
{
    if (!$getFileId)
    {
        //Es muss eine FileID uebergeben werden
        //beides ist auch nicht erlaubt
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    if($getFileId > 0)
    {
        try
        {
            // get recordset of current file from databse
            $file = new TableFile($gDb);
            $file->getFileForDownload($getFileId);
        }
        catch(AdmException $e)
        {
        	$e->showText();
        }  

        if ($file->delete())
        {
            // Loeschen erfolgreich -> Rueckgabe fuer XMLHttpRequest
            echo 'done';
        }
    }

    unset($_SESSION['download_request']);
}


// Ordner erstellen
elseif ($getMode == 3)
{

    if ($getFolderId == 0) {
        //FolderId ist zum Anlegen eines Unterordners erforderlich
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    try
    {
        // get recordset of current folder from databse
        $targetFolder = new TableFolder($gDb);
        $targetFolder->getFolderForDownload($getFolderId);
    }
    catch(AdmException $e)
    {
    	$e->showHtml();
    }
    
    $newFolderName = null;

    if (strlen($_POST['new_folder']) > 0)
    {
        $ret_code = isValidFileName($_POST['new_folder']);

        if ($ret_code == 1)
        {
            $newFolderName = $_POST['new_folder'];
        }
        else
        {
            if ($ret_code == -1)
            {
                $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('SYS_NAME')));
            }
            elseif ($ret_code == -2)
            {
                $gMessage->show($gL10n->get('DOW_FOLDER_NAME_INVALID'));
            }
        }
    }
    else
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('SYS_NAME')));
    }

    $newFolderDescription = $_POST['new_description'];


    //Test ob der Ordner schon existiert im Filesystem
    if (file_exists($targetFolder->getCompletePathOfFolder(). '/'.$newFolderName)) 
    {
        $gMessage->show($gL10n->get('DOW_FOLDER_EXISTS', $newFolderName));
    }
    else
    {
        // Ordner erstellen
        $b_return = $targetFolder->createFolder($newFolderName);

        if(strlen($b_return['text']) == 0)
        {
            //Jetzt noch den Ordner der DB hinzufuegen...
            $newFolder = new TableFolder($gDb);

            $newFolder->setValue('fol_fol_id_parent', $targetFolder->getValue('fol_id'));
            $newFolder->setValue('fol_type', 'DOWNLOAD');
            $newFolder->setValue('fol_name', $newFolderName);
            $newFolder->setValue('fol_description', $newFolderDescription);
            $newFolder->setValue('fol_path', $targetFolder->getValue('fol_path'). '/'.$targetFolder->getValue('fol_name'));
            $newFolder->setValue('fol_locked', $targetFolder->getValue('fol_locked'));
            $newFolder->setValue('fol_public', $targetFolder->getValue('fol_public'));
            $newFolder->save();

            //Ordnerberechtigungen des ParentOrdners uebernehmen
            $newFolder->setRolesOnFolder($targetFolder->getRoleArrayOfFolder());
        }
        else
        {
            // der entsprechende Ordner konnte nicht angelegt werden
            $gMessage->setForwardUrl($g_root_path.'/adm_program/modules/downloads/downloads.php');
            $gMessage->show($gL10n->get($b_return['text'], $b_return['path'], '<a href="mailto:'.$gPreferences['email_administrator'].'">', '</a>'));
        }

        $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
        $gMessage->show($gL10n->get('DOW_FOLDER_CREATED', $newFolderName));
    }
}


//Datei / Ordner umbenennen
elseif ($getMode == 4)
{
    if ( (!$getFileId && !$getFolderId) OR ($getFileId && $getFolderId) )
    {
        //Es muss entweder eine FileID ODER eine FolderId uebergeben werden
        //beides ist auch nicht erlaubt
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    if($getFileId > 0)
    {
        try
        {
            // get recordset of current file from databse
            $file = new TableFile($gDb);
            $file->getFileForDownload($getFileId);
        }
        catch(AdmException $e)
        {
        	$e->showHtml();
        }    

        $oldFile = $file->getCompletePathOfFile();
        $newFile = null;

        if(strlen($_POST['new_name']) > 0)
        {
            $ret_code = isValidFileName($_POST['new_name'], true);

            if($ret_code < 0)
            {
                if($ret_code == -1)
                {
                    $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('DOW_NEW_NAME')));
                }
                elseif($ret_code == -2)
                {
                    $gMessage->show($gL10n->get('DOW_FILE_NAME_INVALID'));
                }
                elseif($ret_code == -3)
                {
                    $gMessage->show($gL10n->get('DOW_FILE_EXTENSION_INVALID'));
                }
            }
            else {
                $newFile = $_POST['new_name'].admFuncGetFilenameExtension($oldFile);
            }
        }
        else
        {
            $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('DOW_NEW_NAME')));
        }

        $newDescription = $_POST['new_description'];

        //Test ob die Datei schon existiert im Filesystem
        if ($newFile != $file->getValue('fil_name')
         && file_exists(SERVER_PATH. $file->getValue('fol_path'). '/'. $file->getValue('fol_name'). '/'.$newFile))
        {
            $gMessage->show($gL10n->get('DOW_FILE_EXIST', $newFile));
        }
        else
        {
            $oldName = $file->getValue('fil_name');

            // Datei umbenennen im Filesystem und in der Datenbank
            if (rename($oldFile,SERVER_PATH. $file->getValue('fol_path'). '/'. $file->getValue('fol_name'). '/'.$newFile))
            {
                $file->setValue('fil_name', $newFile);
                $file->setValue('fil_description', $newDescription);
                $file->save();

                $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
                $gMessage->show($gL10n->get('DOW_FILE_RENAME',$oldName));
            }
            else {
                $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
                $gMessage->show($gL10n->get('DOW_FILE_RENAME_ERROR',$oldName));
            }
        }

    }
    else if ($getFolderId > 0)
    {
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

        $oldFolder = $folder->getCompletePathOfFolder();
        $newFolder = null;

        if (strlen($_POST['new_name']) > 0)
        {
            $ret_code = isValidFileName($_POST['new_name']);

            if ($ret_code == 1)
            {
                $newFolder = $_POST['new_name'];
            }
            else
            {
                if ($ret_code == -1)
                {
                    $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('DOW_NEW_NAME')));
                }
                elseif ($ret_code == -2)
                {
                    $gMessage->show($gL10n->get('DOW_FOLDER_NAME_INVALID'));
                }
            }
        }
        else
        {
            $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('DOW_NEW_NAME')));
        }

        $newDescription = $_POST['new_description'];

        //Test ob der Ordner schon existiert im Filesystem
        if ($newFolder != $folder->getValue('fol_name')
         && file_exists(SERVER_PATH. $folder->getValue('fol_path'). '/'.$newFolder))
        {
            $gMessage->show($gL10n->get('DOW_FOLDER_EXISTS', $newFolder));
        }
        else
        {
            $oldName = $folder->getValue('fol_name');

            // Ordner umbenennen im Filesystem und in der Datenbank
            if (rename($oldFolder,SERVER_PATH. $folder->getValue('fol_path'). '/'.$newFolder))
            {
                $folder->setValue('fol_description', $newDescription);
                $folder->rename($newFolder, $folder->getValue('fol_path'));

                $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
                $gMessage->show($gL10n->get('DOW_FOLDER_RENAME',$oldName));
            }
            else {
                $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
                $gMessage->show($gL10n->get('DOW_FOLDER_RENAME_ERROR',$oldName));
            }
        }

    }
}


//Folder loeschen
elseif ($getMode == 5)
{
    if (!$getFolderId)
    {
        //Es muss eine FolderId uebergeben werden
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }
    else if ($getFolderId > 0)
    {
        try
        {
            // get recordset of current folder from databse
            $folder = new TableFolder($gDb);
            $folder->getFolderForDownload($getFolderId);
        }
        catch(AdmException $e)
        {
        	$e->showText();
        }

        if ($folder->delete())
        {
            // Loeschen erfolgreich -> Rueckgabe fuer XMLHttpRequest
            echo 'done';
        }
    }

    unset($_SESSION['download_request']);
}


//Datei / Ordner zur DB hinzufeuegen
elseif ($getMode == 6)
{
    if ($getFolderId == 0) 
	{
        //FolderId ist zum hinzufuegen erforderlich
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    if(strlen($getName) > 0)
    {
        $ret_code = isValidFileName(urldecode($getName), true);
        if($ret_code == 1)
        {
            $getName = urldecode($getName);
        }
        else
        {
            if($ret_code == -2)
            {
                $gMessage->show($gL10n->get('DOW_FILE_NAME_INVALID'));
            }
            elseif($ret_code == -3)
            {
                $gMessage->show($gL10n->get('DOW_FILE_EXTENSION_INVALID'));
            }
        }
    }
    else
    {
        //name ist zum hinzufuegen erforderlich
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    try
    {
        // get recordset of current folder from databse
        $targetFolder = new TableFolder($gDb);
        $targetFolder->getFolderForDownload($getFolderId);
    }
    catch(AdmException $e)
    {
    	$e->showHtml();
    }

    //Pruefen ob das neue Element eine Datei order ein Ordner ist.
    if (is_file($targetFolder->getCompletePathOfFolder(). '/'. $getName)) {
        //Datei hinzufuegen
        $newFile = new TableFile($gDb);
        $newFile->setValue('fil_fol_id',$targetFolder->getValue('fol_id'));
        $newFile->setValue('fil_name',$getName);
        $newFile->setValue('fil_locked',$targetFolder->getValue('fol_locked'));
        $newFile->setValue('fil_counter','0');
        $newFile->save();

        //Zurueck zur letzten Seite
        $gNavigation->addUrl(CURRENT_URL);
        $location = 'Location: '.$g_root_path.'/adm_program/system/back.php';
        header($location);
        exit();
    }
    else if (is_dir($targetFolder->getCompletePathOfFolder(). '/'. $getName)) {

        //Ordner der DB hinzufuegen
        $newFolder = new TableFolder($gDb);
        $newFolder->setValue('fol_fol_id_parent', $targetFolder->getValue('fol_id'));
        $newFolder->setValue('fol_type', 'DOWNLOAD');
        $newFolder->setValue('fol_name', $getName);
        $newFolder->setValue('fol_path', $targetFolder->getValue('fol_path'). '/'.$targetFolder->getValue('fol_name'));
        $newFolder->setValue('fol_locked', $targetFolder->getValue('fol_locked'));
        $newFolder->setValue('fol_public', $targetFolder->getValue('fol_public'));
        $newFolder->save();

        //Ordnerberechtigungen des ParentOrdners uebernehmen
        $newFolder->setRolesOnFolder($targetFolder->getRoleArrayOfFolder());

        //Zurueck zur letzten Seite
        $gNavigation->addUrl(CURRENT_URL);
        $location = 'Location: '.$g_root_path.'/adm_program/system/back.php';
        header($location);
        exit();
   }

}

//Berechtigungen fuer einen Ordner speichern
elseif ($getMode == 7)
{
    if ($getFolderId == 0) {
        //FolderId ist zum hinzufuegen erforderlich
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    try
    {
        // get recordset of current folder from databse
        $targetFolder = new TableFolder($gDb);
        $targetFolder->getFolderForDownload($getFolderId);
    }
    catch(AdmException $e)
    {
    	$e->showHtml();
    }

    //Formularinhalt aufbereiten
    if(isset($_POST['fol_public']) == false || $_POST['fol_public'] != 0)
    {
        $publicFlag = 1;
    }
    else {
        $publicFlag = 0;
    }

    //setze schon einmal das Public_Flag
    $targetFolder->editPublicFlagOnFolder($publicFlag);

    $rolesArray = null;

    //Nur wenn der Ordner oeffentlich nicht zugaenglich ist
    //werden die Rollenbrechtigungen gespeichert.
    //Ansonsten wird ein leeres Rollenset gespeichert...
    if ($publicFlag == 0) {

        //Rollenberechtigungen aufbereiten
        if(array_key_exists('AllowedRoles', $_POST))
        {
            $sentAllowedRoles = $_POST['AllowedRoles'];

            //fuege alle neuen Rollen hinzu
            foreach ($sentAllowedRoles as $newRole)
            {

                $rolesArray[] = array('rol_id'        => $newRole,
                                      'rol_name'      => '');

            }
        }
    }

    //jetzt noch die Rollenberechtigungen in die DB schreiben
    $targetFolder->setRolesOnFolder($rolesArray);


    $targetFolder->save();

    $gMessage->setForwardUrl($g_root_path.'/adm_program/system/back.php');
    $gMessage->show($gL10n->get('SYS_SAVE_DATA'));
}


?>