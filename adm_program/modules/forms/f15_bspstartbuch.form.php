<?php
/******************************************************************************
 * Form for Vereinsmail ändern
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send2webbi.php?form_id=15" method="post" autocomplete="off" accept-charset="UTF-8" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Breitensportstartbuch Bestellung (Digital, für Präambelturniere)</h3>
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
                    <legend>Bitte Anzahl der digitalen Startbücher und Startklassen auswählen</legend>
                    <table>
                      <tr>
                        <td>Schüler-Klasse:</td>
                        <td>
                          <select size="1" id="BSP-S" name="BSP-S">
                            <option>-</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option> 
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>Junioren-Klasse:</td>
                        <td>
                          <select size="1" id="BSP-J" name="BSP-J">
                            <option>-</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option> 
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td>C-Klasse:</td>
                        <td>
                          <select size="1" id="BSP-C" name="BSP-C">
                            <option>-</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option> 
                          </select>
                        </td>
                      </tr>
                    </table>
                    <p style="text-align:justify;">obige Anzahl an digitalen Startbüchern für die entsprechenden Startklassen, werden dem
                       Verein zugeordnet. Diese Startbücher sind nicht personalisiert und werden für die Unterstützung von Sportturnieren
                       (Präambelturnieren) kostenfrei zur Verfügung gestellt.</p>  
                  </fieldset>
                  <br />
                  <fieldset>
                    <legend>Bemerkungen</legend>
                    <table>
                      <tr valign="top">
                        <td>
                          <textarea name="Bemerkungen" rows="3" cols="40" tabindex="5">?</textarea>
                        </td>
                      </tr>
                    </table>                                                                
                  </fieldset>
                  </font>
                  <!-- Ende der Beispielangaben -->
                  <p>
                    <input type="submit" value="Absenden" />
                    <input type="reset"  value="Zurücksetzen" />
                  </p>
              </form>';  
    
?>