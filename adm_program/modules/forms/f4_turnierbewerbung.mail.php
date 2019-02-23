<?php
/******************************************************************************
 * Form for Turnierbewerbung Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Turnierbewerbung:\n\n\n";
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";                      
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Bewerbung für:\n";
  $strMailtextMod .= "----\n";  
  if($_POST['NDM-BW']){$strMailtextMod   .= "Norddeutsche Meisterschaft Boogie-Woogie\n";};
  if($_POST['NDM-BWF']){$strMailtextMod  .= "Norddeutsche Meisterschaft Formationen Boogie-Woogie\n";};  
  if($_POST['NDM-RRF']){$strMailtextMod  .= "Norddeutsche Meisterschaft Formationen Rock'n'Roll\n";};  
  if($_POST['SDM-BW']){$strMailtextMod   .= "Süddeutsche Meisterschaft Boogie-Woogie\n";};  
  if($_POST['SDM-BWF']){$strMailtextMod  .= "Süddeutsche Meisterschaft Formationen Boogie-Woogie\n";};  
  if($_POST['SDM-RRF']){$strMailtextMod  .= "Süddeutsche Meisterschaft Formationen Rock'n'Roll\n";};  
  if($_POST['DC-BW']){$strMailtextMod    .= "Deutschland-Cup Boogie-Woogie\n";};  
  if($_POST['DC-BWF']){$strMailtextMod   .= "Deutschland-Cup Formationen Boogie-Woogie\n";};  
  if($_POST['DC-RRF']){$strMailtextMod   .= "Deutschland-Cup Formationen Rock'n'Roll\n";};
  if($_POST['DC-RR']){$strMailtextMod    .= "Deutschland-Cup S/J/C/B/A Rock'n'Roll\n";};  
  if($_POST['GPvD-BW']){$strMailtextMod  .= "Großer Preis von Deutschland Boogie-Woogie\n";};  
  if($_POST['GPvD-BWF']){$strMailtextMod .= "Großer Preis von Deutschland Formationen Boogie-Woogie\n";};  
  if($_POST['GPvD-RRF']){$strMailtextMod .= "Großer Preis von Deutschland Formationen Rock'n'Roll\n";};  
  if($_POST['GPvD-RR']){$strMailtextMod  .= "Großer Preis von Deutschland S/J/C/B/A Rock'n'Roll\n";};  
  if($_POST['DM-BW']){$strMailtextMod    .= "Deutsche Meisterschaft Boogie-Woogie\n";};  
  if($_POST['DM-BWF']){$strMailtextMod   .= "Deutsche Meisterschaft Formationen Boogie-Woogie\n";};  
  if($_POST['DM-RRF']){$strMailtextMod   .= "Deutsche Meisterschaft Formationen Rock'n'Roll\n";};  
  if($_POST['DM-RR']){$strMailtextMod    .= "Deutsche Meisterschaft S/J/C/B/A Rock'n'Roll\n";};  
  if($_POST['DM-CLUB']){$strMailtextMod  .= "Deutsche Meisterschaft der Clubs\n";};  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Angaben zum Veranstalter:\n";  
  $strMailtextMod .= "----\n";  
  $strMailtextMod .= "Name".$strDelimiter1.": ".$_POST['VeranstalterName']."\n";  
  $strMailtextMod .= "Straße".$strDelimiter1.": ".$_POST['VeranstalterStrasse']."\n";  
  $strMailtextMod .= "PLZ/Ort".$strDelimiter1.": ".$_POST['VeranstalterPLZ']." ".$_POST['VeranstalterOrt']."\n";  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Angaben zum Ansprechpartner:\n";  
  $strMailtextMod .= "----\n";  
  $strMailtextMod .= "Name".$strDelimiter1.": ".$_POST['AnsprechpartnerName']."\n";  
  $strMailtextMod .= "Straße".$strDelimiter1.": ".$_POST['AnsprechpartnerStrasse']."\n";  
  $strMailtextMod .= "PLZ/Ort".$strDelimiter1.": ".$_POST['AnsprechpartnerPLZ']." ".$_POST['AnsprechpartnerOrt']."\n";  
  $strMailtextMod .= "Telefon".$strDelimiter2.": ".$_POST['AnsprechpartnerTelefon']."\n";  
  $strMailtextMod .= "Handy".$strDelimiter2.": ".$_POST['AnsprechpartnerHandy']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter2.": ".$_POST['AnsprechpartnerMail']."\n";                      
  $strMailtextMod .= "Webseite".$strDelimiter2.": ".$_POST['AnsprechpartnerInternet']."\n";                      
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Angaben zur Veranstaltung:\n";  
  $strMailtextMod .= "----\n";  
  $strMailtextMod .= "Datum".$strDelimiter1.": ".$_POST['VeranstaltungDatum']."\n";  
  $strMailtextMod .= "Beginn".$strDelimiter1.": ".$_POST['VeranstaltungBeginn']."\n";  
  $strMailtextMod .= "Ende".$strDelimiter1.": ".$_POST['VeranstaltungEnde']."\n";  
  $strMailtextMod .= "Bezeichnung des Ortes".$strDelimiter1.": ".$_POST['VeranstaltungOrtsbezeichner']."\n";  
  $strMailtextMod .= "Straße".$strDelimiter1.": ".$_POST['VeranstaltungStrasse']."\n";  
  $strMailtextMod .= "PLZ/Ort".$strDelimiter1.": ".$_POST['VeranstaltungPLZ']." ".$_POST['VeranstaltungOrt']."\n";  
  $strMailtextMod .= "Beschreibung".$strDelimiter1.": ".$_POST['VeranstaltungBeschreibung']."\n";  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Startklassen:\n";  
  $strMailtextMod .= "----\n";  
  if($_POST['Schüler/Junioren']){$strMailtextMod .= $_POST['Schüler/Junioren']."\n";}; 
  if($_POST['C-Klasse']){$strMailtextMod .= $_POST['C-Klasse']."\n";};  
  if($_POST['B-Klasse']){$strMailtextMod .= $_POST['B-Klasse']."\n";};  
  if($_POST['A-Klasse']){$strMailtextMod .= $_POST['A-Klasse']."\n";};  
  if($_POST['Main']){$strMailtextMod .= $_POST['Main']."\n";};  
  if($_POST['Senior']){$strMailtextMod .= $_POST['Senior']."\n";};  
  if($_POST['Junior']){$strMailtextMod .= $_POST['Junior']."\n";};  
  if($_POST['Master-Formationen-RR']){$strMailtextMod .= $_POST['Master-Formationen-RR']."\n";};  
  if($_POST['Master-Formationen-BW']){$strMailtextMod .= $_POST['Master-Formationen-BW']."\n";};  
  if($_POST['Jugend-Formationen-RR']){$strMailtextMod .= $_POST['Jugend-Formationen-RR']."\n";};  
  if($_POST['Lady-Formationen-RR']){$strMailtextMod .= $_POST['Lady-Formationen-RR']."\n";};  
  if($_POST['Girl-Formationen-RR']){$strMailtextMod .= $_POST['Girl-Formationen-RR']."\n";};  
  if($_POST['Showteam-Formationen-RR']){$strMailtextMod .= $_POST['Showteam-Formationen-RR']."\n";};  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Sonstiges:\n";  
  $strMailtextMod .= "----\n";  
  $strMailtextMod .= "Ausgerichtete Veranstaltungen".$strDelimiter1.": ".$_POST['VeranstaltungenAusgerichtete']."\n";
  $strMailtextMod .= "Geplanter Ablauf der Veranstaltung".$strDelimiter1.": ".$_POST['VeranstaltungenAblauf']."\n";
  $strMailtextMod .= "Wünsche zu TL/WR/Moderation".$strDelimiter1.": ".$_POST['Wünsche']."\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Richtigkeit der Angaben: ";
  if($_POST['AngabenRichtig']){$strMailtextMod .= "bestätigt!";}else{$strMailtextMod .= "nicht bestätigt!";}                    
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Ausrichter AGB: ";
  if($_POST['AGBgelesen']){$strMailtextMod .= "anerkannt!";}else{$strMailtextMod .= "nicht anerkannt!";}                    
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Bemerkungen".$strDelimiter2.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
