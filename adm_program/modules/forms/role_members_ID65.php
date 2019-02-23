<?php
/******************************************************************************
 * Show role members list
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * mode   : Ausgabeart   (html, print, csv-ms, csv-oo, pdf, pdfl)
 * lst_id : ID der Listenkonfiguration, die angezeigt werden soll
 *          Wird keine ID uebergeben, wird die Default-Konfiguration angezeigt
 * rol_id : Rolle, fuer die die Funktion dargestellt werden soll
 * start  : Position of query recordset where the visual output should start
 * show_members : 0 - (Default) show active members of role
 *                1 - show former members of role
 *                2 - show active and former members of role
 *
 *****************************************************************************/
 
// Initialize and check the parameters
$getMode        = 'csv';
$getListId      = 65; //Kontaktdaten Startbuch Formationen
if($gCurrentUser->isWebmaster()){  
  //$getRoleId    = 17;  //1_Dresdner_RRC, overwrite here if testing is needed
  //$getRoleId    = 22;  //KWH
}  
$getStart       = 0;   //Start bei 1 heist ohne Leiter (0) bei Annahme nur 1 Leiter pro Vereinsrolle
$getShowMembers = 0;   //Alle aktiven Members einer Rolle

// Initialize the content of this parameter (otherwise some servers will keep the content)
unset($role_ids);
  
if($getRoleId > 0)
{
  $role_ids[] = $getRoleId;
}
else
{
  $role_ids = $_SESSION['role_ids'];
  $getRoleId = $role_ids[0];
}

// Rollenobjekt erzeugen
$role = new TableRoles($gDb, $getRoleId);

//Testen ob Recht zur Listeneinsicht besteht
if($role->viewRole() == false)
{
  $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

$valueQuotes = '';
$separator   = ';'; 
$valueQuotes = '';

// Array um den Namen der Tabellen sinnvolle Texte zuzuweisen
$arr_col_name = array('usr_login_name' => $gL10n->get('SYS_USERNAME'),
                      'usr_photo'      => $gL10n->get('PHO_PHOTO'),
                      'mem_begin'      => $gL10n->get('SYS_START'),
                      'mem_end'        => $gL10n->get('SYS_END'),
                      'mem_leader'     => $gL10n->get('SYS_LEADER')
                      );

$mainSql      = '';   // enthaelt das Haupt-Sql-Statement fuer die Liste
//$str_csv    = '';   // enthaelt die komplette CSV-Datei als String
$leiter       = 0;    // Gruppe besitzt Leiter
$memberStatus = '';

try
{
  // create list configuration object and create a sql statement out of it
  $list = new ListConfiguration($gDb, $getListId);
  $mainSql = $list->getSQL($role_ids, $getShowMembers);
  //echo $mainSql; exit();  
}
catch(AdmException $e)
{
  $e->showHtml();
}

// SQL-Statement der Liste ausfuehren und pruefen ob Daten vorhanden sind
$resultList = $gDb->query($mainSql);
$numMembers = $gDb->num_rows($resultList);
$numMembersWithoutLeiter = $numMembers - $getStart;  

if($numMembers == 0)
{
  // Es sind keine Daten vorhanden !
  $gMessage->show($gL10n->get('LST_NO_USER_FOUND'));
}

//$str_csv = $str_csv. "\n";
//$listRowNumber = $getStart;
$member_form_array = array(array());  

for($j = 0; $j < $numMembers && $j + $getStart < $numMembers; $j++)
{
    
    if($row = $gDb->fetch_array($resultList))
    {
      if($row[mem_leader]==0){  
        // print_r($row);echo' :DEBUG::$row<br>';        

        // Felder zu Datensatz
        for($column_number = 1; $column_number <= $list->countColumns(); $column_number++)
        {
            $column = $list->getColumnObject($column_number);

            // da im SQL noch mem_leader und usr_id vor die eigentlichen Spalten kommen,
            // muss der Index auf row direkt mit 2 anfangen
            $sql_column_number = $column_number + 1;

            if($column->getValue('lsc_usf_id') > 0)
            {
                // pruefen, ob ein benutzerdefiniertes Feld und Kennzeichen merken
                $b_user_field = true;
                $usf_id = $column->getValue('lsc_usf_id');
            }
            else
            {
                $b_user_field = false;
                $usf_id = 0;
            }

            // versteckte Felder duerfen nur von Leuten mit entsprechenden Rechten gesehen werden
            if($usf_id == 0
            || $gCurrentUser->editUsers()
            || $gProfileFields->getPropertyById($usf_id, 'usf_hidden') == 0)
            {
              if($column_number == 1)
              {
                // erste Spalte zeigt lfd. Nummer an
                //$str_csv = $str_csv. $valueQuotes. $listRowNumber. $valueQuotes;
              }    
                $content  = '';
                /*****************************************************************/
                // create field content for each field type and output format
                /*****************************************************************/
                if($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'DATE'
                ||     $column->getValue('lsc_special_field') == 'mem_begin'
                ||     $column->getValue('lsc_special_field') == 'mem_end') 
                {
                    if(strlen($row[$sql_column_number]) > 0)
                    {
                        // date must be formated
                        $date = new DateTimeExtended($row[$sql_column_number], 'Y-m-d', 'date');
                        $content = $date->format($gPreferences['system_date']);
                    }
                }
                elseif( ($gProfileFields->getPropertyById($usf_id, 'usf_type') == 'DROPDOWN'
                      || $gProfileFields->getPropertyById($usf_id, 'usf_type') == 'RADIO_BUTTON') 
                && $getMode == 'csv')
                {
                    if(strlen($row[$sql_column_number]) > 0)
                    {
                        // show selected text of optionfield or combobox
                        $arrListValues = $gProfileFields->getPropertyById($usf_id, 'usf_value_list', 'text');
                        $content       = $arrListValues[$row[$sql_column_number]];
                    }
                }
                else 
                {
                    $content = $row[$sql_column_number];
                }
                //$str_csv = $str_csv. $separator. $valueQuotes. $content. $valueQuotes;
                $member_form_array[$j][] = $content;
                //echo var_dump($member_array);
            }
         }                 
       } //$str_csv = $str_csv. "\n";
        //$listRowNumber++;
    }
}  // End-While (jeder gefundene User)


?>