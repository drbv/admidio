<?php
/******************************************************************************
 * Class manages access to database table adm_users
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu ein Userobjekt zu erstellen.
 * Ein User kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Beside the methods of the parent class there are the following additional methods:
 *
 * updateLoginData()    - Anzahl Logins hochsetzen, Datum aktualisieren und
 *                        ungueltige Logins zuruecksetzen
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');
require_once(SERVER_PATH. '/adm_program/libs/phpass/passwordhash.php');

class TableUsers extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_users. 
	 *  If the id is set than the specific user will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $userId The recordset of the user with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $userId = 0)
    {
        parent::__construct($db, TBL_USERS, 'usr', $userId);
    }

    /** Additional to the parent method the user will be set @b valid per default.
	 */
    public function clear()
    {
        parent::clear();

        // new user should be valid (except registration)
        $this->setValue('usr_valid', 1);
    }

	/** Deletes the selected user of the table and all the many references in other tables. 
	 *  After that the class will be initialize.
	 *  @return @b true if no error occured
	 */
    public function delete()
    {
        $this->db->startTransaction();

        $sql    = 'UPDATE '. TBL_ANNOUNCEMENTS. ' SET ann_usr_id_create = NULL
                    WHERE ann_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_ANNOUNCEMENTS. ' SET ann_usr_id_change = NULL
                    WHERE ann_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_DATES. ' SET dat_usr_id_create = NULL
                    WHERE dat_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_DATES. ' SET dat_usr_id_change = NULL
                    WHERE dat_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_FOLDERS. ' SET fol_usr_id = NULL
                    WHERE fol_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_FILES. ' SET fil_usr_id = NULL
                    WHERE fil_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_GUESTBOOK. ' SET gbo_usr_id_create = NULL
                    WHERE gbo_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_GUESTBOOK. ' SET gbo_usr_id_change = NULL
                    WHERE gbo_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_LINKS. ' SET lnk_usr_id_create = NULL
                    WHERE lnk_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_LINKS. ' SET lnk_usr_id_change = NULL
                    WHERE lnk_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_LISTS. ' SET lst_usr_id = NULL
                    WHERE lst_global = 1
                      AND lst_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_PHOTOS. ' SET pho_usr_id_create = NULL
                    WHERE pho_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_PHOTOS. ' SET pho_usr_id_change = NULL
                    WHERE pho_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_usr_id_create = NULL
                    WHERE rol_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_usr_id_change = NULL
                    WHERE rol_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_ROLE_DEPENDENCIES. ' SET rld_usr_id = NULL
                    WHERE rld_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_USER_LOG. ' SET usl_usr_id_create = NULL
                    WHERE usl_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_USERS. ' SET usr_usr_id_create = NULL
                    WHERE usr_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'UPDATE '. TBL_USERS. ' SET usr_usr_id_change = NULL
                    WHERE usr_usr_id_change = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_LIST_COLUMNS. '
                    WHERE lsc_lst_id IN (SELECT lst_id FROM '. TBL_LISTS. ' WHERE lst_usr_id = '.$this->getValue('usr_id').' AND lst_global = 0)';
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_LISTS. ' WHERE lst_global = 0 AND lst_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_GUESTBOOK_COMMENTS. ' WHERE gbc_usr_id_create = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_MEMBERS. ' WHERE mem_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_REGISTRATIONS. ' WHERE reg_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_AUTO_LOGIN. ' WHERE atl_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_SESSIONS. ' WHERE ses_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_USER_LOG. ' WHERE usl_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_USER_DATA. ' WHERE usd_usr_id = '. $this->getValue('usr_id');
        $this->db->query($sql);

        $return = parent::delete();

        $this->db->endTransaction();
        return $return;
    }

    /** Set a new value for a column of the database table.
     *  The value is only saved in the object. You must call the method @b save to store the new value to the database
     *  @param $columnName The name of the database column whose value should get a new value
     *  @param $newValue The new value that should be stored in the database field
     *  @param $checkValue The value will be checked if it's valid. If set to @b false than the value will not be checked.  
     *  @return Returns @b true if the value is stored in the current object and @b false if a check failed
     */ 
    public function setValue($columnName, $newValue, $checkValue = true)
    {
        // encode Passwort with phpAss
        if(($columnName == 'usr_password' || $columnName == 'usr_new_password') && strlen($newValue) < 30)
        {
            $checkValue    = false;
            $passwordHasher = new PasswordHash(9, true);
            $newValue    = $passwordHasher->HashPassword($newValue);
        }
		// username should not contain special characters
		elseif($columnName == 'usr_login_name')
		{
			if (strlen($newValue) > 0 && strValidCharacters($newValue, 'noSpecialChar') == false)
			{
				return false;
			}
		}

        return parent::setValue($columnName, $newValue, $checkValue);
    }
	
    // Anzahl Logins hochsetzen, Datum aktualisieren und ungueltige Logins zuruecksetzen
    public function updateLoginData()
    {
        $this->setValue('usr_last_login',   $this->getValue('usr_actual_login', 'Y-m-d H:i:s'));
        $this->setValue('usr_number_login', $this->getValue('usr_number_login') + 1);
        $this->setValue('usr_actual_login', DATETIME_NOW);
        $this->setValue('usr_date_invalid', NULL);
        $this->setValue('usr_number_invalid', 0);
        $this->save(false); // Zeitstempel nicht aktualisieren
    }
}
?>