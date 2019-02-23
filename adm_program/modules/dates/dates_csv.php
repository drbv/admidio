<?php
/******************************************************************************
 * Show a list of all events
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * mode: actual       - (Default) shows actual dates and all events in future
 *       old          - shows events in the past
 *       period       - shows all events in a specified period (date_from/date_to)
 *       day          - shows all events of a specified day (date_from)
 *       all          - shows all events in past and future
 * start              - Position of query recordset where the visual output should start
 * headline           - Headline shown over events
 *                      (Default) Dates
 * cat_id             - show all events of calendar with this id
 * id                 - Show only one event
 * date               - All events for a date are listed
 *                      Format: YYYYMMDD
 * calendar-selection - 1: The box is shown
 *                      0: The box is not shown
 *
 * date_from          - is set to actual date,
 *                      if no date information is delivered
 * date_to            - is set to 31.12.9999,
 *                      if no date information is delivered
 * view_mode          - content output in 'html', 'compact' or 'print' view
 *                      (Default: according to preferences)
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/drbv_funktionen.php');
require_once('../../system/classes/form_elements.php');
require_once('../../system/classes/module_dates.php');
require_once('../../system/classes/module_menu.php');
require_once('../../system/classes/participants.php');
require_once('../../system/classes/table_category.php');
require_once('../../system/classes/table_date.php');
require_once('../../system/classes/table_rooms.php');

unset($_SESSION['dates_request']);

$separator   = ";";
$valueQuotes = '"';

$datei = $valueQuotes . $gL10n->get('SYS_START') . $valueQuotes . $separator
       . $valueQuotes . $gL10n->get('SYS_END') . $valueQuotes . $separator 
       . $valueQuotes . $gL10n->get('DAT_DATE') . $valueQuotes . $separator    
       . $valueQuotes . $gL10n->get('SYS_LOCATION') . $valueQuotes . $separator
       . $valueQuotes . "Verein" . $valueQuotes . $separator       
       . $valueQuotes . "Ansprechpartner" . $valueQuotes . $separator
       . $valueQuotes . "E-Mail" . $valueQuotes . $separator
       . $valueQuotes . "Startklassen" . $valueQuotes . $separator
       . $valueQuotes . "Kalender" . $valueQuotes . "\n"; 


// create object
$dates = new ModuleDates();

// Initialize and check the parameters
$getMode     = admFuncVariableIsValid($_GET, 'mode', 'string', 'actual', false, $dates->getModes());
$getStart    = admFuncVariableIsValid($_GET, 'start', 'numeric', 0);
$getHeadline = admFuncVariableIsValid($_GET, 'headline', 'string', $gL10n->get('DAT_DATES'));
$getDateId   = admFuncVariableIsValid($_GET, 'id', 'numeric', 0);
$getDate     = admFuncVariableIsValid($_GET, 'date', 'numeric');
$getCatId    = admFuncVariableIsValid($_GET, 'cat_id', 'numeric', 0);
$getCalendarSelection = admFuncVariableIsValid($_GET, 'calendar-selection', 'boolean', $gPreferences['dates_show_calendar_select']);
$getDateFrom = admFuncVariableIsValid($_GET, 'date_from', 'date', DATE_NOW, false);
$getDateTo   = admFuncVariableIsValid($_GET, 'date_to', 'date', '9999-12-31', false);
$getViewMode = admFuncVariableIsValid($_GET, 'view_mode', 'string', $gPreferences['dates_viewmode'], false, $dates->getViewModes());

// if exact date is set then convert it to our new syntax with dateFrom and dateTo
if(strlen($getDate) > 0)
{
    $getDateFrom = substr($getDate,0,4). '-'. substr($getDate,4,2). '-'. substr($getDate,6,2);
    $getDateTo   = $getDateFrom;
}

//autoset mode
if($getMode=='actual')
{
    if($getDateFrom==$getDateTo)
    {
        $getMode='day';
    }
    elseif($getDateFrom!=DATE_NOW && $getDateTo!='9999-12-31')
    {
        $getMode='period';
    }
}

//select dates
if($getDateId > 0)
{
    $dates->setDateId($getDateId);
}
else
{
    $dates->setMode($getMode, $getDateFrom, $getDateTo);
    
    if($getCatId > 0)
    {
        $dates->setCatId($getCatId);
    }   
}

//Convert dates to system format
$objDate = new DateTimeExtended($dates->getDateFrom(), 'Y-m-d', 'date');
$dateFromSystemFormat = $objDate->format($gPreferences['system_date']);

$objDate = new DateTimeExtended($dates->getDateTo(), 'Y-m-d', 'date');
$dateToSystemFormat = $objDate->format($gPreferences['system_date']);

// get headline of dates relative to date values
$htmlHeadline = $dates->getHeadline($getHeadline, $getDateFrom, $getDateTo);

// Fill input fields only if user values exist
$dateFromHtmlOutput = $dates->getFormValue($getDateFrom, DATE_NOW);
$dateToHtmlOutput = $dates->getFormValue($getDateTo, '9999-12-31');

if($getCatId > 0)
{
    $calendar = new TableCategory($gDb, $getCatId);
}

// Navigation starts here
if($getDateId  == 0 || $getViewMode == 'compact' && $getDateId > 0)
{
    $gNavigation->clear();
    $gNavigation->addUrl(CURRENT_URL);
}

$dates_per_page = 200;

// read all events for output
$datesResult = $dates->getDates($getStart, $dates_per_page);

if($datesResult['totalCount'] != 0)
{   
    // Initialize counter and object instances
    $count = 0;
    $date = new TableDate($gDb);
    $participants = new Participants ($gDb);
    
    // New array for the participants of a date
    $memberElements = array();
    
} 
      
    // List events
    if($datesResult['numResults'] > 0)
    {                
        foreach($datesResult['dates'] as $row)
        {
            // Initialize object and write new data
            $date->readDataById($row['dat_id']);

            $endDate='';
            if($date->getValue('dat_begin', $gPreferences['system_date']) != $date->getValue('dat_end', $gPreferences['system_date']))
            {
                $endDate=$date->getValue('dat_end', $gPreferences['system_date']);
            }

            // Initialize variables
            $registerIcon='';
            $registerText='';
            $participantIcon='';
            $participantText='';
            $participantIcon4turnierleiter='';
            $participantText4turnierleiter='';           
            $mgrpartIcon='';
            $mgrpartText='';
            $dateElements = array();
            $firstElement = true;
            $maxMembers='';
            $numMembers='';
            $leadersHtml='';
            $locationHtml='';
            $drbv_locationHtml='';
                
             
         //print only if cat_id is Turnierkalender National(id=31) or International(id=35)
         if ($date->getValue('dat_cat_id') == 31 or $date->getValue('dat_cat_id') == 35)
          {
           if ($date->getValue('dat_sk_s') == 1 || $date->getValue('dat_sk_j') == 1 || $date->getValue('dat_sk_c') == 1 || $date->getValue('dat_sk_b') == 1 || $date->getValue('dat_sk_a') == 1)
             $startklassen .= "Rock'n'Roll: ";
             if ($date->getValue('dat_sk_s') == 1) {$startklassen .='S';}
             if ($date->getValue('dat_sk_j') == 1) {$startklassen .=' J';}
             if ($date->getValue('dat_sk_c') == 1) {$startklassen .=' C';}
             if ($date->getValue('dat_sk_b') == 1) {$startklassen .=' B';}
             if ($date->getValue('dat_sk_a') == 1) {$startklassen .=' A';}
               
           if ($date->getValue('dat_sk_bwh') == 1 || $date->getValue('dat_sk_bwo') == 1 || $date->getValue('dat_sk_bwj') == 1) 
             $startklassen .= ' Boogie-Woogie: ';
             if ($date->getValue('dat_sk_bwh') == 1) {$startklassen .='Haupt';}
             if ($date->getValue('dat_sk_bwo') == 1) {$startklassen .=' Oldie';}
             if ($date->getValue('dat_sk_bwj') == 1) {$startklassen .=' Jugend';} 

           if ($date->getValue('dat_sk_frm') == 1 || $date->getValue('dat_sk_frl') == 1 || $date->getValue('dat_sk_frg') == 1 || $date->getValue('dat_sk_frj') == 1 || $date->getValue('dat_sk_frs') == 1 || $date->getValue('dat_sk_fbm') == 1)                    
             $startklassen .= ' Formationen: ';
             if ($date->getValue('dat_sk_frm') == 1) {$startklassen .='Master RR';}
             if ($date->getValue('dat_sk_frl') == 1) {$startklassen .=' Lady RR';}
             if ($date->getValue('dat_sk_frg') == 1) {$startklassen .=' Girl RR';}
             if ($date->getValue('dat_sk_frj') == 1) {$startklassen .=' Jugend RR';}
             if ($date->getValue('dat_sk_frs') == 1) {$startklassen .=' Showteam RR';}      
             if ($date->getValue('dat_sk_fbm') == 1) {$startklassen .=' Master BW';}        
    
             if ($date->getValue('dat_sk_bsp') == 1) 
             {
              $startklassen .= ' Breitensport';
             } 
          }             
                   
// Daten schhreiben

// Beginn          
$datei .= $valueQuotes . $date->getValue('dat_begin', $gPreferences['system_date']) . $valueQuotes . $separator;
// Ende
if($date->getValue('dat_end', $gPreferences['system_date']) != $date->getValue('dat_begin', $gPreferences['system_date']))          
   $datei .= $valueQuotes . $date->getValue('dat_end', $gPreferences['system_date']) . $valueQuotes . $separator;
else
   $datei .= $valueQuotes . $valueQuotes . $separator;
// Veranstaltung
$datei .= $valueQuotes . $date->getValue('dat_headline') . $valueQuotes . $separator;
// Ort
$datei .= $valueQuotes . $date->getValue('dat_location') . $valueQuotes . $separator;
// Verein
$datei .= $valueQuotes . $date->getValue('dat_verein') . $valueQuotes . $separator;
// Ansprechpartner
$datei .= $valueQuotes . $date->getValue('dat_ansprechpartner') . $valueQuotes . $separator;
// E-Mail
$datei .= $valueQuotes . $date->getValue('dat_mail') . $valueQuotes . $separator;
// Startklassen
$datei .= $valueQuotes . $startklassen . $valueQuotes . $separator;
// Kalender
$datei .= $valueQuotes . $date->getValue('cat_name') . $valueQuotes;

$datei .= "\n"; 

unset($startklassen);
    }

}

// echo "<p><h1>Inhalt</h1></p>";

   $filename = 'DRBV_Kalender.csv';
   // for IE the filename must have special chars in hexadecimal 
   if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
   {
   $filename = urlencode($filename);
   }
   
   header('Content-Type: text/comma-separated-values; charset='.$charset);
   header('Content-Disposition: attachment; filename="'.$filename.'"');
    
   // neccessary for IE6 to 8, because without it the download with SSL has problems
   header('Cache-Control: private');
   header('Pragma: public');

   echo utf8_decode($datei);  
?>