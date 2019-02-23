<?php
/******************************************************************************
 * Create and edit dates
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * dat_id   - ID des Termins, der bearbeitet werden soll
 * headline - Ueberschrift, die ueber den Terminen steht
 *            (Default) Termine
 * copy : true - der uebergebene ID-Termin wird kopiert und kann neu gespeichert werden
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/drbv_database.php');
require_once('../../system/login_valid.php');
require_once('../../system/classes/ckeditor_special.php');
require_once('../../system/classes/form_elements.php');
require_once('../../system/classes/table_date.php');
require_once('../../system/classes/table_rooms.php');
require_once('../../system/classes/table_roles.php');

// Initialize and check the parameters
$getDateId   = admFuncVariableIsValid($_GET, 'dat_id', 'numeric', 0);
$getHeadline = admFuncVariableIsValid($_GET, 'headline', 'string', $gL10n->get('DAT_DATES'));
$getCopy     = admFuncVariableIsValid($_GET, 'copy', 'boolean', 0);

// print_r($_POST);echo"<br>";

// define arrays for Turnierleiter and Turnierform
// Turnierleiter aus Datenbank lesen

$tleiter_org = array();

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 101";
    
$turnier_leiter = mysqli_query(ADMIDIOdb(), $sqlab);
$x = 1;
while($temp=mysqli_fetch_array($turnier_leiter))
   {
     $t_leiter_id = $temp[0];
     $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
     $ergebnis=mysqli_query(ADMIDIOdb(), $sqlab);
     while($name=mysqli_fetch_array($ergebnis))
           {
            $temp_id = $name[0];
            $temp_name = $name[1];
            if($temp_id == 1)
               $n_name = $temp_name;
            if($temp_id == 2)
               $v_name = $temp_name;
           }   
           
    $tleiter_org[$x] = utf8_encode($n_name) . " " . utf8_encode($v_name);
    $x = $x + 1;         
   }
natsort($tleiter_org);

$tleiter = array();
$x = 1;
foreach($tleiter_org as $leiter_drehen)
       {
         $ungedreht = explode(" ", $leiter_drehen);
         
          if($ungedreht[2])
               $tleiter[$x] = $ungedreht[1] . " " . $ungedreht[2] . " " . $ungedreht[0];
         else           
            $tleiter[$x] = $ungedreht[1] . " " . $ungedreht[0];
        $x = $x + 1; 
        } 

$tform = array(1 => 'Breitensportwettbewerb', 'Sportturnier', 'Nord-Cup', 'Süd-Cup', 'Offene Landesmeisterschaft', 'Geschlossene Landesmeisterschaft', 'Einladungsturnier', 'Ranglistenturnier', 'Qualifikationsturnier', 'Deutsche Meisterschaft', 'Europameisterschaft', 'Weltmeisterschaft', 'Continental Meisterschaft', 'World Master', 'World Cup');
$tform_international = array(0 => 'National', 1 => 'International');
$tform_cupserie = array(1 => 'Nord-Cup', 'Süd-Cup');  

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($gPreferences['enable_dates_module'] == 0)
{
    // das Modul ist deaktiviert
    $gMessage->show($gL10n->get('SYS_MODULE_DISABLED'));
}

if(!$gCurrentUser->editDates())
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}

// lokale Variablen der Uebergabevariablen initialisieren
$dateRegistrationPossible = 0;
$dateCurrentUserAssigned  = 0;

$gNavigation->addUrl(CURRENT_URL);

// Terminobjekt anlegen
$date = new TableDate($gDb);

if($getDateId > 0)
{
    $date->readDataById($getDateId);
    
    if($getCopy)
    {
        $date->getVisibleRoles();
        $date->setValue('dat_id', 0);
        $getDateId = 0;
    }
    
    // Pruefung, ob der Termin zur aktuellen Organisation gehoert bzw. global ist
    if($date->editRight() == false)
    {
        $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
    }
}
else
{
    // bei neuem Termin Datum mit aktuellen Daten vorbelegen
    $date->setValue('dat_begin', date('Y-m-d H:00:00', time()));
    $date->setValue('dat_end', date('Y-m-d H:00:00', time()+3600));
    
    // wurde ein Kalender uebergeben, dann diesen vorbelegen
    if(strlen($getHeadline) > 0)
    {
        $sql = 'SELECT cat_id FROM '.TBL_CATEGORIES.' WHERE cat_name = \''.$getHeadline.'\'';
        $gDb->query($sql);
        $row = $gDb->fetch_array();
        $date->setValue('dat_cat_id', $row['cat_id']);
    }
}

if(isset($_SESSION['dates_request']))
{
    // durch fehlerhafte Eingabe ist der User zu diesem Formular zurueckgekehrt
    // nun die vorher eingegebenen Inhalte ins Objekt schreiben
	$date->setArray($_SESSION['dates_request']);

    // ausgewaehlte Rollen vorbelegen
    $numberRoleSelect = 1;
    $arrRoles = array();
    while(isset($_SESSION['dates_request']['role_'.$numberRoleSelect]))
    {
        $arrRoles[] = $_SESSION['dates_request']['role_'.$numberRoleSelect];
        $numberRoleSelect++;
    }
    $date->setVisibleRoles($arrRoles);
    
    $date_from  = $_SESSION['dates_request']['date_from'];
    $time_from  = $_SESSION['dates_request']['time_from'];
    $date_to    = $_SESSION['dates_request']['date_to'];
    $time_to    = $_SESSION['dates_request']['time_to'];
	
	// check if a registration to this event is possible
    if(array_key_exists('dateRegistrationPossible', $_SESSION['dates_request']))
    {
        $dateRegistrationPossible = $_SESSION['dates_request']['dateRegistrationPossible'];
    }
	
	// check if current user is assigned to this date
    if(array_key_exists('dateCurrentUserAssigned', $_SESSION['dates_request']))
    {
        $dateCurrentUserAssigned = $_SESSION['dates_request']['dateCurrentUserAssigned'];
    }
    
    unset($_SESSION['dates_request']);
}
else
{
    // Zeitangaben von/bis aus Datetime-Feld aufsplitten
    $date_from = $date->getValue('dat_begin', $gPreferences['system_date']);
    $time_from = $date->getValue('dat_begin', $gPreferences['system_time']);

    // Datum-Bis nur anzeigen, wenn es sich von Datum-Von unterscheidet
    $date_to = $date->getValue('dat_end', $gPreferences['system_date']);
    $time_to = $date->getValue('dat_end', $gPreferences['system_time']);

    // read all roles that could see this event
    if($getDateId == 0)
    {
        if($getCopy == 0)
        {
            // a new event will be visible for all users per default
            $date->setVisibleRoles(array('-1'));
        }
    }
    else
    {
        $date->getVisibleRoles();
    }
	
	// check if a registration to this event is possible
	if($date->getValue('dat_rol_id') > 0)
	{
		$dateRegistrationPossible = 1;
	}
	// check if current user is assigned to this date
	$dateCurrentUserAssigned = $gCurrentUser->isLeaderOfRole($date->getValue('dat_rol_id'));
}

// create an object of ckeditor and replace textarea-element
$ckEditor = new CKEditorSpecial();

// Html-Kopf ausgeben
if($getDateId > 0)
{
    $gLayout['title'] = $gL10n->get('SYS_EDIT_VAR', $getHeadline);
}
else
{
    $gLayout['title'] = $gL10n->get('SYS_CREATE_VAR', $getHeadline);
}

if($date->getValue('dat_rol_id') > 0)
{
	$dateRoleID = $date->getValue('dat_rol_id');
}
else
{
	$dateRoleID = '0';
}

$gLayout['header'] = '
<script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
<script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
<link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
<script type="text/javascript"><!--
    // Funktion blendet Zeitfelder ein/aus
    function setAllDay() {
		if ($("#dat_all_day:checked").val() !== undefined) {
			$("#time_from").hide();
			$("#time_to").hide();
        }
        else {
			$("#time_from").show("slow");
			$("#time_to").show("slow");
        }
    }
	
	
	function setDateParticipation() {
		if ($("#dateRegistrationPossible:checked").val() !== undefined) {
			$("#admAssignYourself").css("display", "block");
			$("#admMaxMembers").css("display", "block");
		}
		else {
			$("#admAssignYourself").css("display", "none");
			$("#admMaxMembers").css("display", "none");
		}
	}

    // Funktion belegt das Datum-bis entsprechend dem Datum-Von
    function setDateTo() {
        var dateFrom = Date.parseDate($("#date_from").val(), "'.$gPreferences['system_date'].'");
        var dateTo   = Date.parseDate($("#date_to").val(), "'.$gPreferences['system_date'].'");

        if(dateFrom.getTime() > dateTo.getTime()) {
            $("#date_to").val($("#date_from").val());
        }
    }

    var calPopup = new CalendarPopup("calendardiv");
    calPopup.setCssPrefix("calendar");
    var numberRoleSelect = 1;

    function addRoleSelection(roleID) {
        $.ajax({url: "dates_function.php?mode=5&number_role_select=" + numberRoleSelect + "&rol_id=" + roleID, type: "GET", async: false, 
            success: function(data){
                if(numberRoleSelect == 1) {
                    $("#liRoles").html($("#liRoles").html() + data);
                }
                else {
                    number = numberRoleSelect - 1;
                    $("#roleID_"+number).after(data);
                }
            }});
        numberRoleSelect++;
    }
    
    function removeRoleSelection(id) {
        $("#"+id).hide("slow");
        $("#"+id).remove();
		numberRoleSelect = numberRoleSelect - 1;
    }
	
	function setLocationCountry() {
		if($("#dat_location").val().length > 0) {
			$("#admDateCountry").show("slow");
		}
		else {
			$("#admDateCountry").hide();
		}
	}

    $(document).ready(function() 
    {
		var dateRoleID = '.$dateRoleID.';
		
        setAllDay();
		setDateParticipation();
		setLocationCountry();
        $("#dat_headline").focus();
		
		$("#dateRegistrationPossible").click(function() {setDateParticipation();});
		$("#dat_all_day").click(function() {setAllDay();});
		$("#dat_location").change(function() {setLocationCountry();});';

        // alle Rollen anzeigen, die diesen Termin sehen duerfen
        foreach($date->getVisibleRoles() as $key => $roleID)
        {
            $gLayout['header'] .= 'addRoleSelection('.$roleID.');';
        }

		$gLayout['header'] .= '
		// if date participation should be removed than ask user
		$("#admButtonSave").click(function () {
      if(dateRoleID > 0 && $("#dateRegistrationPossible").is(":checked") == false) {';
      if($gCurrentUser->isWebmaster()){
        $gLayout['header'] .= 'var msg_result = confirm("'.$gL10n->get('DAT_REMOVE_APPLICATION_ORG').'");';
      } else {
        $gLayout['header'] .= 'var msg_result = confirm("'.$gL10n->get('DAT_REMOVE_APPLICATION').'");';      
      }  
        $gLayout['header'] .= '
				if(msg_result) {
					$("#formDate").submit();
				}
			}
			else {
				$("#formDate").submit();
			}
		});
	}); 
//--></script>';

require(SERVER_PATH. '/adm_program/system/overall_header.php');
 
// Vereinsname eintragen

// Verein suchen
if($date->getValue('dat_vereinsnummer'))
  {
//   echo $date->getValue('dat_vereinsnummer');
   $sqlab = "SELECT usr_id FROM adm_users WHERE usr_login_name = " . $date->getValue('dat_vereinsnummer');
    
   $verein = mysqli_query(ADMIDIOdb(), $sqlab);
   $temp = mysqli_fetch_row($verein);
   $id = $temp[0];
//   echo"<br>$id<br>";
    
    // Vereinsdaten auslesen
    $sqlab = "SELECT usd_value FROM adm_user_data WHERE usd_usr_id = $id AND usd_usf_id = 28";
    
    $daten = mysqli_query(ADMIDIOdb(), $sqlab);
    $temp = mysqli_fetch_row($daten);
  //  $vereinsname = $temp[0];
    $date->setValue('dat_verein', $temp[0]);
//    echo"$vereinsname<br>";
  }
  
  
// Html des Modules ausgeben
echo '
<form method="post" id="formDate" action="'.$g_root_path.'/adm_program/modules/dates/dates_function.php?dat_id='.$getDateId.'&amp;mode=1">
<div class="formLayout" id="edit_dates_form">
    <div class="formHead">'. $gLayout['title']. '</div>
    <div class="formBody">
		<div class="groupBox" id="admTitleLocation">
			<div class="groupBoxHeadline" id="admTitleLocationHead">
				<a class="iconShowHide" href="javascript:showHideBlock(\'admTitleLocationBody\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
				id="admTitleLocationBodyImage" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>'.$gL10n->get('SYS_TITLE').' & '.$gL10n->get('DAT_LOCATION').'
			</div>

			<div class="groupBoxBody" id="admTitleLocationBody">
				<ul class="formFieldList">
					<li>
						<dl>
							<dt><label for="dat_headline">'.$gL10n->get('SYS_TITLE').':</label></dt>
							<dd>
                <input type="text" id="dat_headline" name="dat_headline" style="width: 90%;" maxlength="50" value="'. $date->getValue('dat_headline'). '" />
								<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
							</dd>
						</dl>
					</li>
					<li>
            <dl>
              <dt><label for="dat_location_wo">Örtlichkeit:</label></dt>
              <dd>
                <input type="text" id="dat_location_wo" name="dat_location_wo" style="width: 90%;" maxlength="99" value="'. $date->getValue('dat_location_wo'). '" />                
              </dd>
            </dl>
          </li>
          <li>
            <dl>
              <dt><label for="dat_location_str">Strasse Nr.:</label></dt>
              <dd>
                <input type="text" id="dat_location_str" name="dat_location_str" style="width: 90%;" maxlength="99" value="'. $date->getValue('dat_location_str'). '" />                
              </dd>
            </dl>
          </li>
          <li>
            <dl>
              <dt><label for="dat_location_plz">Postleitzahl:</label></dt>
              <dd>
                <input type="text" id="dat_location_plz" name="dat_location_plz" style="width: 90%;" maxlength="10" value="'. $date->getValue('dat_location_plz'). '" />                
              </dd>
            </dl>
          </li>                                      
          <li>
            <dl>
              <dt><label for="dat_location_ort">Stadt:</label></dt>
              <dd>
                <input type="text" id="dat_location_ort" name="dat_location_ort" style="width: 90%;" maxlength="99" value="'. $date->getValue('dat_location_ort'). '" />                
              </dd>
            </dl>
          </li>';

// dat_location wird durch einzelne Felder ersetzt.
// der DB wert dat_location in der dates function aber beschrieben durch die neuen felder
// feld und li elem auf hidden gesetzt, da sonst dat_location nicht geschrieben wird und 
// die form sonst hängt
/*
          echo '
          <li style="visibility: hidden;">
						<dl>
							<dt><label for="dat_location">'.$gL10n->get('DAT_LOCATION').':</label></dt>
							<dd>
              <input type="hidden" id="dat_location" name="dat_location" style="width: 90%;" maxlength="99" value="'. $date->getValue('dat_location'). '" />';
								if($gPreferences['dates_show_map_link'])
								{
									echo '<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DAT_LOCATION_LINK&amp;inline=true"><img 
										onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DAT_LOCATION_LINK\',this)" 
										onmouseout="ajax_hideTooltip()" class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>';
								}
							echo '</dd>
						</dl>
					</li>';
*/ 

