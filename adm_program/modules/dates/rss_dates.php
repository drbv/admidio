<?php
/******************************************************************************
 * RSS feed of events
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Erzeugt einen RSS 2.0 - Feed mit Hilfe der RSS-Klasse fuer die 10 naechsten Termine
 *
 * Spezifikation von RSS 2.0: http://www.feedvalidator.org/docs/rss2.html
 *
 * Parameters:
 *
 * headline  - Ueberschrift fuer den RSS-Feed
 *             (Default) Termine
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/classes/rss.php');
require_once('../../system/classes/table_date.php');
require_once('../../system/classes/module_dates.php');

// Nachschauen ob RSS ueberhaupt aktiviert ist bzw. das Modul oeffentlich zugaenglich ist
if ($gPreferences['enable_rss'] != 1)
{
    $gMessage->setForwardUrl($gHomepage);
    $gMessage->show($gL10n->get('SYS_RSS_DISABLED'));
}

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_dates_module'] != 1)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}

// Initialize and check the parameters
$getHeadline = admFuncVariableIsValid($_GET, 'headline', 'string', $gL10n->get('DAT_DATES'));


//create Object
$dates = new ModuleDates();

// read events for output
$datesResult = $dates->getDates(0, 10);

// ab hier wird der RSS-Feed zusammengestellt

// create RSS feed object with channel information
$rss  = new RSSfeed($gCurrentOrganization->getValue('org_longname'). ' - '. $getHeadline, 
            $gCurrentOrganization->getValue('org_homepage'), 
            $gL10n->get('DAT_CURRENT_DATES_OF_ORGA', $gCurrentOrganization->getValue('org_longname')),
            $gCurrentOrganization->getValue('org_longname'));
$date = new TableDate($gDb);

// Dem RSSfeed-Objekt jetzt die RSSitems zusammenstellen und hinzufuegen
if($datesResult['numResults'] > 0)
{
    $date = new TableDate($gDb);
    foreach($datesResult['dates'] as $row)
    {

        // ausgelesene Termindaten in Date-Objekt schieben
        $date->clear();
        $date->setArray($row);
    
    	// set data for attributes of this entry
        $title = $date->getValue('dat_begin', $gPreferences['system_date']);
        if($date->getValue('dat_begin', $gPreferences['system_date']) != $date->getValue('dat_end', $gPreferences['system_date']))
        {
            $title = $title. ' - '. $date->getValue('dat_end', $gPreferences['system_date']);
        }
        $title  	 = $title. ' '. $date->getValue('dat_headline');
        $link   	 = $g_root_path.'/adm_program/modules/dates/dates.php?id='. $date->getValue('dat_id');
        $author 	 = $row['create_name'];
        $pubDate 	 = date('r',strtotime($date->getValue('dat_timestamp_create')));
    	
    	// add additional informations about the event to the description
    	$descDateTo   = '';
    	$descDateFrom = $date->getValue('dat_begin', $gPreferences['system_date']);
    
        if ($date->getValue('dat_all_day') == 0)
        {
    		$descDateFrom = $descDateFrom. ' '. $date->getValue('dat_begin', $gPreferences['system_time']).' '.$gL10n->get('SYS_CLOCK');
            
            if($date->getValue('dat_begin', $gPreferences['system_date']) != $date->getValue('dat_end', $gPreferences['system_date']))
            {
                $descDateTo = $date->getValue('dat_end', $gPreferences['system_date']). ' ';
            }
            $descDateTo  = $descDateTo. ' '. $date->getValue('dat_end', $gPreferences['system_time']). ' '.$gL10n->get('SYS_CLOCK');
    		$description = $gL10n->get('SYS_DATE_FROM_TO', $descDateFrom, $descDateTo);
        }
        else
        {
            if($date->getValue('dat_begin', $gPreferences['system_date']) != $date->getValue('dat_end', $gPreferences['system_date']))
            {
                $description = $gL10n->get('SYS_DATE_FROM_TO', $descDateFrom, $date->getValue('dat_end', $gPreferences['system_date']));
            }
        }
    
        if ($date->getValue('dat_location') != '')
        {
            $description = $description. '<br /><br />'.$gL10n->get('DAT_LOCATION').':&nbsp;'. $date->getValue('dat_location');
        }
    
        $description = $description. '<br /><br />'. $date->getValue('dat_description');
    
        //i-cal downloadlink
        $description = $description. '<br /><br /><a href="'.$g_root_path.'/adm_program/modules/dates/dates_function.php?dat_id='.$date->getValue('dat_id').'&mode=6">'.$gL10n->get('DAT_ADD_DATE_TO_CALENDAR').'</a>';
    
        // add entry to RSS feed
        $rss->addItem($title, $description, $link, $author, $pubDate);
    }
}
// jetzt nur noch den Feed generieren lassen
$rss->buildFeed();

?>