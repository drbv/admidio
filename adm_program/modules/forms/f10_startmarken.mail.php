<?php
/******************************************************************************
 * Form for Startmarken Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Startmarkenbestellung:\n\n\n";
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
  $strMailtextMod .= "Für folgende Startbücher/Startkarten werden Startmarken für die laufende Saison bestellt:\n\n";
  $strMailtextMod .= $StartbuchLaufString;
  $strMailtextMod .= "\n\n";
  $strMailtextMod .= "Für folgende Startbücher/Startkarten werden Startmarken für die folgende Saison bestellt:\n\n";
  $strMailtextMod .= $StartbuchFolgString;  
  $strMailtextMod .= "\n\n";
  $strMailtextMod .= "Bemerkungen".$strDelimiter1.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