// Thomas 13.13.15
          echo'
          <li>
            <dl>
              <dt><label for="dat_location">Wo:</label></dt>
              <dd>
               ' . $date->getValue('dat_location'). '&nbsp    
              </dd>
            </dl>
          </li>';
?>
<input type="hidden" id="dat_location" name="dat_location" style="width: 90%;" maxlength="99" value=" " />
<?php 
// End Thomas                 
					if($gPreferences['dates_show_map_link'])
					{
						if(strlen($date->getValue('dat_country')) == 0 && $getDateId == 0)
						{
							$date->setValue('dat_country', $gPreferences['default_country']);
						}
						echo '<li id="admDateCountry">
							<dl>
								<dt><label for="dat_location">'.$gL10n->get('SYS_COUNTRY').':</label></dt>
								<dd>
									<select size="1" id="dat_country" name="dat_country">
										<option value="">- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
										foreach($gL10n->getCountries() as $key => $value)
										{
											echo '<option value="'.$key.'" ';
											if($value == $date->getValue('dat_country'))
											{
												echo ' selected="selected" ';
											}
											echo '>'.$value.'</option>';
										}
									echo '</select>
								</dd>
							</dl>
						</li>';
					}
					if($gPreferences['dates_show_rooms']==1) //nur wenn Raumauswahl aktiviert ist
					{
						echo'<li>
								<dl>
									<dt><label for="dat_room_id">'.$gL10n->get('SYS_ROOM').':</label></dt>
									<dd>
										<select id="dat_room_id" name="dat_room_id" size="1">
											<option value="0"';
											if($date->getValue('dat_room_id') == 0)
											{
												echo ' selected="selected" ';
											}
											echo '>'.$gL10n->get('SYS_NONE').'</option>';
				
											$sql = 'SELECT room_id, room_name, room_capacity, room_overhang 
													  FROM '.TBL_ROOMS.'
													 ORDER BY room_name';
											$result = $gDb->query($sql);
				
											while($row = $gDb->fetch_array($result))
											{
												echo '<option value="'.$row['room_id'].'"';
													if($date->getValue('dat_room_id') == $row['room_id'])
													{
														echo ' selected="selected" ';
													}
												echo '>'.$row['room_name'].' ('.$row['room_capacity'].'+'.$row['room_overhang'].')</option>';
											}
										echo '</select>
									</dd>
								</dl>
							</li>';
					}
				echo '</ul>
			</div>
		</div>

		<div class="groupBox" id="admPeriodCalendar">
			<div class="groupBoxHeadline" id="admPeriodCalendarHead">
				<a class="iconShowHide" href="javascript:showHideBlock(\'admPeriodCalendarBody\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
				id="admPeriodCalendarBodyImage" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>'.$gL10n->get('SYS_PERIOD').' & '.$gL10n->get('DAT_CALENDAR').'
			</div>
			<div class="groupBoxBody" id="admPeriodCalendarBody">
				<ul class="formFieldList">
					<li>
						<dl>
							<dt><label for="date_from">'.$gL10n->get('SYS_START').':</label></dt>
							<dd>
								<span>
									<input type="text" id="date_from" name="date_from" onchange="javascript:setDateTo();" size="10" maxlength="10" value="'.$date_from.'" />
									<a class="iconLink" id="anchor_date_from" href="javascript:calPopup.select(document.getElementById(\'date_from\'),\'anchor_date_from\',\''.$gPreferences['system_date'].'\',\'date_from\',\'date_to\',\'time_from\',\'time_to\');"><img 
										src="'.THEME_PATH.'/icons/calendar.png" alt="'.$gL10n->get('SYS_SHOW_CALENDAR').'" title="'.$gL10n->get('SYS_SHOW_CALENDAR').'" /></a>
									<span id="calendardiv" style="position: absolute; visibility: hidden; "></span>
								</span>
								<span style="margin-left: 10px;">
									<input type="text" id="time_from" name="time_from" size="5" maxlength="5" value="'.$time_from.'" />
									<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
								</span>
								<span style="margin-left: 15px;">
									<input type="checkbox" id="dat_all_day" name="dat_all_day" ';
									if($date->getValue('dat_all_day') == 1)
									{
										echo ' checked="checked" ';
									}
									echo ' value="1" />
									<label for="dat_all_day">'.$gL10n->get('DAT_ALL_DAY').'</label>
								</span>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt><label for="date_to">'.$gL10n->get('SYS_END').':</label></dt>
							<dd>
								<span>
									<input type="text" id="date_to" name="date_to" size="10" maxlength="10" value="'.$date_to.'" />
									<a class="iconLink" id="anchor_date_to" href="javascript:calPopup.select(document.getElementById(\'date_to\'),\'anchor_date_to\',\''.$gPreferences['system_date'].'\',\'date_from\',\'date_to\',\'time_from\',\'time_to\');"><img 
										src="'.THEME_PATH.'/icons/calendar.png" alt="'.$gL10n->get('SYS_SHOW_CALENDAR').'" title="'.$gL10n->get('SYS_SHOW_CALENDAR').'" /></a>
								</span>
								<span style="margin-left: 10px;">
									<input type="text" id="time_to" name="time_to" size="5" maxlength="5" value="'.$time_to.'" />
									<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'" id="timeToMandatory"';
									if($date->getValue('dat_repeat_type') != 0)
									{
										echo ' style="visibility: hidden;"';
									}
									echo '>*</span>
								</span>
							</dd>
						</dl>
					</li>
					<li>
						<dl>
							<dt><label for="dat_cat_id">'.$gL10n->get('DAT_CALENDAR').':</label></dt>
							<dd>
								'.FormElements::generateCategorySelectBox('DAT', $date->getValue('dat_cat_id'), 'dat_cat_id').'
								<span class="mandatoryFieldMarker" title="'.$gL10n->get('SYS_MANDATORY_FIELD').'">*</span>
							</dd>
              <dd>
                    <br><b>Turnierkalender national:</b> Nationale Turniere
                    <br><b>Turnierkalender international:</b> Internationale Turniere       
              </dd>
              <dd>
                    <b>Sitzungskalender:</b> Alle Sitzungen    
              </dd>
              <dd>
                    <b>Veranstaltungskalender:</b> <br> Vereinsveranstaltungen / Workshops / Vereinsseminare
              </dd>     
              <dd>
                    <b>Schulungskalender:</b><br> DRBV-Schulungen / WRRC-Schulungen <br>Lizenzmaßnahmen / Kadertraining DRBV    
              </dd>     
            </dl>
          </li>';                   
    echo '   
        </ul>
      </div>
    </div>';

  echo '
    <div class="groupBox" id="admTurnierCalendar">
      <div class="groupBoxHeadline" id="admTurnierCalendarHead">
        <a class="iconShowHide" href="javascript:showHideBlock(\'admTurnierCalendarBody\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
        id="admPeriodTurnierBodyImage" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>DRBV Turnierinformation
      </div>
      <div class="groupBoxBody" id="admTurnierCalendarBody">
        <ul class="formFieldList">';

         echo'<select name="Art">';
              if($date->getValue('Art') == "1")
                 echo'<option value="1" selected>Sportturnier</option>';
              else
                 echo'<option value="1" >Sportturnier</option>';
              if($date->getValue('Art') == "2")                 
                 echo'<option value="2" selected>Breitensportwettbewerb</option>';
              else
                 echo'<option value="2">Breitensportwettbewerb</option>';
         echo"</select>";         
               
            $jahr = date("Y");
            $jahr_kurz = date("y");
            $jahr1 = $jahr + 1;
            $jahr1_kurz = $jahr_kurz + 1;            
            $jahr2 = $jahr + 2;
            $jahr2_kurz = $jahr_kurz + 2;
                 
         echo'<select name="jahr">';
              if($_SESSION['dates_request']['jahr'] == 0000)
                 echo'<option value="0000" selected>0000</option>';
              else
                 echo'<option value="0000">0000</option>';
              if($_SESSION['dates_request']['jahr'] == $jahr_kurz)
                 echo'<option value="' . $jahr_kurz . '" selected>' . $jahr . '</option>';
              else
                 echo'<option value="' . $jahr_kurz . '">' . $jahr . '</option>';
              if($date->getValue('jahr') == "$jahr1_kurz")
                 echo'<option value="' . $jahr1_kurz . '" selected>' . $jahr1 . '</option>';
              else
                 echo'<option value="' . $jahr1_kurz . '">' . $jahr1 . '</option>';  
              if($date->getValue('jahr') == "$jahr2_kurz")
                 echo'<option value="' . $jahr2_kurz . '" selected>' . $jahr2 . '</option>';
              else
                 echo'<option value="' . $jahr2_kurz . '">' . $jahr2 . '</option>';                 
         echo"</select>";  

         echo'<select name="monat" onchange="submit();">';
              if($date->getValue('monat') == "00")
                 echo'<option value="00" selected>0</option>';
              else
                 echo'<option value="00">0</option>';
              if($date->getValue('monat') == "01")
                 echo'<option value="01" selected>1</option>';
              else
                 echo'<option value="01">1</option>';
              if($date->getValue('monat') == "02")
                 echo'<option value="02" selected>2</option>';
              else
                 echo'<option value="02">2</option>';  
              if($date->getValue('monat') == "3")
                 echo'<option value="03" selected>3</option>';
              else
                 echo'<option value="03">3</option>';
              if($date->getValue('monat') == "4")
                 echo'<option value="04" selected>4</option>';
              else
                 echo'<option value="04">4</option>';
              if($date->getValue('monat') == "5")
                 echo'<option value="05" selected>5</option>';
              else
                 echo'<option value="05">5</option>';
              if($date->getValue('monat') == "6")
                 echo'<option value="06" selected>6</option>';
              else
                 echo'<option value="06">6</option>';  
              if($date->getValue('monat') == "7")
                 echo'<option value="07" selected>7</option>';
              else
                 echo'<option value="07">7</option>';
              if($date->getValue('monat') == "8")
                 echo'<option value="08" selected>8</option>';
              else
                 echo'<option value="08">8</option>';                 
              if($date->getValue('monat') == "9")
                 echo'<option value="09" selected>9</option>';
              else
                 echo'<option value="09">9</option>';
              if($date->getValue('monat') == "10")
                 echo'<option value="10" selected>10</option>';
              else
                 echo'<option value="10">10</option>';  
              if($date->getValue('monat') == "11")
                 echo'<option value="11" selected>11</option>';
              else
                 echo'<option value="11">11</option>';
              if($date->getValue('monat') == "12")
                 echo'<option value="12" selected>12</option>';
              else
                 echo'<option value="12">12</option>';                                                 
         echo"</select>";
              // echo"Session" . $_SESSION['$neue_tn'];         
         if(!$date->getValue('dat_turniernummer'))
           { 
         echo '          
          <li>
            <dl>
              <dt><label for="dat_turniernummer">Turniernummer:</label></dt>
              <dd>';                     
                echo'<input type="text" id="dat_turniernummer" name="dat_turniernummer" style="width: 30%;" maxlength="10" value="' . $_SESSION['$neue_tn'] . '" />';                        
           }
         else
           { 
          echo '
          <li>
            <dl>
              <dt><label for="dat_turniernummer">Turniernummer:</label></dt>
              <dd>
                <input type="text" id="dat_turniernummer" name="dat_turniernummer" style="width: 30%;" maxlength="10" value="'. $date->getValue('dat_turniernummer'). '" />';
           } 
            echo '         
              </dd>
						</dl>
					</li>
    <li id="admDateTL">
            <dl>
              <dt><label for="dat_tl">Turnierleiter:</label></dt>
              <dd>
                <select size="1" id="dat_tl" name="dat_tl">
                  <option value="">- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
                  foreach( $tleiter as $key => $value)
                    {
                      echo '<option value="'.$value.'" ';
                      if($value == $date->getValue('dat_tl'))
                      {
                        echo ' selected="selected" ';
                      }
                      echo '>'.$value.'</option>';
                    }
                  echo '</select>
              </dd>
            </dl>
          </li>';
            echo '<li id="admDateTFORM">
            <dl>
              <dt><label for="dat_tform">Turnierform:</label></dt>
              <dd>
                <select size="1" id="dat_tform" name="dat_tform">
                  <option value="">- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
                  foreach( $tform as $key => $value)
                    {
                      echo '<option value="'.$value.'" ';
                      if($value == $date->getValue('dat_tform'))
                      {
                        echo ' selected="selected" ';
                      }
                      echo '>'.$value.'</option>';
                    }
                  echo '</select>
              </dd>
            </dl>
          </li>';
            echo '<li id="admDateTFORMINT">
            <dl>
              <dt><label for="dat_tform_international">Turnierart:</label></dt>
              <dd>
                <select size="1" id="dat_tform_international" name="dat_tform_international">';
                  foreach( $tform_international as $key => $value)
                    {
                      echo '<option value="'.$value.'" ';
                      if($value == $date->getValue('dat_tform_international'))
                      {
                        echo ' selected="selected" ';
                      }
                      echo '>'.$value.'</option>';
                    }
                  echo '</select>
              </dd>
            </dl>
          </li>'; 
            echo '<li id="admDateTFORMCUP">
            <dl>
              <dt><label for="dat_tform_cupserie">Cup-Serie:</label></dt>
              <dd>
                <select size="1" id="dat_tform_cupserie" name="dat_tform_cupserie">
                  <option value="">- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
                  foreach( $tform_cupserie as $key => $value)
                    {
                      echo '<option value="'.$value.'" ';
                      if($value == $date->getValue('dat_tform_cupserie'))
                      {
                        echo ' selected="selected" ';
                      }
                      echo '>'.$value.'</option>';
                    }
                  echo '</select>
                </dd>
              </dl>
            </li>';
                echo '<li>
                <dl>
                    <dt><label for="dat_skl">Startklasse RR:</label></dt>
                     <dd>
                      <input type="checkbox" id="dat_sk_s" name="dat_sk_s" ';
                      if($date->getValue('dat_sk_s') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" /><label for="dat_sk_s">S-Klasse</label>
                      <input type="checkbox" id="dat_sk_j" name="dat_sk_j" ';
                      if($date->getValue('dat_sk_j') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />              
                      <label for="dat_sk_j">J-Klasse</label>
                      <input type="checkbox" id="dat_sk_c" name="dat_sk_c" ';
                      if($date->getValue('dat_sk_c') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />              
                      <label for="dat_sk_c">C-Klasse</label>
                      <input type="checkbox" id="dat_sk_b" name="dat_sk_b" ';
                      if($date->getValue('dat_sk_b') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />              
                      <label for="dat_sk_b">B-Klasse</label>
                      <input type="checkbox" id="dat_sk_a" name="dat_sk_a" ';
                      if($date->getValue('dat_sk_a') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />              
                      <label for="dat_sk_a">A-Klasse</label>            
                    </dd>
                </dl>
            </li>';
            echo '<li>
                <dl>
                    <dt><label for="dat_skl">Startklasse BW:</label></dt>
                    <dd>
                      <input type="checkbox" id="dat_sk_bwh" name="dat_sk_bwh" ';
                      if($date->getValue('dat_sk_bwh') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_bwh">BW-Hauptklasse A</label>
                      <input type="checkbox" id="dat_sk_bwo" name="dat_sk_bwo" ';
                      if($date->getValue('dat_sk_bwo') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_bwo">BW-Oldieklasse A</label>
                      <input type="checkbox" id="dat_sk_bwj" name="dat_sk_bwj" ';
                      if($date->getValue('dat_sk_bwj') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_bwj">BW-Jugendklasse</label>
                   </dd>
                   <dd>
                      <input type="checkbox" id="dat_sk_bwh_b" name="dat_sk_bwh_b" ';
                      if($date->getValue('dat_sk_bwh_b') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_bwh_b">BW-Hauptklasse B</label>
                      <input type="checkbox" id="dat_sk_bwo_b" name="dat_sk_bwo_b" ';
                      if($date->getValue('dat_sk_bwo_b') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_bwo">BW-Oldieklasse B</label>

                   </dd>

                </dl>
            </li>';
            echo '<li>
                <dl>
                    <dt><label for="dat_skl">Startklasse Formation:</label></dt>
                    <dd>
                      <input type="checkbox" id="dat_sk_frm" name="dat_sk_frm" ';
                      if($date->getValue('dat_sk_frm') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_frm">Master RR</label>
                      <input type="checkbox" id="dat_sk_frj" name="dat_sk_frj" ';
                      if($date->getValue('dat_sk_frj') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_frj">Jugend RR</label>
                      <input type="checkbox" id="dat_sk_frl" name="dat_sk_frl" ';
                      if($date->getValue('dat_sk_frl') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_frl">Lady RR</label>
                      <input type="checkbox" id="dat_sk_frg" name="dat_sk_frg" ';
                      if($date->getValue('dat_sk_frg') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_frg">Girl RR</label>                        
                   </dd>
                   <dd>
                      <input type="checkbox" id="dat_sk_frs" name="dat_sk_frs" ';
                      if($date->getValue('dat_sk_frs') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_frs">Showteam RR</label>                                                                        
                      <input type="checkbox" id="dat_sk_fbm" name="dat_sk_fbm" ';
                      if($date->getValue('dat_sk_fbm') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />                    
                      <label for="dat_sk_fbm">Master BW</label>                                   
                   </dd>     
                </dl>
            </li>';
          echo '<li>
                <dl>
                    <dt><label for="dat_quali">Qualifikation gefordert:</label></dt>
                     <dd>
                      <input type="checkbox" id="dat_quali" name="dat_quali" ';
                      if($date->getValue('dat_quali') == 1)
                      {
                        echo ' checked="checked" ';
                      }
                      echo ' value="1" />
                        <label for="dat_quali">ja</label>           
                    </dd>
                </dl>
            </li>';  
//          echo '<li>
//                <dl>
//                    <dt><label for="dat_sk_bsp">Breitensport:</label></dt>
//                     <dd>
//                      <input type="checkbox" id="dat_sk_bsp" name="dat_sk_bsp" ';
//                      if($date->getValue('dat_sk_bsp') == 1)
//                      {
//                        echo ' checked="checked" ';
//                      }
//                      echo ' value="1" />
//                        <label for="dat_sk_bsp">ja</label>           
//                    </dd>
//                </dl>
//            </li>';
          echo '<li>
            <dl>
              <dt><label for="dat_ansprechpartner">Ansprechpartner:</label></dt>
              <dd>
                <input type="text" id="dat_ansprechpartner" name="dat_ansprechpartner" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_ansprechpartner'). '" />            
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_ansprechpartner_anschrift">Ansprechpartner Anschrift:</label></dt>
              <dd>
                <input type="text" id="dat_ansprechpartner_anschrift" name="dat_ansprechpartner_anschrift" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_ansprechpartner_anschrift'). '" />            
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_verein">Verein/Veranstalter:</label></dt>
              <dd>
                <input type="text" id="dat_verein" name="dat_verein" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_verein'). '" />
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_vereinsnummer">Vereinsnummer:</label></dt>
              <dd>
                <input type="text" id="dat_vereinsnummer" name="dat_vereinsnummer" style="width: 30%;" maxlength="6" value="'. $date->getValue('dat_vereinsnummer'). '" />      
              </dd>
            </dl>
          </li>';          
          echo '<li>
            <dl>
              <dt><label for="dat_tel">Telefon:</label></dt>
              <dd>
                <input type="text" id="dat_tel" name="dat_tel" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_tel'). '" />
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_fax">Fax:</label></dt>
              <dd>
                <input type="text" id="dat_fax" name="dat_fax" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_fax'). '" />
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_handy">Handy:</label></dt>
              <dd>
                <input type="text" id="dat_handy" name="dat_handy" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_handy'). '" />
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_mail">Mail zum Veranstalter:</label></dt>
              <dd>
                <input type="text" id="dat_mail" name="dat_mail" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_mail'). '" />
              </dd>
            </dl>
          </li>';        
          echo '<li>
            <dl>
              <dt><label for="dat_link">Link zur Veranstaltung:</label></dt>
              <dd>
                <input type="text" id="dat_link" name="dat_link" style="width: 90%;" maxlength="100" value="'. $date->getValue('dat_link'). '" />
              </dd>
            </dl>
          </li>';                               
        echo '
				</ul>
			</div>
		</div>';
	  
		echo'
		<div class="groupBox" id="admVisibilityRegistration">
			<div class="groupBoxHeadline" id="admVisibilityRegistrationHead">
				<a class="iconShowHide" href="javascript:showHideBlock(\'admVisibilityRegistrationBody\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
				id="admVisibilityRegistrationBodyImage" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>'.$gL10n->get('DAT_VISIBILITY').' & '.$gL10n->get('SYS_REGISTRATION').'
			</div>

			<div class="groupBoxBody" id="admVisibilityRegistrationBody">
				<ul class="formFieldList">
					<li id="liRoles"></li>
					<li>
						<dl>
							<dt>&nbsp;</dt>
							<dd><span id="add_attachment" class="iconTextLink">
									<a href="javascript:addRoleSelection(0)"><img
									src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('DAT_ADD_ROLE').'" /></a>
									<a href="javascript:addRoleSelection(0)">'.$gL10n->get('DAT_ADD_ROLE').'</a>
								</span></dd>
						</dl>
					</li>
                    <li>
						<dl>
							<dt>&nbsp;</dt>
							<dd>
								<input type="checkbox" id="dat_highlight" name="dat_highlight"';
								if($date->getValue('dat_highlight') == 1)
								{
									echo ' checked="checked" ';
								}
								echo ' value="1" />
								<label for="dat_highlight">'.$gL10n->get('DAT_HIGHLIGHT_DATE').'</label>
							</dd>
						</dl>
					</li>';

					// besitzt die Organisation eine Elternorga oder hat selber Kinder, so kann die Ankuendigung auf "global" gesetzt werden
					if($gCurrentOrganization->getValue('org_org_id_parent') > 0
						|| $gCurrentOrganization->hasChildOrganizations())
					{
						echo '
						<li>
							<dl>
								<dt>&nbsp;</dt>
								<dd>
									<input type="checkbox" id="dat_global" name="dat_global" ';
									if($date->getValue('dat_global') == 1)
									{
										echo ' checked="checked" ';
									}
									echo ' value="1" />
									<label for="dat_global">'.$gL10n->get('SYS_ENTRY_MULTI_ORGA').'</label>
									<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=SYS_DATA_GLOBAL&amp;inline=true"><img 
										onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=SYS_DATA_GLOBAL\',this)" onmouseout="ajax_hideTooltip()"
										class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
								</dd>
							</dl>
						</li>';
					}

					echo '
					<li>
						<dl>
							<dt>&nbsp;</dt>
              <dd>';
								if($dateRegistrationPossible == 1)
								{
                  echo'<input type="checkbox" id="dateRegistrationPossible" name="dateRegistrationPossible"';
									echo ' checked="checked" ';
                  echo ' value="1" />';
								}
                if($dateRegistrationPossible == 0)
                {
                  echo'<input type="checkbox" id="dateRegistrationPossible" name="dateRegistrationPossible"';
                  echo ' value="1" />';
                }
               
                echo' <label for="dateRegistrationPossible">'.$gL10n->get('DAT_REGISTRATION_POSSIBLE').'</label>
								<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DAT_LOGIN_POSSIBLE&amp;inline=true"><img 
									onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DAT_LOGIN_POSSIBLE\',this)" 
									onmouseout="ajax_hideTooltip()" class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
							</dd>
						</dl>
					</li>
					<li id="admAssignYourself">
						<dl>
							<dt>&nbsp;</dt>
							<dd>
								<input type="checkbox" id="dateCurrentUserAssigned" name="dateCurrentUserAssigned"';

								if($dateCurrentUserAssigned == 1)
								{

									echo ' checked="checked" ';
								}
								echo ' value="1" />
								<label for="dateCurrentUserAssigned">'.$gL10n->get('DAT_PARTICIPATE_AT_DATE').'</label>
								<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DAT_PARTICIPATE_AT_DATE_DESC&amp;inline=true"><img 
									onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DAT_PARTICIPATE_AT_DATE_DESC\',this)" 
									onmouseout="ajax_hideTooltip()" class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
							</dd>
						</dl>
					</li>
					<li id="admMaxMembers">
						<dl>
							<dt><label for="dat_max_members">'.$gL10n->get('DAT_PARTICIPANTS_LIMIT').':</label></dt>
							<dd>
								<input type="text" id="dat_max_members" name="dat_max_members" style="width: 50px;" maxlength="5" value="'.($date->getValue('dat_max_members')).'" />
								<a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DAT_MAX_MEMBERS&amp;inline=true"><img 
									onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DAT_MAX_MEMBERS\',this)" 
									onmouseout="ajax_hideTooltip()" class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
							</dd>
						</dl>
					</li>
				</ul>
			</div>
		</div>
		<div class="groupBox" id="admDescription">
			<div class="groupBoxHeadline" id="admDescriptionHead">
				<a class="iconShowHide" href="javascript:showHideBlock(\'admDescriptionBody\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
        id="admDescriptionBodyImage" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>'.'DRBV interne Notiz'.'
			</div>

			<div class="groupBoxBody" id="admDescriptionBody">
                <ul class="formFieldList">
                    <li>'.$ckEditor->createEditor('dat_description', $date->getValue('dat_description'), 'AdmidioDefault', 150).'</li>
                </ul>
            </div>
    </div>
    <div class="groupBox" id="admDescription2">                  
      <div class="groupBoxHeadline" id="admDescriptionHead2">
        <a class="iconShowHide" href="javascript:showHideBlock(\'admDescriptionBody2\', \''.$gL10n->get('SYS_FADE_IN').'\', \''.$gL10n->get('SYS_HIDE').'\')"><img
        id="admDescriptionBody2Image" src="'. THEME_PATH. '/icons/triangle_open.gif" alt="'.$gL10n->get('SYS_HIDE').'" title="'.$gL10n->get('SYS_HIDE').'" /></a>'.'&Ouml;ffentliche Bemerkung'.'
      </div>

      <div class="groupBoxBody" id="admDescriptionBody2">
        <ul class="formFieldList">
          <li>'.$ckEditor->createEditor('dat_notiz', $date->getValue('dat_notiz'), 'AdmidioDefault', 150).'</li>
        </ul>
      </div>
        </div>';

        // show informations about user who creates the recordset and changed it
        echo admFuncShowCreateChangeInfoById($date->getValue('dat_usr_id_create'), $date->getValue('dat_timestamp_create'), $date->getValue('dat_usr_id_change'), $date->getValue('dat_timestamp_change')).'

        <div class="formSubmit">
            <button id="admButtonSave" type="button"><img src="'. THEME_PATH. '/icons/disk.png" alt="'.$gL10n->get('SYS_SAVE').'" />&nbsp;'.$gL10n->get('SYS_SAVE').'</button>
        </div>
    </div>
</div>
</form>

<ul class="iconTextLinkList">
    <li>
        <span class="iconTextLink">
            <a href="'.$g_root_path.'/adm_program/system/back.php"><img
            src="'. THEME_PATH. '/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
            <a href="'.$g_root_path.'/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
        </span>
    </li>
</ul>';

require(SERVER_PATH. '/adm_program/system/overall_footer.php');
?>

