<?php
/******************************************************************************
 * Form for Sporttauglichkeit Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Sporttauglichkeitsnachweis:\n\n\n";
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";                      
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Startbuch Nummer".$strDelimiter1.": ".$_POST['Stbuchnr']."\n";  
  $strMailtextMod .= "Startmarke gültig".$strDelimiter1.": ".$_POST['Stbuchvalid']."\n";  
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Daten Herr:\n\n";  
  $strMailtextMod .= "  ".$_POST['VornameHr']." ".$_POST['NameHr']."\n";  
  $strMailtextMod .= "  ".$_POST['SprtlkAltHr']." - Datum der letzten Sporttauglichkeit\n";  
  $strMailtextMod .= "  ".$_POST['SprtlkNeuHr']." - Datum der neuen Sporttauglichkeit\n";      
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "Daten Dame:\n\n";  
  $strMailtextMod .= "  ".$_POST['VornameDa']." ".$_POST['NameDa']."\n";  
  $strMailtextMod .= "  ".$_POST['SprtlkAltDa']." - Datum der letzten Sporttauglichkeit\n";  
  $strMailtextMod .= "  ".$_POST['SprtlkNeuDa']." - Datum der neuen Sporttauglichkeit\n";      
  $strMailtextMod .= "\n\n";
  $strMailtextMod .= "Bemerkungen".$strDelimiter1.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
