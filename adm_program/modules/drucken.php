<?php

$fn = 'druckfile.txt';
$fp = fopen($fn,"r");

while(($Daten = fgets($fp, 100)) !== FALSE)
      {
      $Daten = trim(strip_tags($Daten, ENT_QUOTES));
      $lauf_nr = $lauf_nr + 1;
      if(strlen($Daten) >'0')
       {
        if(substr($Daten,0,8) == 'Vorstand')
          {
           $flag = 1;
           continue;
          }
        if($flag == 1)
          {
           unset($flag);
           continue;
          }
        if(substr($Daten,5,1) == '.' && substr($Daten,12,1) == '.')
          {
           continue;
          }
        if(substr($Daten,5,1) == '.' && $lauf_nr == 16)
          {
           continue;
          }
        if(substr($Daten,0,15) == 'Startbuchnummer')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,5) == 'Zugeh')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,11) == 'Startklasse')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,8) == 'Teamname')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,10) == 'Startmarke')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,8) == 'Rocktime')
          {
           continue;
          }
        if(substr($Daten,0,8) == 'alt="on"')
          {
           $html .= 'Ja <br>';
           continue;
          }
        if(substr($Daten,0,12) == 'Qualifiziert')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,5) == 'f" />')
          {
           $html .= 'Nein <br>';
           continue;
          }
        if(substr($Daten,0,5) == 'Nachr')
          {
           $html .= $Daten .' ';
           continue;
          }
          
        if(substr($Daten,0,16) == 'Wertungsfreigabe')
          {
           $flag = 1;
           continue;
          }
        if(substr($Daten,0,12) == 'Junior Start')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,10) == 'Main Start')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,12) == 'Senior Start')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,14) == 'Startmeldung f')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,4) == 'Herr')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,4) == 'Dame')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,9) == 'Akrobatik')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,10) == 'Musiktitel')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,5) == 'Sorry')
          {
           continue;
          }
        if(substr($Daten,-20) == 'type="audio/mpeg" />')
          {
           continue;
          }
        if(substr($Daten,-25) == 'Musik-ID eingetragen!" />')
          {
           continue;
          }
        if(substr($Daten,-27) == 'keine Musik abgespielt, ist')
          {
           continue;
          }
        if(substr($Daten,0,14) == 'Formation RFID')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,13) == 'Anzahl Aktive')
          {
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,14) == 'Formation RFID')
          {
           $html .= $Daten .' ';
           continue;
          }
          
        if(substr($Daten,-10) == 'E01:&nbsp;' && $flag_2 == 0)
         {
          $flag_2 = 1;
          $html .= '******************** Ersatztänzer ********************<br> ';
         }
          
        if(substr($Daten,0,4) == 'Name')
          {
           $flag_1 = 1;
           $html .= 'Name: ';
           continue;
          }
        if($flag_1 == 1)
          {
           $flag_1 = $flag_1 + 1;
           $html .= $Daten .' ';
           continue;
          }
        if(substr($Daten,0,12) == 'Geburtsdatum')
          {
           $html .= 'Geburtsdatum: ';
           continue;
          }
        if($Daten == '>')
          {
           continue;
          }
           $html .= $Daten . '<br>';
       }
      }
fclose($fn);

$kopf_html='<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><title>Datenblatt</title></head><body>';
$logo = '<img src="./DRBV_DTV_Logo.png" width="300" height="66"><br>';
$end_html ='</body></html>';

$html = $kopf_html . $logo . $html . $end_html;
//echo $html;

require_once '../../../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf(['tempDir' => $_SERVER["DOCUMENT_ROOT"] . '/tmp/mpdf']);
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output();

?>