<?php
/******************************************************************************
 * Class manages access to database table adm_lists
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu ein Listenobjekt zu erstellen. 
 * Eine Liste kann ueber diese Klasse in der Datenbank verwaltet werden
 *
 * Beside the methods of the parent class there are the following additional methods:
 *
 * setDefault()       - Aktuelle Liste wird zur Default-Liste der Organisation
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableLists extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_lists. 
	 *  If the id is set than the specific list will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $lst_id The recordset of the list with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $lst_id = 0)
    {
        parent::__construct($db, TBL_LISTS, 'lst', $lst_id);
    }

	/** Deletes the selected list with all associated fields. 
	 *  After that the class will be initialize.
	 *  @return @b true if no error occured
	 */
    public function delete()
    {
		$this->db->startTransaction();
		
        // alle Spalten der Liste loeschen
        $sql = 'DELETE FROM '. TBL_LIST_COLUMNS. ' WHERE lsc_lst_id = '. $this->getValue('lst_id');
        $result = $this->db->query($sql);
        
        $return = parent::delete();

		$this->db->endTransaction();
		return $return;
    } 

	/** Save all changed columns of the recordset in table of database. Therefore the class remembers if it's 
	 *  a new record or if only an update is neccessary. The update statement will only update
	 *  the changed columns. If the table has columns for creator or editor than these column
	 *  with their timestamp will be updated.
	 *  Per default the organization, user and timestamp will be set.
	 *  @param $updateFingerPrint Default @b true. Will update the creator or editor of the recordset if table has columns like @b usr_id_create or @b usr_id_changed
	 */
    public function save($updateFingerPrint = true)
    {
        global $gCurrentOrganization, $gCurrentUser;
        
        // Standardfelder fuellen
        if($this->new_record)
        {
            $this->setValue('lst_timestamp', DATETIME_NOW);
            $this->setValue('lst_usr_id', $gCurrentUser->getValue('usr_id'));
            $this->setValue('lst_org_id', $gCurrentOrganization->getValue('org_id'));
        }
        else
        {
            $this->setValue('lst_timestamp', DATETIME_NOW);
            $this->setValue('lst_usr_id', $gCurrentUser->getValue('usr_id'));
        }
        
        // falls nicht explizit auf global = 1 gesetzt wurde, immer auf 0 setzen
        if($this->getValue('lst_global') <> 1)
        {
            $this->setValue('lst_global', 0);
        }
        
        parent::save($updateFingerPrint);
    }
        
    // Aktuelle Liste wird zur Default-Liste der Organisation
    public function setDefault()
    {
        global $gCurrentOrganization;
		$this->db->startTransaction();
        
        // erst die bisherige Default-Liste zuruecksetzen
        $sql = 'UPDATE '. TBL_LISTS. ' SET lst_default = 0
                 WHERE lst_org_id  = '. $gCurrentOrganization->getValue('org_id'). '
                   AND lst_default = 1 ';
        $this->db->query($sql);

        // jetzt die aktuelle Liste zur Default-Liste machen
        $sql = 'UPDATE '. TBL_LISTS. ' SET lst_default = 1
                 WHERE lst_id = '. $this->getValue('lst_id');
        $this->db->query($sql);
		
		$this->db->endTransaction();
    }
}
?>