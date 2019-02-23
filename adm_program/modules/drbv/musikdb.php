<?php
/******************************************************************************
 * Musik Datenbank / Turnierleiter
 *
 * Copyright    : (c) 2018 DRBV Webteam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * user_id: zeigt das Profil der uebergebenen user_id an
 *          (wird keine user_id uebergeben, dann Profil des eingeloggten Users anzeigen)
 *
 *****************************************************************************/
require_once('../../system/common.php');
require_once('../../system/classes/table_roles.php');
require_once('../../system/drbv_funktionen.php');

unset($_SESSION[$sk]);
unset($_SESSION['link']);
unset($_SESSION['titel']);
  
// Initialize and check the parameters
$getUserId = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
$getFormId = admFuncVariableIsValid($_GET, 'form_id', 'numeric', 0);
$getMode   = admFuncVariableIsValid($_GET, 'mode');
  
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

function datenbankabfrage($query, $valueQuotes, $separator, $sk='', $rundenbz='', $ersatz=''){
  global $str_body, $anzahl_auswahl, $y;
  unset($lfdnr);
  $lieder = mysqli_query(MUSIKdb(), $query);
  $anzahl_auswahl = mysqli_affected_rows(MUSIKdb());

  while($temp = mysqli_fetch_array($lieder)){
    $_SESSION[y] = $_SESSION[y] + 1;
    $y = $y + 1;

    $wertung = $temp['wertung'];
    $bezeichnung = str_replace('&', '&teil2=', $temp['bezeichnung']);
   
    unset($stern);
    if($wertung == 1) $stern = '<img src="./1Stern.png">';
    if($wertung == 2) $stern = '<img src="./2Sterne.png">';
    if($wertung == 3) $stern = '<img src="./3Sterne.png">';
    if($wertung == 4) $stern = '<img src="./4Sterne.png">';
    if($wertung == 5) $stern = '<img src="./5Sterne.png">';

    $id        = $temp['id'];
    $titel     = $temp['titel'];
    $interpret = $temp['interpret'];
    $takte     = $temp['takte'];
    $genre     = $temp['genre'];
    if($temp['rocknroll'] == 1){
      $rr = '&bull;';
    } else {
      $rr = '';
    }
    if($temp['boogiewoogie'] == 1){
      $bw = '&bull;';
    } else {
      $bw = '';
    }  

    $str_html .='<tr><td align="center">'.$y.'</td>';
    
    if($_POST['turnier_erstellen'] == 1 && !$_SESSION[$sk][$_SESSION[y]]){
      if(in_array($id, $_SESSION['doppelt'])){
        $ersatzlieder = mysqli_query(MUSIKdb(), $ersatz);
        while($temp1 = mysqli_fetch_array($ersatzlieder)){
          $id = $temp1['id'];
          if(!in_array($id, $_SESSION['doppelt'])){
            $wertung = $temp1['wertung'];
            $bezeichnung = str_replace('&', '&teil2=', $temp1['bezeichnung']);

            unset($stern);
            if($wertung == 1) $stern = '<img src="./1Stern.png">';
            if($wertung == 2) $stern = '<img src="./2Sterne.png">';
            if($wertung == 3) $stern = '<img src="./3Sterne.png">';
            if($wertung == 4) $stern = '<img src="./4Sterne.png">';
            if($wertung == 5) $stern = '<img src="./5Sterne.png">';
                  
            $titel     = $temp1['titel'];
            $interpret = $temp1['interpret'];
            $takte     = $temp1['takte'];
            $genre     = $temp1['genre'];
            $link      = $temp1['link'];
            $dateiname = $temp1['dateiname'];

            break;
          }
        }//end while
      }//end if(in_array($id, $_SESSION['doppelt']))
        
      
      if(in_array($id, $_SESSION['doppelt']))
        $str_html .='<td align="center" bgcolor="#FFCC00"><input type="checkbox" name="'.$sk.$_SESSION[y].'" value="1" checked>';
      else 
        $str_html .='<td align="center"><input type="checkbox" name="'.$sk.$_SESSION[y].'" value="1" checked>'; 
      $_SESSION[$sk][$_SESSION[y]] = $id;
      $_SESSION['doppelt'][] = $id;
      $_SESSION['titel_doppelt'][] = $titel;
      $str_html .='</td>';
    } // end if($_POST['turnier_erstellen'] == 1 && !$_SESSION[$sk][$_SESSION[y]])
    else if(current($_SESSION[$sk])){
      $str_html .='<td align="center"><input type="checkbox" name="'.$sk.$rundenbz.$_SESSION[y].'" >';
    }
    else {
      $str_html .='<td align="center"><input type="checkbox" name="id'.$sk.$rundenbz.$_SESSION[y].'" id="id'.$sk.$rundenbz.$_SESSION[y].'" value="'.$sk.$rundenbz.$_SESSION[y].'" checked><label for="id'.$sk.$_SESSION[y].'"></label></td>'; 
    }
    
    $str_html .='<td>'.$titel.'</td><td>'.$interpret.'</td><td align="center">'.$takte.'</td><td align="center">'.$genre.'</td><td align="center">'.$stern.'</td>';               
    $str_html .='<td style="background:#cccccc;"><audio controls preload="none">
           <source src="https://www.drbv.de/turniermusik/musikdb.php?pfad='.$temp['pfad'].'&file='.$bezeichnung.'" type="audio/mpeg">
           Your browser does not support the audio element.
           </audio></td></tr>';
    
    // Zeile für CSV-Datei
    $separator   = ";";
    $valueQuotes = '"';
//    $str_csv[$sk.$rundenbz.$_SESSION[y]] = $valueQuotes.$sk.$valueQuotes.
//             $separator.$valueQuotes.$rundenbz.$valueQuotes.  
//             $separator.$valueQuotes.$titel.$valueQuotes.  
//             $separator.$valueQuotes.$interpret.$valueQuotes.  
//             $separator.$valueQuotes.$takte.$valueQuotes.  
//             $separator.$valueQuotes.$genre.$valueQuotes.  
//             $separator.$valueQuotes.'https://www.drbv.de/turniermusik/musikdb.php?pfad='.$temp['pfad'].'&file='.$bezeichnung.$valueQuotes.  
//             $separator.$valueQuotes.$bezeichnung.$valueQuotes."\r\n";      

    $str_csv[$sk.$rundenbz.$_SESSION[y]] = $valueQuotes.$sk.'_'.$rundenbz.$valueQuotes.
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$takte.'_'.$titel.'-'.$interpret.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes.  
             $separator.$valueQuotes.'https://www.drbv.de/turniermusik/musikdb.php?pfad='.$temp['pfad'].'&file='.$bezeichnung.$valueQuotes.  
             $separator.$valueQuotes.$valueQuotes."\r\n";              
  }//end while
  return array($str_html,$str_csv);
//  return $str_html;
}//end function datenbankabfrage
  
