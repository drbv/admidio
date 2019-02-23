<?php
/******************************************************************************
 * Form for Turnierbewerbung
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=4" method="post" autocomplete="off" accept-charset="UTF-8" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Turnier Bewerbung</h3>
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
                    <legend>Angaben zur Turnierbewerbung:</legend>
                    <table>
                      <tr>
                        <td valign=top>Bewerbung für:</td>     
                        <td valign=top>
                                <input id="NDM-BW" name="NDM-BW" class="element checkbox" type="checkbox" value="Norddeutsche Meisterschaft Boogie-Woogie" />&nbsp;Norddeutsche Meisterschaft Boogie-Woogie 
                          <br /><input id="NDM-BWF" name="NDM-BWF" class="element checkbox" type="checkbox" value="Norddeutsche Meisterschaft Formationen Boogie-Woogie" />&nbsp;Norddeutsche Meisterschaft Formationen Boogie-Woogie
                          <br /><input id="NDM-RRF" name="NDM-RRF" class="element checkbox" type="checkbox" value="Norddeutsche Meisterschaft Formationen Rock\'n\'Roll" />&nbsp;Norddeutsche Meisterschaft Formationen Rock\'n\'Roll      
                          <br /><br /><input id="SDM-BW" name="SDM-BW" class="element checkbox" type="checkbox" value="Süddeutsche Meisterschaft Boogie-Woogie" />&nbsp;Süddeutsche Meisterschaft Boogie-Woogie 
                          <br /><input id="SDM-BWF" name="SDM-BWF" class="element checkbox" type="checkbox" value="Süddeutsche Meisterschaft Formationen Boogie-Woogie" />&nbsp;Süddeutsche Meisterschaft Formationen Boogie-Woogie
                          <br /><input id="SDM-RRF" name="SDM-RRF" class="element checkbox" type="checkbox" value="Süddeutsche Meisterschaft Formationen Rock\'n\'Roll" />&nbsp;Süddeutsche Meisterschaft Formationen Rock\'n\'Roll
                          <br /><br /><input id="DC-BW" name="DC-BW" class="element checkbox" type="checkbox" value="Deutschland-Cup Boogie-Woogie" />&nbsp;Deutschland-Cup Boogie-Woogie 
                          <br /><input id="DC-BWF" name="DC-BWF" class="element checkbox" type="checkbox" value="Deutschland-Cup Formationen Boogie-Woogie" />&nbsp;Deutschland-Cup Formationen Boogie-Woogie
                          <br /><input id="DC-RRF" name="DC-RRF" class="element checkbox" type="checkbox" value="Deutschland-Cup Formationen Rock\'n\'Roll" />&nbsp;Deutschland-Cup Formationen Rock\'n\'Roll
                          <br /><input id="DC-RR" name="DC-RR" class="element checkbox" type="checkbox" value="Deutschland-Cup S/J/C/B/A Rock\'n\'Roll" />&nbsp;Deutschland-Cup S/J/C/B/A Rock\'n\'Roll         
                          <br /><br /><input id="GPvD-BW" name="GPvD-BW" class="element checkbox" type="checkbox" value="Großer Preis von Deutschland Boogie-Woogie " />&nbsp;Großer Preis von Deutschland Boogie-Woogie 
                          <br /><input id="GPvD-BWF" name="GPvD-BWF" class="element checkbox" type="checkbox" value="Großer Preis von Deutschland Formationen Boogie-Woogie" />&nbsp;Großer Preis von Deutschland Formationen Boogie-Woogie
                          <br /><input id="GPvD-RRF" name="GPvD-RRF" class="element checkbox" type="checkbox" value="Großer Preis von Deutschland Formationen Rock\'n\'Roll" />&nbsp;Großer Preis von Deutschland Formationen Rock\'n\'Roll
                          <br /><input id="GPvD-RR" name="GPvD-RR" class="element checkbox" type="checkbox" value="Großer Preis von Deutschland S/J/C/B/A Rock\'n\'Roll" />&nbsp;Großer Preis von Deutschland S/J/C/B/A Rock\'n\'Roll         
                          <br /><br /><input id="DM-BW" name="DM-BW" class="element checkbox" type="checkbox" value="Deutsche Meisterschaft Boogie-Woogie" />&nbsp;Deutsche Meisterschaft Boogie-Woogie 
                          <br /><input id="DM-BWF" name="DM-BWF" class="element checkbox" type="checkbox" value="Deutsche Meisterschaft Formationen Boogie-Woogie" />&nbsp;Deutsche Meisterschaft Formationen Boogie-Woogie
                          <br /><input id="DM-RRF" name="DM-RRF" class="element checkbox" type="checkbox" value="Deutsche Meisterschaft Formationen Rock\'n\'Roll" />&nbsp;Deutsche Meisterschaft Formationen Rock\'n\'Roll
                          <br /><input id="DM-RR" name="DM-RR" class="element checkbox" type="checkbox" value="Deutsche Meisterschaft S/J/C/B/A Rock\'n\'Roll" />&nbsp;Deutsche Meisterschaft S/J/C/B/A Rock\'n\'Roll           
                          <br /><input id="DM-CLUB" name="DM-CLUB" class="element checkbox" type="checkbox" value="Deutsche Meisterschaft der Clubs" />&nbsp;Deutsche Meisterschaft der Clubs           
                        </td> 
                      </tr>
                      <tr>
                      </tr>
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Angaben zum Veranstalter/Ausrichter</legend>
                    <table>
                      <tr>
                        <td>Name:</td>
                        <td><input required type="text" name="VeranstalterName" value="'.$user->getValue('VEREIN').'" tabindex="4" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Straße:</td>
                        <td><input required type="text" name="VeranstalterStrasse" value="'.$user->getValue('STRAßE').'" tabindex="5" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input required type="text" name="VeranstalterPLZ" value="'.$user->getValue('PLZ').'" tabindex="6" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                           <input required type="text" name="VeranstalterOrt" value="'.$user->getValue('ORT').'" tabindex="7" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         </td>
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Ansprechpartner für Turnierorganisation</legend>
                    <table>
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <div class="formlabel">
                            Diese Angaben werden im Internet veröffentlicht. Daher die zur Veröffentlichung gewünschten Daten angeben.
                          </div>
                        </td>
                      </tr>  
                      <tr>
                        <td>Name:</td>
                        <td><input required type="text" name="AnsprechpartnerName" tabindex="8" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Straße:</td>
                        <td><input required type="text" name="AnsprechpartnerStrasse" tabindex="9" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input required type="text" name="AnsprechpartnerPLZ" tabindex="10" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                           <input required type="text" name="AnsprechpartnerOrt" tabindex="11" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         </td>
                      </tr>
                      <tr>
                        <td>Telefon:</td>
                        <td><input type="text" name="AnsprechpartnerTelefon" tabindex="12" size="55"></td>
                      </tr>
                      <tr>
                        <td>Handy:</td>
                        <td><input type="text" name="AnsprechpartnerHandy" tabindex="13" size="55"></td>
                      </tr>
                      <tr>
                        <td>E-Mail:</td>
                        <td><input required type="text" name="AnsprechpartnerMail" value="'.$user->getValue('EMAIL').'" tabindex="14" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Internet:</td>
                        <td><input type="text" name="AnsprechpartnerInternet" value="'.$user->getValue('WEBSEITE').'" tabindex="15" size="55"></td>
                      </tr>
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Planungen zur Veranstaltung</legend>
                    <table>
                      <tr>
                        <td>Datum:</td>
                        <td>
                        <script type="text/javascript">
                          var calDate = new CalendarPopup("calendardiv");
                          calDate.setCssPrefix("calendar");
                          calDate.showNavigationDropdowns();
                          calDate.setYearSelectStartOffset(50);
                          calDate.setYearSelectEndOffset(10);
                        </script>
                        <input required type="text" id="IdVerDat" name="VeranstaltungDatum" tabindex="17" size="10" maxlength="10" value=""><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                        <a class="iconLink" id="anchor_VerDat" href="javascript:calDate.select(document.getElementById(\'IdVerDat\'),\'anchor_VerDat\',\'d.m.Y\');">
                          <img src="http://drbv.de/adm/adm_themes/classic/icons/calendar.png" alt="Kalender anzeigen" title="Kalender anzeigen" />
                        </a>
                        <span id="calendardiv" style="position: absolute; visibility: hidden;"></span>
                        </td>
                      </tr>   
                      <tr>
                        <td>Beginn/Ende:</td>
                        <td>
                          <select size="1" id="VeranstaltungBeginn" name="VeranstaltungBeginn">'.$uhrzeit_select.'</select>
                          &nbsp;/&nbsp;
                          <select size="1" id="VeranstaltungEnde" name="VeranstaltungEnde">'.$uhrzeit_select.'</select>
                        </td>
                      </tr>  
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <div class="formlabel">
                            Bei Schüler-, Junioren- und Jugendturnieren sind die Bestimmungen des Jugendschutzes einzuhalten.
                          </div>
                        </td>
                      </tr>  
                      <tr>
                        <td>Bezeichnung des Ortes:</td>
                        <td><input type="text" name="VeranstaltungOrtsbezeichner" tabindex="20" size="55"></td>
                      </tr>
                      <tr>
                        <td>Straße:</td>
                        <td><input type="text" name="VeranstaltungStrasse" tabindex="21" size="55"></td>
                      </tr>
                      <tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input type="text" name="VeranstaltungPLZ" tabindex="22" size="5" maxlength="5">
                           <input type="text" name="VeranstaltungOrt" tabindex="23" size="43">
                         </td>
                      </tr>
                      <tr>
                        <td valign=top>Beschreibung:</td>
                        <td valign=top><textarea name="VeranstaltungBeschreibung" rows="3" cols="58" tabindex="24">Nähere Infos zur Lokalität... (Hallenkapazität, Plan, Aufbau etc.)</textarea></td>
                      </tr>
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Startklassen</legend>
                    <table>
                      <tr>
                        <td valign=top>Rock\'n\'Roll:</td>
                        <td valign=top>
                          <input id="Schüler/Junioren" name="Schüler/Junioren" class="element checkbox" type="checkbox" value="Schüler/Junioren" />&nbsp;Schüler/Junioren
                          <br /><input id="C-Klasse" name="C-Klasse" class="element checkbox" type="checkbox" value="C-Klasse" />&nbsp;C-Klasse
                          <br /><input id="B-Klasse" name="B-Klasse" class="element checkbox" type="checkbox" value="B-Klasse" />&nbsp;B-Klasse
                          <br /><input id="A-Klasse" name="A-Klasse" class="element checkbox" type="checkbox" value="A-Klasse" />&nbsp;A-Klasse
                        </td>
                      </tr>  
                      <tr>
                        <td valign=top>Boogie-Woogie:</td>
                        <td valign=top>
                          <input id="Main" name="Main" class="element checkbox" type="checkbox" value="Main" />&nbsp;Main <span class="formlabel">(A- & B-Klasse)</span>
                          <br /><input id="Senior" name="Senior" class="element checkbox" type="checkbox" value="Senior" />&nbsp;Senior <span class="formlabel">(A- & B-Klasse)</span>
                          <br /><input id="Junior" name="Junior" class="element checkbox" type="checkbox" value="Junior" />&nbsp;Junior                          
                        </td>
                      </tr>  
                      <tr>
                        <td valign=top>&nbsp;</td>
                        <td valign=top>
                          <span class="formlabel">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bei Main und Senior sind immer die A- & B-Klasse auszurichten (außer bei der Deutschen Meisterschaft).
                          </span>
                        </td>
                      </tr>  
                      <tr>
                        <td valign=top>Formationen:</td>
                        <td valign=top>
                          <input id="Master-Formationen-RR" name="Master-Formationen-RR" class="element checkbox" type="checkbox" value="Master-Formationen-RR" />&nbsp;Master-Formationen Rock\'n\'Roll
                          <br /><input id="Master-Formationen-BW" name="Master-Formationen-BW" class="element checkbox" type="checkbox" value="Master-Formationen-BW" />&nbsp;Master-Formationen Boogie-Woogie
                          <br /><input id="Jugend-Formationen-RR" name="Jugend-Formationen-RR" class="element checkbox" type="checkbox" value="Jugend-Formationen-RR" />&nbsp;Jugend-Formationen Rock\'n\'Roll
                          <br /><input id="Lady-Formationen-RR" name="Lady-Formationen-RR" class="element checkbox" type="checkbox" value="Lady-Formationen-RR" />&nbsp;Lady-Formationen Rock\'n\'Roll
                          <br /><input id="Girl-Formationen-RR" name="Girl-Formationen-RR" class="element checkbox" type="checkbox" value="Girl-Formationen-RR" />&nbsp;Girl-Formationen Rock\'n\'Roll
                          <br /><input id="Showteam-Formationen-RR" name="Showteam-Formationen-RR" class="element checkbox" type="checkbox" value="Showteam-Formationen-RR" />&nbsp;Showteam-Formationen Rock\'n\'Roll
                        </td>
                      </tr>  
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                  <legend>Wir haben bisher folgende Veranstaltungen ausgerichtet:</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="VeranstaltungenAusgerichtete" rows="3" cols="70" tabindex="25">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  <br />
                  <fieldset>
                  <legend>So stellen wir uns den Ablauf der Veranstaltung vor:</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="VeranstaltungenAblauf" rows="3" cols="70" tabindex="26">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  <br />
                  <fieldset>
                  <legend>Wünsche für Moderation, Turnierleitung, Wertungsrichter:</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Wünsche" rows="3" cols="70" tabindex="27">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="28">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  </font>
                  <!-- Ende der Beispielangaben -->
                  <p>
                    <table>
                     <tr>
                        <td valign=top>
                          <input id="AngabenRichtig" name="AngabenRichtig" class="element checkbox" type="checkbox" value="AngabenRichtig" />&nbsp;
                        </td>
                        <td valign=top>
                          Wir versichern die Richtigkeit dieser Angaben und bewerben uns für die oben
                          genannte Veranstaltung beim Präsidium des DRBV.<br />
                        </td>
                      </tr>  
                     <tr>
                        <td valign=top>
                          <input id="AGBgelesen" name="AGBgelesen" class="element checkbox" type="checkbox" value="AGBgelesen" />&nbsp;
                        </td>
                        <td valign=top>
                          Mit dieser Bewerbung werden die <a target="_blank" href="http://www.drbv.de/cms/images/PDF/Ausrichter_AGB.pdf">Ausrichter AGB des DRBV</a> anerkannt.<br /><br />
                          <input type="submit" value="Absenden" />
                          <input type="reset"  value="Zurücksetzen" />
                        </td>
                      </tr>  
                    </table>      
                  </p>
              </form>';  
    
?>