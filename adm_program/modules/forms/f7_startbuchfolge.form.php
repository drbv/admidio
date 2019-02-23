<?php
/******************************************************************************
 * Form for Startbuchfolgebestellung
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
print_r($member_array);echo' :DEBUG::member_array<br>';
  
echo '         <form name="Startbuchfolgebestellung" id="formSwitcher" method="post" autocomplete="off" accept-charset="UTF-8">';
                 echo'  
                 <font face="Verdana" size="3" color="#000080">
                 <h3>&nbsp;Startbuch/Startkarten Folgebestellung</h3>
                 <br />
                 <fieldset>
                   <legend>Folgebestellung für</legend>               
                     <select name="Folgebestellung" id="wechsler">
                       <option value="" selected="selected">Bitte auswählen ...</option>';                        
                        $stb_cnt = 1;
                        foreach ($member_array as $memberrow) {
                          if(substr($memberrow[3],0,1) != '5'){
                            foreach ($memberrow as $membercol => $membercont) {
                              if($membercol==4){
                                if(substr($membercont,0,1) == 'F'){
                                  $name = $memberrow[6];
                                } elseif(substr($membercont,0,1) == 'B') {
                                  if($memberrow[7]){
                                    $name = $memberrow[7].' '.$memberrow[8];
                                  }
                                  if($memberrow[14]){
                                    $name = $memberrow[14].' '.$memberrow[15];
                                  }
                                } else {
                                  $name = $memberrow[7].' '.$memberrow[8].' & '.$memberrow[14].' '.$memberrow[15];
                                }
                              }
                            }
                            echo '<option value="Member'.$stb_cnt.'">'.$memberrow[3].' - '.$name.'</option>';
                            $stb_cnt = $stb_cnt + 1;
                          }
                        }  
                     echo'  
                     </select>
                 </fieldset>
                 <br />
                 </font>
               </form>
  
               <!-- Versteckte Container hier fallen lassen -->
               <div id="pasteMe"></div>';
  
               $stb_cnt = 1;
               foreach ($member_array as $memberrow) {                 
                 echo '
                 <!-- Container: Einzelpaare S,J,C,B,A  -->
                 <div id="Member'.$stb_cnt.'" class="hideMe">
                 <form name="Startbuchbestellung1" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=7" method="post" accept-charset="UTF-8" onsubmit="return chkFormular()">
                   <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Absender Informationen</legend>
                     <table>
                       <tr>
                         <td width="30%">Ihr Name:</td>
                         <td><input type="text" name="Versender" tabindex="1" size="50"></td>
                       </tr>
                       <tr>
                         <td>Ihre Mailadresse:</td>
                         <td><input type="text" name="EMail" tabindex="2" size="50"></td>
                       </tr>
                       <tr>
                         <td>Ihre Telefon-/Handynummer:</td>
                         <td><input type="text" name="TelHandy" tabindex="3" size="50"></td>
                       </tr>
                       <tr>
                         <td>&nbsp;</td>
                         <td>
                           <div class="formlabel">
                             Zusätzlich zur Vereinsmail wird die Formularbestätigung an diese Mail versendet.
                           </div>
                         </td>
                       </tr>
                       <tr>
                         <td>Verein:</td>
                         <td><input type="text" name="Verein" value="'.$user->getValue('VEREIN').'" readonly="readonly"></td>
                       </tr>  
                       <tr>
                         <td>Vereinsnummer:</td>
                         <td><input type="text" name="Vereinsnummer" value="'.$user->getValue('VEREINSNUMMER').'" readonly="readonly"></td>
                       </tr>    
                       <tr>
                         <td>Bundesland:</td>
                         <td><input type="text" name="Bundesland" value="'.$user->getValue('BUNDESLAND').'" readonly="readonly"></td>
                       </tr>    
                     </table>    
                   </fieldset>
                   </font>';                                    
                 foreach ($memberrow as $membercol => $membercont) {
                   if($membercol==4){
                     if(substr($membercont,0,1) == 'F'){
                       $name = $memberrow[6];
                       
                 echo '
                   <font face="Verdana" size="3" color="#000080"><br />  
                   <fieldset>
                     <legend>Angaben zur Startbuchfolgebestellung für: '.$name.'</legend>  
                     <!-- br />
                     <div class="hinweis">Das betreffende Startbuch/Startkarte ist an die Geschäftsstelle zu senden.<br />
                        Bitte dazu diesen <a href="http://www.drbv.de/cms/images/PDF/Formulare/Einsendung_StartbuchStartkarte.pdf" target="_blank">Adressvordruck</a> verwenden!<p>&nbsp;</p></div -->
                     <table>
                       <tr>
                         <td width="40%">Startklasse Formationen:<br><br><br><br><br><br></td>
                         <td>
                           <input id="Startbuch-FBWMA"';if($membercont=="F_BW_M"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="BW-Master" />BW-Master<br>
                           <input id="Startbuch-FRRMA"';if($membercont=="F_RR_M"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="RR-Master" />RR-Master<br>
                           <input id="Startbuch-FRRJF"';if($membercont=="F_RR_J"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Juniorenformation" />Juniorenformation<br>
                           <input id="Startbuch-FRRLF"';if($membercont=="F_RR_LF"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Ladyformation" />Ladyformation<br>
                           <input id="Startbuch-FRRGF"';if($membercont=="F_RR_GF"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Girlformation" />Girlformation<br>
                           <input id="Startbuch-FRRST"';if($membercont=="F_RR_ST"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Showteam" />Showteam<br>
                           <input type="hidden" name="Startbuchnummer" value="'.$memberrow[3].'">
                           </td>
                       </tr>
                       <tr>
                         <td width="40%">Startmarke für:<br><br></td>
                         <td>
                           <input id="StartmarkeLauf" name="Startmarke" class="element radio" type="radio" value="Laufende Saison" />laufende Saison bis 31. Dezember<br>
                           <input id="StartmarkeFolg" name="Startmarke" class="element radio" type="radio" value="Folgende Saison" />folgende Saison ab 1. Januar<br>            
                         </td>
                       </tr>  
                     </table>
                   </fieldset>
                   <br />
                   <fieldset>
                     <legend>Angaben zum Formationsnamen</legend>
                     <table>
                       <tr>
                         <td width="40%">Formationsname:</td>
                         <td><input type="text" value="'.$memberrow[6].'" name="FormationsName" tabindex="4" size="55"></td>
                       </tr>
                     </table>    
                   </fieldset>                    
                   <br />                             
                    <fieldset>
                      <legend>Angaben zum Formationsverantwortlichen</legend>
                      <table>
                       <tr>
                        <td width="40%">Name:</td>
                        <td><input type="text" name="NameDa" value="'.$memberrow[15].'" tabindex="5" size="55"></td>
                       </tr>
                       <tr>
                        <td>Vorname:</td>
                        <td><input type="text" name="VornameDa" value="'.$memberrow[14].'" tabindex="6" size="55"></td>
                       </tr>
                       <tr>
                        <td>Strasse:</td>
                        <td><input type="text" name="StrasseDa" value="'.$memberrow[17].'" tabindex="7" size="55"></td>
                       </tr>  
                       <tr>
                        <td>PLZ / Ort:</td>
                        <td>
                          <input type="text" name="PLZDa" value="'.$memberrow[18].'" tabindex="8" size="5" maxlength="5">
                          <input type="text" name="OrtDa" value="'.$memberrow[19].'" tabindex="9" size="43">
                        </td>
                       </tr>  
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailDa" tabindex="10" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelDa" tabindex="11" size="55"></td>
                       </tr>  
                     </table>   
                   </fieldset>
                   <!-- Ende der Beispielangaben -->
                   <br />';
                     } elseif(substr($membercont,0,1) == 'B') {                       
                 echo '
                   <font face="Verdana" size="3" color="#000080"><br />  
                   <fieldset>
                     <legend>Angaben zur Startkartenfolgebestellung für: ';
                       if($memberrow[7]){
                         echo $memberrow[7].' '.$memberrow[8];
                       } else {
                         echo $memberrow[14].' '.$memberrow[15];  
                       };
                echo'</legend>  
                     <!-- br />
                     <div class="hinweis">Das betreffende Startbuch/Startkarte ist an die Geschäftsstelle zu senden.<br />
                        Bitte dazu diesen <a href="http://www.drbv.de/cms/images/PDF/Formulare/Einsendung_StartbuchStartkarte.pdf" target="_blank">Adressvordruck</a> verwenden!<p>&nbsp;</p></div -->
                     <table>
                       <tr>
                        <td width="45%">Startklasse Boogie-Woogie:<br><br><br></td>
                        <td>
                          <input id="Startbuch-BWA"';if($membercont=="BW_A"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-A" />Boogie-Woogie-A<br>
                          <input id="Startbuch-BWB"';if($membercont=="BW_B"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-B" />Boogie-Woogie-B<br>            
                          <input id="Startbuch-BWJ"';if($membercont=="BW_J"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-J" />Boogie-Woogie-Junior<br>            
                          <input type="hidden" name="Startbuchnummer" value="'.$memberrow[3].'">
                       </td>
                       </tr>
                       <tr>
                         <td width="45%">Startmarke für:<br><br></td>
                         <td>
                           <input id="StartmarkeLauf" name="Startmarke" class="element radio" type="radio" value="Laufende Saison" />laufende Saison bis 31. Dezember<br>
                           <input id="StartmarkeFolg" name="Startmarke" class="element radio" type="radio" value="Folgende Saison" />folgende Saison ab 1. Januar<br>            
                         </td>
                       </tr>  
                     </table>
                   </fieldset>
                   <br />';
                       if($memberrow[7]){echo'
                   <fieldset>
                     <legend>Angaben zum Herrn</legend>
                     <table>
                       <tr>
                         <td width="40%">Name:</td>
                         <td><input type="text" name="NameHr" value="'.$memberrow[8].'" tabindex="5" size="55"></td>
                       </tr>
                       <tr>
                         <td>Vorname:</td>
                         <td><input type="text" name="VornameHr" value="'.$memberrow[7].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Strasse:</td>
                         <td><input type="text" name="StrasseHr" value="'.$memberrow[10].'" tabindex="6" size="55"></td>
                       </tr>  
                      <tr>
                         <td>PLZ / Ort:</td>
                          <td>
                           <input type="text" name="PLZHr" value="'.$memberrow[11].'" tabindex="7" size="5" maxlength="5">
                           <input type="text" name="OrtHr" value="'.$memberrow[12].'" tabindex="8" size="43">
                         </td>
                       </tr>  
                       <tr>
                         <td>Geburtsdatum:</td>
                       <td>
                         <input type="text" id="IdGebHr" name="GebHr" value="'.$memberrow[9].'" size="10" maxlength="10" value="" readonly="readonly">
                       </td>
                       </tr> 
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailHr" tabindex="10" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelHr" tabindex="11" size="55"></td>
                       </tr>  
                      </table>    
                    </fieldset>                  
                    <br />';                                                  
                       }
                       if($memberrow[14]){echo'
                    <fieldset>
                      <legend>Angaben zur Dame</legend>
                      <table>
                       <tr>
                        <td width="40%">Name:</td>
                        <td><input type="text" name="NameDa" value="'.$memberrow[15].'" tabindex="10" size="55"></td>
                       </tr>
                       <tr>
                        <td>Vorname:</td>
                        <td><input type="text" name="VornameDa" value="'.$memberrow[14].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                        <td>Strasse:</td>
                        <td><input type="text" name="StrasseDa" value="'.$memberrow[17].'" tabindex="11" size="55"></td>
                       </tr>  
                       <tr>
                        <td>PLZ / Ort:</td>
                        <td>
                          <input type="text" name="PLZDa" value="'.$memberrow[18].'" tabindex="12" size="5" maxlength="5">
                          <input type="text" name="OrtDa" value="'.$memberrow[19].'" tabindex="13" size="43">
                        </td>
                       </tr>  
                       <tr>
                        <td>Geburtsdatum:</td>
                        <td>
                          <input type="text" id="IdGebDa" name="GebDa" value="'.$memberrow[16].'" size="10" maxlength="10" value="" readonly="readonly">
                        </td>
                       </tr>
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailDa" tabindex="15" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelDa" tabindex="16" size="55"></td>
                       </tr>  
                     </table>   
                   </fieldset>
                   <!-- Ende der Beispielangaben -->
                   <br />';
                       }
                     } else {
                         $name = $memberrow[7].' '.$memberrow[8].' & '.$memberrow[14].' '.$memberrow[15];
                       
                 echo '
                   <font face="Verdana" size="3" color="#000080"><br />  
                   <fieldset>
                     <legend>Angaben zur Startbuchfolgebestellung für: '.$name.'</legend>  
                     <!-- br />
                     <div class="hinweis">Das betreffende Startbuch/Startkarte ist an die Geschäftsstelle zu senden.<br />
                        Bitte dazu diesen <a href="http://www.drbv.de/cms/images/PDF/Formulare/Einsendung_StartbuchStartkarte.pdf" target="_blank">Adressvordruck</a> verwenden!<p>&nbsp;</p></div -->
                     <table>
                       <tr>
                         <td width="40%">Startklasse Rock\'n\'Roll:<br><br><br><br><br></td>
                         <td>
                           <input id="Startbuch-RRS"';if($membercont=="RR_S"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Schüler-Klasse" />Schüler-Klasse<br>
                           <input id="Startbuch-RRJ"';if($membercont=="RR_J"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="Junioren-Klasse" />Junioren-Klasse<br>
                           <input id="Startbuch-RRC"';if($membercont=="RR_C"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="C-Klasse" />C-Klasse<br>
                           <input id="Startbuch-RRB"';if($membercont=="RR_B"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="B-Klasse" />B-Klasse<br>
                           <input id="Startbuch-RRA"';if($membercont=="RR_A"){echo' checked="checked"';};echo' name="Startbuch" class="element radio" type="radio" value="A-Klasse" />A-Klasse<br>
                           <input type="hidden" name="Startbuchnummer" value="'.$memberrow[3].'">
                         </td>
                       </tr>
                       <tr>
                         <td width="40%">Startmarke für:<br><br></td>
                         <td>
                           <input id="StartmarkeLauf" name="Startmarke" class="element radio" type="radio" value="Laufende Saison" />laufende Saison bis 31. Dezember<br>
                           <input id="StartmarkeFolg" name="Startmarke" class="element radio" type="radio" value="Folgende Saison" />folgende Saison ab 1. Januar<br>            
                         </td>
                       </tr>  
                     </table>
                   </fieldset>
                   <br />
                   <fieldset>
                     <legend>Angaben zum Herrn</legend>
                     <table>
                       <tr>
                         <td width="40%">Name:</td>
                         <td><input type="text" name="NameHr" value="'.$memberrow[8].'" tabindex="5" size="55"></td>
                       </tr>
                       <tr>
                         <td>Vorname:</td>
                         <td><input type="text" name="VornameHr" value="'.$memberrow[7].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Strasse:</td>
                         <td><input type="text" name="StrasseHr" value="'.$memberrow[10].'" tabindex="6" size="55"></td>
                       </tr>  
                      <tr>
                         <td>PLZ / Ort:</td>
                          <td>
                           <input type="text" name="PLZHr" value="'.$memberrow[11].'" tabindex="7" size="5" maxlength="5">
                           <input type="text" name="OrtHr" value="'.$memberrow[12].'" tabindex="8" size="43">
                         </td>
                       </tr>  
                       <tr>
                         <td>Geburtsdatum:</td>
                       <td>
                         <input type="text" id="IdGebHr" name="GebHr" value="'.$memberrow[9].'" size="10" maxlength="10" value="" readonly="readonly">
                       </td>
                       </tr> 
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailHr" tabindex="10" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelHr" tabindex="11" size="55"></td>
                       </tr>  
                      </table>    
                    </fieldset>                  
                    <br />
                    <fieldset>
                      <legend>Angaben zur Dame</legend>
                      <table>
                       <tr>
                        <td width="40%">Name:</td>
                        <td><input type="text" name="NameDa" value="'.$memberrow[15].'" tabindex="12" size="55"></td>
                       </tr>
                       <tr>
                        <td>Vorname:</td>
                        <td><input type="text" name="VornameDa" value="'.$memberrow[14].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                        <td>Strasse:</td>
                        <td><input type="text" name="StrasseDa" value="'.$memberrow[17].'" tabindex="13" size="55"></td>
                       </tr>  
                       <tr>
                        <td>PLZ / Ort:</td>
                        <td>
                          <input type="text" name="PLZDa" value="'.$memberrow[18].'" tabindex="14" size="5" maxlength="5">
                          <input type="text" name="OrtDa" value="'.$memberrow[19].'" tabindex="15" size="43">
                        </td>
                       </tr>  
                       <tr>
                        <td>Geburtsdatum:</td>
                        <td>
                          <input type="text" id="IdGebDa" name="GebDa" value="'.$memberrow[16].'" size="10" maxlength="10" value="" readonly="readonly">
                        </td>
                       </tr>
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailDa" tabindex="17" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelDa" tabindex="18" size="55"></td>
                       </tr>  
                     </table>   
                   </fieldset>
                   <!-- Ende der Beispielangaben -->
                   <br />';
                                              
                     }
                   }
                 }
                 echo '
                   <fieldset>
                     <legend>Bemerkungen</legend>
                     <table>
                       <tr valign="top">
                         <td>
                           <textarea name="Bemerkungen" rows="3" cols="40" tabindex="20">?</textarea>
                         </td>
                       </tr>
                     </table>                   
                   </fieldset>        
                   </font>
                   <p>
                     <input type="submit" value="Absenden" />
                     <input type="reset"  value="Zurücksetzen" />
                   </p>    
                 </form>
                 </div> <!-- ENDE Container: Einzelpaare S,J,C,B,A  --> ';                                                         
                 $stb_cnt = $stb_cnt + 1;
               }  
                   
?>