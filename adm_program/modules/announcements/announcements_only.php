<?php
/******************************************************************************
 * Show a list of all announcements
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * start     - Position of query recordset where the visual output should start
 * headline  - Ueberschrift, die ueber den Ankuendigungen steht
 *             (Default) Ankuendigungen
 * id        - Nur eine einzige Annkuendigung anzeigen lassen.
 * date      - Alle Ankuendigungen zu einem Datum werden aufgelistet
 *             Uebergabeformat: YYYYMMDD
 *
 *****************************************************************************/
  
require_once(SERVER_PATH. '/adm_program/system/common.php');
require_once(SERVER_PATH. '/adm_program/system/classes/table_announcement.php');
require_once(SERVER_PATH. '/adm_program/system/classes/module_announcements.php');
require_once(SERVER_PATH. '/adm_program/system/classes/module_menu.php');
unset($_SESSION['announcements_request']);

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_announcements_module'] == 0)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}
elseif($gPreferences['enable_announcements_module'] == 2)
{
    // nur eingeloggte Benutzer duerfen auf das Modul zugreifen
    require(SERVER_PATH. '/adm_program/system/login_valid.php');
}

// Initialize and check the parameters
$getStart    = admFuncVariableIsValid($_GET, 'start', 'numeric', 0);
$getHeadline = admFuncVariableIsValid($_GET, 'headline', 'string', $gL10n->get('ANN_ANNOUNCEMENTS'));
$getAnnId    = admFuncVariableIsValid($_GET, 'id', 'numeric', 0);
$getDate     = admFuncVariableIsValid($_GET, 'date', 'numeric');

if(strlen($getDate) > 0)
{
  $getDate = substr($getDate,0,4). '-'. substr($getDate,4,2). '-'. substr($getDate,6,2);
}

// Navigation faengt hier im Modul an
$gNavigation->clear();
$gNavigation->addUrl(CURRENT_URL);

// create objects to manage the selected announcements
$announcements = new ModuleAnnouncements($getAnnId, $getDate);
$announcementsCount = $announcements->getAnnouncementsCount();

// number of announcements per page
if($gPreferences['announcements_per_page'] > 0)
{
    $announcementsPerPage = $gPreferences['announcements_per_page'];
}
else
{
    $announcementsPerPage = $announcementsCount;
}
  
if($announcementsCount == 0)
{
  // no announcements found
  echo '';
}
else
{
  echo '<div class="formLayout" id="announcements_list_form">
          <div class="formHead4Announce">Aktuelle Ank&uuml;ndigungen</div>
            <div class="formBody4Announce">
              <ul class="formFieldList">
                <li id="lmenu_announcements_announcements">
                  <dl>
                   <dt>&nbsp;</dt>
                   <dd style="margin-left: 0;">';
  
    $announcementsArray = $announcements->getAnnouncements($getStart, $announcementsPerPage);    
    $announcement = new TableAnnouncement($gDb);

    // show all announcements
    foreach($announcementsArray['announcements'] as $row)
    {
        $announcement->clear();
        $announcement->setArray($row);
        echo '
        <div class="boxLayout4Announce" id="ann_'.$announcement->getValue("ann_id").'">
            <div class="boxHead4Announce">
                <div class="boxHeadLeft">
                    <img src="'. THEME_PATH. '/icons/announcements.png" alt="'. $announcement->getValue("ann_headline"). '" />'.
                    $announcement->getValue("ann_headline"). '
                </div>
                <div class="boxHeadRight">'.$announcement->getValue("ann_timestamp_create", $gPreferences['system_date']).'&nbsp;';
                    
                echo '</div>
            </div>

            <div class="boxBody4Announce">'.
                $announcement->getValue('ann_description').'
            </div>
        </div>';
    }  // Ende While-Schleife
    
    // If neccessary show links to navigate to next and previous recordsets of the query
    $base_url = $g_root_path.'/adm_program/modules/announcements/announcements.php?headline='.$getHeadline;
    echo admFuncGeneratePagination($base_url, $announcementsCount, $announcementsPerPage, $getStart, TRUE);
    
    echo '    </dd>
             </dl>
            </li>
           </ul>
          </div></div>';
}
  
?>