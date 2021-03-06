<?php
/*****************************************************************************/
/** @class UserRegistration
 *  @brief Creates, assign and update user registrations in database
 *
 *  This class extends the User class with some special functions for new registrations.
 *  If a new user is saved than there will be an additional table entry in the 
 *  registration table. This entry must be deleted if a registration is confirmed
 *  or deleted. If a registration is confirmed or deleted then a notification SystemMail
 *  will be send to the user. If email couldn't be send than an AdmException will be thrown.
 *  @par Example 1
 *  @code // create a valid registration
 *  $user = new UserRegistration($gDb, $gProfileFields);
 *  $user->setValue('LAST_NAME', 'Schmidt');
 *  $user->setValue('FIRST_NAME', 'Franka');
 *  ...
 *  // save user data and create registration
 *  $user->save(); @endcode
 *  @par Example 2
 *  @code // assign a registration
 *  $userId = 4711;
 *  $user = new UserRegistration($gDb, $gProfileFields, $userId);
 *  // set user to valid and send notification email
 *  $user->acceptRegistration(); @endcode
 */
/*****************************************************************************
 *
 *  Copyright    : (c) 2004 - 2013 The Admidio Team
 *  Homepage     : http://www.admidio.org
 *  License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/user.php');

class UserRegistration extends User
{
	private $sendEmail; ///< Flag if the object will send a SystemMail if registration is accepted or deleted.

	/** Constuctor that will create an object of a recordset of the users table. 
	 *  If the id is set than this recordset will be loaded.
	 *  @param $db 				Object of the class database. This could be the default object @b $gDb.
	 *  @param $userFields 		An object of the ProfileFields class with the profile field structure 
	 *					   		of the current organization. This could be the default object @b $gProfileFields.
	 *  @param $userId 			The id of the user who should be loaded. If id isn't set than an empty object with no specific user is created.
	 *  @param $organizationId 	The id of the organization for which the user should be registrated. 
	 *		 				   	If no id is set than the user will be registrated for the current organization.
	 */
    public function __construct(&$db, $userFields, $userId = 0, $organizationId = 0)
    {
		global $gCurrentOrganization;
		
		$this->sendEmail = true;
		
        parent::__construct($db, $userFields, $userId);

		if($organizationId > 0)
		{
			$this->setOrganization($organizationId);
		}
		
		// create recordset for registration table
		$this->TableRegistration = new TableAccess($this->db, TBL_REGISTRATIONS, 'reg');
		$this->TableRegistration->readDataByColumns(array('reg_org_id' => $this->organizationId, 'reg_usr_id' => $userId));
    }
	
	/** Deletes the registration record and set the user to valid. The user will also be
     *  assigned to all roles that have the flag @b rol_default_registration. After that 
	 *  a notification email is send to the user. If function returns true than the user
	 *  can login for the organization of this object.
	 *  @return Returns @b true if the registration was succesful
	 */
	public function acceptRegistration()
	{
		global $gMessage, $gL10n, $gPreferences;

		$this->db->startTransaction();
		
		// set user active
		$this->setValue('usr_valid', 1);
		$this->save();
		
		// delete registration record in registration table
		$this->TableRegistration->delete();

		// every user will get the default roles for registration
		$this->assignDefaultRoles();
		
		$this->db->endTransaction();
		
        // only send mail if systemmails are enabled
        if($gPreferences['enable_system_mails'] == 1 && $this->sendEmail)
        {
            // send mail to user that his registration was accepted
            $sysmail = new SystemMail($this->db);
            $sysmail->addRecipient($this->getValue('EMAIL'), $this->getValue('FIRST_NAME'). ' '. $this->getValue('LAST_NAME'));
            $sendMailResult = $sysmail->sendSystemMail('SYSMAIL_REGISTRATION_USER', $this);
            if(strlen($sendMailResult) > 1)
            {
                throw new AdmException('SYS_EMAIL_NOT_SEND', $this->getValue('EMAIL'), $sendMailResult);
            }
        }

		return true;
	}
	
	/** Deletes the selected user registration. If user is not valid and has no other registrations 
	 *  than delete user because he has no use for the system. After that 
	 *  a notification email is send to the user. If the user is valid than only
	 *  the registration will be deleted! 
	 *  @return @b true if no error occured
	 */
    public function delete()
    {
		global $gMessage, $gL10n, $gPreferences;

		$userEmail = $this->getValue('EMAIL');
        
		$this->db->startTransaction();
		
		// delete registration record in registration table
		$return = $this->TableRegistration->delete();
		
		// if user is not valid and has no other registrations 
		// than delete user because he has no use for the system
		if($this->getValue('usr_valid') == 0)
		{
			$sql = 'SELECT reg_id FROM '.TBL_REGISTRATIONS.' WHERE reg_usr_id = '.$this->getValue('usr_id');
			$this->db->query($sql);

			if($this->db->num_rows() == 0)
			{
				$return = parent::delete();
			}
		}
		
        $this->db->endTransaction();
        
        // only send mail if systemmails are enabled
		// send email before user will be deleted
        if($gPreferences['enable_system_mails'] == 1 && $this->sendEmail)
        {
            // send mail to user that his registration was accepted
            $sysmail = new SystemMail($this->db);
            $sysmail->addRecipient($this->getValue('EMAIL'), $this->getValue('FIRST_NAME'). ' '. $this->getValue('LAST_NAME'));
            $sendMailResult = $sysmail->sendSystemMail('SYSMAIL_REFUSE_REGISTRATION', $this);
            if(strlen($sendMailResult) > 1)
            {
                throw new AdmException('SYS_EMAIL_NOT_SEND', $userEmail, $sendMailResult);
            }
        }        
        
        return $return;	
	}
	
	/** If called than the object will not send a SystemMail when registration was accepted or deleted.
	 */
	public function notSendEmail()
	{
    	$this->sendEmail = false;
	}

	/** Save all changed columns of the recordset in table of database. If it's a new user 
	 *  than the registration table will also be filled with a new recordset and optional a 
	 *  notification mail will be send to all users of roles that have the right to confirm registrations
	 *  @param $updateFingerPrint Default @b true. Will update the creator or editor of the recordset 
	 *                            if table has columns like @b usr_id_create or @b usr_id_changed
	 */
    public function save($updateFingerPrint = true)
    {
		global $gMessage, $gL10n, $gPreferences;

		// if new registration is saved then set user not valid
		if($this->TableRegistration->isNewRecord())
		{
			$this->setValue('usr_valid', 0);
		}
		
        parent::save($updateFingerPrint);
		
		// if new registration is saved then save also record in registration table and send notification mail
		if($this->TableRegistration->isNewRecord())
		{
		    // save registration record
			$this->TableRegistration->setValue('reg_org_id', $this->organizationId);
			$this->TableRegistration->setValue('reg_usr_id', $this->getValue('usr_id'));
			$this->TableRegistration->setValue('reg_timestamp', DATETIME_NOW);
			$this->TableRegistration->save();

            // send a notification mail to all role members of roles that can approve registrations
            // therefore the flags system mails and notification mail for roles with approve registration must be activated			
            if($gPreferences['enable_system_mails'] == 1 && $gPreferences['enable_registration_admin_mail'] == 1 && $this->sendEmail)
            {
                $sql = 'SELECT DISTINCT first_name.usd_value as first_name, last_name.usd_value as last_name, email.usd_value as email
                          FROM '. TBL_ROLES. ', '. TBL_CATEGORIES. ', '. TBL_MEMBERS. ', '. TBL_USERS. '
                         RIGHT JOIN '. TBL_USER_DATA. ' email
                            ON email.usd_usr_id = usr_id
                           AND email.usd_usf_id = '. $this->mProfileFieldsData->getProperty('EMAIL', 'usf_id'). '
                           AND LENGTH(email.usd_value) > 0
                          LEFT JOIN '. TBL_USER_DATA. ' first_name
                            ON first_name.usd_usr_id = usr_id
                           AND first_name.usd_usf_id = '. $this->mProfileFieldsData->getProperty('FIRST_NAME', 'usf_id'). '
                          LEFT JOIN '. TBL_USER_DATA. ' last_name
                            ON last_name.usd_usr_id = usr_id
                           AND last_name.usd_usf_id = '. $this->mProfileFieldsData->getProperty('LAST_NAME', 'usf_id'). '
                         WHERE rol_approve_users = 1
                           AND rol_cat_id        = cat_id
                           AND cat_org_id        = '.$this->organizationId.'
                           AND mem_rol_id        = rol_id
                           AND mem_begin        <= \''.DATE_NOW.'\'
                           AND mem_end           > \''.DATE_NOW.'\'
                           AND mem_usr_id        = usr_id
                           AND usr_valid         = 1 ';
                $result = $this->db->query($sql);
                $sysmail = new SystemMail($this->db);
        
                while($row = $this->db->fetch_array($result))
                {
                    // send mail that a new registration is available
                    $sysmail->addRecipient($row['email'], $row['first_name']. ' '. $row['last_name']);

                    $sendMailResult = $sysmail->sendSystemMail('SYSMAIL_REGISTRATION_WEBMASTER', $this);
                    if(strlen($sendMailResult) > 1)
                    {
                        throw new AdmException('SYS_EMAIL_NOT_SEND', $row['email'], $sendMailResult);
                    }
                }
            }
		}
    }
}
?>