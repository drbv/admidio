<?php
/******************************************************************************
 * Form for Startmarken
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=10" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Startmarkenbestellung</h3>
                  <br />
                  <fieldset>
                    <legend>Absender Informationen</legend>
                    <table>
                      <tr>
                        <td>Ihr Name:</td>
                        <td><input required type="text" name="Versender" tabindex="1" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Ihre Mailadresse:</td>
                        <td><input required type="text" name="EMail" tabindex="2" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                  </font>
                  <font face="Verdana" size="3" color="#000080">
                  <br />
                  <fieldset>                    
                  <legend>Folgende Startbücher/Startkarten wurden gefunden</legend>
                    <br />
                    <i>Bitte ankreuzen, für welche Startbücher/Startkarten und für welche Saison 
                       Startmarken bestellt werden.<br />&nbsp;</i>
                    <table border="0" width="95%" align="center">
                      <tr>
                        <th align="center">Nummer:</th>
                        <th>Klasse:</th>
                        <th>Name:</th>
                        <th align="center">Startmarke:</th>
                        <th align="center" style="font-size: 60%;">Laufende<br>Saison<br>bis 31.12.</th>
                        <th align="center" style="font-size: 60%;">Folgende<br>Saison<br>ab 01.01.</th>
                      </tr>';
                        $stb_invalid_cnt = 0;
                        $stb_cnt = 1;
                        foreach ($member_array as $memberrow) {
                        echo '<tr>';                          
                          foreach ($memberrow as $membercol => $membercont) {
                            //Praeambelturnierstartbuecher 50000+ werden ausgenommen 
                            if($membercol==4 && (substr($memberrow[3],0,1) != '5')){
                              echo'<td align="center">'.$memberrow[3].'</td>';
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
                              if($memberrow[5] == '1'){
                                $stb_valid = 'Ja';
                              } else {
                                $stb_valid = 'Nein';
                                $stb_invalid_cnt = $stb_invalid_cnt + 1;
                              }   
                              $hinweis_alter = '';
                              if($membercont=="RR_S"){
                                $sbisjahr = min(substr($memberrow[9],6,4),substr($memberrow[16],6,4))+14;
                                $hinweis_alter = 'bis '.($sbisjahr);
                              }
                              if($membercont=="RR_J"){
                                $sbisjahr = min(substr($memberrow[9],6,4),substr($memberrow[16],6,4))+17;                   
                                $hinweis_alter = 'bis '.($sbisjahr);
                              }                                                            
                              echo '
                              <td><input type="hidden" name="Startklasse" value="'.$membercont.'" readonly="readonly">'.$membercont.'</td>
                              <td>'.$name.'</td>
                              <td align="center">'.$stb_valid.'</td>
                              <td align="center">
                                <input type="hidden" name="sent" value="yes">
                                <input id="StartbuchLauf'.$stb_cnt.'" name="StartbuchLauf[]" class="element checkbox" type="checkbox" value="'.$memberrow[3].' - '.$name.' / '.$memberrow[4].' '.$hinweis_alter.'" />
                              </td>
                              <td align="center">
                                <input id="StartbuchFolg'.$stb_cnt.'" name="StartbuchFolg[]" class="element checkbox" type="checkbox" value="'.$memberrow[3].' - '.$name.' / '.$memberrow[4].' '.$hinweis_alter.'" />
                              </td>';
                              if(substr($membercont,0,1) == 'R'){
                                echo '</tr>
                                  <script type="text/javascript"><!--
                                      $(document).ready(function() {
                                          $("#Versender").focus();
                                      $(".admLinkAddAttachment'.$memberrow[3].'").css("cursor", "pointer");
                                      // add new line to add new attachment to this mail
                                      $(".admLinkAddAttachment'.$memberrow[3].'").click(function () {
                                        newAttachment'.$memberrow[3].' = document.createElement("input");
                                        $(newAttachment'.$memberrow[3].').attr("type", "file");
                                        $(newAttachment'.$memberrow[3].').attr("name", "userfile[]");
                                        $(newAttachment'.$memberrow[3].').css("display", "block");
                                        $(newAttachment'.$memberrow[3].').css("width", "90%");
                                        $(newAttachment'.$memberrow[3].').css("margin-bottom", "5px");
                                        $(newAttachment'.$memberrow[3].').hide();
                                        $("#admAddAttachment'.$memberrow[3].'").before(newAttachment'.$memberrow[3].');
                                        $(newAttachment'.$memberrow[3].').show("slow");
                                      });
                                     });   
                                  //--></script>         
                                  <tr>
                                    <td>&nbsp;</td>
                                    <td style="font-size:.8em;" input type="hidden" name="Altershinweis" value="'.$hinweis_alter.'" readonly="readonly">'.$hinweis_alter.'&nbsp;</td>
                                    <td colspan=4>
                                      <input type="hidden" name="'.$memberrow[3].'" value="' . ($gPreferences['max_email_attachment_size'] * 1024) . '" />
                                      <span id="admAddAttachment'.$memberrow[3].'" class="iconTextLink" style="display: block;">
                                        <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'&amp;inline=true"><img 
                                         onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'\',this)" onmouseout="ajax_hideTooltip()"
                                         class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
                                        <a class="colorboxHelp" href="#">
                                         <img class="iconHelpLink" src="'. THEME_PATH. '/icons/info.png" alt="Info" title="Datum der letzten Sporttauglichkeit: &#10;Herr: '.$memberrow[13].' &#10;Dame: '.$memberrow[20].'" /></a>
                                        <a class="admLinkAddAttachment'.$memberrow[3].'"><img
                                         src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('MAI_ADD_ATTACHEMENT').'" /></a>
                                        <a class="admLinkAddAttachment'.$memberrow[3].'" style="font-size:.8em;">Sporttauglichkeit</a>                                 
                                    </span>
                                  </td></tr>';
                              } else {echo '</tr>';}                              
                            }                                                        
                          }
                          $stb_cnt = $stb_cnt + 1;
                        }  
                   echo'
                    </table>
                     <br /><i>'.$stb_invalid_cnt.' Startbücher/Startkarten haben aktuell keine Startmarke!</i>    
                  </fieldset>
                  </font>
                  <font face="Verdana" size="3" color="#000080">
                  <br />
                  <fieldset>
                    <legend>Expressbearbeitung</legend>
                    <table>
                       <tr valign="top">
                        <td>
                          <input id="Expressbearbeitung" name="Expressbearbeitung" class="element checkbox" type="checkbox" value="Ja" /> Wenn eine Expressbearbeitung gewünscht wird, bitte ankreuzen.
                        </td>
                      </tr>
                      <tr valign="top">
                        <td>
                          <p align="justify">Die Bearbeitungszeit für alle Anträge von Startbüchern, -karten und -marken, Lizenzpässen und -marken
                          beträgt mind. 8 Werktage nach Eingang der vollständigen Unterlagen in der Geschäftsstelle und werden
                          zu den Gebühren gemäß der Punkte 1.2.3 und 1.2.4 der FO des DRBV berechnet.</p>
                          <p align="justify">Bei einem Expressbearbeitungswunsch von Startbüchern, -karten und -marken, Lizenzpässen und -marken
                          innerhalb der letzten 7 bis 3 Werktage vor dem letzten möglichen Turnieranmeldungstag verdoppelt sich
                          die jeweilige Gebühr. Gültig ist der Tag des Eingangs.</p>
                          <p align="justify">Eine Bearbeitung innerhalb von 1-2 Werktagen ist nicht möglich.</p>
                        </td>
                      </tr>
                   </table>                                                                
                  </fieldset>
                  </font>
                  <font face="Verdana" size="3" color="#000080">
                  <br />
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="4">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  </font>
                  <!-- Ende der Beispielangaben -->
                  <p>
                    <input type="submit" value="Absenden" name="button_submit"/>
                    <input type="reset"  value="Zurücksetzen" />
                  </p>
              </form>';  
    
?>