function get_db_data($startklasse,$startzahl,$rundenkz,$hoffrnd){
  
  global $MUSIKdb, $valueQuotes, $separator, $str_csv_download;       
    
  unset($y);
  unset($_SESSION[y]);
    
  $rundenbz = '';
  $wrtg_tit = '';    
  $wrtg_ers = '';    
  if($rundenkz == '_v'){
    $rundenbz = 'Vorrunde';
    $wrtg_tit = 'wertung >="2" AND wertung <="3"';
    $wrtg_ers = 3;    
    $anzahl_s = round(($startzahl +0.5) / 2) + 3;    
  }
  if($rundenkz == '_h'){
    $rundenbz = 'Hoffnungsrunde';
    $wrtg_tit = 'wertung >="2" AND wertung <="3"';
    $wrtg_ers = 3;    
    $anzahl_s = round(($startzahl +0.5) / 2);    
  }
  if($rundenkz == '_z'){
    $rundenbz = 'Zwischenrunde';
    $wrtg_tit = 'wertung ="4"';
    $wrtg_ers = 4;    
    $anzahl_s = round(($startzahl +0.5) / 4);//thomas hatte hier fest 10??? bei 50% kann durch 4 geteilt werden   
  }  
  if($rundenkz == '_e'){
    $rundenbz = 'Endrunde';
    $wrtg_tit = 'wertung >="4"';
    $wrtg_ers = 4;    
    $anzahl_s = 7+3; //lt. TSO Auswahl aus 10 Titeln
  }
  if($rundenkz == '_ea'){
    $rundenbz = 'Endrunde Akrobatik';
    $wrtg_tit = 'wertung >="4"';
    $wrtg_ers = 4;    
    $anzahl_s = 5; 
  }
  if($rundenkz == '_ef'){
    $rundenbz = 'Endrunde Fußtechnik';
    $wrtg_tit = 'wertung >="4"';
    $wrtg_ers = 4;    
    $anzahl_s = 5; 
  }
  
  $klassenbz = '';
  $tempo     = '';
  if($startklasse == 's_klasse'){
    $klassenbz = 'RR_S';
    $tempo     = 47;
  }
  if($startklasse == 'j_klasse'){
    $klassenbz = 'RR_J';
    $tempo     = 47;
  }
  if($startklasse == 'c_klasse'){
    $klassenbz = 'RR_C';
    $tempo     = 48;
  }
  if($startklasse == 'b_klasse'){
    $klassenbz = 'RR_B';
    if($rundenkz == '_ef'){
      $tempo     = 50;
    } else {
      $tempo     = 49;      
    }
  }
  if($startklasse == 'a_klasse'){
    $klassenbz = 'RR_A';
    if($rundenkz == '_ef'){
      $tempo     = 50;
    } else {
      $tempo     = 49;      
    }
  }
  //print_r($_SESSION['titel_doppelt']);echo' :titel_doppelt<br>';
  $exclude_titel = implode("\", \"", $_SESSION['titel_doppelt']);    
  //echo $exclude_titel;
  
  $db_data_str  = '<tr><th style="background:#cccccc;" colspan ="8"><b><i><br>'.$klassenbz.' '.$rundenbz.'</i></b></th></tr>';    
  $str_body     = $valueQuotes.$klassenbz.' '.$rundenbz.$valueQuotes."\r\n";        
  $sqlab        = 'SELECT * From turniermusik WHERE titel NOT IN ("'.$exclude_titel.'") AND takte ="'.$tempo.'" AND rocknroll ="1" AND '.$wrtg_tit.' ORDER BY RAND() LIMIT '.$anzahl_s;
  $ersatz       = 'SELECT * From turniermusik WHERE takte ="'.$tempo.'" AND rocknroll ="1" AND wertung ="'.$wrtg_ers.'" ORDER BY RAND() LIMIT 50';
  list($str_html, $str_csv) = datenbankabfrage($sqlab, $valueQuotes, $separator, $klassenbz, $rundenbz, $ersatz);
  
  foreach ($str_csv as $key => $value){
    $str_csv_download[$key] = $value;
  }  
  //print_r($str_csv_download);echo' :str_csv_download<br>';
  $db_data_str .= $str_html;
  return $db_data_str;
}  
    
