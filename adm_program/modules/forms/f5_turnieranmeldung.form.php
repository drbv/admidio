<?php
/******************************************************************************
 * Form for Turnieranmeldung
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/
  
echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=5" method="post" autocomplete="off" accept-charset="UTF-8" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Turnier Anmeldung</h3>
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
                    <legend>Angaben zur Veranstaltung</legend>
                    <table>
                      <tr>
                        <td>Name:</td>
                        <td><input required type="text" name="VeranstaltungName" tabindex="16" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
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
                        <td><input required type="text" name="VeranstaltungOrtsbezeichner" tabindex="19" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                        <td>Straße:</td>
                        <td><input required type="text" name="VeranstaltungStrasse" tabindex="20" size="55"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                      <tr>
                         <td>PLZ / Ort:</td>
                         <td>
                           <input required type="text" name="VeranstaltungPLZ" tabindex="21" size="5" maxlength="5"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                           <input required type="text" name="VeranstaltungOrt" tabindex="22" size="41"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span>
                         </td>
                      </tr>
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Wettbewerbsarten</legend>
                    <table>
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <input id="Meisterschaft" name="Meisterschaft" class="element checkbox" type="checkbox" value="Meisterschaft" />&nbsp;Meisterschaft
                          &nbsp;&nbsp;&nbsp;<input id="OffenesTurnier" name="OffenesTurnier" class="element checkbox" type="checkbox" value="Offenes Turnier" />&nbsp;Offenes Turnier
                          &nbsp;&nbsp;&nbsp;<input id="Einladungsturnier" name="Einladungsturnier" class="element checkbox" type="checkbox" value="Einladungsturnier" />&nbsp;Einladungsturnier
                          <br />
                          <input id="Einzelwettbewerb" name="Einzelwettbewerb" class="element checkbox" type="checkbox" value="Einzelwettbewerb" />&nbsp;Einzelwettbewerb
                          &nbsp;&nbsp;&nbsp;<input id="Mannschaftswettbewerb" name="Mannschaftswettbewerb" class="element checkbox" type="checkbox" value="Mannschaftswettbewerb" />&nbsp;Mannschaftswettbewerb
                          &nbsp;&nbsp;&nbsp;<input id="Formationswettbewerb" name="Formationswettbewerb" class="element checkbox" type="checkbox" value="Formationswettbewerb" />&nbsp;Formationswettbewerb
                          <br />
                          <input id="national" name="national" class="element checkbox" type="checkbox" value="national" />&nbsp;national
                          &nbsp;&nbsp;&nbsp;<input id="international" name="international" class="element checkbox" type="checkbox" value="international" />&nbsp;international
                        </td>
                      </tr>  
                    </table>    
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Startklassen</legend>
                    <table>
                      <tr>
                        <td valign=top>Rock\'n\'Roll:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                        <td valign=top>
                          <input id="Schüler/Junioren" name="Schüler/Junioren" class="element checkbox" type="checkbox" value="Schüler/Junioren" />&nbsp;Schüler/Junioren
                          <br /><input id="C-Klasse" name="C-Klasse" class="element checkbox" type="checkbox" value="C-Klasse" />&nbsp;C-Klasse
                          <br /><input id="B-Klasse" name="B-Klasse" class="element checkbox" type="checkbox" value="B-Klasse" />&nbsp;B-Klasse
                          <br /><input id="A-Klasse" name="A-Klasse" class="element checkbox" type="checkbox" value="A-Klasse" />&nbsp;A-Klasse
                        </td>
                      </tr>  
                      <tr>
                        <td valign=top>Boogie-Woogie:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                        <td valign=top>Formationen:<span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
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
                    <legend>Sonstiges</legend>
                    <table>
                     <tr>
                       <td>Turnierleiter:&nbsp;</td>
                       <td>
                         <select size="1" id="Turnierleiter" name="Turnierleiter">
                         <option value="">- '.$gL10n->get('SYS_PLEASE_CHOOSE').' -</option>';
                         foreach( $tleiter as $key => $value)
                         {
                           echo '<option value="'.$value.'" ';
                           echo '>'.$value.'</option>';
                         }
                         echo '</select>
                       </td>
                     </tr>  
                     <tr>
                        <td valign=top align="right">
                          <input id="KeineVideoGebühr" name="KeineVideoGebühr" class="element checkbox" type="checkbox" value="KeineVideoGebühr" />&nbsp;
                        </td>
                        <td valign=top>
                          Wir erheben <b>keine</b> Video-Aufzeichnunsgebühr und beanspruchen nicht das Ausschließlichkeitsrecht (s. 1.2.2 Finanzordnung).
                        </td>
                      </tr>  
                    </table>    
                  </fieldset>
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="24">?</textarea>
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
                          Wir versichern die Richtigkeit dieser Angaben und werden das Turnier nach den Bestimmungen
                          der Turnier- und Sportordnung (TSO) des DRBV durchführen.<br /><br />
                          <input type="submit" value="Absenden" />
                          <input type="reset"  value="Zurücksetzen" />
                        </td>
                      </tr>  
                    </table>      
                  </p>
              </form>';  
    
?>