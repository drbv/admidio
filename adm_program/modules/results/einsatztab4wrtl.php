<?php
/******************************************************************************
 * Profil mit Wertung Wertungsrichter anzeigen
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * user_id: zeigt das Profil der uebergebenen user_id an
 *          (wird keine user_id uebergeben, dann Profil des eingeloggten Users anzeigen)
 *
 *****************************************************************************/
require_once('../../system/common.php');
require_once('../../system/login_valid.php');
require_once('../../system/drbv_funktionen.php');
require_once('../../system/classes/table_roles.php');

// Initialize and check the parameters
$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getTnrNr   = admFuncVariableIsValid($_GET, 'tnrsel', 'numeric', 0);  

unset($_SESSION['turnier']);

$getStartkl = $_POST['skl'];  
$getView    = $_POST['view'];  
$user_liznr = $_SESSION['profile_user_liznr'];

if($_POST['view'] == '') $getView = 'bogen';

  
if(!$_GET){
  if(!$_SESSION["lizenznr"] || $_POST["lizenznr"] != $_SESSION["lizenznr"])
      $_SESSION["lizenznr"] = $_POST["lizenznr"];  
  if(!$_SESSION["t_jahr"] || $_POST["t_jahr"] != $_SESSION["t_jahr"])
      $_SESSION["t_jahr"] = $_POST["t_jahr"];
  if(!$_SESSION["turnier"] || $_POST["turnier"] != $_SESSION["turnier"])
      $_SESSION["turnier"] = $_POST["turnier"];
  if(!$_SESSION["getAuswahl"] || $_POST["auswahl"] != $_SESSION["getAuswahl"])
      $_SESSION["getAuswahl"] = $_POST["auswahl"];  
} 

if(!$getTnrNr) $getTnrNr = $_SESSION["turnier"];  
if($_GET['tn']) $_SESSION['turniernummer'] = $_GET['tn'];

// create user object
$user = new User($gDb, $gProfileFields, $getUserId);

//Testen ob Recht besteht Profil einzusehn
if(!$gCurrentUser->viewProfile($user))
{
    $gMessage->show($gL10n->get('SYS_NO_RIGHTS'));
}
  
unset($_SESSION['profile_request']);
// Seiten fuer Zuruecknavigation merken
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gNavigation->clear();
}
$gNavigation->addUrl(CURRENT_URL);

// Funktion Startklasse gewertet
// -----------------------------  
function stkl_gewertet($getTnrNr, $getWrIdTlp){

  $sqlab  = 'SELECT rund_tab_id FROM wertungen WHERE turniernummer = "'.$getTnrNr.'" AND wr_id = "'.$getWrIdTlp.'" ';
  $runden = mysqli_query(DRBVdb(), $sqlab);
  $temp   = mysqli_fetch_array($runden);

  while($temp = mysqli_fetch_array($runden)){      
    $sqlab  = 'SELECT startklasse FROM rundentab WHERE turniernummer = "'.$getTnrNr.'" AND rt_id_tlp = "'.$temp[0].'"';
    $stkl   = mysqli_query(DRBVdb(),$sqlab);
    
    while($temp = mysqli_fetch_array($stkl)){
      $stkl_a[] = $temp[startklasse];
    }    
  }
  //print_r(array_unique($stkl_a));echo' :stkl_a<br>';    
  return array(array_unique($stkl_a));
}
    
