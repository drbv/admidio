<?php
/******************************************************************************
 * Form for Turnierreservierung Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Turnierreservierung:\n\n\n";
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";                      
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
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
  $strMailtextMod .= "Zu welchem Termin soll die Veranstaltung reserviert werden:\n";  
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
  $strMailtextMod .= "Wettbewerbsarten:\n";
  $strMailtextMod .= "----\n";  
  if($_POST['Einzelwettbewerb']){$strMailtextMod .= $_POST['Einzelwettbewerb']."\n";};  
  if($_POST['Mannschaftswettbewerb']){$strMailtextMod .= $_POST['Mannschaftswettbewerb']."\n";};  
  if($_POST['Formationswettbewerb']){$strMailtextMod .= $_POST['Formationswettbewerb']."\n";};  
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
  $strMailtextMod .= "Bemerkungen".$strDelimiter2.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
