<?php
/******************************************************************************
 * Ranglisten anzeigen
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
require_once('../../system/classes/table_roles.php');
require_once('../../system/drbv_funktionen.php');

session_start();
session_cache_limiter(1440);
  
//Use timezone
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, "de_DE.utf8");
$monate = array(1=>"Januar",
                2=>"Februar",
                3=>"M&auml;rz",
                4=>"April",
                5=>"Mai",
                6=>"Juni",
                7=>"Juli",
                8=>"August",
                9=>"September",
               10=>"Oktober",
               11=>"November",
               12=>"Dezember");
    
// Initialize and check the parameters
//$getUserId  = admFuncVariableIsValid($_GET, 'user_id', 'numeric', $gCurrentUser->getValue('usr_id'));
//$getRegion  = admFuncVariableIsValid($_GET, 'regio');   
  
// create user object
//$user = new User($gDb, $gProfileFields, $getUserId);
unset($turnierinfo);
  
// rfid holen und turniernummer ruecksetzen 
if(!$_GET){
  if($_POST['rfid_teilnehmer']) $_SESSION['getRFID'][] = $_POST['rfid_teilnehmer'];  
  
  if($_POST["rfid_teilnehmer"] == 'reset'){
    unset($_SESSION['getTurniernr']);
    unset($_SESSION['getAbmeldungen']);
    unset($_SESSION['getVergessen']);
  }
} 
// wenn keine turniernummer da, dann anfordern und rfid liste loeschen  
if(!isset($_SESSION['getTurniernr'])){
  $_SESSION['getTurniernr'] = $_POST['turniernr'];
  unset($_SESSION['getRFID']);
}
// abmeldungen und vergessene rfid
if($_POST['paar_abmelden']) $_SESSION['getAbmeldungen'][] = $_POST['paar_abmelden'];  
if($_POST['rfid_vergessen']) $_SESSION['getVergessen'][]  = $_POST['rfid_vergessen'];  
  
  
// startnummer zuordnen
// BOZO noch ueber file oder formular realisieren  
$startnummer = array(19168=>51,19423=>52,19579=>53,19587=>1,19637=>2,19639=>3,19636=>4,19635=>5,19239=>6,19370=>7,19375=>8,19475=>9,19480=>10,19489=>11,19505=>12,19526=>13,19533=>14,19544=>15,19601=>16,19150=>17,19241=>18,19258=>19,19314=>20,19461=>21,19512=>22,19569=>23,19577=>24,19586=>25,19598=>26,19600=>27,19602=>28,19623=>29,18754=>30,18893=>31,19348=>32,19386=>33,19390=>34,19399=>35,19470=>36,19497=>37,19552=>38,19564=>39,19574=>40,19593=>41,19605=>42,19612=>43,19148=>44,19338=>45,19389=>46,19391=>47,19467=>48,19551=>49,19596=>50);  
//print_r($startnummer);echo'::DEBUG:$startnummer:<br>';
  
// Funktion alle Teilnehmer auflisten
// und anwesende markieren  
// ----------------------------------
function listrfid_teilnehmer($startklasse){
  
  global $teilnehmer;
  unset($html);
  unset($html_sel_opt_paar);
  unset($html_sel_opt_rfid);
  unset($paarcnt);
  
  $count_prda  = 0;
  $count_hrda  = 0;
  $count_dada  = 0;
  $count_prnda = 0;
  $count_hrnda = 0;
  $count_danda = 0;  
  $alleda      = FALSE;
  
  foreach($teilnehmer as $key_startbnr => $value){
    if($startklasse == $value['startklasse']){
      if((in_array($value['rfidhr'], $_SESSION['getRFID']) && 
          in_array($value['rfidda'], $_SESSION['getRFID'])) ||
         (in_array($value['rfidhr'], $_SESSION['getVergessen']) && 
          in_array($value['rfidda'], $_SESSION['getVergessen'])) ||
         (in_array($value['rfidhr'], $_SESSION['getRFID']) && 
          in_array($value['rfidda'], $_SESSION['getVergessen'])) ||
         (in_array($value['rfidhr'], $_SESSION['getVergessen']) && 
          in_array($value['rfidda'], $_SESSION['getRFID']))){
        $html .= '
          <img style="vertical-align: middle" width="20px" height="20px" 
          title="'.$value['vnherr'].' '.$value['nnherr'].' / '.$value['vndame'].' '.$value['nndame'].'&#10;('.$value['vereinname'].')"
          src="icon/stbuchpaar_da">&nbsp;
          <span id="bgr">'.$key_startbnr.'</span>';
        $count_prda++;    
      } elseif(in_array($value['rfidhr'], $_SESSION['getRFID']) || 
               in_array($value['rfidda'], $_SESSION['getRFID']) ||
               in_array($value['rfidhr'], $_SESSION['getVergessen']) ||
               in_array($value['rfidda'], $_SESSION['getVergessen'])){
        $html .= '
          <img style="vertical-align: middle" width="20px" height="20px" 
          title="'.$value['vnherr'].' '.$value['nnherr'].' / '.$value['vndame'].' '.$value['nndame'].'&#10;('.$value['vereinname'].')"
          src="icon/stbuchpaar_da">&nbsp;<b>'.$key_startbnr.'</b>';
      } elseif(in_array($key_startbnr, $_SESSION['getAbmeldungen'])){
        $html .= '
          <img style="vertical-align: middle" width="20px" height="20px" 
          title="'.$value['vnherr'].' '.$value['nnherr'].' / '.$value['vndame'].' '.$value['nndame'].'&#10;('.$value['vereinname'].')"
          src="icon/stbuchpaar_nda">&nbsp;<s>'.$key_startbnr.'</s>';        
      } else {
        $html .= '
          <img style="vertical-align: middle" width="20px" height="20px" 
          title="'.$value['vnherr'].' '.$value['nnherr'].' / '.$value['vndame'].' '.$value['nndame'].'&#10;('.$value['vereinname'].')"
          src="icon/stbuchpaar_nda">&nbsp;'.$key_startbnr;    
          $count_prnda++;
          $nicht_da_paar[$startklasse][] = $key_startbnr.'.'.$value['vnherr'].'.'.$value['nnherr'].'.'.$value['vndame'].'.'.$value['nndame'];    
          
          $html_sel_opt_paar .= '<option value="'.$key_startbnr.'">'.$key_startbnr.' - '.$value['vnherr'].' '.$value['nnherr'].' & '.$value['vndame'].' '.$value['nndame'].'</option>';;    
      }
      if(in_array($value['rfidhr'], $_SESSION['getRFID'])){
        $html .= ' - <img class="picrund16" src="https://drbv.de/adm/photos/'.$value['usr_id'].'.jpg" width="16" height="16" title="'.$value['vnherr'].' '.$value['nnherr'].'">';        
        //$html .= ' - <img src="icon/teiln_gr" title="'.$value['vnherr'].' '.$value['nnherr'].'">';
        $count_hrda++;
      } elseif(in_array($key_startbnr, $_SESSION['getAbmeldungen'])){
        $html .= ' - <img src="icon/teiln_rd"title="'.$value['vnherr'].' '.$value['nnherr'].'">';                
      } elseif(in_array($value['rfidhr'], $_SESSION['getVergessen'])){
        $html .= ' - <img src="icon/teiln_gr"title="'.$value['vnherr'].' '.$value['nnherr'].'">';                
      } else {
        $html .= ' - <img src="icon/teiln_bl"title="'.$value['vnherr'].' '.$value['nnherr'].'">';        
        $count_hrnda++;
        $nicht_da_rfid[$value['rfidhr']] = $value['vnherr'].'.'.$value['nnherr'];    

        $html_sel_opt_rfid .= '<option value="'.$value['rfidhr'].'">'.$key_startbnr.' - '.$value['vnherr'].' '.$value['nnherr'];    
      
      }
      if(in_array($value['rfidda'], $_SESSION['getRFID'])){
        $html .= ' <img class="picrund16" src="https://drbv.de/adm/photos/'.$value['usr_id'].'a.jpg" width="16" height="16" title="'.$value['vndame'].' '.$value['nndame'].'">';    
        //$html .= ' <img src="icon/teiln_gr"title="'.$value['vndame'].' '.$value['nndame'].'">';    
        $count_dada++;    
      } elseif(in_array($key_startbnr, $_SESSION['getAbmeldungen'])){
        $html .= ' <img src="icon/teiln_rd"title="'.$value['vndame'].' '.$value['nndame'].'">';                
      } elseif(in_array($value['rfidda'], $_SESSION['getVergessen'])){
        $html .= ' <img src="icon/teiln_gr"title="'.$value['vndame'].' '.$value['nndame'].'">';                
      } else {
        $html .= ' <img src="icon/teiln_bl"title="'.$value['vndame'].' '.$value['nndame'].'">';        
        $count_danda++;  
        $nicht_da_rfid[$value['rfidda']] = $value['vndame'].'.'.$value['nndame'];   

        $html_sel_opt_rfid .= '<option value="'.$value['rfidda'].'">'.$key_startbnr.' - '.$value['vndame'].' '.$value['nndame'];    
        
      }    
      //$html .= '<br><br>';
      $html .= '<br>'.$value['rfidhr'].' '.$value['rfidda'].'<br>';
      $paarcnt++;
    }
  }
  //BOZO hier noch abgemeldete kodieren bzw. Hinweis geben, damit
  //Spalte gruen wird = alle da bzw. abgemeldet
  //if($paarcnt - count($_SESSION['getAbmeldungen'])){
  if($paarcnt){
    $html .= $count_prda.'/'.($paarcnt).' anwesend';
  }
  if(count($_SESSION['getAbmeldungen'])){
    $html .= '<br>'.count($_SESSION['getAbmeldungen']).'/'.($paarcnt).' abgemeldet';
  }
  if(TRUE){
    $html .= '<br>xxx vergessene RFIDs';
  }
  if(($paarcnt - count($_SESSION['getAbmeldungen'])) == $count_prda) $alleda = TRUE;
  
  //print_r($nicht_da_paar);echo'::DEBUG:$nicht_da_paar:<br>';
  //print_r($nicht_da_rfid);echo'::DEBUG:$nicht_da_rfid:<br>';
  $html .= '
    <form action="" method=post>';
  if($html_sel_opt_paar){
    $html .= '
      <select name="paar_abmelden">
        <option value="">Paar</option>
        '.$html_sel_opt_paar.'
      </select>
      <button type="submit">abmelden!</button>';
  }
  if($html_sel_opt_rfid){
    $html .= '    
      <select name="rfid_vergessen">
        <option value="">RFID</option>
        '.$html_sel_opt_rfid.'
      </select>
      <button type="submit">vergessen!</button>';
  }
  $html .= '</form>';
  
  return array($html,$alleda);
}

// Funktion Teilnehmer Info anzeigen
// ----------------------------------
function show_rfid_info($gotRFID){
  
  global $teilnehmer, $startnummer, $turnierinfo; 
  unset($html);
  unset($teilnehmer_gefunden);
  
  foreach($teilnehmer as $key_startbnr => $value){
    if($value['rfidhr'] == $gotRFID || $value['rfidda'] == $gotRFID){
      $html .= '          
      <table border="0px" class="tg">
        <tr>
          <th class="tg-0pky" colspan="5"><h1>'.$turnierinfo['dat_headline'].'  /  Check-in</h1>
             <span class="smalldat">'.date('d.m.Y', strtotime($turnierinfo['dat_begin'])).'</span>
          </th>
        </tr>
        <tr>
          <td class="tg-0lax" colspan="5"><div class="smalldat">'.substr($value['startklasse'],3,1).'-Klasse</div></td>
        </tr>
        <tr>
          <td class="tg-0lax" colspan="5">'.$value['vereinname'].'</td>
        </tr>
        <tr>
          <td class="tg-0lax-m" rowspan="2">'.$startnummer[$key_startbnr].'</td>
          <td class="tg-0lax-p">
             <img class="picrund100" src="https://drbv.de/adm/photos/'.$value['usr_id'].'.jpg"></td>
          <td class="tg-0lax" colspan="3">'.$value['vnherr'].' '.$value['nnherr'].'
            <br><div class="smalldat">'.date('d.m.Y', strtotime($value['gebhr'])).'</div>                                                                  
          </td>
        </tr>
        <tr>
          <td class="tg-0lax-p">
             <img class="picrund100" src="https://drbv.de/adm/photos/'.$value['usr_id'].'a.jpg"></td>
          <td class="tg-0lax" colspan="3">'.$value['vndame'].' '.$value['nndame'].'
            <br><div class="smalldat">'.date('d.m.Y', strtotime($value['gebda'])).'</div>                                                                  
          </td>
        </tr>
      </table>';
      $teilnehmer_gefunden = TRUE;      
    }
  }//end foreach      
  // wenn RFID nicht gemeldet ist
  if(!$teilnehmer_gefunden){
    $html .= '          
      <table border="0px" class="tg">
        <tr>
          <th class="tg-0pky" colspan="5"><h1>'.$turnierinfo['dat_headline'].'  /  Check-in</h1>
             <span class="smalldat">'.date('d.m.Y', strtotime($turnierinfo['dat_begin'])).'</span>
          </th>
        </tr>
        <tr>
          <td class="tg-0lax-red" colspan="5">Kein gemeldeter Teilnehmer</td>
        </tr>
        <tr>
          <td class="tg-0lax-red" colspan="5">&nbsp;</td>
        </tr>
        <tr>
          <td class="tg-0lax-m-red" rowspan="2">00</td>
          <td class="tg-0lax-red"></td>
          <td class="tg-0lax-red" colspan="3"></td>
        </tr>
        <tr>
          <td class="tg-0lax-red"></td>
          <td class="tg-0lax-red" colspan="3"></td>
        </tr>
      </table>';            
  }  
  return $html;
}
  
// o funktion einfuegen die eine Absage eines Teilnehmers: Startbuchnr/Name durch selektfeld realisiert
// o teilnehmer nach klassen aufzeigen und completion melden/anzeigen
// o bilder und Startnummer anzeigen (startnummern eingabe realisieren -> check turnierprogramm) 
  
// Html-Kopf ausgeben
$gLayout['header'] = '
    <script type="text/javascript" src="https://drbv.de/adm/adm_program/system/js/date-functions.js"></script>
    <script type="text/javascript" src="https://drbv.de/adm/adm_program/system/js/form.js"></script>
    <script type="text/javascript" src="https://drbv.de/adm//adm_program/modules/profile/profile.js"></script>
    <style>
      .rlraw {
        font-size: 0.95em;
        background-color:#f7f5f2;
      }
      p.rlraw {
        font-size: 0.95em;
      }
      h1.rlraw {
        font-size: 27px;
        text-align: center;
        font-family: "Yanone Kaffeesatz";
        color: #039; 
      }
    </style>';
  
  
echo' 
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de" xml:lang="de">
  <head>
    <!-- (c) 2004 - 2013 The Admidio Team - http://www.admidio.org -->
    <!-- (c) 2014 Adapted by DRBV Webteam to a online version of the couples competition book -->  
  <title>Deutscher Rock&#039;n&#039;Roll und Boogie-Woogie Verband e.V. - Startbuch Check</title>
  <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
  <link rel="shortcut icon" type="image/x-icon" href="https://drbv.de/adm/adm_themes/classic/icons/favicon.png" />
  <link rel="apple-touch-icon" href="https://drbv.de/adm/adm_themes/classic/icons/webclipicon.png" />
  <style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:300);
    body {
      font-family: "Yanone Kaffeesatz",Helvetica,Arial,Sans-Serif;
      font-size: 17px;
    }    
  </style>
  <style>
    #box_rra {
      background-color: #E6E6E6;
      width:  140px;
      height: 400px;
      margin: 3px 0px 3px 0px;  
      float:left;
    }
    #box_rra_alleda {
      background-color: #BEF781;
      width:  140px;
      height: 400px;
      margin: 3px 0px 3px 0px;  
      float:left;
    }
    #box_rrb {
      background-color: #D8D8D8;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left;
    }
    #box_rrb_alleda {
      background-color: #BEF781;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left;
    }
    #box_rrc {
      background-color: #BDBDBD;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left
    }  
    #box_rrc_alleda {
      background-color: #BEF781;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left
    }  
    #box_rrj {
      background-color: #D8D8D8;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left;
    }  
    #box_rrj_alleda {
      background-color: #BEF781;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left;
    }  
    #box_rrs {
      background-color: #E6E6E6;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;
      float:left;
    }  
    #box_rrs_alleda {
      background-color: #BEF781;
      width:  150px;
      height: 400px;
      margin: 3px 3px 3px 0px;  
      float:left;
    }  
    #box_top {
      margin-left: auto;
      margin-right: auto;
      position: absolute;
    }  
    #bgr {
      color: #267F00;
      font-weight: bold; 
      text-decoration: none;
    }  
  
    .tg {
      border-collapse:collapse;
      border-spacing:0;
      border-color:#aabcfe;
      width: 764px;
      height: 400px;
    }
    .tg .tg-0lax-m{
      vertical-align:center;
      text-align:center;
      background-color:#BEF781;
      font-size: 140px;
      font-weight: bold;
      width: 40%; 
    }
    .tg .tg-0lax-m-red{
      vertical-align:center;
      text-align:center;
      background-color:red;
      font-size: 140px;
      font-weight: bold;
      width: 40%; 
    }
    .tg .tg-0lax-p{
      vertical-align:center;
      text-align:center;
      background-color:#BEF781;
      width: 110px; 
    }
    .tg .tg-0lax{
      vertical-align:center;
      background-color:#BEF781;
      font-size: 40px;
      font-weight: bold;
    }
    .tg .tg-0lax-red{
      vertical-align:center;
      background-color:red;
      font-size: 40px;
      font-weight: bold;
    }
  
    .smalldat{
      font-size: 30px;
      font-weight: normal;
      text-decoration: blink;
      font-style: italic;
    }
    .picrund100 {
      height: 100px;
      object-fit: cover;
      width: 100px;  
      border-radius: 100%;
      -webkit-border-radius: 100%;
      -moz-border-radius: 100%;
     }  
    .picrund16 {
      height: 16px;
      object-fit: cover;
      width: 16px;  
      border-radius: 100%;
      -webkit-border-radius: 100%;
      -moz-border-radius: 100%;
     }  
     select {
       width:65px;
      }  
  </style>                  
  ';      

echo $gLayout['header'];
echo'
  </head>
  
  <body style="background-color:#f7f5f2;">
    <div align="left" class="rlraw">';

    $teilnehmer = getTurnierteilnehmer($_SESSION["getTurniernr"], TRUE);
    //print_r($teilnehmer_stb);echo'::DEBUG:$teilnehmer_stb:<br>';
    //foreach($teilnehmer as $value){
    //  $contents=file_get_contents(SERVER_PATH.'/adm_my_files/user_profile_photos/'.$value['usr_id'].'.jpg');
    //  $savefile = fopen('/userpics/'.$value['usr_id'], "w");
    //  fwrite($savefile, $contents);
    //  fclose($savefile);      
    //}  
    //print_r($contents);echo'::DEBUG:$contents:<br>';
    //print_r($_SESSION['getRFID']);echo'::DEBUG:$_SESSION[getRFID]:<br>';
    //print_r($_SESSION['getAbmeldungen']);echo'::DEBUG:$_SESSION[getAbmeldungen]:<br>';
    //print_r($_SESSION['getVergessen']);echo'::DEBUG:$_SESSION[getVergessen]:<br>';

    echo'  
      <form action="" method=post>
        <div class="input">';
        if(!isset($_SESSION['getTurniernr'])){
        echo'
            <p>Turnier Eingabe:<br>
                <input required type="text" name="turniernr" tabindex="1" size="50">
            </p>';        
      } else {
        echo'
            <p>RFID Eingabe:<br>
                <input required type="text" name="rfid_teilnehmer" tabindex="1" size="50" autofocus>
            </p>';      
      }  
  
   echo'        
        </div>
      </form>';
   
   if(isset($_SESSION['getTurniernr'])){  
     $turnierinfo = getTurnierInfoADM($_SESSION['getTurniernr']);
   }
     
   if($_POST["rfid_teilnehmer"]){
     echo show_rfid_info($_POST["rfid_teilnehmer"]);
   } else {
     echo '
     <table border="1px" class="tg">
       <tr>';
         if(isset($_SESSION['getTurniernr'])){
           echo'<th class="tg-0pky" colspan="5"><h1>'.$turnierinfo['dat_headline'].'  /  Check-in</h1>
                  <br><div class="smalldat">'.date('d.m.Y', strtotime($turnierinfo['dat_begin'])).'</div>
                </th>';
         } else {
           echo'<th class="tg-0pky" colspan="5">Noch keine Turniernummer eingegeben.</th>';         
         }
       echo'
       </tr>           
       <tr>
         <td class="tg-0lax" colspan="5"></td>
         </tr>
         <tr>
           <td class="tg-0lax-m" rowspan="2">00</td>
           <td class="tg-0lax"></td>
           <td class="tg-0lax" colspan="3"></td>
         </tr>
         <tr>
           <td class="tg-0lax"></td>
           <td class="tg-0lax" colspan="3"></td>
         </tr>
       </table> ';     
   }//end else
   
   echo '<table border="0" class="tg"><tr><td align="center">';
   unset($css_color);
   list($html,$alleda) = listrfid_teilnehmer('RR_S');
   if($alleda) $css_color = '_alleda';
   echo '<div id="box_rrs'.$css_color.'"><h3>S</h3>';
   echo $html;
   echo '</div>';  
   unset($css_color); 
   list($html,$alleda) = listrfid_teilnehmer('RR_J');
   if($alleda) $css_color = '_alleda';
   echo '<div id="box_rrj'.$css_color.'"><h3>J</h3>';
   echo $html;
   echo '</div>';  
   unset($css_color);
   list($html,$alleda) = listrfid_teilnehmer('RR_C');
   if($alleda) $css_color = '_alleda';
   echo '<div id="box_rrc'.$css_color.'"><h3>C</h3>';
   echo $html;
   echo '</div>';  
   unset($css_color);
   list($html,$alleda) = listrfid_teilnehmer('RR_B');
   if($alleda) $css_color = '_alleda';
   echo '<div id="box_rrb'.$css_color.'"><h3>B</h3>';
   echo $html;
   echo '</div>';  
   unset($css_color);
   list($html,$alleda) = listrfid_teilnehmer('RR_A');
   if($alleda) $css_color = '_alleda';
   echo '<div id="box_rra'.$css_color.'"><h3>A</h3>';
   echo $html;
   echo '</div>';  
   echo '</td></tr></table>';
  
   echo'
    </div>
  </body>
</html>';

?>
