<?php
/******************************************************************************
 * Form for Startbuch ungültig
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" action="'.$g_root_path.'/adm_program/modules/forms/mail_send.php?form_id=9" method="post" autocomplete="off" accept-charset="UTF-8" onsubmit="return chkFormular()">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Startbuch/Startkarten Ungültigkeitsantrag</h3>
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
                  <legend>Folgende '.$numMembersWithoutLeiter.' Startbücher/Startkarten wurden gefunden</legend>
                    <br />
                    <i>Bitte ankreuzen, welche Startbücher/Startkarten ungültig gesetzt werden sollen.</i><br /><br />                    
                    <table border="0" width="95%" align="center">
                      <tr>
                        <th align="center">Nummer:</th>
                        <th>Name:</th>
                        <th align="center">Startmarke:</th>
                      </tr>';
                        $stb_invalid_cnt = 0;
                        $stb_cnt = 1;
                        foreach ($member_array as $memberrow) {
                        echo '<tr>';                          
                          foreach ($memberrow as $membercol => $membercont) {
                            if($membercol==4){
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
                              echo '
                              <td>'.$name.'</td>
                              <td align="center">'.$stb_valid.'</td>
                              <td>
                                <input type="hidden" name="sent" value="yes">
                                <input id="StartbuchInvalid" name="StartbuchInvalid[]" class="element checkbox" type="checkbox" value="'.$memberrow[3].' - '.$name.'" />ungültig
                              </td>                              
                              <!-- td>'.$memberrow[4].'</td -->';                              
                            }
                          }
                          $stb_cnt = $stb_cnt + 1;
                          echo '</tr>';
                        }  
                   echo'
                    </table>
                     <br /><i>'.$stb_invalid_cnt.' Startbücher/Startkarten haben aktuell keine Startmarke!</i>    
                  </fieldset>
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
                    <input type="submit" value="Absenden" />
                    <input type="reset"  value="Zurücksetzen" />
                  </p>
              </form>';  
    
?>