<?php
/******************************************************************************
 * Check email form and send email
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * usr_id  - Send email to this user
 * subject - set email subject independent of the form
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/classes/email.php');
require_once('../../system/classes/table_roles.php');

//Stop if mail module is disabled
if($gPreferences['enable_mail_module'] != 1)
{
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}

// Initialize and check the parameters
$getUserId  = admFuncVariableIsValid($_GET, 'usr_id', 'numeric', 0);
$postRoleId = admFuncVariableIsValid($_POST, 'rol_id', 'numeric', 0);

// Check form values
$postSubject     = admFuncVariableIsValid($_POST, 'subject', 'string', '');
$postName        = admFuncVariableIsValid($_POST, 'name', 'string', '');
$postFrom        = admFuncVariableIsValid($_POST, 'mailfrom', 'string', '');
$postTo          = admFuncVariableIsValid($_POST, 'mailto', 'string', '');
$postBody        = admFuncVariableIsValid($_POST, 'mail_body', 'html', '');
$postDeliveryConfirmation  = admFuncVariableIsValid($_POST, 'delivery_confirmation', 'boolean', 0);
$postCaptcha     = admFuncVariableIsValid($_POST, 'captcha', 'string');
$postShowMembers = admFuncVariableIsValid($_POST, 'show_members', 'numeric', 0);

// show option to send a copy to your email address only for registered users because of spam abuse
if($gValidLogin)
{
    $postCarbonCopy = admFuncVariableIsValid($_POST, 'carbon_copy', 'boolean', 0);
}
else
{
    $postCarbonCopy = 0;
}

//if user is logged in then show sender name and email
if ($gCurrentUser->getValue('usr_id') > 0)
{
    $postName = $gCurrentUser->getValue('FIRST_NAME'). ' '. $gCurrentUser->getValue('LAST_NAME');
    $postFrom = $gCurrentUser->getValue('EMAIL');
}
//if not User is not able to ask for delivery confirmation 
if(!($gCurrentUser->getValue('usr_id')>0 && $gPreferences['mail_delivery_confirmation']==2) && $gPreferences['mail_delivery_confirmation']!=1)
{
    $postDeliveryConfirmation = 0;
}

//put values into SESSION
$_SESSION['mail_request'] = array(
    'name'          => $postName,
    'mailfrom'      => $postFrom,
    'subject'       => $postSubject,
    'mail_body'     => $postBody,
    'rol_id'        => $postRoleId,
    'carbon_copy'   => $postCarbonCopy,
    'delivery_confirmation' => $postDeliveryConfirmation,
    'show_members' => $postShowMembers);

// if User ID is delivered
if ($getUserId > 0)
{
    // Falls eine Usr_id uebergeben wurde, muss geprueft werden ob der User ueberhaupt
    // auf diese zugreifen darf oder ob die UsrId ueberhaupt eine gueltige Mailadresse hat...
    if (!$gValidLogin)
    {
        //in ausgeloggtem Zustand duerfen nie direkt usr_ids uebergeben werden...
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }

    //usr_id wurde uebergeben, dann Kontaktdaten des Users aus der DB fischen
    $user = new User($gDb, $gProfileFields, $getUserId);

    // darf auf die User-Id zugegriffen werden    
    if(($gCurrentUser->editUsers() == false && isMember($user->getValue('usr_id')) == false)|| strlen($user->getValue('usr_id')) == 0 )
    {
        $gMessage->show($gL10n->get('SYS_USER_ID_NOT_FOUND'));
    }

    // besitzt der User eine gueltige E-Mail-Adresse
    if (!strValidCharacters($user->getValue('EMAIL'), 'email'))
    {
        $gMessage->show($gL10n->get('SYS_USER_NO_EMAIL', $user->getValue('FIRST_NAME').' '.$user->getValue('LAST_NAME')));
    }
    
    $postTo = $user->getValue('EMAIL');
}
elseif ($postRoleId > 0)
{
    // wird eine bestimmte Rolle aufgerufen, dann pruefen, ob die Rechte dazu vorhanden sind
    $sql = 'SELECT rol_mail_this_role, rol_name, rol_id 
              FROM '. TBL_ROLES. ', '. TBL_CATEGORIES. '
             WHERE rol_cat_id    = cat_id
               AND (  cat_org_id = '. $gCurrentOrganization->getValue('org_id').'
                   OR cat_org_id IS NULL)
               AND rol_id = '.$postRoleId;
    $result = $gDb->query($sql);
    $row    = $gDb->fetch_array($result);

    // Ausgeloggte duerfen nur an Rollen mit dem Flag "alle Besucher der Seite" Mails schreiben
    // Eingeloggte duerfen nur an Rollen Mails schreiben, zu denen sie berechtigt sind
    // Rollen muessen zur aktuellen Organisation gehoeren
    if(($gValidLogin == false && $row['rol_mail_this_role'] != 3)
    || ($gValidLogin == true  && $gCurrentUser->mailRole($row['rol_id']) == false)
    || $row['rol_id']  == null)
    {
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }
}

// aktuelle Seite im NaviObjekt speichern. Dann kann in der Vorgaengerseite geprueft werden, ob das
// Formular mit den in der Session gespeicherten Werten ausgefuellt werden soll...
$gNavigation->addUrl(CURRENT_URL);

// Falls Attachmentgroesse die max_post_size aus der php.ini uebertrifft, ist $_POST komplett leer.
// Deswegen muss dies ueberprueft werden...
if (empty($_POST))
{
    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

//Erst mal ein neues Emailobjekt erstellen...
$email = new Email();

// und ein Dummy Rollenobjekt dazu
$role = new TableRoles($gDb);

//Nun der Mail die Absenderangaben,den Betreff und das Attachment hinzufuegen...
if(strlen($postName) == 0)
{
    $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('SYS_NAME')));
}

//Absenderangaben checken falls der User eingeloggt ist, damit ein paar schlaue User nicht einfach die Felder aendern koennen...
if ( $gValidLogin 
&& (  $postFrom != $gCurrentUser->getValue('EMAIL') 
   || $postName != $gCurrentUser->getValue('FIRST_NAME').' '.$gCurrentUser->getValue('LAST_NAME')) )
{
    $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
}

//Absenderangaben setzen
if ($email->setSender($postFrom,$postName))
{
    //Betreff setzen
    if ($email->setSubject($postSubject))
    {
        //Pruefen ob moeglicher Weise ein Attachment vorliegt
        if (isset($_FILES['userfile']))
        {
            //noch mal schnell pruefen ob der User wirklich eingelogt ist...
            if (!$gValidLogin)
            {
                $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
            }
            $attachment_size = 0;
            // Nun jedes Attachment
            for($act_attachment_nr = 0; isset($_FILES['userfile']['name'][$act_attachment_nr]) == true; $act_attachment_nr++)
            {
                //Pruefen ob ein Fehler beim Upload vorliegt
                if (($_FILES['userfile']['error'][$act_attachment_nr] != 0) &&  ($_FILES['userfile']['error'][$act_attachment_nr] != 4))
                {
                    $gMessage->show($gL10n->get('MAI_ATTACHMENT_TO_LARGE'));
                }
                //Wenn ein Attachment vorliegt dieses der Mail hinzufuegen
                if ($_FILES['userfile']['error'][$act_attachment_nr] == 0)
                {
                    // pruefen, ob die Anhanggroesse groesser als die zulaessige Groesse ist
                    $attachment_size = $attachment_size + $_FILES['userfile']['size'][$act_attachment_nr];
                    if($attachment_size > $email->getMaxAttachementSize("b"))
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
                        $email->AddAttachment($_FILES['userfile']['tmp_name'][$act_attachment_nr], $_FILES['userfile']['name'][$act_attachment_nr], $encoding = 'base64', $_FILES['userfile']['type'][$act_attachment_nr]);
                    }
                    catch (phpmailerException $e)
                    {
                        $gMessage->show($e->errorMessage());
                    }                  
                }
            }
        }
    }
    else
    {
        $gMessage->show($gL10n->get('SYS_FIELD_EMPTY', $gL10n->get('MAI_SUBJECT')));
    }
}
else
{
    $gMessage->show($gL10n->get('SYS_EMAIL_INVALID', $gL10n->get('SYS_EMAIL')));
}

if ($getUserId == 0)
{
    // wurde kein Benutzer uebergeben, dann muss Rolle uebergeben werden
    if ($postRoleId == 0)
    {
        $gMessage->show($gL10n->get('MAI_CHOOSE_ROLE'));
    }
    
    $role->readDataById($postRoleId);

    // Falls der User eingeloggt ist checken ob er das recht hat der Rolle eine Mail zu schicken
    if ($gValidLogin == true && !$gCurrentUser->mailRole($postRoleId))
    {
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }
    // Falls der User nicht eingeloggt ist, muss der Wert 3 sein
    if ($gValidLogin == false && $role->getValue('rol_mail_this_role') != 3)
    {
        $gMessage->show($gL10n->get('SYS_INVALID_PAGE_VIEW'));
    }
}

// Falls der User nicht eingeloggt ist, aber ein Captcha geschaltet ist,
// muss natuerlich der Code ueberprueft werden
if (!$gValidLogin && $gPreferences['enable_mail_captcha'] == 1)
{
    if ( !isset($_SESSION['captchacode']) || admStrToUpper($_SESSION['captchacode']) != admStrToUpper($postCaptcha) )
    {
        if($gPreferences['captcha_type']=='pic') {$gMessage->show($gL10n->get('SYS_CAPTCHA_CODE_INVALID'));}
        else if($gPreferences['captcha_type']=='calc') {$gMessage->show($gL10n->get('SYS_CAPTCHA_CALC_CODE_INVALID'));}
    }
}

// if possible send html mail
if($gValidLogin == true && $gPreferences['mail_html_registered_users'] == 1)
{
    $email->sendDataAsHtml();
}

// Falls eine Kopie benoetigt wird, das entsprechende Flag im Mailobjekt setzen
if (isset($postCarbonCopy) && $postCarbonCopy == true)
{
    $email->setCopyToSenderFlag();

    //Falls der User eingeloggt ist, werden die Empfaenger der Mail in der Kopie aufgelistet
    if ($gValidLogin)
    {
        $email->setListRecipientsFlag();
    }
}

//Nun die Empfaenger zusammensuchen und an das Mailobjekt uebergeben
if ($getUserId > 0)
{
    //den gefundenen User dem Mailobjekt hinzufuegen...
    $email->addRecipient($user->getValue('EMAIL'), $user->getValue('FIRST_NAME').' '.$user->getValue('LAST_NAME'));
}
else
{
    // Rolle wurde uebergeben, dann alle Mitglieder auslesen (ausser dem Sender selber)
    // je nach Einstellung mit oder nur Ehemalige
    
    if($postShowMembers == 1)
    {
        // only former members
        $sqlConditions = ' AND mem_end < \''.DATE_NOW.'\' ';
    }
    elseif($postShowMembers == 2)
    {
        // former members and active members
        $sqlConditions = ' AND mem_begin < \''.DATE_NOW.'\' ';
    }
    else
    {
        // only active members
        $sqlConditions = ' AND mem_begin  <= \''.DATE_NOW.'\'
                           AND mem_end     > \''.DATE_NOW.'\' ';
    }
    
    $sql   = 'SELECT first_name.usd_value as first_name, last_name.usd_value as last_name, 
                     email.usd_value as email, rol_name
                FROM '. TBL_ROLES. ', '. TBL_CATEGORIES. ', '. TBL_MEMBERS. ', '. TBL_USERS. '
                JOIN '. TBL_USER_DATA. ' as email
                  ON email.usd_usr_id = usr_id
                 AND LENGTH(email.usd_value) > 0
                JOIN '.TBL_USER_FIELDS.' as field
                  ON field.usf_id = email.usd_usf_id
                 AND field.usf_type = \'EMAIL\'
                LEFT JOIN '. TBL_USER_DATA. ' as last_name
                  ON last_name.usd_usr_id = usr_id
                 AND last_name.usd_usf_id = '. $gProfileFields->getProperty('LAST_NAME', 'usf_id'). '
                LEFT JOIN '. TBL_USER_DATA. ' as first_name
                  ON first_name.usd_usr_id = usr_id
                 AND first_name.usd_usf_id = '. $gProfileFields->getProperty('FIRST_NAME', 'usf_id'). '
               WHERE rol_id      = '.$postRoleId.'
                 AND rol_cat_id  = cat_id
                 AND (  cat_org_id  = '. $gCurrentOrganization->getValue('org_id'). '
                     OR cat_org_id IS NULL )
                 AND mem_rol_id  = rol_id
                 AND mem_usr_id  = usr_id
                 AND usr_valid   = 1 '.
                     $sqlConditions;

    // Wenn der User eingeloggt ist, wird die UserID im Statement ausgeschlossen, 
    //damit er die Mail nicht an sich selber schickt.
    if ($gValidLogin)
    {
        $sql =$sql. ' AND usr_id <> '. $gCurrentUser->getValue('usr_id');
    } 
    $result = $gDb->query($sql);

    if($gDb->num_rows($result) > 0)
    {
        if($gPreferences['mail_sender_into_to'] == 1)
        {
            // always fill recipient if preference is set to prevent problems with provider
            $email->addRecipient($postFrom,$postName);
        }
        
        // all role members will be attached as BCC
        while ($row = $gDb->fetch_object($result))
        {
            $email->addBlindCopy($row->email, $row->first_name.' '.$row->last_name);
        }
    }
    else
    {
        // Falls in der Rolle kein User mit gueltiger Mailadresse oder die Rolle gar nicht in der Orga
        // existiert, muss zumindest eine brauchbare Fehlermeldung präsentiert werden...
        $gMessage->show($gL10n->get('MAI_ROLE_NO_EMAILS'));
    }

}

// Falls eine Lesebestätigung angefordert wurde
if($postDeliveryConfirmation == 1)
{
    $email->ConfirmReadingTo = $gCurrentUser->getValue('EMAIL');
}

// prepare body of email with note of sender and homepage
$email->setSenderInText($postName, $postFrom, $role->getValue('rol_name'), $postShowMembers);

//set Text
$email->setText($postBody);

//Nun kann die Mail endgueltig versendet werden...
$sendMailResult = $email->sendEmail();
if ($sendMailResult === TRUE)
{
    // Der CaptchaCode wird bei erfolgreichem Mailversand aus der Session geloescht
    if (isset($_SESSION['captchacode']))
    {
        unset($_SESSION['captchacode']);
    }

    // Bei erfolgreichem Versenden wird aus dem NaviObjekt die am Anfang hinzugefuegte URL wieder geloescht...
    $gNavigation->deleteLastUrl();
    // dann auch noch die mail.php entfernen
    $gNavigation->deleteLastUrl();

    // Meldung ueber erfolgreichen Versand und danach weiterleiten
    if($gNavigation->count() > 0)
    {
        $gMessage->setForwardUrl($gNavigation->getUrl());
    }
    else
    {
        $gMessage->setForwardUrl($gHomepage);
    }
    
    if ($role->getValue('rol_id') > 0)
    {
        $gMessage->show($gL10n->get('SYS_EMAIL_SEND', $gL10n->get('MAI_TO_ROLE', $role->getValue('rol_name'))));
    }
    else
    {
        $gMessage->show($gL10n->get('SYS_EMAIL_SEND', $postTo));
    }
}
else
{
    if ($role->getValue('rol_id') > 0)
    {
        $gMessage->show($sendMailResult.'<br />'.$gL10n->get('SYS_EMAIL_NOT_SEND', $gL10n->get('MAI_TO_ROLE', $role->getValue('rol_name')), $sendMailResult));
    }
    else
    {
        $gMessage->show($sendMailResult.'<br />'.$gL10n->get('SYS_EMAIL_NOT_SEND', $postTo, $sendMailResult));
    }
}
?>