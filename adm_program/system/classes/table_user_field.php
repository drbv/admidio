<?php
/******************************************************************************
 * Class manages access to database table adm_user_fields
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu einen Benutzerdefiniertes Feldobjekt zu erstellen.
 * Eine Benutzerdefiniertes Feldobjekt kann ueber diese Klasse in der Datenbank 
 * verwaltet werden
 *
 * Es stehen die Methoden der Elternklasse TableAccess zur Verfuegung
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableUserField extends TableAccess
{
	/** Constuctor that will create an object of a recordset of the table adm_user_fields. 
	 *  If the id is set than the specific user field will be loaded.
	 *  @param $db Object of the class database. This should be the default object $gDb.
	 *  @param $usf_id The recordset of the user field with this id will be loaded. If id isn't set than an empty object of the table is created.
	 */
    public function __construct(&$db, $usf_id = 0)
    {
		// read also data of assigned category
		$this->connectAdditionalTable(TBL_CATEGORIES, 'cat_id', 'usf_cat_id');

        parent::__construct($db, TBL_USER_FIELDS, 'usf', $usf_id);
    }
    
	/** Deletes the selected field and all references in other tables. Also 
	 *  the gap in sequence will be closed. After that the class will be initialize.
	 *  @return @b true if no error occured
	 */
    public function delete()
    {
        global $gCurrentSession;
        
		$this->db->startTransaction();
		
        // close gap in sequence
        $sql = 'UPDATE '. TBL_USER_FIELDS. ' SET usf_sequence = usf_sequence - 1 
                 WHERE usf_cat_id   = '. $this->getValue('usf_cat_id'). '
                   AND usf_sequence > '. $this->getValue('usf_sequence');
        $this->db->query($sql);

        // close gap in sequence of saved lists
        $sql = 'SELECT lsc_lst_id, lsc_number FROM '. TBL_LIST_COLUMNS. ' 
                 WHERE lsc_usf_id = '.$this->getValue('usf_id');
        $result_lst = $this->db->query($sql);
        
        while($row_lst = $this->db->fetch_array($result_lst))
        {
            $sql = 'UPDATE '. TBL_LIST_COLUMNS. ' SET lsc_number = lsc_number - 1 
                     WHERE lsc_lst_id = '. $row_lst['lsc_lst_id']. '
                       AND lsc_number > '. $row_lst['lsc_number'];
            $this->db->query($sql);
        }

        // delete all dependencies in other tables
        $sql    = 'DELETE FROM '. TBL_USER_LOG. '
                    WHERE usl_usf_id = '. $this->getValue('usf_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_USER_DATA. '
                    WHERE usd_usf_id = '. $this->getValue('usf_id');
        $this->db->query($sql);

        $sql    = 'DELETE FROM '. TBL_LIST_COLUMNS. ' 
                    WHERE lsc_usf_id = '. $this->getValue('usf_id');
        $this->db->query($sql);

        // einlesen aller Userobjekte der angemeldeten User anstossen, 
        // da Aenderungen in den Profilfeldern vorgenommen wurden 
        $gCurrentSession->renewUserObject();

        $return = parent::delete();
		
		$this->db->endTransaction();
		return $return;
    }

    // diese rekursive Methode ermittelt fuer den uebergebenen Namen einen eindeutigen Namen
    // dieser bildet sich aus dem Namen in Grossbuchstaben und der naechsten freien Nummer (index)
    // Beispiel: 'Mitgliedsnummer' => 'MITGLIEDSNUMMER_2'
    private function getNewNameIntern($name, $index)
    {
        $newNameIntern = strtoupper(str_replace(' ', '_', $name));
        if($index > 1)
        {
            $newNameIntern = $newNameIntern.'_'.$index;
        }
        $sql = 'SELECT usf_id FROM '.TBL_USER_FIELDS.' WHERE usf_name_intern = \''.$newNameIntern.'\'';
        $this->db->query($sql);
        
        if($this->db->num_rows() > 0)
        {
            $index++;
            $newNameIntern = $this->getNewNameIntern($name, $index);
        }
        return $newNameIntern;
    }
	
    /** Get the value of a column of the database table.
     *  If the value was manipulated before with @b setValue than the manipulated value is returned.
     *  @param $columnName The name of the database column whose value should be read
     *  @param $format For column @c usf_value_list the following format is accepted: @n
     *                 @b plain returns database value of usf_value_list; @n
     *                 @b text extract only text from usf_value_list, image infos will be ignored @n
     *                 For date or timestamp columns the format should be the date/time format e.g. @b d.m.Y = '02.04.2011' @n
     *                 For text columns the format can be @b plain that would be the database value without any transformations
     *  @return Returns the value of the database column.
     *          If the value was manipulated before with @b setValue than the manipulated value is returned.
     */ 
    public function getValue($columnName, $format = '')
    {
		global $gL10n;

		if($columnName == 'usf_description')
        {
			if(isset($this->dbColumns['usf_description']) == false)
			{
				$value = '';
			}
			elseif($format == 'plain')
			{
				$value = html_entity_decode(strStripTags($this->dbColumns['usf_description']), ENT_QUOTES, 'UTF-8');
			}
			else
			{
				$value = $this->dbColumns['usf_description'];
			}
        }
		elseif($columnName == 'usf_name_intern')
		{
			// internal name should be read with no conversion
			$value = parent::getValue($columnName, 'plain');
		}
        else
        {
            $value = parent::getValue($columnName, $format);
        }
		
		if(($columnName == 'usf_name' || $columnName == 'cat_name')
		&& $format != 'plain')
		{
			// if text is a translation-id then translate it
			if(strpos($value, '_') == 3)
			{
				$value = $gL10n->get(admStrToUpper($value));
			}
		}
		elseif($columnName == 'usf_value_list' && $format != 'plain')
		{
			if($this->dbColumns['usf_type'] == 'DROPDOWN'
			|| $this->dbColumns['usf_type'] == 'RADIO_BUTTON')
			{
				$arrListValues = explode("\r\n", $value);
				$arrListValuesWithKeys = array(); 	// array with list values and keys that represents the internal value

				foreach($arrListValues as $key => &$listValue)
				{
					if($this->dbColumns['usf_type'] == 'RADIO_BUTTON')
					{
						// if value is imagefile or imageurl then show image
						if(strpos(admStrToLower($listValue), '.png') > 0 || strpos(admStrToLower($listValue), '.jpg') > 0)
						{
							// if there is imagefile and text separated by | then explode them
							if(strpos($listValue, '|') > 0)
							{
								$listValueImage = substr($listValue, 0, strpos($listValue, '|'));
								$listValueText  = substr($listValue, strpos($listValue, '|') + 1);
							}
							else
							{
								$listValueImage = $listValue;
								$listValueText  = $this->getValue('usf_name');
							}
							
							// if text is a translation-id then translate it
							if(strpos($listValueText, '_') == 3)
							{
								$listValueText = $gL10n->get(admStrToUpper($listValueText));
							}

							if($format == 'text')
							{
								// if no image is wanted then return the text part or only the position of the entry
								if(strpos($listValue, '|') > 0)
								{
									$listValue = $listValueText;
								}
								else
								{
									$listValue = $key + 1;
								}
							}
							else
							{
								// create html for optionbox entry
								if(isValidFileName($listValueImage, true))
								{
									$listValue = '<img src="'.THEME_PATH.'/icons/'.$listValueImage.'" title="'.$listValueText.'" alt="'.$listValueText.'" />';
								}
								elseif(strpos(admStrToLower($listValueImage), 'http') == 0 && strValidCharacters($listValueImage, 'url'))
								{
									$listValue = '<img src="'.$listValueImage.'" title="'.$listValueText.'" alt="'.$listValueText.'" />';
								}
							}
						}
					}

					// if text is a translation-id then translate it
					if(strpos($listValue, '_') == 3)
					{
						$listValue = $gL10n->get(admStrToUpper($listValue));
					}

					// save values in new array that starts with key = 1
					$arrListValuesWithKeys[++$key] = $listValue;
				}
				$value = $arrListValuesWithKeys;
			}
		}
		elseif($columnName == 'usf_icon' && $format != 'plain')
		{
			// if value is imagefile or imageurl then show image
			if(strpos(admStrToLower($value), '.png') > 0 || strpos(admStrToLower($value), '.jpg') > 0)
			{
				// create html for icon
				if(isValidFileName($value, true))
				{
					$value = '<img src="'.THEME_PATH.'/icons/'.$value.'" style="vertical-align: middle;" title="'.$this->getValue('usf_name').'" alt="'.$this->getValue('usf_name').'" />';
				}
				elseif(strpos(admStrToLower($value), 'http') == 0 && strValidCharacters($value, 'url'))
				{
					$value = '<img src="'.$value.'" style="vertical-align: middle;" title="'.$this->getValue('usf_name').'" alt="'.$this->getValue('usf_name').'" />';
				}
			}

		}

        return $value;
    }
    
    // das Feld wird um eine Position in der Reihenfolge verschoben
    public function moveSequence($mode)
    {
        global $gCurrentOrganization;

        // die Kategorie wird um eine Nummer gesenkt und wird somit in der Liste weiter nach oben geschoben
        if(admStrToUpper($mode) == 'UP')
        {
            $sql = 'UPDATE '. TBL_USER_FIELDS. ' SET usf_sequence = '.$this->getValue('usf_sequence').'
                     WHERE usf_cat_id   = '.$this->getValue('usf_cat_id').'
                       AND usf_sequence = '.$this->getValue('usf_sequence').' - 1 ';
            $this->db->query($sql);
            $this->setValue('usf_sequence', $this->getValue('usf_sequence')-1);
            $this->save();
        }
        // die Kategorie wird um eine Nummer erhoeht und wird somit in der Liste weiter nach unten geschoben
        elseif(admStrToUpper($mode) == 'DOWN')
        {
            $sql = 'UPDATE '. TBL_USER_FIELDS. ' SET usf_sequence = '.$this->getValue('usf_sequence').'
                     WHERE usf_cat_id   = '.$this->getValue('usf_cat_id').'
                       AND usf_sequence = '.$this->getValue('usf_sequence').' + 1 ';
            $this->db->query($sql);
            $this->setValue('usf_sequence', $this->getValue('usf_sequence')+1);
            $this->save();
        }
    }

	/** Save all changed columns of the recordset in table of database. Therefore the class remembers if it's 
	 *  a new record or if only an update is neccessary. The update statement will only update
	 *  the changed columns. If the table has columns for creator or editor than these column
	 *  with their timestamp will be updated.
	 *  For new records the name intern will be set per default.
	 *  @param $updateFingerPrint Default @b true. Will update the creator or editor of the recordset if table has columns like @b usr_id_create or @b usr_id_changed
	 */
    public function save($updateFingerPrint = true)
    {
        global $gCurrentSession;
        $fields_changed = $this->columnsValueChanged;
        
		// if new field than generate new name intern, otherwise no change will be made
		if($this->new_record == true)
        {
            $this->setValue('usf_name_intern', $this->getNewNameIntern($this->getValue('usf_name', 'plain'), 1));
        }
        
        parent::save($updateFingerPrint);
        
        if($fields_changed && is_object($gCurrentSession))
        {
            // einlesen aller Userobjekte der angemeldeten User anstossen, 
            // da Aenderungen in den Profilfeldern vorgenommen wurden 
            $gCurrentSession->renewUserObject();
        }
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
        // name, category and type couldn't be edited if it's a system field
        if(($columnName == 'usf_name' || $columnName == 'usf_cat_id' || $columnName == 'usf_type')
		&& $this->getValue('usf_system') == 1)
        {
            return false;
        }
        elseif($columnName == 'usf_cat_id'
        && $this->getValue($columnName) != $newValue)
        {
            // erst einmal die hoechste Reihenfolgennummer der Kategorie ermitteln
            $sql = 'SELECT COUNT(*) as count FROM '. TBL_USER_FIELDS. '
                     WHERE usf_cat_id = '.$newValue;
            $this->db->query($sql);

            $row = $this->db->fetch_array();

            $this->setValue('usf_sequence', $row['count'] + 1);
        }
        elseif($columnName == 'usf_description')
        {
            return parent::setValue($columnName, $newValue, false);
        }
        elseif($columnName == 'usf_url' && strlen($newValue) > 0)
		{
			// Homepage darf nur gueltige Zeichen enthalten
			if (!strValidCharacters($newValue, 'url'))
			{
				return false;
			}
			// Homepage noch mit http vorbelegen
			if(strpos(admStrToLower($newValue), 'http://')  === false
			&& strpos(admStrToLower($newValue), 'https://') === false )
			{
				$newValue = 'http://'. $newValue;
			}
		}
		
        return parent::setValue($columnName, $newValue, $checkValue);
    }
}
?>