<?php
/******************************************************************************
 * Class manages access to database table adm_guestbook_comments
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu ein Gaestebuchkommentarobjekt zu erstellen. 
 * Eine Gaestebuchkommentar kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Beside the methods of the parent class there are the following additional methods:
 *
 * moderate()       - guestbook entry will be published, if moderate mode is set
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableGuestbookComment extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_guestbook_comments. 
	 *  If the id is set than the specific guestbook comment will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $gbc_id The recordset of the guestbook comment with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $gbc_id = 0)
    {
		// read also data of assigned guestbook entry
		$this->connectAdditionalTable(TBL_GUESTBOOK, 'gbo_id', 'gbc_gbo_id');

        parent::__construct($db, TBL_GUESTBOOK_COMMENTS, 'gbc', $gbc_id);
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
        if($columnName == 'gbc_text')
        {
			if(isset($this->dbColumns['gbc_text']) == false)
			{
				$value = '';
			}
			elseif($format == 'plain')
			{
				$value = html_entity_decode(strStripTags($this->dbColumns['gbc_text']));
			}
			else
			{
				$value = $this->dbColumns['gbc_text'];
			}
        }
        else
        {
            $value = parent::getValue($columnName, $format);
        }
 
        return $value;
    }
    
    // guestbook entry will be published, if moderate mode is set
    function moderate()
    {
        // unlock entry
        $this->setValue('gbc_locked', '0');
        $this->save();
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
        global $gCurrentOrganization;
        
        if($this->new_record)
        {
            $this->setValue('gbc_org_id', $gCurrentOrganization->getValue('org_id'));
            $this->setValue('gbc_ip_address', $_SERVER['REMOTE_ADDR']);
        }

        parent::save($updateFingerPrint);
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
        if(strlen($newValue) > 0)
        {
            if($columnName == 'gbc_email')
            {
                $newValue = admStrToLower($newValue);
                if (!strValidCharacters($newValue, 'email'))
                {
                    // falls die Email ein ungueltiges Format aufweist wird sie nicht gesetzt
                    return false;
                }
            }
        }
		
        if($columnName == 'gbc_text')
        {
            return parent::setValue($columnName, $newValue, false);
        }
		
        return parent::setValue($columnName, $newValue, $checkValue);
    } 
}
?>