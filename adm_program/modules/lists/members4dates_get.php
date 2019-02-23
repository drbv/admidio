<?php
/******************************************************************************
 * Show list with members of a role
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * rol_id       : Rolle der Mitglieder hinzugefuegt oder entfernt werden sollen
 * mem_show_all : 0 - (Default) nur Mitglieder der Organisation anzeigen
 *                1 - alle Benutzer aus der Datenbank anzeigen
 * mem_search   : Suchstring nach dem Mitglieder angezeigt werden sollen
 *
 *****************************************************************************/
require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/table_roles.php');
require_once('../../system/classes/table_date.php');

$gMessage->setExcludeThemeBody();

// Initialize and check the parameters
$getRoleId          = admFuncVariableIsValid($_GET, 'rol_id', 'numeric', null, true);
$postMembersShowAll = admFuncVariableIsValid($_POST, 'mem_show_all', 'string', 'off');
$postMembersSearch  = admFuncVariableIsValid($_POST, 'mem_search', 'string');

// Objekt der uebergeben Rollen-ID erstellen
$role = new TableRoles($gDb, $getRoleId);

// rmenken
$startklassenlist = array();
$stklrrhtml='';
$stklbwhtml='';
$stklfrhtml='';
$stklformation=FALSE;
$warningformation=FALSE;
$warningnoakro=FALSE;  
$date = new TableDate($gDb);
$date->readDataByColumns(array('dat_rol_id' => $getRoleId));
// Dabei ist: 1=RR_S   6=F_RR_M   12=BW_A
//            2=RR_J   7=F_RR_J   13=BW_B
//            3=RR_C   8=F_RR_LF
//            4=RR_B   9=F_RR_GF
//            5=RR_A  10=F_RR_ST
//                    11=F_BW_M                       
if($date->getValue('dat_sk_s')=='1'){$startklassenlist[]='1';$stklrrhtml.=' S';}  
if($date->getValue('dat_sk_j')=='1'){$startklassenlist[]='2';$stklrrhtml.=' J';}  
if($date->getValue('dat_sk_c')=='1'){$startklassenlist[]='3';$stklrrhtml.=' C';}  
if($date->getValue('dat_sk_b')=='1'){$startklassenlist[]='4';$stklrrhtml.=' B';}  
if($date->getValue('dat_sk_a')=='1'){$startklassenlist[]='5';$stklrrhtml.=' A';}  
if($date->getValue('dat_sk_frm')=='1'){$startklassenlist[]='6';$stklfrhtml.=' RR-MA';$stklformation=TRUE;}  
if($date->getValue('dat_sk_frj')=='1'){$startklassenlist[]='7';$stklfrhtml.=' RR-JU';$stklformation=TRUE;}  
if($date->getValue('dat_sk_frl')=='1'){$startklassenlist[]='8';$stklfrhtml.=' RR-LA';$stklformation=TRUE;}  
if($date->getValue('dat_sk_frg')=='1'){$startklassenlist[]='9';$stklfrhtml.=' RR-GI';$stklformation=TRUE;}  
if($date->getValue('dat_sk_frs')=='1'){$startklassenlist[]='10';$stklfrhtml.=' RR-ST';$stklformation=TRUE;}  
if($date->getValue('dat_sk_fbm')=='1'){$startklassenlist[]='11';$stklfrhtml.=' BW-MA';$stklformation=TRUE;}  
if($date->getValue('dat_sk_bwh')=='1'){$startklassenlist[]='12';}  
if($date->getValue('dat_sk_bwo')=='1'){$startklassenlist[]='12';}  
if($date->getValue('dat_sk_bwj')=='1'){$startklassenlist[]='12';}  
if($date->getValue('dat_sk_bwh_b')=='1'){$startklassenlist[]='13';}  
if($date->getValue('dat_sk_bwo_b')=='1'){$startklassenlist[]='13';}
if(in_array(12,$startklassenlist)){$stklbwhtml.=' BW-A';}
if(in_array(13,$startklassenlist)){$stklbwhtml.=' BW-B';}
if($stklrrhtml==''){$stklrrhtml.='--';}
if($stklbwhtml==''){$stklbwhtml.='--';}
if($stklfrhtml==''){$stklfrhtml.='--';}

//Check if Qualifikation is needed DC/GPvD/DM
$quali_dcgprr = false;
$quali_dmrr   = false;  

