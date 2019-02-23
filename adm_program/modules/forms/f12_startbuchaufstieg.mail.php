<?php
/******************************************************************************
 * Form for Starbuchaufstieg Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Startbuchaufstieg: ".$_POST['Startbuch']."\n\n\n";
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";                      
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "StartbuchNr".$strDelimiter1.": ".$_POST['Startbuchnummer']."\n";  
  $strMailtextMod .= "Startklassenwechsel".$strDelimiter1.": ".$_POST['Startklassenwechsel']."\n";
  $strMailtextMod .= "Aufstiegspunkte".$strDelimiter1.": ".$_POST['Aufstiegspunkte']."\n";
  $strMailtextMod .= "Aktuelle Startklasse".$strDelimiter1.": ".$_POST['AktuelleStartklasse']."\n";  
  $strMailtextMod .= "Neue Startklasse".$strDelimiter1.": ".$_POST['Startbuch']."\n";  
  $strMailtextMod .= "Startmarke".$strDelimiter1.": ".$_POST['Startmarke']."\n";  
  $strMailtextMod .= "\n";
  if($_POST['Startbuch']=="BW-Master" ||
     $_POST['Startbuch']=="RR-Master" ||
     $_POST['Startbuch']=="Juniorenformation" ||
     $_POST['Startbuch']=="Ladyformation" ||
     $_POST['Startbuch']=="Girlformation" ||
     $_POST['Startbuch']=="Showteam"){
  $strMailtextMod .= "Name der Formation:\n\n";  
  $strMailtextMod .= "  ".$_POST['FormationsName']."\n";  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Daten Formationsverantwortlicher:\n\n";  
  $strMailtextMod .= "  ".$_POST['VornameFo']." ".$_POST['NameFo']."\n";  
  $strMailtextMod .= "  ".$_POST['StrasseHr']."\n";  
  $strMailtextMod .= "  ".$_POST['PLZFo']." ".$_POST['OrtFo']."\n";  
  $strMailtextMod .= "  ".$_POST['MailFo']."\n";  
  $strMailtextMod .= "  ".$_POST['TelFo']."\n";  
  $strMailtextMod .= "\n";
  }
  else {
  $strMailtextMod .= "Daten Herr:\n\n";  
  $strMailtextMod .= "  ".$_POST['VornameHr']." ".$_POST['NameHr']."\n";  
  $strMailtextMod .= "  ".$_POST['StrasseHr']."\n";  
  $strMailtextMod .= "  ".$_POST['PLZHr']." ".$_POST['OrtHr']."\n";  
  $strMailtextMod .= "  ".$_POST['GebHr']."\n";  
  $strMailtextMod .= "  ".$_POST['MailHr']."\n";      
  $strMailtextMod .= "  ".$_POST['TelHr']."\n";      
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Daten Dame:\n\n";  
  $strMailtextMod .= "  ".$_POST['VornameDa']." ".$_POST['NameDa']."\n";  
  $strMailtextMod .= "  ".$_POST['StrasseDa']."\n";  
  $strMailtextMod .= "  ".$_POST['PLZDa']." ".$_POST['OrtDa']."\n";  
  $strMailtextMod .= "  ".$_POST['GebDa']."\n";      
  $strMailtextMod .= "  ".$_POST['MailDa']."\n";      
  $strMailtextMod .= "  ".$_POST['TelDa']."\n";      
  $strMailtextMod .= "\n";  
  }
  $strMailtextMod .= "Bemerkungen".$strDelimiter1.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
