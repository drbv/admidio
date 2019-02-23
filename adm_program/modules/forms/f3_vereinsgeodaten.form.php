<?php
/******************************************************************************
 * Form for Vereinsgeodaten ändern
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=3" method="post" autocomplete="off" accept-charset="UTF-8" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Änderung der Geodaten</h3>
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
                    <legend>Angaben zur Geodatenänderung</legend>
                    <table>
                      <tr>
                        <td>Bestehende Geodaten:</td>
                        <td><input type="text" name="Geodaten" value="'.$user->getValue('GEODATEN').'" size="50" readonly="readonly"></td>
                      </tr>
                      <tr>
                        <td>Neue Geodaten:</td>
                        <td><input required type="text" name="GeodatenNeu" tabindex="4" size="50"><span class="mandatoryFieldMarker" title="Pflichtfeld">*</span></td>
                      </tr>
                    </table>    
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