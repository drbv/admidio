<?php
/******************************************************************************
 * Profile Wertungen Versenden
 *
 * Copyright    : (c) 2017 DRBV Webteam
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

// Initialize and check the parameters
$getFormId = admFuncVariableIsValid($_GET, 'form_id', 'numeric', 0);
  
// Konfiguration der Form-Mail #####################################################
// An welche Adresse sollen die Mails gesendet werden?
$strEmpfaenger0  = 'webmaster1@drbv.de'; // to check usage
  
if ($_POST['Empf1']){
  $strEmpfaenger1  = $_POST['Empf1'];
} 
if ($_POST['Empf2']){
  $strEmpfaenger2  = $_POST['Empf2'];
}
if ($_POST['Bemerkungen'] != '?'){
  $strBemerkungen  = $_POST['Bemerkungen'];
}
  
if ($strEmpfaenger1 == '' && $strEmpfaenger2 == ''){
  $gMessage->show("Es wurde kein Empfänger angegeben!");
}
  
// Welche Adresse soll als Absender angegeben werden?
if((strlen($_SESSION['profile_vmail']) > 0 )){  
  $strFrom    = $_SESSION['profile_vmail'];
  $strFromTxt = $_SESSION['profile_vname'];
} else {
  $strFrom    = 'info@drbv';
  $strFromTxt = 'DRBV e.V';
}  

// Welchen Betreff sollen die Mails erhalten?
$strSubject   = 'DRBV AktivenPORTAL Wertungsergebnis';

// Zu welcher Seite soll weitergeleitet werden?
$strReturnhtml = $g_root_path.'/adm_program/modules/profile/profile_wrtg.php?user_id='.$_SESSION['profile_usrid'].'&tnrsel='.$_SESSION['profile_tnr'];
// Ende Konfiguration ###############################################################
  
if (empty($_POST)){
  $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}
  
//Neue Emailobjekte erstellen...
//Mail an Empfänger 
$mail2share   = new Email();

$htmlmailtext  = '';  
$htmlmailtext .= '
  <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
  <body>';
$htmlmailtext .= $_SESSION['profile_share'];
$htmlmailtext .= '<br><b><i>Bemerkungen</i></b><br>';
$htmlmailtext .= $strBemerkungen;  
$htmlmailtext .= '</body></html>';
    
//Absenderangaben setzen
$mail2share->setSender($strFrom,$strFromTxt);

//Betreff setzen
$mail2share->setSubject($strSubject);
  
//Nun den Empfaenger an das Mailobjekt uebergeben
//$mail2share->addBlindCopy($strEmpfaenger0);// nur zum initialen check
$mail2share->addRecipient($strEmpfaenger1);
$mail2share->addRecipient($strEmpfaenger2);

//set Text
$mail2share->setText($htmlmailtext);
$mail2share->sendDataAsHtml();
  
//Nun kann die Mail endgueltig versendet werden...
$sendMailShare = $mail2share->sendEmail();

if ($sendMailShare === FALSE){
  $gMessage->show("Die Mail an den/die Empfänger konnte nicht versendet werden!");
}

header("Location: $strReturnhtml");
?>
