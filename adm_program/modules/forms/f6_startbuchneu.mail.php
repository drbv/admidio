<?php
/******************************************************************************
 * Form for Starbuchneubestellung Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Startbuchneubestellung: ".$_POST['Startbuch']."\n\n\n";
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Expressbearbeitung".$strDelimiter1.": ";
  if($_POST['Expressbearbeitung']){$strMailtextMod .= "JA";}else{$strMailtextMod .= "NEIN";}    
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";                      
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Startklasse".$strDelimiter1.": ".$_POST['Startbuch']."\n";  
  $strMailtextMod .= "Startmarke".$strDelimiter1.": ".$_POST['Startmarke']."\n";  
  $strMailtextMod .= "rocktime".$strDelimiter1.": ".$_POST['rocktime']."\n";  
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
  if($_POST['NameHr'])    $strMailtextMod .= "Daten Herr:\n\n";  
  if($_POST['NameHr'])    $strMailtextMod .= "  ".$_POST['VornameHr']." ".$_POST['NameHr']."\n";  
  if($_POST['StrasseHr']) $strMailtextMod .= "  ".$_POST['StrasseHr']."\n";  
  if($_POST['PLZHr'])     $strMailtextMod .= "  ".$_POST['PLZHr']." ".$_POST['OrtHr']."\n";  
  if($_POST['GebHr'])     $strMailtextMod .= "  ".$_POST['GebHr']."\n";  
  if($_POST['NationHr'])  $strMailtextMod .= "  ".$_POST['NationHr']."\n";      
  if($_POST['SprtlkHr'])  $strMailtextMod .= "  ".$_POST['SprtlkHr']." - Datum der letzten Sporttauglichkeit\n";  
  if($_POST['MailHr'])    $strMailtextMod .= "  ".$_POST['MailHr']."\n";      
  if($_POST['TelHr'])     $strMailtextMod .= "  ".$_POST['TelHr']."\n";      
  $strMailtextMod .= "\n";  
  if($_POST['NameDa'])    $strMailtextMod .= "Daten Dame:\n\n";  
  if($_POST['NameDa'])    $strMailtextMod .= "  ".$_POST['VornameDa']." ".$_POST['NameDa']."\n";  
  if($_POST['StrasseDa']) $strMailtextMod .= "  ".$_POST['StrasseDa']."\n";  
  if($_POST['PLZDa'])     $strMailtextMod .= "  ".$_POST['PLZDa']." ".$_POST['OrtDa']."\n";  
  if($_POST['GebDa'])     $strMailtextMod .= "  ".$_POST['GebDa']."\n";      
  if($_POST['NationDa'])  $strMailtextMod .= "  ".$_POST['NationDa']."\n";      
  if($_POST['SprtlkDa'])  $strMailtextMod .= "  ".$_POST['SprtlkDa']." - Datum der letzten Sporttauglichkeit\n";  
  if($_POST['MailDa'])    $strMailtextMod .= "  ".$_POST['MailDa']."\n";      
  if($_POST['TelDa'])     $strMailtextMod .= "  ".$_POST['TelDa']."\n";      
  $strMailtextMod .= "\n";  
  }
  $strMailtextMod .= "Bemerkungen".$strDelimiter2.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