function phpalert($msg){
  echo'<script Type = "text/javascript">alert("'.$msg.'")</script>';
}
 
// Html-Kopf ausgeben
if($user->getValue('usr_id') == $gCurrentUser->getValue('usr_id'))
{
    $gLayout['title'] = $gL10n->get('PRO_MY_MUSIKDB');
}
else
{
    $gLayout['title'] = $gL10n->get('PRO_PROFILE_FROM', $user->getValue('FIRST_NAME'), $user->getValue('LAST_NAME'));
}
  
if($_POST['download']){  
  header('Content-Type: text/comma-separated-values; charset=iso-8859-1');
  header('Content-Disposition: attachment; filename="'.$_POST['dateiname'].'.csv"');    
  //neccessary for IE, because without it the download with SSL has problems
  header('Cache-Control: private');
  header('Pragma: public');
  //print_r($_POST);echo" :POST<br>";  
} else {
  $gLayout['header'] = '
    <link rel="stylesheet" href="'.THEME_PATH. '/css/calendar.css" type="text/css" />
    <link rel="stylesheet" href="'.$g_root_path.'/adm_program/modules/forms/forms.css" type="text/css" />         
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/libs/calendar/calendar-popup.js"></script>
    <script type="text/javascript" src="'.$g_root_path.'/adm_program/system/js/form.js"></script>         
    <!-- Einbindung der Jquery Libary -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>         
    <script type="text/javascript">
        $(function() {
            $(\'#formSwitcher input\').on(\'change\', function() {
                var url = \'musikdb.php\';
                var queryString = \'?bestaetige=\';
                var cnt = 0;
                $(\'#formSwitcher input:checked\').each(function() {
                    cnt++;
                    queryString += cnt > 1 ? \',\' : \'\';
                    queryString += $(this).val();
                })
                $(\'#dynLink\').attr(\'href\', url + queryString);
            })
        })
    </script>
    <script type="text/javascript">
      function stopEnterKey(evt) {
        var evt = (evt) ? evt : ((event) ? event : null);
        var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
        if ((evt.keyCode == 13) && (node.type=="text")) {return false;}
      }
      document.onkeypress = stopEnterKey;
    </script>
    <script type="text/javascript">
      $(function(){
        formSwitch.init();
      })

    var formSwitch = {
    formId: \'#formSwitcher\', // ID des Formulars
    formValWrap: \'#wechsler\', //die ID des Select elements
    pasteId: \'#pasteMe\', // die ID der Div in die später der Formularinhalt eingegeben wird
    idSel: null, // brauchen wir später um festzustellen wlcher Inhalt eingebunden werden soll
    init: function(){
        var self = this;
        // die check funktion wird gestartet sobald sich der Wert der Liste ändert
        $(this.formValWrap).change(function(){
            self.idSel= $(this).val(); // gewählte Wert wird gespeichert
            self.check();
        })
            
    },
    check: function(){
    // da der Wert sich öffters ändern kann, wenn der user mehrmals das Selectfeld benutzt prüfen wir erst ob es bereits    
        if($(this.pasteId).children().length>0){
            $(this.pasteId).html(\'\'); // wenn Elelemente vorhanden dann löschen 
            }
        this.paste(); // aufruf der Funktion zum klonen der Inhalte und dem Sichtbarschalten
    },
    paste:function(){
        $(\'#\'+this.idSel).clone().appendTo(this.pasteId);
        $(this.pasteId).children().css(\'display\',\'block\');
    }
  }
  </script>
           
  <style type="text/css">
    .TableMusikDB th {
      background:  #4dc5c3;
      color #000;
      font-family: verdana, sans-serif;
      font-size:   10pt;  
      font-weight: normal;  
    }           
    .TableMusikDB td {
      font-family: verdana, sans-serif;
      font-size:   10pt;  
      font-weight: normal;  
    }
    .TableMusikDB tbody tr:nth-child(even) { 
      background-color: #e4ebf2; 
      color: #000; 
    }
    .TableMusikDB tbody tr:nth-child(odd) { 
      background-color: #4dc5c3; 
      color: #000; 
    }
  </style>
           
  <style type="text/css">
    .hideMe{display:none;}
  </style>';

  require(SERVER_PATH. '/adm_program/system/overall_header.php');
  //print_r($_POST);echo" :POST<br>";  
}//end if $_POST['download']) 
  
$html_out = '
<div class="formLayout" id="profile_form">
    <div class="formHead">'. $gLayout['title']. '</div>
    <div class="formBody">
        <div>';
        // *******************************************************************************
        // Userdaten-Block
        // *******************************************************************************
        $html_out .= '
          <div style="width: 100%; float: left;">
            <div id="admProfileMasterData" class="groupBox">
               <div class="groupBoxBody">
               <form name="MusikAbfrage" id="formSwitcher" method="post" autocomplete="off" accept-charset="UTF-8">                  
                 <font face="Verdana" size="3" color="#000080">
                 <br />
                 <fieldset>
                   <legend>Auswahlmodus:</legend>               
                   <select name="turnier_erstellen" id="wechsler" onchange="submit();">';
                     $auswahlmodus = '---';
                     if($_POST["turnier_erstellen"]) $auswahlmodus = $_POST["turnier_erstellen"];
                     if($auswahlmodus == '---'){
                       $html_out .='<option value="---" selected>Auswahlmodus...</option>';
                     } else {
                       $html_out .='<option value="---" >Auswahlmodus...</option>';
                     }
                     if($auswahlmodus == 'turnier'){  
                       $html_out .='<option value="turnier" selected>Turnier erstellen</option>';
                     } else {
                       $html_out .='<option value="turnier">Turnier erstellen</option>';
                     }                     
                     if($auswahlmodus == 'manuell'){  
                       $html_out .='<option value="manuell" selected>Manuell auswählen</option>';
                     } else {
                       $html_out .='<option value="manuell">Manuell auswählen</option>';
                     }
                   $html_out .='
                   </select>                    
                   im Bereich';
                   $skl = 'rr';
                   if($_POST["startklasse"]) $skl = $_POST["startklasse"];
                   if($skl == 'rr'){ 
                     $html_out .='<input id="rr" name="startklasse" type="radio" value="rr" checked onchange="submit();"/>Rock\'n\'Roll';
                   } else {
                     $html_out .='<input id="rr" name="startklasse" type="radio" value="rr" onchange="submit();"/>Rock\'n\'Roll';
                   }
                   if($skl == 'bw'){ 
                     $html_out .='<input id="bw" name="startklasse" type="radio" value="bw" checked onchange="submit();"/>Boogie-Woogie';
                   } else {
                     $html_out .='<input id="bw" name="startklasse" type="radio" value="bw" onchange="submit();"/>Boogie-Woogie';
                   }
                 $html_out .='                    
                 </fieldset>
                 <br />
                 </font>
               
               <!-- Versteckte Container hier fallen lassen -->
               <div id="pasteMe"></div>';
  
               if($auswahlmodus == 'manuell'){
               $html_out .='     
               <!-- Container: Manuell  -->
               <div id="manuell" class="hideMe-NOT">
                 <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                 <font face="Verdana" size="3" color="#000080">                    
                 <fieldset>
                   <legend>Manuelle Auswahl</legend>
                   <table>
                     <tr>
                       <td>Anzahl Titel:</td>
                       <td>';
                       $html_out .='<select name="anzahl" onchange="submit();">';
                       if($_POST["anzahl"] == '0')
                          $html_out .='<option value="0" selected>0</option>';
                       else
                          $html_out .='<option value="0">0</option>';
                       for($x=1;$x<=30;$x++){
                         if($_POST["anzahl"] == $x)
                           $html_out .='<option value="'.$x.'" selected>'.$x.'</option>';
                         else
                           $html_out .='<option value="'.$x.'">'.$x.'</option>';
                         $html_out .= $x.'<br>';
                       }
                       $html_out .='</select>';
                       $html_out .=' 
                       </td>
                     </tr>
                     <tr>
                       <td>Tempo:</td>
                       <td>';                       
                       if($skl == 'rr'){                   
                         $tempo_von = 47;                         
                         $tempo_bis = 52;
                         $tempo_min = 47;
                         $tempo_max = 52;
                       } else {
                         $tempo_von = 27;
                         $tempo_bis = 54;                       
                         $tempo_min = 27;
                         $tempo_max = 54;
                       }                         
                       if($_POST["von"]) $tempo_von = $_POST["von"];
                       if($_POST["bis"]) $tempo_bis = $_POST["bis"];                   
                       $html_out .='<select name="von" onchange="submit();">';
                       if($tempo_von == $tempo_min)
                          $html_out .='<option value="'.$tempo_min.'" selected>'.$tempo_min.'</option>';
                       else
                          $html_out .='<option value="'.$tempo_min.'">'.$tempo_min.'</option>';
                       for($x=$tempo_min+1;$x<=$tempo_max;$x++){
                         if($tempo_von == $x)
                           $html_out .='<option value="'.$x.'" selected>'.$x.'</option>';
                         else
                           $html_out .='<option value="'.$x.'">'.$x.'</option>';
                         $html_out .= $x.'<br>';
                       }
                       $html_out .='</select>';
                       $html_out .=' bis ';
                       $html_out .='<select name="bis" onchange="submit();">';
                       for($x=$tempo_von;$x<$tempo_max;$x++){
                        if($tempo_bis == $x)
                          $html_out .='<option value="'.$x.'" selected>'.$x.'</option>';
                        else
                          $html_out .='<option value="'.$x.'">'.$x.'</option>';
                        $html_out .= $x.'<br>';
                       }
                       if($tempo_bis == $tempo_max)
                         $html_out .='<option value="'.$tempo_max.'" selected>'.$tempo_max.'</option>';
                       else
                         $html_out .='<option value="'.$tempo_max.'">'.$tempo_max.'</option>';
                       $html_out .='</select> Takte';                                                  
                       $html_out .=' 
                       </td>
                     </tr>';
                     $sel_genre = '---';
                     if($_POST["genre"]) $sel_genre = $_POST["genre"];                       
                     if($skl == 'bw'){
                       $sqlab = 'SELECT genre FROM turniermusik ORDER BY genre';
                       $genres_auswahl = mysqli_query(MUSIKdb(), $sqlab);                       
                       $html_out .='
                       <tr>
                         <td>Genre:</td>
                         <td>
                           <select name="genre"onchange="submit();">';                         
                           while($temp = mysqli_fetch_row($genres_auswahl)){
                             if($temp[0] != $akt){
                               if($sel_genre == $temp[0])
                                 $html_out .='<option value="'.$temp[0].'" selected>'.$temp[0].'</option>';
                               else
                                 $html_out .='<option value="'.$temp[0].'">'.$temp[0].'</option>';              
                               $akt = $temp[0];
                             }//end if
                           }//end while
                           $html_out .='
                           </select>                                                  
                         </td>
                       </tr>';
                     }//end if ($skl == 'bw')                     
                   $html_out .='                         
                     <tr>
                       <td>Bewertung:</td>
                       <td>
                         <select name="wertung" onchange="submit();">';
                         $sel_wertung = 'alle';
                         if($_POST["wertung"]) $sel_wertung = $_POST["wertung"];
                         if($sel_wertung == 'alle'){
                           $html_out .='<option value="alle" selected>alle</option>';
                         } else {
                           $html_out .='<option value="alle">alle</option>';
                         }
                         for($x=1;$x<6;$x++){
                           if($sel_wertung == $x){
                             $html_out .='<option value="'.$x.'" selected>'.$x.'</option>';
                           } else {
                             $html_out .='<option value="'.$x.'">'.$x.'</option>';
                           }
                           $html_out .= $x.'<br>';
                         }
                         $html_out .='</select> Sterne
                      </td>
                     </tr>
                   </table>    
                 </fieldset>
                 </font>
                 <font face="Verdana" size="3" color="#000080"><br />                                          
               </div>';
               } //end if($auswahlmodus == 'manuell') 
                 
               if($auswahlmodus == 'turnier'){   
               $html_out .='
               <!-- Container: Turnier  -->
               <div id="turnier" class="hideMe-NOT">
                 <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                 <font face="Verdana" size="3" color="#000080">
                 <fieldset>
                   <legend>Turnier Auswahl</legend>
                   <table>';
                   if($skl == 'rr'){
                     $html_out .='                 
                     <tr>
                       <td>Anzahl Teilnehmer:</td>
                       <td>
                         <table>
                           <tr>
                             <td align="center">S</td>
                             <td align="center">J</td>
                             <td align="center">C</td>
                             <td align="center">B</td>
                             <td align="center">A</td>
                             <td align="center">&nbsp;</td>
                           </tr>
                           <tr>
                             <td align="center"><input style="text-align: center;" type=text name="s_klasse" value="'.$_POST['s_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="j_klasse" value="'.$_POST['j_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="c_klasse" value="'.$_POST['c_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="b_klasse" value="'.$_POST['b_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="a_klasse" value="'.$_POST['a_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center">&nbsp;</td>
                           </tr>
                           <tr>
                             <td align="center"><input type="checkbox" name="hoff_s" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_j" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_c" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_b" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_a" value="1"></td>
                             <td align="center">incl. Hoffnungsrunde?</td>
                           </tr>
                           <tr>
                             <td colspan="6" align="left"><br><input type="submit" name="anzeigen" value="Auslosen"></td>
                           </tr>
                         </table>
                       </td>
                     </tr>';
                   } else {
                     $html_out .='
                     <tr>
                       <td>Anzahl Teilnehmer:</td>
                       <td>
                         <table>
                           <tr>
                             <td align="center">J</td>
                             <td align="center">MA</td>
                             <td align="center">MB</td>
                             <td align="center">SA</td>
                             <td align="center">SB</td>
                             <td align="center">&nbsp;</td>
                           </tr>
                           <tr>
                             <td align="center"><input style="text-align: center;" type=text name="bw_j_klasse" value="'.$_POST['bw_j_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="bw_ma_klasse" value="'.$_POST['bw_ma_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="bw_mb_klasse" value="'.$_POST['bw_mb_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="bw_sa_klasse" value="'.$_POST['bw_sa_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center"><input style="text-align: center;" type=text name="bw_sb_klasse" value="'.$_POST['bw_sb_klasse'].'" size="2" maxlength="2"></td>
                             <td align="center">&nbsp;</td>
                           </tr>
                           <tr>
                             <td align="center"><input type="checkbox" name="hoff_bj" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_ma" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_mb" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_sa" value="1"></td>
                             <td align="center"><input type="checkbox" name="hoff_sb" value="1"></td>
                             <td align="center">incl. Hoffnungsrunde?</td>
                           </tr>
                           <tr>
                             <td colspan="6" align="left"><br><input type="submit" name="anzeigen" value="Auslosen"></td>
                           </tr>
                         </table>
                       </td>
                     </tr>';                     
                   }//end else if($skl == 'rr')                                  
                   $html_out .='
                   </table>    
                 </fieldset>
                 </font>
                 <font face="Verdana" size="3" color="#000080"><br />  
               </div>';
               } //end if($auswahlmodus == 'turnier') 
                      
// Kopfzeile für CSV-Datei
$separator       = ";";
$valueQuotes     = '"';
$csv_separator   = ";";
$csv_valueQuotes = '"';
//$str_csv_header  = $csv_valueQuotes.'Klasse'.$csv_valueQuotes.
//                   $csv_separator.$csv_valueQuotes.'Runde'.$csv_valueQuotes.
//                   $csv_separator.$csv_valueQuotes.'Titel'.$csv_valueQuotes.  
//                   $csv_separator.$csv_valueQuotes.'Interpret'.$csv_valueQuotes.  
//                   $csv_separator.$csv_valueQuotes.'Takte'.$csv_valueQuotes.  
//                   $csv_separator.$csv_valueQuotes.'Genre'.$csv_valueQuotes.  
//                   $csv_separator.$csv_valueQuotes.'Link'.$csv_valueQuotes.  
//                   $csv_separator.$csv_valueQuotes.'Dateiname'.$csv_valueQuotes."\r\n";      

$str_csv_header  = $csv_valueQuotes.'Startklasse'.$csv_valueQuotes.
                   $csv_separator.$csv_valueQuotes.'Vorname Dame'.$csv_valueQuotes.
                   $csv_separator.$csv_valueQuotes.'Name Dame'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Vorname Herr'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Name Herr'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Verein'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Team Name'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'E-Mail'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Musik Fusstechnik'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Musik Akrobatik'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Musik Stellprobe'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Musik Tanzmusik'.$csv_valueQuotes.  
                   $csv_separator.$csv_valueQuotes.'Musik Ersatzmusik'.$csv_valueQuotes."\r\n";            

$str_html_header = '<tr>
                      <th width="20"  align="center">Nr.</th>
                      <th width="40"  align="center">&nbsp;</th>
                      <th width="300" align="left">Titel</th>
                      <th width="300" align="left">Interpret</th>
                      <th width="50"  align="center">Takte</th>
                      <th width="100" align="center">Genre</th>
                      <th width="82"  align="center">Bewertung</th>
                      <th width="150" align="center">&nbsp;</th>
                    </tr>';

$html_ergebnis  = '';                   
$html_ergebnis .= '
             <div style="width: 100%; float: left;">
             <div id="admProfileMasterData" class="groupBox">
               <div class="groupBoxHeadline">
                 <div style="float: left;"></div>
               </div>';
               if($auswahlmodus == 'manuell'){
                 $html_ergebnis .= '<div class="groupBoxBody">';  
                 $sqlab  = 'SELECT * From turniermusik ';
                 if($skl == 'rr') $sqlab .= ' WHERE rocknroll = 1 ';
                 if($skl == 'bw') $sqlab .= ' WHERE boogiewoogie = 1 ';   
                                  $sqlab .= ' AND takte >= '.$tempo_von.' AND takte <= '.$tempo_bis;  
                 if($sel_genre != '---'){
                   $sqlab .= ' AND genre = "'.$sel_genre.'"';
                 }
                 if($sel_wertung != 'alle'){
                   $sqlab .= ' AND wertung = '.$sel_wertung.'';
                 }
                 if($_POST['anzahl'] !='0'){
                   $sqlab .= ' ORDER BY RAND() LIMIT '.$_POST['anzahl'];
                 }
                 $html_ergebnis .='<table class="TableMusikDB">';  
                 $html_ergebnis .= $str_html_header;
                 list($str_html, $str_csv) = datenbankabfrage($sqlab,$valueQuotes,$separator);
                 $str_csv_download = $str_csv;
                 $html_ergebnis .= $str_html;                                                                    
                 $html_ergebnis .='</table>                              
               </div>';//end <div class="groupBoxBody">
               if($_POST['anzahl'] > 0) $got_data = true;
               } else {
                 $html_ergebnis .='                                        
                 <div class="groupBoxBody">
                   <table class="TableMusikDB">'.$str_html_header;
                 
                      $startklassen_arr = array("s_klasse","j_klasse","c_klasse","b_klasse","a_klasse");                                       
                      $got_data = false;
                      foreach ($startklassen_arr as $value) {
                        if($_POST[$value] > 7){
                          $got_data = true;
                          $html_ergebnis .= get_db_data($value,$_POST[$value],'_v',FALSE);
                        }
                        if($_POST[$value] > 7 && $_POST['hoff_s']){
                          $got_data = true;
                          $html_ergebnis .= get_db_data($value,$_POST[$value],'_h',TRUE);
                        }
                        if($_POST[$value] > 20){
                          $got_data = true;
                          $html_ergebnis .= get_db_data($value,$_POST[$value],'_z',FALSE);  
                        }
                        if($value == 'b_klasse' || $value == 'a_klasse'){
                          if($_POST[$value]){
                            $got_data = true;
                            $html_ergebnis .= get_db_data($value,$_POST[$value],'_ef',FALSE);
                          }    
                          if($_POST[$value]){
                            $got_data = true;
                            $html_ergebnis .= get_db_data($value,$_POST[$value],'_ea',FALSE);
                          }    
                        } else {
                          if($_POST[$value]){
                            $got_data = true;
                            $html_ergebnis .= get_db_data($value,$_POST[$value],'_e',FALSE);
                          }    
                        }  
                      }//end foreach  
                 $html_ergebnis .='</table>                              
               </div>';//end <div class="groupBoxBody">
               }     
               $html_ergebnis .='
             </div>
           </div>
    <div style="clear: left; font-size: 1pt;">&nbsp;</div>
  </div>
</div>&nbsp;';              

if($_POST['download']){
  //Download CSV Datei
  echo $str_csv_header;
  //rmenken: hier download csv modifizieren und nur checked auswahl zusammen stellen
  $str_csv_unpacked = '';
  foreach ($_SESSION['csv'] as $key => $value){
    if($_POST['id'.$key]){
      $str_csv_unpacked .= $value;
    }
  }
  echo $str_csv_unpacked;        
} else {
  //Print HTML Page
  echo $html_out;  
             if($got_data){
               $_SESSION['csv'] = $str_csv_download;   
               echo '
               <!-- Container: Turnier  -->
               <div id="turnier_" class="hideMe-NOT">
                 <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                 <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Download</legend>
                     Dateiname: <input type=text name="dateiname" value="filename" size="15" maxlength="20">
                     <button type="submit" name="download" value="download">für Musikdownloader runterladen!</button>
                   </fieldset>
                 </font>
                 <font face="Verdana" size="3" color="#000080"><br />
               </div>';
             }                                    
  echo'  </div>';  //end <div id="admProfileMasterData"
  echo'</div>';  
  
  echo $html_ergebnis;
  
  //print_r($_SESSION['csv']);echo' :$_SESSION[csv]<br>';
  
  if($got_data){
    //echo 'Create: '.create_zipfile($_SESSION['csv']).' file successfully!';
    //echo '<br>ops<br>';
    //print_r($_SESSION['titel']);echo' :titel<br>';
  }
  
  if($user->getValue('usr_id') != $gCurrentUser->getValue('usr_id')){
    echo'
    <br>&nbsp;
    <ul class="iconTextLinkList">
        <li>
            <span class="iconTextLink">
                <a href="'.$g_root_path.'/adm_program/system/back.php"><img
                src="'.THEME_PATH.'/icons/back.png" alt="'.$gL10n->get('SYS_BACK').'" /></a>
                <a href="'.$g_root_path.'/adm_program/system/back.php">'.$gL10n->get('SYS_BACK').'</a>
            </span>
        </li>
    </ul>';
  } 
  require(SERVER_PATH. '/adm_program/system/overall_footer.php');
}

?>