// Funktion Turniere gewertet
// --------------------------  
function turniere_gewertet($jahr, $lizenznr){
  
  $trnr_gewertet      = array();
  $stkl_gewertet      = array();
  $html_stkl_gewertet = array();
  $anzahl_gewertet    = 0;
  
  $sqlab = 'SELECT turniernummer,lizenznummer,name,wr_id_tlp FROM wertungsrichter WHERE lizenznummer='.$lizenznr.'';
  $turnier_gewertet = mysqli_query(DRBVdb(), $sqlab);

  while($temp = mysqli_fetch_array($turnier_gewertet)){
    $sqlab     = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$temp[0].' AND YEAR(datum) = "'.$jahr.'"'; 
    $turnier   = mysqli_query(DRBVdb(), $sqlab);
    $aktuell   = mysqli_fetch_row($turnier);
        
    if($jahr != '' && substr($aktuell[2],0,4) == $jahr){
      $gew_stkl  = stkl_gewertet($aktuell[0],$temp[3]);      
      $html_stkl = '';
      foreach($gew_stkl[0] as $key){
        $html_stkl .= sklbezeichnung_kurz($key).' / ';
      }      
      $trnr_gewertet[]      = '('.$aktuell[0].') '.utf8_encode($aktuell[1]);
      $stkl_gewertet[]      = count($gew_stkl[0]);
      $html_stkl_gewertet[] = substr($html_stkl,0,-2);
      $anzahl_gewertet++;
    }
  }
  //print_r($trnr_gewertet);echo' :trnr_gewertet<br>';
  //print_r($anzahl_gewertet);echo' :anzahl_gewertet<br>';
  return array($trnr_gewertet,$anzahl_gewertet,$stkl_gewertet,$html_stkl_gewertet);
}  
  
// Funktion Turniere geleitet
// --------------------------  
function turniere_geleitet($jahr, $lizenznr){
  
  $trnr_geleitet   = array();
  $anzahl_geleitet = 0;
  
  $sqlab = 'SELECT turniernummer,lizenznummer,name FROM T_Leiter WHERE lizenznummer='.$lizenznr.'';
  $turnier_geleitet = mysqli_query(DRBVdb(), $sqlab);

  while($temp = mysqli_fetch_array($turnier_geleitet)){
    $sqlab     = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$temp[0].' AND YEAR(datum) = "'.$jahr.'"'; 
    $turnier   = mysqli_query(DRBVdb(), $sqlab);
    $aktuell   = mysqli_fetch_row($turnier);
    
    if($jahr != '' && substr($aktuell[2],0,4) == $jahr){
      $trnr_geleitet[] = '('.$aktuell[0].') '.utf8_encode($aktuell[1]);
      $anzahl_geleitet++;
    }
  }
  //print_r($trnr_geleitet);echo' :trnr_geleitet<br>';
  //print_r($anzahl_geleitet);echo' :anzahl_geleitet<br>';
  return array($trnr_geleitet,$anzahl_geleitet);
}

// Turnierinfo aus DB suchen:
// --------------------------  
$sqlab       = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE turniernummer = '.$getTnrNr.''; 
$turniersel  = mysqli_query(DRBVdb(), $sqlab);
$turnierinfo = mysqli_fetch_row($turniersel);    
$getTnrName  = utf8_encode($turnierinfo[1]);      
$getTnrDatum = new DateTime($turnierinfo[2]);
                
