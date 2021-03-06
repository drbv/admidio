<?php
/******************************************************************************
 * Search for existing user names and show users with similar names
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * new_user_id : ID of user who should be assigned
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/login_valid.php');

// Initialize and check the parameters
$getNewUserId = admFuncVariableIsValid($_GET, 'new_user_id', 'numeric', null, true);

// nur Webmaster duerfen User zuordnen, ansonsten Seite verlassen
if($gCurrentUser->approveUsers() == false)
{
   $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// pruefen, ob Modul aufgerufen werden darf
if($gPreferences['registration_mode'] == 0)
{
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}

// create user object for new user
$new_user = new User($gDb, $gProfileFields, $getNewUserId);

// search for users with similar names (SQL function SOUNDEX only available in MySQL)
if($gPreferences['system_search_similar'] == 1 && $gDbType == 'mysql')
{
    $sql_similar_name = 
    '(  (   SUBSTRING(SOUNDEX(last_name.usd_value),  1, 4) LIKE SUBSTRING(SOUNDEX(\''. $new_user->getValue('LAST_NAME').'\'), 1, 4)
        AND SUBSTRING(SOUNDEX(first_name.usd_value), 1, 4) LIKE SUBSTRING(SOUNDEX(\''. $new_user->getValue('FIRST_NAME').'\'), 1, 4) )
     OR (   SUBSTRING(SOUNDEX(last_name.usd_value),  1, 4) LIKE SUBSTRING(SOUNDEX(\''. $new_user->getValue('FIRST_NAME').'\'), 1, 4)
        AND SUBSTRING(SOUNDEX(first_name.usd_value), 1, 4) LIKE SUBSTRING(SOUNDEX(\''. $new_user->getValue('LAST_NAME').'\'), 1, 4) ) )';
}
else
{
    $sql_similar_name = 
    '(  (   last_name.usd_value  LIKE \''. $new_user->getValue('LAST_NAME').'\'
        AND first_name.usd_value LIKE \''. $new_user->getValue('FIRST_NAME').'\')
     OR (   last_name.usd_value  LIKE \''. $new_user->getValue('FIRST_NAME').'\'
        AND first_name.usd_value LIKE \''. $new_user->getValue('LAST_NAME').'\') )';
}

// alle User aus der DB selektieren, die denselben Vor- und Nachnamen haben
$sql = 'SELECT usr_id, usr_login_name, last_name.usd_value as last_name, 
               first_name.usd_value as first_name, address.usd_value as address,
               zip_code.usd_value as zip_code, city.usd_value as city,
               email.usd_value as email
          FROM '. TBL_USERS. '
         RIGHT JOIN '. TBL_USER_DATA. ' as last_name
            ON last_name.usd_usr_id = usr_id
           AND last_name.usd_usf_id = '. $gProfileFields->getProperty('LAST_NAME', 'usf_id'). '
         RIGHT JOIN '. TBL_USER_DATA. ' as first_name
            ON first_name.usd_usr_id = usr_id
           AND first_name.usd_usf_id = '. $gProfileFields->getProperty('FIRST_NAME', 'usf_id'). '
          LEFT JOIN '. TBL_USER_DATA. ' as address
            ON address.usd_usr_id = usr_id
           AND address.usd_usf_id = '. $gProfileFields->getProperty('ADDRESS', 'usf_id'). '
          LEFT JOIN '. TBL_USER_DATA. ' as zip_code
            ON zip_code.usd_usr_id = usr_id
           AND zip_code.usd_usf_id = '. $gProfileFields->getProperty('POSTCODE', 'usf_id'). '
          LEFT JOIN '. TBL_USER_DATA. ' as city
            ON city.usd_usr_id = usr_id
           AND city.usd_usf_id = '. $gProfileFields->getProperty('CITY', 'usf_id'). '
          LEFT JOIN '. TBL_USER_DATA. ' as email
            ON email.usd_usr_id = usr_id
           AND email.usd_usf_id = '. $gProfileFields->getProperty('EMAIL', 'usf_id'). '
         WHERE usr_valid = 1 
           AND '.$sql_similar_name;
$result_usr   = $gDb->query($sql);
$member_found = $gDb->num_rows($result_usr);

// if current user can edit profiles than create link to profile otherwise create link to auto assign new registration
if($gCurrentUser->editUsers())
{
	$urlCreateNewUser = $g_root_path.'/adm_program/modules/profile/profile_new.php?new_user=3&user_id='.$getNewUserId;
}
else
{
	$urlCreateNewUser = $g_root_path.'/adm_program/administration/new_user/new_user_function.php?mode=5&new_user_id='.$getNewUserId;
}

if($member_found == 0)
{
    // if user doesn't exists than show profile or auto assign roles
	header('Location: '.$urlCreateNewUser);
	exit();
}

$gNavigation->addUrl(CURRENT_URL);

// Html-Kopf ausgeben
$gLayout['title'] = $gL10n->get('NWU_ASSIGN_REGISTRATION');
require(SERVER_PATH. '/adm_program/system/overall_header.php');

// Html des Modules ausgeben
echo '
<div class="formLayout" id="assign_users_form" style="width: 400px;">
    <div class="formHead">'.$gLayout['title'].'</div>
    <div class="formBody">
        '.$gL10n->get('SYS_SIMILAR_USERS_FOUND', $new_user->getValue('FIRST_NAME'). ' '. $new_user->getValue('LAST_NAME')).'<br />

        <div class="groupBox">
            <div class="groupBoxHeadline">'.$gL10n->get('SYS_USERS_FOUND').'</div>
            <div class="groupBoxBody">';
                // Alle gefundenen Benutzer mit Adresse ausgeben und einem Link zur weiteren moeglichen Verarbeitung
                $i = 0;
                while($row = $gDb->fetch_object($result_usr))
                {
                    if($i > 0)
                    {
                        echo '<hr />';
                    }
                    echo '<div style="margin-left: 20px;">
						<a href="'. $g_root_path. '/adm_program/modules/profile/profile.php?user_id='.$row->usr_id.'"><img 
                             src="'.THEME_PATH.'/icons/profile.png" alt="'.$gL10n->get('SYS_SHOW_PROFILE').'" /></a>
                        <a href="'. $g_root_path. '/adm_program/modules/profile/profile.php?user_id='.$row->usr_id.'">'.
                            $row->first_name.' '.$row->last_name.'</a><br />';
                        if(strlen($row->address) > 0)
                        {
                            echo $row->address.'<br />';
                        }
                        if(strlen($row->zip_code) > 0 || strlen($row->city) > 0)
                        {
                            echo $row->zip_code.' '.$row->city.'<br />';
                        }
                        if(strlen($row->email) > 0)
                        {
                            if($gPreferences['enable_mail_module'] == 1)
                            {
                                echo '<a href="'.$g_root_path.'/adm_program/modules/mail/mail.php?usr_id='.$row->usr_id.'">'.$row->email.'</a><br />';
                            }
                            else
                            {
                                echo '<a href="mailto:'.$row->email.'">'.$row->email.'</a><br />';
                            }
                        }

                        if(isMember($row->usr_id))
                        {
                            // gefundene User ist bereits Mitglied dieser Organisation
                            if(strlen($row->usr_login_name) > 0)
                            {
                                // Logindaten sind bereits vorhanden -> Logindaten neu zuschicken                    
                                echo '<br />'.$gL10n->get('NWU_USER_VALID_LOGIN');
                                if($gPreferences['enable_system_mails'] == 1)
                                {
                                    echo '<br />'.$gL10n->get('NWU_REMINDER_SEND_LOGIN').'<br />

                                    <span class="iconTextLink">
                                        <a href="'.$g_root_path.'/adm_program/administration/new_user/new_user_function.php?new_user_id='.$getNewUserId.'&amp;user_id='.$row->usr_id.'&amp;mode=6"><img
                                        src="'. THEME_PATH. '/icons/key.png" alt="'.$gL10n->get('NWU_SEND_LOGIN').'" /></a>
                                        <a href="'.$g_root_path.'/adm_program/administration/new_user/new_user_function.php?new_user_id='.$getNewUserId.'&amp;user_id='.$row->usr_id.'&amp;mode=6">'.$gL10n->get('NWU_SEND_LOGIN').'</a>
                                    </span>';
                                }
                            }
                            else
                            {
                                // Logindaten sind NICHT vorhanden -> diese nun zuordnen
                                echo '<br />'.$gL10n->get('NWU_USER_NO_VALID_LOGIN').'<br />

                                <span class="iconTextLink">
                                    <a href="'.$g_root_path.'/adm_program/administration/new_user/new_user_function.php?new_user_id='.$getNewUserId.'&amp;user_id='.$row->usr_id.'&amp;mode=1"><img
                                    src="'. THEME_PATH. '/icons/new_registrations.png" alt="'.$gL10n->get('NWU_ASSIGN_LOGIN').'" /></a>
                                    <a href="'.$g_root_path.'/adm_program/administration/new_user/new_user_function.php?new_user_id='.$getNewUserId.'&amp;user_id='.$row->usr_id.'&amp;mode=1">'.$gL10n->get('NWU_ASSIGN_LOGIN').'</a>
                                </span>';
                            }
                        }
                        else
                        {
                            // gefundene User ist noch KEIN Mitglied dieser Organisation
                            $link = $g_root_path.'/adm_program/administration/new_user/new_user_function.php?new_user_id='.$getNewUserId.'&amp;user_id='.$row->usr_id.'&amp;mode=2';

                            if(strlen($row->usr_login_name) > 0)
                            {
                                // Logindaten sind bereits vorhanden
                                echo '<br />'.$gL10n->get('NWU_NO_MEMBERSHIP', $gCurrentOrganization->getValue('org_shortname')).'<br />

                                <span class="iconTextLink">
                                    <a href="'.$link.'"><img src="'.THEME_PATH.'/icons/new_registrations.png" 
                                        alt="'.$gL10n->get('NWU_ASSIGN_MEMBERSHIP_AND_LOGIN').'" /></a>
                                    <a href="'.$link.'">'.$gL10n->get('NWU_ASSIGN_MEMBERSHIP_AND_LOGIN').'</a>
                                </span>';
                            }               
                            else
                            {
                                // KEINE Logindaten vorhanden
                                echo '<br />'.$gL10n->get('NWU_NO_MEMBERSHIP_NO_LOGIN', $gCurrentOrganization->getValue('org_shortname')).'<br />
                                
                                <span class="iconTextLink">
                                    <a href="'.$link.'"><img src="'. THEME_PATH. '/icons/new_registrations.png" 
                                        alt="'.$gL10n->get('NWU_ASSIGN_MEMBERSHIP').'" /></a>
                                    <a href="'.$link.'">'.$gL10n->get('NWU_ASSIGN_MEMBERSHIP').'</a>
                                </span>';
                            }               
                        }
                    echo '</div>';
                    $i++;
                }
            echo '</div>
        </div>

        <div class="groupBox">
            <div class="groupBoxHeadline">'.$gL10n->get('SYS_CREATE_NEW_USER').'</div>
            <div class="groupBoxBody">
                <div style="margin-left: 20px;">
                    '. $gL10n->get('SYS_CREATE_NOT_FOUND_USER'). '<br />
                    
                    <span class="iconTextLink">
                        <a href="'.$urlCreateNewUser.'"><img 
							src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('SYS_CREATE_NEW_USER').'" /></a>
                        <a href="'.$urlCreateNewUser.'">'.$gL10n->get('SYS_CREATE_NEW_USER').'</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

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

?>