if($date->getValue('dat_tform')=='Ranglistenturnier' && $date->getValue('dat_quali')=='1'){
  $quali_dcgprr = true;
}  
if($date->getValue('dat_tform')=='Deutsche Meisterschaft' && $date->getValue('dat_quali')=='1'){
  $quali_dmrr   = true;
}
  
// roles of other organizations can't be edited
if($role->getValue('cat_org_id') != $gCurrentOrganization->getValue('org_id') && $role->getValue('cat_org_id') > 0)
{
  $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

  $startklassen = 'OPS='.$date->getValue('dat_sk_s').$date->getValue('dat_sk_j').
    $date->getValue('dat_sk_c').$date->getValue('dat_sk_b').$date->getValue('dat_sk_a');
  
// check if user is allowed to assign members to this role
if($role->allowedToAssignMembers($gCurrentUser) == false)
{
  $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// rmenken: get current user first name
$getCurrentUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$current_user = new User($gDb, $gProfileFields, $getCurrentUserId);
$myowngroup = $current_user->getValue('FIRST_NAME');  
  
$memberCondition = '';
$limit = '';
  
if($postMembersShowAll == 'on')
{
    // Falls gefordert, aufrufen alle Benutzer aus der Datenbank
    $memberCondition = ' usr_valid = 1 ';

}
else
{
    // Falls gefordert, nur Aufruf von aktiven Mitgliedern der Organisation
    // rmenken: modified search to myowngroup
    $memberCondition = ' EXISTS
        (SELECT 1
           FROM '. TBL_MEMBERS. ', '. TBL_ROLES. ', '. TBL_CATEGORIES. '
          WHERE mem_usr_id = usr_id
            AND mem_rol_id = rol_id
            AND mem_begin <= \''.DATE_NOW.'\'
            AND mem_end    > \''.DATE_NOW.'\'
            AND rol_valid  = 1
            AND rol_cat_id = cat_id
            AND (  cat_org_id = '. $gCurrentOrganization->getValue('org_id'). '
                OR cat_org_id IS NULL )
            AND (  (UPPER(last_name.usd_value)  LIKE UPPER(\''.$myowngroup .'%\'))
                     OR (UPPER(first_name.usd_value) LIKE UPPER(\''.$myowngroup .'%\')))          
        ) ';
}

//Suchstring zerlegen
if(strlen($postMembersSearch) > 0)
{
    $postMembersSearch = str_replace('%', ' ', $postMembersSearch);
    $search_therms = explode(' ', $postMembersSearch);
  
    if(count($search_therms)>0)
    {
      //in Condition einbinden
      foreach($search_therms as $search_therm)
      {
        $memberCondition .= ' AND (  (UPPER(last_name.usd_value)  LIKE UPPER(\''.$search_therm.'%\'))
                     OR (UPPER(first_name.usd_value) LIKE UPPER(\''.$search_therm.'%\'))) ';
      }
    }
    //Ergebnissmenge Limitieren
    $limit .= ' LIMIT 30 ';
}


// SQL-Statement zusammensetzen
$sql = 'SELECT DISTINCT usr_id, last_name.usd_value as last_name, first_name.usd_value as first_name, nachname_herr.usd_value as nachname_herr, vorname_herr.usd_value as vorname_herr, nachname_dame.usd_value as nachname_dame, vorname_dame.usd_value as vorname_dame, 
               startklasse.usd_value as startklasse, teamname.usd_value as teamname, startmarke_gueltig.usd_value as startmarke_gueltig, 
               qualifiziert_dc_gp.usd_value as qualifiziert_dc_gp,
               qualifiziert_dm.usd_value as qualifiziert_dm,
               akro1vr.usd_value as akro1vr, akro2vr.usd_value as akro2vr, akro3vr.usd_value as akro3vr,
               akro4vr.usd_value as akro4vr, akro5vr.usd_value as akro5vr, akro6vr.usd_value as akro6vr,
               akro1zr.usd_value as akro1zr, akro2zr.usd_value as akro2zr, akro3zr.usd_value as akro3zr,
               akro4zr.usd_value as akro4zr, akro5zr.usd_value as akro5zr, akro6zr.usd_value as akro6zr,
               akro1er.usd_value as akro1er, akro2er.usd_value as akro2er, akro3er.usd_value as akro3er,
               akro4er.usd_value as akro4er, akro5er.usd_value as akro5er, akro6er.usd_value as akro6er,
               anzahl_aktive.usd_value as anzahl_aktive,
               musiktitel_tanzmusik.usd_value as musiktitel_tanzmusik,  
               verein.usd_value as verein, birthday.usd_value as birthday,
               city.usd_value as city, address.usd_value as address, zip_code.usd_value as zip_code, country.usd_value as country,
               mem_usr_id as member_this_role, mem_leader as leader_this_role,
                  (SELECT count(*)
                     FROM '. TBL_ROLES. ' rol2, '. TBL_CATEGORIES. ' cat2, '. TBL_MEMBERS. ' mem2
                    WHERE rol2.rol_valid   = 1
                      AND rol2.rol_cat_id  = cat2.cat_id
                      AND (  cat2.cat_org_id = '. $gCurrentOrganization->getValue('org_id'). '
                          OR cat2.cat_org_id IS NULL )
                      AND mem2.mem_rol_id  = rol2.rol_id
                      AND mem2.mem_begin  <= \''.DATE_NOW.'\'
                      AND mem2.mem_end     > \''.DATE_NOW.'\'
                      AND mem2.mem_usr_id  = usr_id) as member_this_orga
        FROM '. TBL_USERS. '
        LEFT JOIN '. TBL_USER_DATA. ' as last_name
          ON last_name.usd_usr_id = usr_id
         AND last_name.usd_usf_id = '. $gProfileFields->getProperty('LAST_NAME', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as first_name
          ON first_name.usd_usr_id = usr_id
         AND first_name.usd_usf_id = '. $gProfileFields->getProperty('FIRST_NAME', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as verein
          ON verein.usd_usr_id = usr_id
         AND verein.usd_usf_id = '. $gProfileFields->getProperty('VEREIN', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as startklasse
          ON startklasse.usd_usr_id = usr_id
         AND startklasse.usd_usf_id = '. $gProfileFields->getProperty('STARTKLASSE', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as teamname
          ON teamname.usd_usr_id = usr_id
         AND teamname.usd_usf_id = '. $gProfileFields->getProperty('TEAMNAME', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as startmarke_gueltig
          ON startmarke_gueltig.usd_usr_id = usr_id
         AND startmarke_gueltig.usd_usf_id = '. $gProfileFields->getProperty('STARTMARKE_GüLTIG', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as qualifiziert_dc_gp
           ON qualifiziert_dc_gp.usd_usr_id = usr_id
           AND qualifiziert_dc_gp.usd_usf_id = '. $gProfileFields->getProperty('QUALIFIZIERT_DC_GP', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as qualifiziert_dm
          ON qualifiziert_dm.usd_usr_id = usr_id
          AND qualifiziert_dm.usd_usf_id = '. $gProfileFields->getProperty('QUALIFIZIERT_DM', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as akro1vr
          ON akro1vr.usd_usr_id = usr_id
          AND akro1vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_1_-_VORRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro2vr
          ON akro2vr.usd_usr_id = usr_id
          AND akro2vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_2_-_VORRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro3vr
          ON akro3vr.usd_usr_id = usr_id
          AND akro3vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_3_-_VORRUNDE', 'usf_id'). '         
        LEFT JOIN '. TBL_USER_DATA. ' as akro4vr
          ON akro4vr.usd_usr_id = usr_id
          AND akro4vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_4_-_VORRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro5vr
          ON akro5vr.usd_usr_id = usr_id
          AND akro5vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_5_-_VORRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro6vr
          ON akro6vr.usd_usr_id = usr_id
          AND akro6vr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_6_-_VORRUNDE', 'usf_id'). '         
        LEFT JOIN '. TBL_USER_DATA. ' as akro1zr
          ON akro1zr.usd_usr_id = usr_id
          AND akro1zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_1_-_ZWRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro2zr
          ON akro2zr.usd_usr_id = usr_id
          AND akro2zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_2_-_ZWRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro3zr
          ON akro3zr.usd_usr_id = usr_id
          AND akro3zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_3_-_ZWRUNDE', 'usf_id'). '         
        LEFT JOIN '. TBL_USER_DATA. ' as akro4zr
          ON akro4zr.usd_usr_id = usr_id
          AND akro4zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_4_-_ZWRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro5zr
          ON akro5zr.usd_usr_id = usr_id
          AND akro5zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_5_-_ZWRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro6zr
          ON akro6zr.usd_usr_id = usr_id
          AND akro6zr.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_6_-_ZWRUNDE', 'usf_id'). '         
        LEFT JOIN '. TBL_USER_DATA. ' as akro1er
          ON akro1er.usd_usr_id = usr_id
          AND akro1er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_1_-_ENDRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro2er
          ON akro2er.usd_usr_id = usr_id
          AND akro2er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_2_-_ENDRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro3er
          ON akro3er.usd_usr_id = usr_id
          AND akro3er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_3_-_ENDRUNDE', 'usf_id'). '         
        LEFT JOIN '. TBL_USER_DATA. ' as akro4er
          ON akro4er.usd_usr_id = usr_id
          AND akro4er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_4_-_ENDRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro5er
          ON akro5er.usd_usr_id = usr_id
          AND akro5er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_5_-_ENDRUNDE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as akro6er
          ON akro6er.usd_usr_id = usr_id
          AND akro6er.usd_usf_id = '. $gProfileFields->getProperty('AKROBATIK_6_-_ENDRUNDE', 'usf_id'). '           
        LEFT JOIN '. TBL_USER_DATA. ' as anzahl_aktive
          ON anzahl_aktive.usd_usr_id = usr_id
          AND anzahl_aktive.usd_usf_id = '. $gProfileFields->getProperty('ANZAHL_AKTIVE', 'usf_id'). '       
        LEFT JOIN '. TBL_USER_DATA. ' as musiktitel_tanzmusik
          ON musiktitel_tanzmusik.usd_usr_id = usr_id
          AND musiktitel_tanzmusik.usd_usf_id = '. $gProfileFields->getProperty('MUSIKTITEL_TANZMUSIK', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as nachname_herr
          ON nachname_herr.usd_usr_id = usr_id
         AND nachname_herr.usd_usf_id = '. $gProfileFields->getProperty('NACHNAME_HERR', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as vorname_herr
          ON vorname_herr.usd_usr_id = usr_id
         AND vorname_herr.usd_usf_id = '. $gProfileFields->getProperty('VORNAME_HERR', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as nachname_dame
          ON nachname_dame.usd_usr_id = usr_id
         AND nachname_dame.usd_usf_id = '. $gProfileFields->getProperty('NACHNAME_DAME', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as vorname_dame
          ON vorname_dame.usd_usr_id = usr_id
         AND vorname_dame.usd_usf_id = '. $gProfileFields->getProperty('VORNAME_DAME', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as birthday
          ON birthday.usd_usr_id = usr_id
         AND birthday.usd_usf_id = '. $gProfileFields->getProperty('BIRTHDAY', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as city
          ON city.usd_usr_id = usr_id
         AND city.usd_usf_id = '. $gProfileFields->getProperty('CITY', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as address
          ON address.usd_usr_id = usr_id
         AND address.usd_usf_id = '. $gProfileFields->getProperty('ADDRESS', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as zip_code
          ON zip_code.usd_usr_id = usr_id
         AND zip_code.usd_usf_id = '. $gProfileFields->getProperty('POSTCODE', 'usf_id'). '
        LEFT JOIN '. TBL_USER_DATA. ' as country
          ON country.usd_usr_id = usr_id
         AND country.usd_usf_id = '. $gProfileFields->getProperty('COUNTRY', 'usf_id'). '
        LEFT JOIN '. TBL_ROLES. ' rol
          ON rol.rol_valid   = 1
         AND rol.rol_id      = '.$getRoleId.'
        LEFT JOIN '. TBL_MEMBERS. ' mem
          ON mem.mem_rol_id  = rol.rol_id
         AND mem.mem_begin  <= \''.DATE_NOW.'\'
         AND mem.mem_end     > \''.DATE_NOW.'\'
         AND mem.mem_usr_id  = usr_id
        WHERE '. $memberCondition. '
        ORDER BY last_name, first_name '.$limit;
$resultUser = $gDb->query($sql);

if($gDb->num_rows($resultUser)>0)
{
    //Buchstaben Navigation bei mehr als 50 personen
    // rmenken: auf 500 gesetzt, da sonst SQL Fehler - eigentlich brauchen wir hier auch keine Buchstaben Navigation!
    if($gDb->num_rows($resultUser) >= 500)
    {
        echo '<div class="pageNavigation">
            <a href="#" letter="all" class="pageNavigationLink">'.$gL10n->get('SYS_ALL').'</a>&nbsp;&nbsp;';
        
            // Nun alle Buchstaben mit evtl. vorhandenen Links im Buchstabenmenue anzeigen
            $letterMenu = 'A';
            
            for($i = 0; $i < 26;$i++)
            {
                // pruefen, ob es Mitglieder zum Buchstaben gibt
                // dieses SQL muss fuer jeden Buchstaben ausgefuehrt werden, ansonsten werden Sonderzeichen nicht immer richtig eingeordnet
                $sql = 'SELECT COUNT(1) as count
                          FROM '. TBL_USERS. ', '. TBL_USER_FIELDS. ', '. TBL_USER_DATA. '
                         WHERE usr_valid  = 1
                           AND usf_name_intern = \'LAST_NAME\'
                           AND usd_usf_id = usf_id
                           AND usd_usr_id = usr_id
                           AND usd_value LIKE \''.$letterMenu.'%\'
                           AND '.$memberCondition.'
                         GROUP BY UPPER(SUBSTRING(usd_value, 1, 1))';
                $result      = $gDb->query($sql);
                $letterRow  = $gDb->fetch_array($result);

                if($letterRow['count'] > 0)
                {
                    echo '<a href="#" letter="'.$letterMenu.'" class="pageNavigationLink">'.$letterMenu.'</a>';
                }
                else
                {
                    echo $letterMenu;
                }
        
                echo '&nbsp;&nbsp;';
        
                $letterMenu = strNextLetter($letterMenu);
            }
        echo '</div>';    
    }

    // print anmeldbare Startklassen
    echo '<p style="font-size:0.85em;">Folgende Startklassen k&ouml;nnen angemeldet werden:
      <ul style="list-style-type:none;font-size:0.8em;">
        <li>Rock&prime;n&prime;Roll: <b>'.$stklrrhtml.'</b></li>
        <li>Boogie-Woogie: <b>'.$stklbwhtml.'</b></li>
        <li>Formationen: <b>'.$stklfrhtml.'</b></li>
      </ul>
    </p>';
    // print qualifizierte Teilnehmer
    if($quali_dcgprr || $quali_dmrr){
      echo '<p style="font-size:0.85em;">Folgende Teilnehmer 
        haben sich qualifiziert und k&ouml;nnen angemeldet werden:</p>';
    }
    
    // create table header
    echo '
    <table class="tableList" cellspacing="0" border="1" rules="all" width="90%">
        <thead>
            <tr>
                <th><img class="iconInformation"
                    src="'. THEME_PATH. '/icons/profile.png" alt="'.$gL10n->get('SYS_MEMBER_OF_ORGANIZATION', $gCurrentOrganization->getValue('org_longname')).'"
                    title="'.$gL10n->get('SYS_MEMBER_OF_ORGANIZATION', $gCurrentOrganization->getValue('org_longname')).'" /></th>
                <th style="text-align: center;">Teilnehmer</th>
                <th style="text-align: center;">'.$gL10n->get('SYS_LASTNAME').' / Startbuch</th>
                <th>Herr</th>
                <th>Dame</th>
                <th>Teamname</th>
                <th>Vereinsname</th>
                <th>Startklasse</th>';
                //rmenken
                //print last three cols only for webmaster, useless for others
                if (hasRole("Webmaster") == true) {
                echo '
                <th><img class="iconInformation" src="'. THEME_PATH. '/icons/map.png"
                    alt="'.$gL10n->get('SYS_ADDRESS').'" title="'.$gL10n->get('SYS_ADDRESS').'" /></th>
                <th>'.$gL10n->get('SYS_BIRTHDAY').'</th>
                <th style="text-align: center;">'.$gL10n->get('SYS_LEADER');

          // show icon that leaders have no additional rights
          if($role->getValue('rol_leader_rights') == ROLE_LEADER_NO_RIGHTS)
          {
            echo '<img class="iconInformation" src="'.THEME_PATH.'/icons/info.png"
            alt="'.$gL10n->get('ROL_LEADER_NO_ADDITIONAL_RIGHTS').'" title="'.$gL10n->get('ROL_LEADER_NO_ADDITIONAL_RIGHTS').'" />';
          }

          // show icon with edit user right if leader has this right
          if($role->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_EDIT
          || $role->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN_EDIT)
          {
            echo '<img class="iconInformation" src="'.THEME_PATH.'/icons/profile_edit.png"
            alt="'.$gL10n->get('ROL_LEADER_EDIT_MEMBERS').'" title="'.$gL10n->get('ROL_LEADER_EDIT_MEMBERS').'" />';
          }

          // show icon with assign role right if leader has this right
          if($role->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN
          || $role->getValue('rol_leader_rights') == ROLE_LEADER_MEMBERS_ASSIGN_EDIT)
          {
            echo '<img class="iconInformation" src="'.THEME_PATH.'/icons/roles.png"
            alt="'.$gL10n->get('ROL_LEADER_ASSIGN_MEMBERS').'" title="'.$gL10n->get('ROL_LEADER_ASSIGN_MEMBERS').'" />';
          }
          echo '</th>';
          }
          echo '</tr>
        </thead>';
        
    $letter_merker = '';
    $this_letter   = '';
    
    function convSpecialChar($specialChar)
    {
        $convTable = array('Ä' => 'A', 'É' => 'E', 'È' => 'E', 'Ö' => 'O', 'Ü' => 'U');
        
        if(array_key_exists($specialChar, $convTable))
        {
            return admstrtoupper($convTable[$specialChar]);
        }
        return $specialChar;
    }

    //Zeilen ausgeben
    while($user = $gDb->fetch_array($resultUser))
    {            
      if($gDb->num_rows($resultUser) >= 50)
      {
            // Buchstaben auslesen
            $this_letter = admstrtoupper(substr($user['last_name'], 0, 1));
            
            if(ord($this_letter) < 65 || ord($this_letter) > 90)
            {
                $this_letter = convSpecialChar(substr($user['last_name'], 0, 2));
            }
            
            if($this_letter != $letter_merker)
            {
                if(mb_strlen($letter_merker) > 0)
                {
                    echo '</tbody>';
                }

                // Ueberschrift fuer neuen Buchstaben
                echo '<tbody block_head_id="'.$this_letter.'" class="letterBlockHead">
                    <tr>
                        <td class="tableSubHeader" colspan="9">
                            '.$this_letter.'
                        </td>
                    </tr>
                </tbody>
                <tbody block_body_id="'.$this_letter.'" class="letterBlockBody">';

                // aktuellen Buchstaben merken
                $letter_merker = $this_letter;
            }
        }

        //Datensatz ausgeben
        $user_text = '';
        if(strlen($user['address']) > 0)
        {
            $user_text = $user['address'];
        }
        if(strlen($user['zip_code']) > 0 || strlen($user['city']) > 0)
        {
            $user_text = $user_text. ' - '. $user['zip_code']. ' '. $user['city'];
        }
        if(strlen($user['country']) > 0)
        {
            $user_text = $user_text. ' - '. $user['country'];
        }

        // Icon fuer Orgamitglied und Nichtmitglied auswaehlen
        if($user['member_this_orga'] > 0)
        {
            $icon = 'profile.png';
            $iconText = $gL10n->get('SYS_MEMBER_OF_ORGANIZATION', $gCurrentOrganization->getValue('org_longname'));
        }
        else
        {
            $icon = 'no_profile.png';
            $iconText = $gL10n->get('SYS_NOT_MEMBER_OF_ORGANIZATION', $gCurrentOrganization->getValue('org_longname'));
        }
 
        //rmenken
        //wenn der Nachname verein ist und nicht zur rolle webmaster gehört, 
        //dann Zeile nicht drucken, damit ein verein als Leiter sich nicht
        //selber rausnehmen kann
      
        //Check ob Profil für DC/GPvD bzw. DM qualifiziert ist
        $qualifiziert = true;
        if($quali_dcgprr){
          if($user['qualifiziert_dc_gp'] == '1'){$qualifiziert = true;}else{$qualifiziert = false;}
        }
        if($quali_dmrr){
          if($user['qualifiziert_dm'] == '1'){$qualifiziert = true;}else{$qualifiziert = false;}
        }

        $ops_text = $user['last_name'];               
        if ($ops_text == "verein" and (hasRole("Webmaster") == false))
        {
          echo '';
        }
        else {
          //rmenken 
          //nur Profile mit gültiger Startmarke ausgeben und nur die Startklassen, die laut 
          //Turnieranmeldung auch anmeldbar sind.
          if ($user['startmarke_gueltig'] == '1' && in_array($user['startklasse'],$startklassenlist) && $qualifiziert) {
          echo '
          <tr class="tableMouseOver" user_id="'.$user['usr_id'].'">
              <td><img class="iconInformation" src="'. THEME_PATH.'/icons/'.$icon.'" alt="'.$iconText.'" title="'.$iconText.'" /></td>';     
              //Bei Formationen Anzahl Aktive und Musik checken
              if ($stklformation == TRUE && 
                 ($user['startklasse']=='6' || $user['startklasse']=='7' || $user['startklasse']=='8' || 
                  $user['startklasse']=='9' || $user['startklasse']=='10' || $user['startklasse']=='11') && 
                 ($user['anzahl_aktive'] == '' || $user['musiktitel_tanzmusik'] == '')) {
                echo'
                <td style="text-align: center;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_big" alt="Anmeldung nicht m&ouml;glich!" title="Anzahl Aktiver oder Tanzmusik fehlen im Profil!" /></td>';
                $warningformation=TRUE;
              //Bei Akroklassen Angabe aller Akros checken
              // Junioren --     
              } elseif ($user['startklasse']=='2' && 
                  ($user['akro1vr'] == '' || $user['akro2vr'] == '' || $user['akro3vr'] == '' ||
                   $user['akro1zr'] == '' || $user['akro2zr'] == '' || $user['akro3zr'] == '' ||                                                        
                   $user['akro1er'] == '' || $user['akro2er'] == '' || $user['akro3er'] == '' )) {
                echo'
                <td style="text-align: center;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_akro_big" alt="Anmeldung nicht m&ouml;glich!" title="Angaben zur Akrobatik fehlen im Profil!" /></td>';
                $warningnoakro=TRUE;
              // C-Klasse --     
              } elseif ($user['startklasse']=='3' && 
                  ($user['akro1vr'] == '' || $user['akro2vr'] == '' || $user['akro3vr'] == '' || $user['akro4vr'] == '' ||
                   $user['akro1zr'] == '' || $user['akro2zr'] == '' || $user['akro3zr'] == '' || $user['akro4zr'] == '' ||                                                        
                   $user['akro1er'] == '' || $user['akro2er'] == '' || $user['akro3er'] == '' || $user['akro4er'] == '' )) {
                echo'
                <td style="text-align: center;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_akro_big" alt="Anmeldung nicht m&ouml;glich!" title="Angaben zur Akrobatik fehlen im Profil!" /></td>';
                $warningnoakro=TRUE;
              // A/B-Klasse --     
              } elseif (($user['startklasse']=='4' || $user['startklasse']=='5') && 
                  ($user['akro1vr'] == '' || $user['akro2vr'] == '' || $user['akro3vr'] == '' || $user['akro4vr'] == '' || $user['akro5vr'] == '' ||
                   $user['akro1zr'] == '' || $user['akro2zr'] == '' || $user['akro3zr'] == '' || $user['akro4zr'] == '' || $user['akro5zr'] == '' ||                                                       
                   $user['akro1er'] == '' || $user['akro2er'] == '' || $user['akro3er'] == '' || $user['akro4er'] == '' || $user['akro5er'] == '' || $user['akro6er'] == '' )) {
                echo'
                <td style="text-align: center;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_akro_big" alt="Anmeldung nicht m&ouml;glich!" title="Angaben zur Akrobatik fehlen im Profil!" /></td>';
                $warningnoakro=TRUE;
              // F-Master-Klasse --     
              } elseif ($user['startklasse']=='6' && 
                  ($user['akro1vr'] == '' || $user['akro2vr'] == '' || $user['akro3vr'] == '' || $user['akro4vr'] == '' || $user['akro5vr'] == '' || $user['akro6vr'] == '' ||
                   $user['akro1zr'] == '' || $user['akro2zr'] == '' || $user['akro3zr'] == '' || $user['akro4zr'] == '' || $user['akro5zr'] == '' || $user['akro6zr'] == '' ||                                                       
                   $user['akro1er'] == '' || $user['akro2er'] == '' || $user['akro3er'] == '' || $user['akro4er'] == '' || $user['akro5er'] == '' || $user['akro6er'] == '' )) {
                echo'
                <td style="text-align: center;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_akro_big" alt="Anmeldung nicht m&ouml;glich!" title="Angaben zur Akrobatik fehlen im Profil!" /></td>';
                $warningnoakro=TRUE;
              } else {
                echo'
                <td style="text-align: center;">';
                    //Haekchen setzen ob jemand Mitglied ist oder nicht
                    if($user['member_this_role'])
                    {
                        echo '<input type="checkbox" id="member_'.$user['usr_id'].'" name="member_'.$user['usr_id'].'" checked="checked" class="memlist_checkbox" checkboxtype="member" />';
                    }
                    else
                    {
                        echo '<input type="checkbox" id="member_'.$user['usr_id'].'" name="member_'.$user['usr_id'].'" class="memlist_checkbox" checkboxtype="member"/>';
                    }
                //rmenken: added verein, startklasse                  
                echo '<b id="loadindicator_member_'.$user['usr_id'].'"></b></td>';
              }
              echo'                                                                                                  
              <td style="text-align: center;">'.$user['last_name'].'</td>
              <td>'.$user['vorname_herr'].' '.$user['nachname_herr'].'</td>
              <td>'.$user['vorname_dame'].' '.$user['nachname_dame'].'</td>
              <td>'.$user['teamname'].'</td>                                      
              <td>'.$user['verein'].'</td>';
              if($user['startklasse']=='1'){echo'<td style="text-align: center;">RR-S</td>';}
              if($user['startklasse']=='2'){echo'<td style="text-align: center;">RR-J</td>';}
              if($user['startklasse']=='3'){echo'<td style="text-align: center;">RR-C</td>';}
              if($user['startklasse']=='4'){echo'<td style="text-align: center;">RR-B</td>';}
              if($user['startklasse']=='5'){echo'<td style="text-align: center;">RR-A</td>';}
              if($user['startklasse']=='6'){echo'<td style="text-align: center;">F-RR-MA</td>';}
              if($user['startklasse']=='7'){echo'<td style="text-align: center;">F-RR-JU</td>';}
              if($user['startklasse']=='8'){echo'<td style="text-align: center;">F-RR-LA</td>';}
              if($user['startklasse']=='9'){echo'<td style="text-align: center;">F-RR-GI</td>';}
              if($user['startklasse']=='10'){echo'<td style="text-align: center;">F-RR-ST</td>';}
              if($user['startklasse']=='11'){echo'<td style="text-align: center;">F-BW-MA</td>';}
              if($user['startklasse']=='12'){echo'<td style="text-align: center;">BW-A</td>';}
              if($user['startklasse']=='13'){echo'<td style="text-align: center;">BW-B</td>';}          
              //rmenken
              //print last three cols only for webmaster, useless for others          
              if (hasRole("Webmaster") == true) {
              echo '<td>';
                  if(strlen($user_text) > 0)
                  {
                      echo '<img class="iconInformation" src="'. THEME_PATH.'/icons/map.png" alt="'.$user_text.'" title="'.$user_text.'" />';
                  }
                  else
                  {
                      echo '&nbsp';
                  }
              echo '</td>
              <td>';
                  //Geburtstag nur ausgeben wenn bekannt
                  if(strlen($user['birthday']) > 0)
                  {
                      $birthdayDate = new DateTimeExtended($user['birthday'], 'Y-m-d', 'date');
                      echo $birthdayDate->format($gPreferences['system_date']);
                  }
              echo '</td>
      
              <td style="text-align: center;">';
                  //Haekchen setzen ob jemand Leiter ist oder nicht
                  if($user['leader_this_role'])
                  {
                      echo '<input type="checkbox" id="leader_'.$user['usr_id'].'" name="leader_'.$user['usr_id'].'" checked="checked" class="memlist_checkbox" checkboxtype="leader"/>';
                  }
                  else
                  {
                      echo '<input type="checkbox" id="leader_'.$user['usr_id'].'" name="leader_'.$user['usr_id'].'" class="memlist_checkbox" checkboxtype="leader" />';
                  }
            echo '<b id="loadindicator_leader_'.$user['usr_id'].'"></b></td>';
            }          
            echo '</tr>';
          }      
        }
    }//End While

  echo '</table>
    <p>'.$gL10n->get('SYS_CHECKBOX_AUTOSAVE').'</p>';
  
  if ($warningformation==TRUE) {
    echo '
      <p style="color: red;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_big" alt="Anmeldung nicht m&ouml;glich!" title="Anzahl Aktiver oder Tanzmusik fehlen im Profil!" />
       Bei Formationen mit diesem Zeichen fehlt zur Startmeldung die Anzahl der Aktiven und/oder deren Tanzmusiktitel im Startbuchprofil!    
      </p>';
  }  
  if ($warningnoakro==TRUE) {
    echo '
      <p style="color: red;"><img class="iconInformation" src="'. THEME_PATH.'/icons/warning_akro_big" alt="Anmeldung nicht m&ouml;glich!" title="Es fehlen Angaben zur Akrobatik im Profil!" />
       Bei Startbuchprofilen mit diesem Zeichen fehlt zur Startmeldung die vollständige Angabe der Akrobatiken im Startbuchprofil!    
      </p>';
  }  
    
  //Hilfe nachladen
  echo '<script type="text/javascript">$("a[rel=\'colorboxHelp\']").colorbox({preloading:true,photo:false,speed:300,rel:\'nofollow\'})</script>';
}
else
{
  echo '<p>'.$gL10n->get('SYS_NO_ENTRIES_FOUND').'</p>';
}
?>
