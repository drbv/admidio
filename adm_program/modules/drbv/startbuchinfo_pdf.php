<?php
/******************************************************************************
 * Alle Startkarten eines Vereins anzeigen, mit Infos
 *
 * Copyright    : (c) 2016 DRBV Webteam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Parameters:
 *
 * user_id: zeigt das Profil der uebergebenen user_id an
 *          (wird keine user_id uebergeben, dann Profil des eingeloggten Users anzeigen)
 *
 *****************************************************************************/

require_once('../../system/common.php');
require_once('../../system/drbv_funktionen.php');
require_once '../../../../vendor/autoload.php';

// Initialize and check the parameters
$getStbNr  = admFuncVariableIsValid($_GET, 'stbnr', 'numeric', 0);
$getMode   = admFuncVariableIsValid($_GET, 'mode');
  
$pdf_input = $_SESSION['form_data_arr'];
//print_r($pdf_input[$getStbNr]);echo" ::pdf_input<br>";
  
$pdf_output = getEinlassFormPDF($pdf_input[$getStbNr]);    
//print_r($pdf_output);echo" ::pdf_output<br>";
//echo $pdf_output;
  
$dateiname = 'Einlassformular_'.$pdf_input[$getStbNr]['TEAMNAME'].'.pdf';            
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($pdf_output);
$mpdf->Output($dateiname,'I');

?>