// Html-Kopf ausgeben
$gLayout['header'] = '
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/modules/profile/profile.js"></script>
    <script type="text/javascript">       
    <!--
        var profileJS = new profileJSClass();
            profileJS.deleteRole_ConfirmText   = \''.$gL10n->get('ROL_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.deleteFRole_ConfirmText   = \''.$gL10n->get('ROL_LINK_MEMBERSHIP_DEL',"[rol_name]").'\';
            profileJS.changeRoleDates_ErrorText = \''.$gL10n->get('ROL_CHANGE_ROLE_DATES_ERROR').'\';
            profileJS.setBy_Text        = \''.$gL10n->get('SYS_SET_BY').'\';
            profileJS.usr_id = '.$user->getValue('usr_id').';
            
            function showHideMembershipInformation(element)
            {
          id = "#" + element.attr("id") + "_Content";

          if($(id).css("display") == "none") {
              $(id).show("fast");
          }
          else {
              $(id).hide("fast");
          }
            }

      $(document).ready(function() {
        profileJS.init();
        $("a[rel=\'lnkDelete\']").colorbox({rel:\'nofollow\', scrolling:false, onComplete:function(){$("#admButtonNo").focus();}});
          $(".admMemberInfo").click(function () { showHideMembershipInformation($(this)) });
      });
      
    //-->
    </script>';
  
require(SERVER_PATH. '/adm_program/system/overall_header.php');          
    
echo '  
<div class="formLayout" id="profile_form" width="100px">
  <div class="formHead">Einsatzübersicht aller Wertungsrichter und Turnierleiter</div>
  <div class="formBody">';
                 
  // *******************************************************************************
  // Userdaten-Block
  // *******************************************************************************
          
  // Wertungsrichter aus Datenbank lesen
  $sqlab = 'SELECT lizenznummer,name,wr_id_tlp FROM wertungsrichter ORDER BY lizenznummer';
  $wertungsrichter = mysqli_query(DRBVdb(), $sqlab);

  // Turnierleiter aus Datenbank lesen
  $sqlab = 'SELECT lizenznummer,name,funktion FROM T_Leiter ORDER BY lizenznummer';
  $turnierleiter = mysqli_query(DRBVdb(), $sqlab);

  if(!$_SESSION["t_jahr"]){        
    $sqlab = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE YEAR(datum) = "2016"';
  } else {
    $sqlab = 'SELECT turniernummer,turniername,datum FROM Turnier WHERE YEAR(datum) = "'.$_SESSION["t_jahr"].'"';  
  } 
  $turnier = mysqli_query(DRBVdb(), $sqlab);
  
  while($temp = mysqli_fetch_array($turnier)){
    $trnr_name_key[$temp[0]] = utf8_encode($temp[1]);
  }
                
  while($temp = mysqli_fetch_array($wertungsrichter)){
    if(!in_array($temp[0], $wr_lizenz)){
      $wr_lizenz[] = $temp[0];
      $wr_name[]   = utf8_encode($temp[1]);
      $wr_funk[]   = utf8_encode($temp[2]);
      $wr_name_key[$temp[0]] = utf8_encode($temp[1]);
    }
  }         
  //print_r($wr_lizenz);echo' :wr_lizenz<br>';
  //print_r($wr_name);echo' :wr_name<br>';
  //print_r($wr_funk);echo' :wr_funk<br>';
  //print_r($wr_name_key);echo' :wr_name_key<br>';

  while($temp = mysqli_fetch_array($turnierleiter)){
    if(!in_array($temp[0], $tl_lizenz)){
      $tl_lizenz[] = $temp[0];
      $tl_name[]   = utf8_encode($temp[1]);
      $tl_funk[]   = utf8_encode($temp[2]);
      $tl_name_key[$temp[0]] = utf8_encode($temp[1]);
    }
  }         

  echo'      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Auswahl nach Jahr, Wertungsrichter oder Turnierleiter</div>
      </div>
      <form action="'.$_SERVER["PHP_SELF"].'" method=post>
      <div class="groupBoxBody">
      <ul class="formFieldList">
        <li>
          <dl>
            <dt>Jahr:</dt>
            <dd>
              <select name="t_jahr" size="1" onchange="submit();">';
                for($x = 2016;$x < 2030;$x++){
                  if($_SESSION["t_jahr"] == $x){
                    echo '<option value="'.$x.'"  selected>'.$x.'</option>';
                  } else {
                    echo'<option value="' . $x . '">' . $x  . '</option>';
                  }
                }
   echo'      </select>          
            </dd>
          </dl>       
        </li>
      </ul>      
      </div>
    </form>
    </div>';
    //class="groupBox" end

    echo'
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadewrtab\').click(function(){$(\'#refwrtab\').toggle(\'slow\');
      })
      });
    </script>    
      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Wertungsrichter&uuml;bersicht&nbsp
          <a class="admWRtabelle" href="#wrtab" id="fadewrtab"><img src="'. THEME_PATH. '/icons/add.png" alt="WR Tabelle" /><br></a>   
        </div>
      </div>
      <div class="groupBoxBody">
      <div style="display:none;" class="groupBoxBody" id="refwrtab">
        <table border=0 style="font-size: 10pt;">
          <tr valign=top>
            <td><b>Lizenz - Name</b></td>';
            if(!$_SESSION["t_jahr"]){
              echo'<td colspan=2 align="left"><b>Anzahl Turniere 2016</b></td>';
            } else {
              echo'<td colspan=2 align="left"><b>Anzahl Turniere '.$_SESSION["t_jahr"].'</b></td>';          
            }
            echo'<td colspan=7><b>Gewertete Startklassen</b></td>
          </tr>';
        for($i=0;$i<count($wr_lizenz);$i++){
          echo'
            <tr valign=top>
              <td>'.$wr_lizenz[$i].' - '.$wr_name[$i].'</td>';
          $today = getdate();
          if(!$_SESSION["t_jahr"]){
            list($trnr_gewertet,$anzahl_gewertet,$stkl_gewertet,$html_stkl_gewertet) = turniere_gewertet(2016,$wr_lizenz[$i]);
          } else {
            list($trnr_gewertet,$anzahl_gewertet,$stkl_gewertet,$html_stkl_gewertet) = turniere_gewertet($_SESSION["t_jahr"],$wr_lizenz[$i]);          
          }          
          echo'
            <td width=25px align="center">'.$anzahl_gewertet.'</td>
            <td>';            
            foreach($trnr_gewertet as $name){
              echo'<b>'.substr($name,1,7).'</b> '.substr($name,10).'<br>';                      
            }          
          echo'
            </td>
            <td colspan=2 align=center width=25px bgcolor="dfac20">';
            foreach($stkl_gewertet as $name){
              echo'<b>'.$name.'</b><br>';
            }
          echo'
            </td>
            <td colspan=5 align=left width=250px bgcolor="dfac20">';
            foreach($html_stkl_gewertet as $name){
              echo $name.'<br>';
            }
          echo'
            </td>
          </tr>';    
        }//end for
  echo'</table>        
      </div>
      </div>
    </div>';
    //class="groupBox" end

  echo'      
    <script>
      $(document).ready(function(){
        /* jQuery-Code */
        $(\'#fadetltab\').click(function(){$(\'#reftltab\').toggle(\'slow\');
      })
      });
    </script>   
      
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
    <div class="groupBox">
      <div class="groupBoxHeadline">
        <div style="float: left;">Turnierleiter&uuml;bersicht&nbsp
          <a class="admTLtabelle" href="#tltab" id="fadetltab"><img src="'. THEME_PATH. '/icons/add.png" alt="TL Tabelle" /><br></a>
        </div>
      </div>
      <div class="groupBoxBody">
      <div style="display:none;" class="groupBoxBody" id="reftltab">
        <table border=0 style="font-size: 10pt;">
          <tr valign=top>
            <td><b>Lizenz - Name</b></td>';
            if(!$_SESSION["t_jahr"]){
              echo'<td colspan=2 align="left"><b>Anzahl Turniere 2016</b></td>';
            } else {
              echo'<td colspan=2 align="left"><b>Anzahl Turniere '.$_SESSION["t_jahr"].'</b></td>';          
            }
            echo'<td colspan=7><b>Funktion</b></td>
          </tr>';
        for($i=0;$i<count($tl_lizenz);$i++){
          echo'
            <tr valign=top>
              <td>'.$tl_lizenz[$i].' - '.$tl_name[$i].'</td>';
          $today = getdate();
          if(!$_SESSION["t_jahr"]){
            list($trnr_geleitet,$anzahl_geleitet) = turniere_geleitet(2016,$tl_lizenz[$i]);
          } else {
            list($trnr_geleitet,$anzahl_geleitet) = turniere_geleitet($_SESSION["t_jahr"],$tl_lizenz[$i]);          
          }
          echo'
            <td width=25px align="center">'.$anzahl_geleitet.'</td>
            <td>';            
            foreach($trnr_geleitet as $name){
              echo'<b>'.substr($name,1,7).'</b> '.substr($name,10).'<br>';                      
            }          
          echo'
            </td>
            <td colspan=7 align=center width=25px bgcolor="dfac20">';
            foreach($trnr_geleitet as $name){
              echo'<b>'.$tl_funk[$i].'</b><br>';
            }
          echo'
            </td>
          </tr>';    
        }//end for
  echo'</table>        
      </div>
      </div>
    </div>';
    //class="groupBox" end  
  echo '
  </div>
</div>';//class="formLayout" end
  
echo '
<ul class="iconTextLinkList">
  <li>
  <span class="iconTextLink">
     <a href="'.$g_root_path.'/adm_program/modules/profile/profile.php?user_id='.$user->getValue('usr_id').'">'.$gL10n->get('SYS_BACK').'</a>
  </span>
  </li>
</ul>';
    
require(SERVER_PATH. '/adm_program/system/overall_footer.php');

?>
