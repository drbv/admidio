<?php
/******************************************************************************
 * Form for Vereinsmail Mail
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
  $strMailtextMod = "";
  $strMailtextMod .= "Formular - Änderung der Vereinsmail:\n\n\n";
  $strMailtextMod .= "Vereinsnummer".$strDelimiter2.": ".$_POST['Vereinsnummer']."\n";
  $strMailtextMod .= "Vereinsname".$strDelimiter2.": ".$_POST['Verein']."\n\n";  
  $strMailtextMod .= "Bundesland".$strDelimiter2.": ".$_POST['Bundesland']."\n";  
  $strMailtextMod .= "Versender".$strDelimiter2.": ".$_POST['Versender']."\n";              
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['EMail']."\n";
  $strMailtextMod .= "Tel/Handy:".$strDelimiter2.": ".$_POST['TelHandy']."\n";  
  $strMailtextMod .= "\n";  
  $strMailtextMod .= "EMail".$strDelimiter3.": ".$_POST['Vereinsmail']."\n";                      
  $strMailtextMod .= "Neue Mail".$strDelimiter2.": ".$_POST['VereinsmailNeu']."\n";              
  $strMailtextMod .= "\n";
  $strMailtextMod .= "Bemerkungen".$strDelimiter2.": ".$_POST['Bemerkungen']."\n";
  $strMailtextMod .= "\n";
}
