<?php
/******************************************************************************
 * Formulare anzeigen
 *
 * Copyright    : (c) 2016 DRBV Webteam
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
//$strEmpfaenger1  = 'webmaster@drbv.de';
$strEmpfaenger1  = 'ruediger.menken@drbv.de';

$strEmpfaenger2  = 'vizepraesident@drbv.de';
  
if ($_POST['EMail']){
  //$strEmpfaenger3  = $user->getValue('EMAIL');
  $strEmpfaenger3  = $_POST['EMail'];
} else {
  //$strEmpfaenger3  = 'webmaster@drbv.de';
  $strEmpfaenger3  = 'ruediger.menken@drbv.de';
}
  
// Welche Adresse soll als Absender angegeben werden?
$strFrom         = 'info@drbv.de';

// Welchen Betreff sollen die Mails erhalten?
$strSubject      = 'DRBV AktivenPORTAL Formulareingang';

// Welchen Betreff sollen die Bestätigungs-Mails erhalten?
$strSubjectBest       = 'DRBV AktivenPORTAL Formularbestätigung';
$strSubjectBestNomail = 'DRBV AktivenPORTAL Formularbestätigung - KEINE Vereinsmail gefunden!';//gibt nur 2 Vereine ohne!

// Welchen Text sollen die Bestätigungs-Mails erhalten?
$strMailtextBest  = "Danke für ihre Formulareingabe.\n";
$strMailtextBest .= "Weitere Informationen erhalten sie in Kürze per Mail.\n\n";
$strMailtextBest .= "Deutscher Rock'n'Roll & Boogie-Woogie Verband e.V.\n\n\n\n";
$strMailtextBest .= "Folgende Daten haben wir erhalten:\n\n";

// Zu welcher Seite soll als "Danke-Seite" weitergeleitet werden?
$strReturnhtml = 'https://drbv.de/adm/adm_program/modules/forms/formsok.php';

// Welche(s) Zeichen soll(en) zwischen dem Feldnamen und dem angegebenen Wert stehen?
$strDelimiter1   = "\t";
$strDelimiter2   = "\t\t";
$strDelimiter3   = "\t\t\t";
// Ende Konfiguration ###############################################################
  
if (empty($_POST)) 
{
  $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}  
//Neue Emailobjekte erstellen...
//Mail an Webmaster mit allen Feldern
$mail2webmaster = new Email();

//Mail an Geschäftsstelle 
$mail2gs        = new Email();

//Return-Mail an den Formularersteller 
$mail2return    = new Email();

//Return-Mail an den Verein
$mail2verein    = new Email();

$strMailtext            = "";
$StartbuchInvalidString = "";
$StartbuchLaufString    = "";
$StartbuchFolgString    = "";
  
while(list($strName,$value) = each($_POST)) 
{
  if(is_array($value))
  {
    foreach($value as $value_array)
    {
      if($strName == 'StartbuchInvalid'){
        $StartbuchInvalidString.= $strDelimiter1.$value_array."\n";
      }
      if($strName == 'StartbuchLauf'){
        $StartbuchLaufString.= $strDelimiter1.$value_array."\n";
      }
      if($strName == 'StartbuchFolg'){
        $StartbuchFolgString.= $strDelimiter1.$value_array."\n";
      }
      $strMailtext .= $strName.$strDelimiter1.$value_array."\n";
    }
  }
  else
  {
    $strMailtext .= $strName.$strDelimiter1.$value."\n";
  }
}    
  
if ($getFormId=='16') {
  require(SERVER_PATH. '/adm_program/modules/forms/f16_guetesiegel.mail.php');
}   
if(get_magic_quotes_gpc())
{
  $strMailtext = stripslashes($strMailtext);
}

$strMailtext .= "\n\n"."-------------------- Formatierter Text --------------------"."\n\n";
$strMailtext .= $strMailtextMod;

$strMailtextFeedback = $strMailtextFb;

if (isset($_POST['EMail'])) {
  $strReturnMail = "";
  // Zeilenumbruchszeichen enthalten? Spamverdacht!
  if (strpos($_POST['EMail'], "\r") !== false or
      strpos($_POST['EMail'], "\n") !== false)
      die('Abbruch wegen Spamversuch.'); // [*]
  $strReturnMail = $_POST['EMail'];
} else {
  $strReturnMail = ''; 
}
  
//Absenderangaben setzen
$mail2webmaster->setSender($strFrom,'DRBV e.V.');
$mail2gs->setSender($strFrom,'DRBV e.V.');
$mail2return->setSender($strFrom,'DRBV e.V.');
$mail2verein->setSender($strFrom,'DRBV e.V.');

//Betreff setzen
$mail2webmaster->setSubject($strSubject);
$mail2gs->setSubject($strSubject);
$mail2return->setSubject($strSubjectBest);

//if ($user->getValue('EMAIL')){
if ($_POST['EMail']){
  $mail2verein->setSubject($strSubjectBest);
} else {
  $mail2verein->setSubject($strSubjectBest);
}
  
//Pruefen ob moeglicher Weise ein Attachment vorliegt
if (isset($_FILES['userfile']))
{
  $attachment_size = 0;
  // Nun jedes Attachment
  for($act_attachment_nr = 0; isset($_FILES['userfile']['name'][$act_attachment_nr]) == true; $act_attachment_nr++)
  {
    //Pruefen ob ein Fehler beim Upload vorliegt
    if (($_FILES['userfile']['error'][$act_attachment_nr] != 0) && ($_FILES['userfile']['error'][$act_attachment_nr] != 4))
    {
      $gMessage->show($gL10n->get('MAI_ATTACHMENT_TO_LARGE'));
    }
    //Wenn ein Attachment vorliegt dieses der Mail hinzufuegen
    if ($_FILES['userfile']['error'][$act_attachment_nr] == 0)
    {
      // pruefen, ob die Anhanggroesse groesser als die zulaessige Groesse ist
      $attachment_size = $attachment_size + $_FILES['userfile']['size'][$act_attachment_nr];
      if($attachment_size > $mail2webmaster->getMaxAttachementSize("b"))
      {
          $gMessage->show($gL10n->get('MAI_ATTACHMENT_TO_LARGE'));
      }    
      if($attachment_size > $mail2gs->getMaxAttachementSize("b"))
      {
          $gMessage->show($gL10n->get('MAI_ATTACHMENT_TO_LARGE'));
      }    
      //Falls der Dateityp nicht bestimmt ist auf Standard setzen
      if (strlen($_FILES['userfile']['type'][$act_attachment_nr]) <= 0)
      {
          $_FILES['userfile']['type'][$act_attachment_nr] = 'application/octet-stream';            
      }    
      //Datei anhängen
      try
      {
         $mail2webmaster->AddAttachment($_FILES['userfile']['tmp_name'][$act_attachment_nr], $_FILES['userfile']['name'][$act_attachment_nr], $encoding = 'base64', $_FILES['userfile']['type'][$act_attachment_nr]);
         $mail2gs->AddAttachment($_FILES['userfile']['tmp_name'][$act_attachment_nr], $_FILES['userfile']['name'][$act_attachment_nr], $encoding = 'base64', $_FILES['userfile']['type'][$act_attachment_nr]);
      }
      catch (phpmailerException $e)
      {
         $gMessage->show($e->errorMessage());
      }      
    }
  }
}

//Nun den Empfaenger an das Mailobjekt uebergeben
//$mail2webmaster->addRecipient($strEmpfaenger1, 'NAME');
$mail2webmaster->addRecipient($strEmpfaenger1);
$mail2gs->addRecipient($strEmpfaenger2);
$mail2return->addRecipient($strReturnMail);
$mail2verein->addRecipient($strEmpfaenger3);

//set Text
$mail2webmaster->setText($strMailtext);
$mail2gs->setText($strMailtextMod);
$mail2return->setText($strMailtextBest.$strMailtextFeedback);
$mail2verein->setText($strMailtextBest.$strMailtextFeedback);

//Nun kann die Mail endgueltig versendet werden...
$sendMailWebmaster = $mail2webmaster->sendEmail();
$sendMailGS        = $mail2gs->sendEmail();
$sendMailReturn    = $mail2return->sendEmail();
$sendMailVerein    = $mail2verein->sendEmail();
  
if ($sendMailWebmaster === FALSE)
{
  die("Die Mail an den Webmaster konnte nicht versendet werden.");
}
if ($sendMailGS === FALSE)
{
  die("Die Mail an die Geschäftsstelle konnte nicht versendet werden.");
}
if ($sendMailReturn === FALSE)
{
  die("Die Returnmail konnte nicht versendet werden.");
}
if ($sendMailVerein === FALSE)
{
  die("Die Mail an den Verein konnte nicht versendet werden.");
}

header("Location: $strReturnhtml");
?>
