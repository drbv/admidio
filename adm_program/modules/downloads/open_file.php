<?php
/******************************************************************************
 * Anzeige von Textdateien Script
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * file_id      :  Die Id der Datei, welche heruntergeladen werden soll
 *
 *****************************************************************************/

require('../../system/common.php');
require('../../system/classes/table_file.php');

// Initialize and check the parameters
$getFileId = admFuncVariableIsValid($_GET, 'file_id', 'numeric', null, true);

//pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_download_module'] != 1)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}
//nur von eigentlicher OragHompage erreichbar
if($gCurrentOrganization->getValue('org_shortname')!= $g_organization)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_ACCESS_FROM_HOMEPAGE_ONLY', $gHomepage));
}

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

//kompletten Pfad der Datei holen
$completePath = $file->getCompletePathOfFile();


//pruefen ob File ueberhaupt physikalisch existiert
if (!file_exists($completePath))
{
    $gMessage->show($gL10n->get('SYS_FILE_NOT_EXIST'));
}

//Dateigroese ermitteln
$fileSize   = filesize($completePath);
$filename   = $file->getValue('fil_name');

// for IE the filename must have special chars in hexadecimal 
if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
{
    $filename = urlencode($filename);
}
 
echo '  
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
<head>
  <!-- (c) 2004 - 2013 The Admidio Team - http://www.admidio.org -->
  <!-- (c) 2014 Adapted by DRBV Webteam to a online version of the couples competition book -->
  
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  
  <title>Deutscher Rock&#039;n&#039;Roll und Boogie-Woogie Verband e.V.</title>    
  
  <link rel="stylesheet" type="text/css" href="http://www.drbv.de/test/admidio/adm_themes/classic/css/system.css" />
  <link rel="stylesheet" href="http://www.drbv.de/test/admidio/adm_themes/classic/css/colorbox.css" type="text/css" media="screen" />

  <link rel="shortcut icon" type="image/x-icon" href="http://www.drbv.de/test/admidio/adm_themes/classic/icons/favicon.png" />
  <link rel="apple-touch-icon" href="http://www.drbv.de/test/admidio/adm_themes/classic/icons/webclipicon.png" /></head>
<body style="text-align:left">';
$fp = fopen($completePath,"r"); 
while(!feof($fp)) { 
   echo utf8_encode(fgets($fp)) . "<br />";
}  
fclose($fp);
echo'  
</body>
</html>';  
  
?>