<?php
/******************************************************************************
 * Database interface to MySQL database
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
 
require_once(SERVER_PATH. '/adm_program/system/db/db_common.php');
require_once(SERVER_PATH. '/adm_program/system/drbv_funktionen.php');
 
class DBMySQL extends DBCommon
{
    // create database connection  
    public function connect($sql_server, $sql_user, $sql_password, $sql_dbName, $new_connection = false)
    {
        $this->dbType   = 'mysql';
        $this->server    = $sql_server;
        $this->user      = $sql_user;
        $this->password  = $sql_password;
        $this->dbName    = $sql_dbName;
        
        //$this->connectId = @mysqli_connect($this->server, $this->user, $this->password, $new_connection);
        //$this->connectId = @mysqli_connect($this->server, $this->user, $this->password, $this->dbName, $new_connection);
        $this->connectId = @mysqli_connect($this->server, $this->user, $this->password, $this->dbName, $new_connection);
        
        if($this->connectId)
        {
            //if (@mysqli_select_db($this->dbName, $this->connectId))
            //if (@mysqli_select_db($this->connectId))
            //{
				// Verbindung zur DB in UTF8 aufbauen
                @mysqli_query($this->connectId, 'SET NAMES \'utf8\'');

                // ANSI Modus setzen, damit SQL kompatibler zu anderen DBs werden kann
                @mysqli_query($this->connectId, 'SET SQL_MODE = \'ANSI\'');
                
                // falls der Server die Joins begrenzt hat, kann dies mit diesem Statement aufgehoben werden
                @mysqli_query($this->connectId, 'SET SQL_BIG_SELECTS = 1');

                return $this->connectId;
            //}
        }
        return false;
    }

    // Bewegt den internen Ergebnis-Zeiger
    public function data_seek($result, $rowNumber)
    {
        return mysqli_data_seek($result, $rowNumber);
    }   
    
    // Uebergibt Fehlernummer und Beschreibung an die uebergeordnete Fehlerbehandlung
    public function db_error($code = 0, $message = '')
    {
        if($code == 0)
        {
            if (!$this->connectId)
            {
                parent::db_error(@mysqli_errno(), @mysqli_error());
            }
            else
            {
                parent::db_error(@mysqli_errno($this->connectId), @mysqli_error($this->connectId));
            }
        }
        else
        {
            parent::db_error($code, $message);
        }
    }

    // Escaped den mysql String
    public function escape_string($string)
    {
        return mysqli_real_escape_string(DRBVdb(), $string);
    }

    // Gibt den Speicher für den Result wieder frei
    public function fetch_assoc($result)
    {
        if($result === false)
        {
            $result = $this->queryResult;
        }
        return mysqli_fetch_assoc($result);
    }

    /** Fetch a result row as an associative array, a numeric array, or both.
     *  @param $result     The result resource that is being evaluated. This result comes from a call to query().
     *  @param $resultType Set the result type. Can contain @b ASSOC for an associative array, 
     *                     @b NUM for a numeric array or @b BOTH (Default).
     *  @return Returns an array that corresponds to the fetched row and moves the internal data pointer ahead. 
     */
    public function fetch_array($result = false, $resultType = 'BOTH')
    {
        $typeArray = array('BOTH' => MYSQLI_BOTH, 'ASSOC' => MYSQLI_ASSOC, 'NUM' => MYSQLI_NUM);

        if($result === false)
        {
            $result = $this->queryResult;
        }
        
        return mysqli_fetch_array($result, $typeArray[$resultType]);
    }

    public function fetch_object($result = false)
    {
        if($result === false)
        {
            $result = $this->queryResult;
        }
        
        return mysqli_fetch_object($result);
    }

    // Liefert den Namen eines Feldes in einem Ergebnis
    public function field_name($result, $index)
    {
       $colObj = mysqli_fetch_field_direct($result,$index);                            
       $col    = $colObj->name;
       //return mysql_field_name($result, $index);
       return $col;
    }

    // Gibt den Speicher für den Result wieder frei
    public function free_result($result)
    {
        return mysqli_free_result($result);
    }

    // Liefert die ID einer vorherigen INSERT-Operation
    public function insert_id()
    {
        return mysqli_insert_id($this->connectId);
    }
    
    // Liefert die Anzahl der Felder in einem Ergebnis
    public function num_fields($result = false)
    {
        if($result === false)
        {
            $result = $this->queryResult;
        }
        
        return mysqli_num_fields($result);
    }
    
    // Liefert die Anzahl der Datensaetze im Ergebnis
    public function num_rows($result = false)
    {
        if($result === false)
        {
            $result = $this->queryResult;
        }
        
        return mysqli_num_rows($result);
    }    
    
	// send sql to database server 
	// sql        : sql statement that should be executed
	// throwError : show error of sql statement and stop current script
    public function query($sql, $throwError = true)
    {
        global $gDebug;
        
        // if debug mode then log all sql statements
        if($gDebug == 1)
        {
            error_log($sql);
        }

        $this->queryResult = mysqli_query($this->connectId,$sql);

        if($this->queryResult == false && $throwError == true)
        {
            return $this->db_error();
        }

        return $this->queryResult;
    }

    // setzt die urspruengliche DB wieder auf aktiv
    // alternativ kann auch eine andere DB uebergeben werden
    public function select_db($database = '')
    {
        if(strlen($database) == 0)
        {
            $database = $this->dbName;
        }
        //return mysqli_select_db($database, $this->connectId);//BOZO geht so nicht mehr
        return mysqli_select_db($this->connectId, $database);
    }

    // returns the MySQL version of the database
    public function server_info()
    {
      return mysqli_get_server_info();
    }

    // setzt die urspruengliche DB wieder auf aktiv
    public function setCurrentDB()
    {
        return $this->select_db($this->dbName);
    }  

	// this method sets db specific properties of admidio
	// this settings are necessary because the database won't work with the default settings of admidio
	public function setDBSpecificAdmidioProperties($version = '')
	{
		// nothing todo
	}

	// This method delivers the columns and their properties of the passed variable as an array
	// The array has the following format:
	// array('Fieldname1' => array('serial' => '1', 'null' => '0', 'key' => '0', 'type' => 'integer'), 
	//       'Fieldname2' => ...)
    public function showColumns($table)
    {
		if(isset($this->dbStructure[$table]) == false)
		{
			$columnProperties = array();

			$sql = 'SHOW COLUMNS FROM '.$table;
			$this->query($sql);
			
			while ($row = $this->fetch_array())
			{
				$columnProperties[$row['Field']]['serial'] = 0;
				$columnProperties[$row['Field']]['null']   = 0;
				$columnProperties[$row['Field']]['key']    = 0;
				
				if($row['Extra'] == 'auto_increment')
				{
					$columnProperties[$row['Field']]['serial'] = 1;
				}
				if($row['Null'] == 'YES')
				{
					$columnProperties[$row['Field']]['null']   = 1;
				}
				if($row['Key'] == 'PRI' || $row['Key'] == 'MUL')
				{
					$columnProperties[$row['Field']]['key'] = 1;
				}

				if(strpos($row['Type'], 'tinyint(1)') !== false)
				{
					$columnProperties[$row['Field']]['type'] = 'boolean';
				}
				elseif(strpos($row['Type'], 'smallint') !== false)
				{
					$columnProperties[$row['Field']]['type'] = 'smallint';
				}
				elseif(strpos($row['Type'], 'int') !== false)
				{
					$columnProperties[$row['Field']]['type'] = 'integer';
				}
				else
				{
					$columnProperties[$row['Field']]['type'] = $row['Type'];
				}
			}
			
			// safe array with table structure in class array
			$this->dbStructure[$table] = $columnProperties;
		}
		
		return $this->dbStructure[$table];
    }  
}
 
?>