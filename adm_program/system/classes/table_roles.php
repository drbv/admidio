<?php
/******************************************************************************
 * Class manages access to database table adm_roles
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu einen Rollenobjekt zu erstellen.
 * Eine Rolle kann ueber diese Klasse in der Datenbank verwaltet werden.
 * Dazu werden die Informationen der Rolle sowie der zugehoerigen Kategorie
 * ausgelesen. Geschrieben werden aber nur die Rollendaten
 *
 * Beside the methods of the parent class there are the following additional methods:
 *
 * allowedToAssignMembers - checks if user is allowed to assign members to this role
 *                          requires userObject of user for this should be checked 
 * allowedToEditMembers - checks if user is allowed to edit members of this role
 *                        requires userObject of user for this should be checked 
 * countVacancies($count_leaders = false) - gibt die freien Plaetze der Rolle zurueck
 *                    dies ist interessant, wenn rol_max_members gesetzt wurde
 * hasFormerMembers() - Methode gibt true zurueck, wenn die Rolle ehemalige Mitglieder besitzt
 * setInactive()    - setzt die Rolle auf inaktiv
 * setActive()      - setzt die Rolle wieder auf aktiv
 * viewRole()       - diese Methode basiert auf viewRole des Usersobjekts, geht aber noch weiter 
 *                    und prueft auch Rollen zu Terminen (hier muss man nicht Mitglied der Rolle
 *                    sein, sondern nur in einer Rolle sein, die den Termin sehen darf)
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

// constants for column rol_leader_rights
define('ROLE_LEADER_NO_RIGHTS', 0);
define('ROLE_LEADER_MEMBERS_ASSIGN', 1);
define('ROLE_LEADER_MEMBERS_EDIT', 2);
define('ROLE_LEADER_MEMBERS_ASSIGN_EDIT', 3);

// class definition
class TableRoles extends TableAccess
{
	protected $countLeaders;	///< number of leaders of this role
	protected $countMembers;	///< number of members (without leaders) of this role

	/** Constuctor that will create an object of a recordset of the table adm_roles. 
	 *  If the id is set than the specific role will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $rol_id The recordset of the role with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $rol_id = 0)
    {
		// read also data of assigned category
		$this->connectAdditionalTable(TBL_CATEGORIES, 'cat_id', 'rol_cat_id');

        parent::__construct($db, TBL_ROLES, 'rol', $rol_id);
    }

	/** checks if user is allowed to assign members to this role
	 *  @param $user UserObject of user who should be checked 
	 */
	public function allowedToAssignMembers($user)
	{
		global $gL10n;
		
		// you aren't allowed to change membership of not active roles
		if($this->getValue('rol_valid'))
		{
			if($user->assignRoles() == false)
			{
				// leader are allowed to assign members if it's configured in the role
				if($user->isLeaderOfRole($this->getValue('rol_id'))
				&& (  $this->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN 
				   || $this->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN_EDIT))
				{
					return true;
				}
			}
			else
			{
				// only webmasters are allowed to assign new members to webmaster role
				if($this->getValue('rol_webmaster') == 0
				|| ($this->getValue('rol_webmaster') == 1 && $user->isWebmaster()))
				{
					return true;
				}
			}
		}
		return false;
	}

	/** checks if user is allowed to edit members of this role
	 *  @param UserObject of user who should be checked 
	 */
	public function allowedToEditMembers($user)
	{
		// you aren't allowed to edit users of not active roles
		if($this->getValue('rol_valid'))
		{
			if($user->editUsers() == false)
			{
				// leader are allowed to assign members if it's configured in the role
				if($user->isMemberOfRole($this->getValue('rol_id'))
				&& (  $this->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_EDIT 
				   || $this->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN_EDIT))
				{
					return true;
				}
				return true;
			}
			else
			{
				return true;
			}
		}
		return false;
	}

	/** Calls clear() Method of parent class and initialize child class specific parameters
	 */
    public function clear()
    {
        parent::clear();

        // initialize class members
        $this->countLeaders = -1;
        $this->countMembers = -1;
    }

	/** Method determines the number of active leaders of this role
	 *  @return Returns the number of leaders of this role
	 */
	public function countLeaders()
	{
		if($this->countLeaders == -1)
		{
            $sql    = 'SELECT COUNT(mem_id) FROM '. TBL_MEMBERS. '
			            WHERE mem_rol_id = '.$this->getValue('rol_id').'
						  AND mem_leader = 1 
						  AND mem_begin <= \''.DATE_NOW.'\'
                          AND mem_end    > \''.DATE_NOW.'\' ';
            $this->db->query($sql);
			$row = $this->db->fetch_array();
			$this->countLeaders = $row[0];
		}
		return $this->countLeaders;
	}
	
	/** Method determines the number of active members (without leaders) of this role
	 * @param exceptUserId UserId witch shoudn't be counted 
     * @return Returns the number of members of this role
	 */
	public function countMembers($exceptUserId=NULL)
	{
		if($this->countMembers == -1)
		{
            $sql    = 'SELECT COUNT(mem_id) FROM '. TBL_MEMBERS. '
			            WHERE mem_rol_id = '.$this->getValue('rol_id').'
						  AND mem_usr_id != '.$exceptUserId.'
						  AND mem_leader = 0 
						  AND mem_begin <= \''.DATE_NOW.'\'
                          AND mem_end    > \''.DATE_NOW.'\' ';
            $this->db->query($sql);
			$row = $this->db->fetch_array();
			$this->countMembers = $row[0];
		}
		return $this->countMembers;
	}

    // die Funktion gibt die Anzahl freier Plaetze zurueck
    // ist rol_max_members nicht gesetzt so wird immer 999 zurueckgegeben
    public function countVacancies($count_leaders = false)
    {
        if(strlen($this->getValue('rol_max_members')) > 0)
        {
            $sql    = 'SELECT mem_usr_id FROM '. TBL_MEMBERS. '
                        WHERE mem_rol_id = '. $this->getValue('rol_id'). '
                          AND mem_begin <= \''.DATE_NOW.'\'
                          AND mem_end    > \''.DATE_NOW.'\'';
            if($count_leaders == false)
            {
                $sql = $sql. ' AND mem_leader = 0 ';
            }
            $this->db->query($sql);

            $num_members = $this->db->num_rows();
            return $this->getValue('rol_max_members') - $num_members;
        }
        return 999;
    }

	/** Deletes the selected role of the table and all references in other tables. 
	 *  After that the class will be initialize.
	 *  @return @b true if no error occured
	 */
    public function delete()
    {
        global $gCurrentSession, $gL10n, $gCurrentOrganization;
        
        if($this->getValue('rol_default_registration') == 1)
        {
			// checks if at least one other role has this flag, if not than this role couldn't be deleted
			$sql = 'SELECT COUNT(1) AS count FROM '.TBL_ROLES.', '.TBL_CATEGORIES.'
			         WHERE rol_default_registration = 1
					   AND rol_id    <> '.$this->getValue('rol_id').'
					   AND rol_cat_id = cat_id
					   AND cat_org_id = '.$gCurrentOrganization->getValue('org_id');
			$this->db->query($sql);
			$row = $this->db->fetch_array();

			if($row['count'] == 0)
			{
                throw new AdmException('ROL_DELETE_NO_DEFAULT_ROLE', $this->getValue('rol_name'), $gL10n->get('ROL_DEFAULT_REGISTRATION'));
			}
        }

        // users are not allowed to delete system roles
        if($this->getValue('rol_system'))
        {
            throw new AdmException('ROL_DELETE_SYSTEM_ROLE', $this->getValue('rol_name'));
        }
        elseif($this->getValue('rol_webmaster'))
        {
            throw new AdmException('ROL_DELETE_ROLE', $gL10n->get('SYS_WEBMASTER'));
        }
        else
        {
			$this->db->startTransaction();
			
            $sql    = 'DELETE FROM '. TBL_ROLE_DEPENDENCIES. '
                        WHERE rld_rol_id_parent = '. $this->getValue('rol_id'). '
                           OR rld_rol_id_child  = '. $this->getValue('rol_id');
            $this->db->query($sql);

            $sql    = 'DELETE FROM '. TBL_MEMBERS. '
                        WHERE mem_rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            $return = parent::delete();

			$this->db->endTransaction();
            
            // all active users must renew their user data because maybe their 
            // rights have been changed if they where members of this role
            $gCurrentSession->renewUserObject();

			return $return;
        }
    }

	/** Returns an array with all cost periods with full name in the specific language.
	 *  @param $costPeriod The number of the cost period for which the name should be returned (-1 = unique, 1 = annually, 2 = semiyearly, 4 = quarterly, 12 = monthly)
	 *  @return Array with all cost or if param costPeriod is set than the full name of that cost period
	 */
	public static function getCostPeriods($costPeriod = 0)
    {
		global $gL10n;
		
		$costPeriods = array(-1 => $gL10n->get('ROL_UNIQUELY'), 
						     1  => $gL10n->get('ROL_ANNUALLY'), 
						     2  => $gL10n->get('ROL_SEMIYEARLY'), 
						     4  => $gL10n->get('ROL_QUARTERLY'), 
						     12 => $gL10n->get('ROL_MONTHLY') );
		
		if($costPeriod != 0)
		{
			return $costPeriods[$costPeriod];
		}
		else
		{
			return $costPeriods;
		}
    }
    
    /** Read the id of the default list of this role. The list is stored in the
     *  column @b rol_lst_id. If there is no list stored then the system default
     *  list will be returned
     *  @return Returns the default list id of this role
     */
    public function getDefaultList()
    {
        global $gCurrentOrganization;
        $defaultListId = $this->getValue('rol_lst_id');
    
        //if default list is not set
        if($defaultListId <= 0 || $defaultListId == null)
        {
            // read and set system default list configuration
            $sql = 'SELECT lst_id FROM '. TBL_LISTS. '
                     WHERE lst_org_id  = '. $gCurrentOrganization->getValue('org_id'). '
                       AND lst_default = 1 ';
            $result = $this->db->query($sql);
            $row    = $this->db->fetch_array($result);
            $defaultListId = $row[0];
            
            if(!is_numeric($defaultListId))
            {
               $defaultListId = 0;
            }     
        }
        return $defaultListId;
    }
	
    /** Get the value of a column of the database table.
     *  If the value was manipulated before with @b setValue than the manipulated value is returned.
     *  @param $columnName The name of the database column whose value should be read
     *  @param $format For date or timestamp columns the format should be the date/time format e.g. @b d.m.Y = '02.04.2011'. @n
     *                 For text columns the format can be @b plain that would return the original database value without any transformations
     *  @return Returns the value of the database column.
     *          If the value was manipulated before with @b setValue than the manipulated value is returned.
     */ 
    public function getValue($columnName, $format = '')
    {
		global $gL10n;
        $value = parent::getValue($columnName, $format);

		if($columnName == 'cat_name' && $format != 'plain')
		{
			// if text is a translation-id then translate it
			if(strpos($value, '_') == 3)
			{
				$value = $gL10n->get(admStrToUpper($value));
			}
		}
        
        return $value;
    }
    
	/** Checks if this role has former members
	 *  @return Returns @b true if the role has former memberships
	 */
    public function hasFormerMembers()
    {
        $sql = 'SELECT COUNT(1) AS count
                  FROM '.TBL_MEMBERS.'
                 WHERE mem_rol_id = '.$this->getValue('rol_id').'
                   AND (  mem_begin > \''.DATE_NOW.'\'
                       OR mem_end   < \''.DATE_NOW.'\')';
        $result = $this->db->query($sql);
        $row    = $this->db->fetch_array($result);

        if($row['count'] > 0)
        {
            return true;
        }
        return false;
    }

	/** Save all changed columns of the recordset in table of database. Therefore the class remembers if it's 
	 *  a new record or if only an update is neccessary. The update statement will only update
	 *  the changed columns. If the table has columns for creator or editor than these column
	 *  with their timestamp will be updated.
	 *  For new records the organization and ip address will be set per default.
	 *  @param $updateFingerPrint Default @b true. Will update the creator or editor of the recordset if table has columns like @b usr_id_create or @b usr_id_changed
	 */
    public function save($updateFingerPrint = true)
    {
        global $gCurrentSession;
        $fields_changed = $this->columnsValueChanged;
 
        parent::save($updateFingerPrint);

        // Nach dem Speichern noch pruefen, ob Userobjekte neu eingelesen werden muessen,
        if($fields_changed && is_object($gCurrentSession))
        {
            // all active users must renew their user data because maybe their 
            // rights have been changed if they where members of this role
            $gCurrentSession->renewUserObject();
        }
    }

    // aktuelle Rolle wird auf aktiv gesetzt
    public function setActive()
    {
        global $gCurrentSession;

        // die Systemrollem sind immer aktiv
        if($this->getValue('rol_system') == false)
        {
            $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_valid = 1
                        WHERE rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            // all active users must renew their user data because maybe their 
            // rights have been changed if they where members of this role
            $gCurrentSession->renewUserObject();

            return 0;
        }
        return -1;
    }

    // aktuelle Rolle wird auf inaktiv gesetzt
    public function setInactive()
    {
        global $gCurrentSession;

        // die Systemrollem sind immer aktiv
        if($this->getValue('rol_system') == false)
        {
            $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_valid = 0
                        WHERE rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            // all active users must renew their user data because maybe their 
            // rights have been changed if they where members of this role
            $gCurrentSession->renewUserObject();

            return 0;
        }
        return -1;
    }
	
    /** Set a new value for a column of the database table. The value is only saved in the object. 
     *  You must call the method @b save to store the new value to the database.
     *  @param $columnName The name of the database column whose value should get a new value
     *  @param $newValue The new value that should be stored in the database field
     *  @param $checkValue The value will be checked if it's valid. If set to @b false than the value will not be checked.  
     *  @return Returns @b true if the value is stored in the current object and @b false if a check failed
     */ 
    public function setValue($columnName, $newValue, $checkValue = true)
    {
		global $gCurrentOrganization;

        if($columnName == 'rol_default_registration' && $newValue == '0' && $this->dbColumns[$columnName] == '1')
        {
			// checks if at least one other role has this flag
			$sql = 'SELECT COUNT(1) AS count FROM '.TBL_ROLES.', '.TBL_CATEGORIES.'
			         WHERE rol_default_registration = 1
					   AND rol_id    <> '.$this->getValue('rol_id').'
					   AND rol_cat_id = cat_id
					   AND cat_org_id = '.$gCurrentOrganization->getValue('org_id');
			$this->db->query($sql);
			$row = $this->db->fetch_array();

			if($row['count'] == 0)
			{
				return false;
			}
        }
        return parent::setValue($columnName, $newValue, $checkValue);
    }
    
    // diese Methode basiert auf viewRole des Usersobjekts, geht aber noch weiter 
    // und prueft auch Rollen zu Terminen (hier muss man nicht Mitglied der Rolle
    // sein, sondern nur in einer Rolle sein, die den Termin sehen darf)
    public function viewRole()
    {
        global $gCurrentUser, $gValidLogin;
        
        if($gValidLogin == true)
        {
            if($this->getValue('cat_name_intern') == 'CONFIRMATION_OF_PARTICIPATION')
            {
                // pruefen, ob der Benutzer Mitglied einer Rolle ist, die den Termin sehen darf
                $sql = 'SELECT dtr_rol_id
                          FROM '.TBL_DATE_ROLE.', '.TBL_DATES.'
                         WHERE dat_rol_id = '.$this->getValue('rol_id').'
                           AND dtr_dat_id = dat_id
                           AND (  dtr_rol_id IS NULL
                               OR EXISTS (SELECT 1
                                            FROM '.TBL_MEMBERS.'
                                           WHERE mem_rol_id = dtr_rol_id
                                             AND mem_usr_id = '.$gCurrentUser->getValue('usr_id').'))';
                $this->db->query($sql);
                
                if($this->db->num_rows() > 0)
                {
                    return true;
                }
            }
            else
            {
                return $gCurrentUser->viewRole($this->getValue('rol_id'));
            }
        }
        return false;
    }
}
?>