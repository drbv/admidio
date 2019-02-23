<?php
/******************************************************************************
 * Form for Vereinswechsel
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Vereinswechsel" id="formSwitcher" method="post" autocomplete="off" accept-charset="UTF-8">
                  
                 <font face="Verdana" size="3" color="#000080">
                 <h3>&nbsp;Startbuch/Startkarten Vereinswechsel</h3>
                 <br />
                 <fieldset>
                   <legend>Vereinswechsel für</legend>               
                     <select name="Vereinswechsel" id="wechsler">
                       <option value="" selected="selected">Bitte auswählen ...</option>
                       <option value="sel_stb_sjcba">Startbuch Paare</option>
                       <option value="sel_stb_form">Startbuch Formationen</option>
                       <option value="sel_stk_hr">Startkarte Herr</option>
                       <option value="sel_stk_da">Startkarte Dame</option>
                     </select>
                 </fieldset>
                 <br />
                 </font>
               </form>
  
                 <!-- Versteckte Container hier fallen lassen -->
                 <div id="pasteMe"></div>
  
                 <!-- Container: Einzelpaare S,J,C,B,A  -->
                 <div id="sel_stb_sjcba" class="hideMe">
                 <form name="Startbuchbestellung1" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=14" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
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
                   <br />
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Bestehendes Startbuch für Vereinswechsel</legend>
                     <table>
                       <tr>
                         <td width="30%">Startbuchnummer:</td>
                         <td><input required type="text" name="Startbuchnummer" tabindex="1" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Letzter Verein:</td>
                         <td><input required type="text" name="LetzterVerein" tabindex="2" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                     </table>    
                   </fieldset>
                   </font>
                   <br />
                   <font face="Verdana" size="3" color="#000080"><br />  
                   <fieldset>
                     <legend>Angaben zur Startbuchbestellung</legend>  
                     <table>
                       <tr>
                         <td width="45%">Startklasse Rock\'n\'Roll:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br><br><br><br></td>
                         <td>
                           <input id="Startbuch-RRS" name="Startbuch" class="element radio" type="radio" value="Schüler-Klasse" />Schüler-Klasse<br>
                           <input id="Startbuch-RRJ" name="Startbuch" class="element radio" type="radio" value="Junioren-Klasse" />Junioren-Klasse<br>
                           <input id="Startbuch-RRC" name="Startbuch" class="element radio" type="radio" value="C-Klasse" />C-Klasse<br>
                           <input id="Startbuch-RRB" name="Startbuch" class="element radio" type="radio" value="B-Klasse" />B-Klasse<br>
                           <input id="Startbuch-RRA" name="Startbuch" class="element radio" type="radio" value="A-Klasse" />A-Klasse<br>
                         </td>
                       </tr>
                       <tr>
                         <td width="45%">Startmarke für:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br></td>
                         <td>
                           <input id="StartmarkeLauf" name="Startmarke" class="element radio" type="radio" value="Laufende Saison" />laufende Saison bis 31. Dezember<br>
                           <input id="StartmarkeFolg" name="Startmarke" class="element radio" type="radio" value="Folgende Saison" />folgende Saison ab 1. Januar<br>            
                         </td>
                       </tr>  
                       <tr>
                         <td width="45%">rocktime Versand an:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br></td>
                         <td>
                           <input id="rocktimeDa" name="rocktime" class="element radio" type="radio" value="Dame" />die Adresse der Dame<br>
                           <input id="rocktimeHr" name="rocktime" class="element radio" type="radio" value="Herr" />die Adresse des Herrn<br>            
                         </td>
                       </tr>  
                     </table>
                   </fieldset>  
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
                         <td><input required type="text" name="NameHr" tabindex="5" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Vorname:</td>
                         <td><input required type="text" name="VornameHr" tabindex="6" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Strasse:</td>
                         <td><input required type="text" name="StrasseHr" tabindex="7" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>  
                       <tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input required type="text" name="PLZHr" tabindex="8" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                           <input required type="text" name="OrtHr" tabindex="9" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         </td>
                       </tr>  
                       <tr>
                         <td>Geburtsdatum:</td>
                         <td>
                         <script type="text/javascript">
                           var calDate = new CalendarPopup("calendardiv");
                           calDate.setCssPrefix("calendar");
                           calDate.showNavigationDropdowns();
                           calDate.setYearSelectStartOffset(50);
                           calDate.setYearSelectEndOffset(10);
                         </script>
                         <input required type="text" id="IdGebHr" name="GebHr" tabindex="10" size="10" maxlength="10" value=""><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         <a class="iconLink" id="anchor_GebHr" href="javascript:calDate.select(document.getElementById(\'IdGebHr\'),\'anchor_GebHr\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
                         </td>
                       </tr> 
                       <tr>
                         <td>Nationalität:</td>
                         <td><input required type="text" name="NationHr" value="Deutsch" tabindex="11" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                         <input type="text" id="IdSprtlkHr" name="SprtlkHr" tabindex="12" size="10" maxlength="10" value="">
                         <a class="iconLink" id="anchor_SprtlkHr" href="javascript:calDate.select(document.getElementById(\'IdSprtlkHr\'),\'anchor_SprtlkHr\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
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
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailHr" tabindex="13" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelHr" tabindex="14" size="55"></td>
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
                        <td><input required type="text" name="NameDa" tabindex="15" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                        <td>Vorname:</td>
                        <td><input required type="text" name="VornameDa" tabindex="16" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                        <td>Strasse:</td>
                        <td><input required type="text" name="StrasseDa" tabindex="17" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>  
                       <tr>
                        <td>PLZ / Ort:</td>
                        <td>
                          <input required type="text" name="PLZDa" tabindex="18" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                          <input required type="text" name="OrtDa" tabindex="19" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                        </td>
                       </tr>  
                       <tr>
                        <td>Geburtsdatum:</td>
                        <td>
                          <script type="text/javascript">
                            var calDate = new CalendarPopup("calendardiv");
                            calDate.setCssPrefix("calendar");
                            calDate.showNavigationDropdowns();
                            calDate.setYearSelectStartOffset(50);
                            calDate.setYearSelectEndOffset(10);
                          </script>
                          <input required type="text" id="IdGebDa" name="GebDa" tabindex="20" size="10" maxlength="10" value=""><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                          <a class="iconLink" id="anchor_GebDa" href="javascript:calDate.select(document.getElementById(\'IdGebDa\'),\'anchor_GebDa\',\'d.m.Y\');">
                            <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                          </a>
                          <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
                        </td>
                       </tr>
                       <tr>
                         <td>Nationalität:</td>
                         <td><input required type="text" name="NationDa" value="Deutsch" tabindex="21" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                          <input type="text" id="IdSprtlkDa" name="SprtlkDa" tabindex="22" size="10" maxlength="10" value="">
                          <a class="iconLink" id="anchor_SprtlkDa" href="javascript:calDate.select(document.getElementById(\'IdSprtlkDa\'),\'anchor_SprtlkDa\',\'d.m.Y\');">
                            <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                          </a>
                          <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
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
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailDa" tabindex="23" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelDa" tabindex="24" size="55"></td>
                       </tr>  
                     </table>   
                   </fieldset>
                   <!-- Ende der Beispielangaben -->
                   <br />  
                   <fieldset>
                     <legend>Bemerkungen</legend>
                     <table>
                       <tr valign="top">
                         <td>
                           <textarea name="Bemerkungen" rows="3" cols="40" tabindex="25">?</textarea>
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
                 </div> <!-- ENDE Container: Einzelpaare S,J,C,B,A  -->

                 <!-- Container: Formationen  -->
                 <div id="sel_stb_form" class="hideMe">
                 <form name="Startbuchbestellung2" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=14" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
                   <!-- Hier die eigentlichen Formularfelder eintragen. -->                  
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Absender Informationen</legend>
                     <table>
                       <tr>
                         <td width="30%">Ihr Name:</td>
                         <td><input required type="text" name="Versender" tabindex="17" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Ihre Mailadresse:</td>
                         <td><input required type="text" name="EMail" tabindex="18" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                   <br />
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Bestehendes Startbuch für Vereinswechsel</legend>
                     <table>
                       <tr>
                         <td width="30%">Startbuchnummer:</td>
                         <td><input required type="text" name="Startbuchnummer" tabindex="1" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Letzter Verein:</td>
                         <td><input required type="text" name="LetzterVerein" tabindex="2" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                     </table>    
                   </fieldset>
                   </font>
                   <br />
                   <font face="Verdana" size="3" color="#000080"><br />  
                   <fieldset>
                     <legend>Angaben zur Startbuchbestellung</legend>  
                     <table>
                       <tr>
                         <td width="45%">Startklasse Formationen:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br><br><br><br><br></td>
                         <td>
                           <input id="Startbuch-FBWMA" name="Startbuch" class="element radio" type="radio" value="BW-Master" />BW-Master<br>
                           <input id="Startbuch-FRRMA" name="Startbuch" class="element radio" type="radio" value="RR-Master" />RR-Master<br>
                           <input id="Startbuch-FRRJF" name="Startbuch" class="element radio" type="radio" value="Juniorenformation" />Juniorenformation<br>
                           <input id="Startbuch-FRRLF" name="Startbuch" class="element radio" type="radio" value="Ladyformation" />Ladyformation<br>
                           <input id="Startbuch-FRRGF" name="Startbuch" class="element radio" type="radio" value="Girlformation" />Girlformation<br>
                           <input id="Startbuch-FRRST" name="Startbuch" class="element radio" type="radio" value="Showteam" />Showteam<br>
                         </td>
                       </tr>
                       <tr>
                         <td width="45%">Startmarke für:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br></td>
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
                         <td><input required type="text" name="FormationsName" tabindex="19" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                     </table>    
                   </fieldset>                    
                   <br />
                   <fieldset>
                     <legend>Angaben zum Formationsverantwortlichen</legend>
                     <table>
                       <tr>
                         <td width="40%">Name:</td>
                         <td><input required type="text" name="NameFo" tabindex="20" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Vorname:</td>
                         <td><input required type="text" name="VornameFo" tabindex="21" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Strasse:</td>
                         <td><input required type="text" name="StrasseFo" tabindex="22" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>  
                       <tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input required type="text" name="PLZFo" tabindex="23" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                           <input required type="text" name="OrtFo" tabindex="24" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         </td>
                       </tr>
                       <tr>
                         <td>Mailadresse:</td>
                         <td><input type="text" name="MailFo" tabindex="25" size="55"></td>
                       </tr>  
                       <tr>
                         <td>Telefon/Handy:</td>
                         <td><input type="text" name="TelFo" tabindex="26" size="55"></td>
                       </tr>  
                      </table>    
                    </fieldset>                  
                  <!-- Ende der Beispielangaben -->
                  <br />  
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="27">?</textarea>
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
                </div><!-- ENDE Container: Formationen  -->

                <!-- Container: Startkarte Herr -->  
                <div id="sel_stk_hr" class="hideMe">
                <form name="Startbuchbestellung3" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=14" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. -->        
                  <font face="Verdana" size="3" color="#000080">
                  <fieldset>
                    <legend>Absender Informationen</legend>
                    <table>
                      <tr>
                        <td width="30%">Ihr Name:</td>
                        <td><input required type="text" name="Versender" tabindex="27" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Ihre Mailadresse:</td>
                        <td><input required type="text" name="EMail" tabindex="28" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                   <br />
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Bestehendes Startbuch für Vereinswechsel</legend>
                     <table>
                       <tr>
                         <td width="30%">Startbuchnummer:</td>
                         <td><input required type="text" name="Startbuchnummer" tabindex="1" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Letzter Verein:</td>
                         <td><input required type="text" name="LetzterVerein" tabindex="2" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                     </table>    
                   </fieldset>
                   </font>
                   <br />
                  <font face="Verdana" size="3" color="#000080"><br />  
                  <fieldset>
                    <legend>Angaben zur Startbuchbestellung</legend>  
                    <table>
                      <tr>
                        <td width="45%">Startklasse Boogie-Woogie:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br><br></td>
                        <td>
                          <input id="Startbuch-BWA" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-A" />Boogie-Woogie-A<br>
                          <input id="Startbuch-BWB" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-B" />Boogie-Woogie-B<br>            
                          <input id="Startbuch-BWJ" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-J" />Boogie-Woogie-Junior<br>            
                        </td>
                      </tr>
                      <tr>
                        <td width="45%">Startmarke für:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br></td>
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
                       <td><input required type="text" name="NameHr" tabindex="29" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>
                     <tr>
                       <td>Vorname:</td>
                       <td><input required type="text" name="VornameHr" tabindex="30" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>
                     <tr>
                       <td>Strasse:</td>
                       <td><input required type="text" name="StrasseHr" tabindex="31" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>  
                     <tr>
                       <td>PLZ / Ort:</td>
                       <td>
                     <input required type="text" name="PLZHr" tabindex="32" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                     <input required type="text" name="OrtHr" tabindex="33" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                       </td>
                     </tr>  
                     <tr>
                       <td>Geburtsdatum:</td>
                       <td>
                         <script type="text/javascript">
                           var calDate = new CalendarPopup("calendardiv");
                           calDate.setCssPrefix("calendar");
                           calDate.showNavigationDropdowns();
                           calDate.setYearSelectStartOffset(50);
                           calDate.setYearSelectEndOffset(10);
                         </script>
                         <input required type="text" id="IdGebHr" name="GebHr" tabindex="34" size="10" maxlength="10" value=""><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span> 
                         <a class="iconLink" id="anchor_GebHr" href="javascript:calDate.select(document.getElementById(\'IdGebHr\'),\'anchor_GebHr\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
                       </td>
                     </tr>  
                     <tr>
                       <td>Nationalität:</td>
                       <td><input required type="text" name="NationHr" value="Deutsch" tabindex="35" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>  
                     <tr>
                       <td>Mailadresse:</td>
                       <td><input type="text" name="MailHr" tabindex="36" size="55"></td>
                     </tr>  
                     <tr>
                       <td>Telefon/Handy:</td>
                       <td><input type="text" name="TelHr" tabindex="37" size="55"></td>
                     </tr>  
                    </table>  
                   </fieldset>
                  </font>
                  <!-- Ende der Beispielangaben -->
                  <br />  
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="38">?</textarea>
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
                </div><!-- Container: Startkarte Herr -->

                <!-- Container: Startkarte Dame -->
                <div id="sel_stk_da" class="hideMe">  
                <form name="Startbuchbestellung4" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=14" method="post" autocomplete="off" accept-charset="UTF-8" enctype="multipart/form-data" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. -->      
                  <font face="Verdana" size="3" color="#000080">
                  <fieldset>
                    <legend>Absender Informationen</legend>
                    <table>
                      <tr>
                        <td width="30%">Ihr Name:</td>
                        <td><input requiredtype="text" name="Versender" tabindex="39" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Ihre Mailadresse:</td>
                        <td><input required type="text" name="EMail" tabindex="40" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                   <br />
                   <font face="Verdana" size="3" color="#000080">
                   <fieldset>
                     <legend>Bestehende Startbuch für Vereinswechsel</legend>
                     <table>
                       <tr>
                         <td width="30%">Startbuchnummer:</td>
                         <td><input required type="text" name="Startbuchnummer" tabindex="1" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                       <tr>
                         <td>Letzter Verein:</td>
                         <td><input required type="text" name="LetzterVerein" tabindex="2" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                       </tr>
                     </table>    
                   </fieldset>
                   </font>
                   <br />
                  <font face="Verdana" size="3" color="#000080"><br />  
                  <fieldset>
                    <legend>Angaben zur Startbuchbestellung</legend>  
                    <table>
                      <tr>
                        <td width="45%">Startklasse Boogie-Woogie:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br><br></td>
                        <td>
                          <input id="Startbuch-BWA" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-A" />Boogie-Woogie-A<br>
                          <input id="Startbuch-BWB" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-B" />Boogie-Woogie-B<br>            
                          <input id="Startbuch-BWJ" name="Startbuch" class="element radio" type="radio" value="Boogie-Woogie-J" />Boogie-Woogie-Junior<br>            
                        </td>
                      </tr>
                      <tr>
                        <td width="45%">Startmarke für:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span><br><br></td>
                        <td>
                           <input id="StartmarkeLauf" name="Startmarke" class="element radio" type="radio" value="Laufende Saison" />laufende Saison bis 31. Dezember<br>
                           <input id="StartmarkeFolg" name="Startmarke" class="element radio" type="radio" value="Folgende Saison" />folgende Saison ab 1. Januar<br>            
                        </td>
                      </tr>
                    </table>
                  </fieldset>  
                  <br />
                   <fieldset>
                    <legend>Angaben zur Dame</legend>
                    <table>
                     <tr>
                       <td width="40%">Name:</td>
                       <td><input required type="text" name="NameDa" tabindex="41" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>
                     <tr>
                       <td>Vorname:</td>
                       <td><input required type="text" name="VornameDa" tabindex="42" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>
                     <tr>
                       <td>Strasse:</td>
                       <td><input required type="text" name="StrasseDa" tabindex="43" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>  
                     <tr>
                       <td>PLZ / Ort:</td>
                       <td>
                         <input required type="text" name="PLZDa" tabindex="44" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         <input required type="text" name="OrtDa" tabindex="45" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                       </td>
                     </tr>  
                     <tr>
                       <td>Geburtsdatum:</td>
                       <td>
                         <script type="text/javascript">
                           var calDate = new CalendarPopup("calendardiv");
                           calDate.setCssPrefix("calendar");
                           calDate.showNavigationDropdowns();
                           calDate.setYearSelectStartOffset(50);
                           calDate.setYearSelectEndOffset(10);
                         </script>
                         <input required type="text" id="IdGebDa" name="GebDa" tabindex="46" size="10" maxlength="10" value=""><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         <a class="iconLink" id="anchor_GebDa" href="javascript:calDate.select(document.getElementById(\'IdGebDa\'),\'anchor_GebDa\',\'d.m.Y\');">
                           <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                         </a>
                         <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
                       </td>
                     </tr>
                     <tr>
                       <td>Nationalität:</td>
                       <td><input required type="text" name="NationDa" value="Deutsch" tabindex="47" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                     </tr>  
                     <tr>
                       <td>Mailadresse:</td>
                       <td><input type="text" name="MailDa" tabindex="48" size="55"></td>
                     </tr>  
                     <tr>
                       <td>Telefon/Handy:</td>
                       <td><input type="text" name="TelDa" tabindex="49" size="55"></td>
                     </tr>  
                   </table>    
                  </fieldset>
                 <!-- Ende der Beispielangaben -->
                 <br />  
                 <fieldset>
                   <legend>Bemerkungen</legend>
                   <table>
                     <tr valign="top">
                       <td>
                         <textarea name="Bemerkungen" rows="3" cols="40" tabindex="50">?</textarea>
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
             </div><!-- ENDE Container: Startkarte Dame -->';
?>