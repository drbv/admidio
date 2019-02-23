<?php
/******************************************************************************
 * Form for Sporttauglichkeit
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Sporttauglichkeit" id="formSwitcher" method="post" autocomplete="off" accept-charset="UTF-8">';
                 echo'  
                 <font face="Verdana" size="3" color="#000080">
                 <h3>&nbsp;Sporttauglichkeitsnachweis nachreichen</h3>
                 <br />
                 <fieldset>
                   <legend>Sporttauglichkeit nachweisen für</legend>               
                    <br />
                    <i>Folgende Startbücher wurden gefunden,<br>die eine Sporttauglichkeit nachweisen müssen.<br><br></i>
                     <select name="Sporttauglichkeit" id="wechsler">
                       <option value="" selected="selected">Bitte auswählen ...</option>';
                        $stb_cnt = 1;
                        foreach ($member_array as $memberrow) {
                          foreach ($memberrow as $membercol => $membercont) {
                            if(substr($membercont,0,1) == 'R' && substr($memberrow[3],0,1) != '5'){
                              if($membercol==4){
                                $name = $memberrow[7].' '.$memberrow[8].' & '.$memberrow[14].' '.$memberrow[15];
                                echo '<option value="Member'.$stb_cnt.'">'.$memberrow[3].' - '.$name.'</option>';
                              }
                            }
                          }                          
                          $stb_cnt = $stb_cnt + 1;
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
                 <form name="Sporttauglichkeit1" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=11" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
                   <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Absender Informationen</legend>
                     <table>
                       <tr>
                         <td width="30%">Ihr Name:</td>
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
                   <br />';                   
                   if($memberrow[5] == '1'){
                     $stb_valid = 'Ja';
                   } else {
                     $stb_valid = 'Nein';
                   }                                                 
                   echo'
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                      <legend>Angaben zum Startbuch</legend>
                      <table>
                       <tr>
                        <td width="40%">Startbuchnummer:</td>
                        <td><input type="text" name="Stbuchnr" value="'.$memberrow[3].'" tabindex="12" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                        <td>Gültige Startmarke:</td>
                        <td><input type="text" name="Stbuchvalid" value="'.$stb_valid.'" size="55" readonly="readonly"></td>
                       </tr>
                     </table>   
                   </fieldset>
                   </font>                                                                
                   <font face="Verdana" size="3" color="#000080">
                   <br />  
                   <script type="text/javascript"><!--
                     $(document).ready(function() {
                         $("#Versender").focus();
                     $(".admLinkAddAttachmentHr").css("cursor", "pointer");
                     // add new line to add new attachment to this mail
                     $(".admLinkAddAttachmentHr").click(function () {
                       newAttachmentHr = document.createElement("input");
                       $(newAttachmentHr).attr("type", "file");
                       $(newAttachmentHr).attr("name", "userfile[]");
                       $(newAttachmentHr).css("display", "block");
                       $(newAttachmentHr).css("width", "90%");
                       $(newAttachmentHr).css("margin-bottom", "5px");
                       $(newAttachmentHr).hide();
                       $("#admAddAttachmentHr").before(newAttachmentHr);
                       $(newAttachmentHr).show("slow");
                     });
                    });   
                    //--></script>                                                                                           
                    <fieldset>
                     <legend>Angaben zum Herrn</legend>
                     <table>
                       <tr>
                         <td width="40%">Name:</td>
                         <td><input type="text" name="NameHr" value="'.$memberrow[8].'" tabindex="5" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Vorname:</td>
                         <td><input type="text" name="VornameHr" value="'.$memberrow[7].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                         <td>Geburtsdatum:</td>
                         <td>
                         <input type="text" id="IdGebHr" name="GebHr" value="'.$memberrow[9].'" size="10" maxlength="10" value="" readonly="readonly">
                         </td>
                       </tr> 
                       <tr>
                         <td>Datum Sporttauglichkeit:</td>
                         <td>
                           <input type="text" id="IdSprtlkAltHr" name="SprtlkAltHr" value="'.$memberrow[13].'" size="10" maxlength="10" value="" readonly="readonly">
                           letzter gültiger Nachweis.                                                                            
                         </td>
                       </tr> 
                       <tr>
                         <td>Datum Sporttauglichkeit:
                           <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DRBV_SPTGK_INF&amp;inline=true"><img 
                             onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DRBV_SPTGK_INF\',this)" onmouseout="ajax_hideTooltip()"
                             class="iconHelpLink" src="'. THEME_PATH. '/icons/info.png" alt="Info" title="" /></a>  
                         </td>
                         <td>
                         <script type="text/javascript">
                           var calDate = new CalendarPopup("calendardiv");
                           calDate.setCssPrefix("calendar");
                           calDate.showNavigationDropdowns();
                           calDate.setYearSelectStartOffset(50);
                           calDate.setYearSelectEndOffset(10);
                         </script>
                         <input type="text" id="IdSprtlkNeuHr" name="SprtlkNeuHr" tabindex="12" size="10" maxlength="10" value="">
                         <a class="iconLink" id="anchor_SprtlkNeuHr" href="javascript:calDate.select(document.getElementById(\'IdSprtlkNeuHr\'),\'anchor_SprtlkNeuHr\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>neuer Nachweis.
                        </td>
                       </tr>  
                       <tr>
                         <td>&nbsp;</td>
                         <td>
                           <input type="hidden" name="AttachmentHr" value="' . ($gPreferences['max_email_attachment_size'] * 1024) . '" />
                           <span id="admAddAttachmentHr" class="iconTextLink" style="display: block;">
                              <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'&amp;inline=true"><img 
                               onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'\',this)" onmouseout="ajax_hideTooltip()"
                               class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
                              <a class="admLinkAddAttachmentHr"><img
                               src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('MAI_ADD_ATTACHEMENT').'" /></a>
                              <a class="admLinkAddAttachmentHr" style="font-size:.8em;">Sporttauglichkeit</a>                                 
                           </span>  
                         </td>
                       </tr>                                                                                                      
                      </table>    
                    </fieldset>                  
                    <br />
                    <script type="text/javascript"><!--
                     $(document).ready(function() {
                         $("#Versender").focus();
                     $(".admLinkAddAttachmentDa").css("cursor", "pointer");
                     // add new line to add new attachment to this mail
                     $(".admLinkAddAttachmentDa").click(function () {
                       newAttachmentDa = document.createElement("input");
                       $(newAttachmentDa).attr("type", "file");
                       $(newAttachmentDa).attr("name", "userfile[]");
                       $(newAttachmentDa).css("display", "block");
                       $(newAttachmentDa).css("width", "90%");
                       $(newAttachmentDa).css("margin-bottom", "5px");
                       $(newAttachmentDa).hide();
                       $("#admAddAttachmentDa").before(newAttachmentDa);
                       $(newAttachmentDa).show("slow");
                     });
                    });   
                    //--></script>                                                                                                             
                    <fieldset>
                      <legend>Angaben zur Dame</legend>
                      <table>
                       <tr>
                        <td width="40%">Name:</td>
                        <td><input type="text" name="NameDa" value="'.$memberrow[15].'" tabindex="12" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                        <td>Vorname:</td>
                        <td><input type="text" name="VornameDa" value="'.$memberrow[14].'" size="55" readonly="readonly"></td>
                       </tr>
                       <tr>
                        <td>Geburtsdatum:</td>
                        <td>
                          <input type="text" id="IdGebDa" name="GebDa" value="'.$memberrow[16].'" size="10" maxlength="10" value="" readonly="readonly">
                        </td>
                       </tr>
                       <tr>
                         <td>Datum Sporttauglichkeit:</td>
                         <td>
                           <input type="text" id="IdSprtlkAltDa" name="SprtlkAltDa" value="'.$memberrow[20].'" size="10" maxlength="10" value="" readonly="readonly">
                           letzter gültiger Nachweis.                                                                            
                         </td>
                       </tr>                                                                                            
                       <tr>
                         <td>Datum Sporttauglichkeit:
                           <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DRBV_SPTGK_INF&amp;inline=true"><img 
                             onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DRBV_SPTGK_INF\',this)" onmouseout="ajax_hideTooltip()"
                             class="iconHelpLink" src="'. THEME_PATH. '/icons/info.png" alt="Info" title="" /></a>  
                         </td>
                         <td>
                         <script type="text/javascript">
                           var calDate = new CalendarPopup("calendardiv");
                           calDate.setCssPrefix("calendar");
                           calDate.showNavigationDropdowns();
                           calDate.setYearSelectStartOffset(50);
                           calDate.setYearSelectEndOffset(10);
                         </script>
                         <input type="text" id="IdSprtlkNeuDa" name="SprtlkNeuDa" tabindex="12" size="10" maxlength="10" value="">
                           <a class="iconLink" id="anchor_SprtlkNeuDa" href="javascript:calDate.select(document.getElementById(\'IdSprtlkNeuDa\'),\'anchor_SprtlkNeuDa\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>neuer Nachweis.
                        </td>
                       </tr>  
                       <tr>
                         <td>&nbsp;</td>
                         <td>
                           <input type="hidden" name="AttachmentDa" value="' . ($gPreferences['max_email_attachment_size'] * 1024) . '" />
                           <span id="admAddAttachmentDa" class="iconTextLink" style="display: block;">
                              <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'&amp;inline=true"><img 
                               onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=MAI_MAX_SPORTTGLKT_SIZE&amp;message_var1='. Email::getMaxAttachementSize('mb').'\',this)" onmouseout="ajax_hideTooltip()"
                               class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Help" title="" /></a>
                              <a class="admLinkAddAttachmentDa"><img
                               src="'. THEME_PATH. '/icons/add.png" alt="'.$gL10n->get('MAI_ADD_ATTACHEMENT').'" /></a>
                              <a class="admLinkAddAttachmentDa" style="font-size:.8em;">Sporttauglichkeit</a>                                 
                           </span>  
                         </td>
                       </tr>                                                                                                                                                                                                            
                     </table>   
                   </fieldset>
                   <!-- Ende der Beispielangaben -->
                   <br />';
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