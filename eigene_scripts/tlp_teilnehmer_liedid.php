<?php
//error_reporting (E_ALL);
        
date_default_timezone_set("Europe/Berlin");
echo date("H:i:s"). '<br>';

require("./intern/dboeffnen.inc.php");
require_once("./intern/bereinigen.php");

function ersetzen($start_klasse, $akrobatik, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp)
   {
    if($start_klasse == "RR_C")
      {
       foreach ($liste_C_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       }   
        
    if($start_klasse == "RR_J")
      {
       foreach ($liste_J_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       }        

    if($start_klasse == "RR_S")
      {
       foreach ($liste_S_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       }    
        
    if($start_klasse == "RR_B")
      {
       foreach ($liste_B_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       }       

    if($start_klasse == "RR_A")
      {
       foreach ($liste_A_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       }    

    if($start_klasse == "F_RR_M")
      {
       foreach ($liste_F_komp as $key => $value)
       {       
         $akro_array =  explode(":", $value);
        if(trim($akro_array[0]) == $akrobatik)
           {
            $wert = trim($akro_array[1]);
            $akro = trim($akro_array[3]);
            break;
           }
        }      
       } 
       
    return array($wert, $akro);
    
     }
     
// Akrolisten einlesen

$sqlab = "SELECT usf_name, usf_value_list FROM adm_user_fields WHERE usf_id > 120 AND usf_id < 143";

// echo"$sqlab<br>";
$akrolisten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($akrolisten))
 {
  $listenname = $temp[0];
  $listenwert = $temp[1];

//  echo"$listenname<br/>$listenwert<br/><br/>";
  
  if($listenname == "Akrobatikliste-C-Int")
    {
     $liste_C_komp = explode("\n", $listenwert);
//     print_r($liste_C_komp);echo"<br>";
    }
  
  if($listenname == "Akrobatikliste-J")
    {
     $liste_J_komp = explode("\n", $listenwert);
//     print_r($liste_J_komp);echo"<br>";     
    }
  
  if($listenname == "Akrobatikliste-S")
    {
     $liste_S_komp = explode("\n", $listenwert);
//     print_r($liste_S_komp);echo"<br>";     
    }
  
  if($listenname == "Akrobatikliste-B")
    {
     $liste_B_komp = explode("\n", $listenwert);
 //    print_r($liste_B_komp);echo"<br>";     
    }
  
  if($listenname == "Akrobatikliste-A")
    {
     $liste_A_komp = explode("\n", $listenwert);
//     print_r($liste_A_komp);echo"<br>";     
    }
  
  if($listenname == "Akrobatikliste-F")
    {
     $liste_F_komp = explode("\n", $listenwert);
//     print_r($liste_F_komp);echo"<br>";     
    }
//    echo"<br/>";                    
 }

$separator   = ";";
$valueQuotes = '"';
$startnummer = 500;
$no_akro = "Akro n gem";
$org = "1/2";
$neu = "halb ";
$org_1 = "/";
$neu_1 = "-";
$akro_null = "0,01";

// Kopzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Startkl'. $valueQuotes.  
         $separator. $valueQuotes. 'Startnr'. $valueQuotes.  
         $separator. $valueQuotes. 'Da_Vorname'. $valueQuotes.  
         $separator. $valueQuotes. 'Da_Nachname'. $valueQuotes.  
         $separator. $valueQuotes. 'He_Vorname'. $valueQuotes.  
         $separator. $valueQuotes. 'He_Nachname'. $valueQuotes.  
         $separator. $valueQuotes. 'Verein_nr'. $valueQuotes.  
         $separator. $valueQuotes. 'Verein_Name'. $valueQuotes.  
         $separator. $valueQuotes. 'Name_Team'. $valueQuotes.  
         $separator. $valueQuotes. 'Startbuch'. $valueQuotes.  
         $separator. $valueQuotes. 'Boogie_Startkarte_H'. $valueQuotes.  
         $separator. $valueQuotes. 'Boogie_Startkarte_D'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro1_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert1_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro2_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert2_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro3_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert3_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro4_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert4_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro5_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert5_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro6_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert6_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro7_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert7_VR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro8_VR'. $valueQuotes.
         $separator. $valueQuotes. 'Wert8_VR'. $valueQuotes.
                  
         $separator. $valueQuotes. 'E_Akro1_VR'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Wert1_VR'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Akro2_VR'. $valueQuotes. 
         $separator. $valueQuotes. 'E_Wert2_VR'. $valueQuotes.                    
  
         $separator. $valueQuotes. 'Akro1_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert1_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro2_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert2_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro3_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert3_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro4_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert4_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro5_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert5_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro6_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert6_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro7_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert7_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro8_ZR'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert8_ZR'. $valueQuotes.
                  
         $separator. $valueQuotes. 'E_Akro1_ZR'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Wert1_ZR'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Akro2_ZR'. $valueQuotes. 
         $separator. $valueQuotes. 'E_Wert2_ZR'. $valueQuotes.                    
           
         $separator. $valueQuotes. 'Akro1_ER'. $valueQuotes.
         $separator. $valueQuotes. 'Wert1_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro2_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert2_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro3_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert3_ER'. $valueQuotes.
         $separator. $valueQuotes. 'Akro4_ER'. $valueQuotes.           
         $separator. $valueQuotes. 'Wert4_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro5_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert5_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro6_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert6_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro7_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert7_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Akro8_ER'. $valueQuotes.  
         $separator. $valueQuotes. 'Wert8_ER'. $valueQuotes.
                  
         $separator. $valueQuotes. 'E_Akro1_ER'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Wert1_ER'. $valueQuotes.         
         $separator. $valueQuotes. 'E_Akro2_ER'. $valueQuotes. 
         $separator. $valueQuotes. 'E_Wert2_ER'. $valueQuotes.                             
        
         $separator. $valueQuotes. 'Musik_FT'. $valueQuotes.
         $separator. $valueQuotes. 'Musik_Akro'. $valueQuotes.
         
         $separator. $valueQuotes. 'Musik_Stell'. $valueQuotes.         
         $separator. $valueQuotes. 'Musik_Form'. $valueQuotes.
         $separator. $valueQuotes. 'Musik_Sieg'. $valueQuotes.
         
         $separator. $valueQuotes. 'Cup_Serie'. $valueQuotes.                                             
         $separator. $valueQuotes. 'Anz_Taenzer'. $valueQuotes.
         $separator. $valueQuotes. 'Turniernr'. $valueQuotes;    
$str_csv = $str_csv. "\r\n";


// Datum festlegen
$datum_unix = time() + 777600 ; // 9 Tage
$datum = date("Y-m-d", $datum_unix);
$jetzt = time();
$null_vier = strtotime($datum) - 777300;
$null_sechs = strtotime($datum) - 777180;

// $_POST["datum"] = "2015-03-15";

// Datum manuell festlegen
echo' <form method="POST" action="' . $_SERVER["PHP_SELF"] . '"> ';
echo'<h3>Bitte gib das Datum im Format: JJJJ-MM-DD ein!</h3><br>';
echo'<input name = "datum" size="10" maxlength="10">';
echo'<input type="submit" name="senden" value="Absenden"/>';
echo"</form>";

if($_POST["datum"])
   {
    $man_datum = strtotime($_POST["datum"]);
// Um jederzeit die Startliste schreiben zu können, die nachfolgende Zeile auskommentieren!    
//   if($man_datum > $datum_unix)
       $datum = $_POST["datum"];
// Um jederzeit die Startliste schreiben zu können, die nachfolgende Zeile auskommentieren!
//    else 
       echo"<h3><font color = ff0000>Die Datei darf nicht neu erstellt werden, weil möglicherweise Tanzpaare ihre Akrobatiken in der Zwischenzeit geändert haben!<font color = 000000></h3>";  
   }

echo $datum ."<br>";

// Um jederzeit die Startliste schreiben zu können, die nachfolgende Zeile auskommentieren!
//if(($jetzt > $null_vier && $jetzt < $null_sechs) || $man_datum > $datum_unix)
//{
// Turnier finden
$sqlab = "SELECT dat_cat_id, dat_rol_id, dat_begin, dat_turniernummer FROM adm_dates";

// echo"$sqlab<br>";
$turniere = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turniere))
 {
  $kategorie = $temp[0];  
  $turnier_id = $temp[1];
  $beginn = $temp[2];
  $t_num = $temp[3];    
  unset($str_body);
  
// Daten schreiben      
  if(substr($beginn,0,10) == $datum && $kategorie == 31)
    {
//     echo"$beginn, $turnier_id, $t_num<br>";
     
   $sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = $turnier_id AND mem_leader = 0 AND mem_end > '" .  $datum . "' ";
//   echo"$sqlab<br>";
   $teilnehmer = mysqli_query($db, $sqlab);
   
   while($t_paar = mysqli_fetch_array($teilnehmer))
     {
             
       $teilnehmer_id = $t_paar[0];
//     echo"<br>Paar: $teilnehmer_id<br>";

// unset alle Variablen
     
      unset($vereinsname);
      unset($vereinsnummer);
      unset($team_name);
      unset($nn_herr);
      unset($vn_herr);
      unset($nn_dame);
      unset($vn_dame);
      unset($startklasse);
      unset($startbuch_nr);
      unset($startbuch_nr_h);
      unset($startbuch_nr_d);
      unset($akro_vr);
      unset($akro_zr);
      unset($akro_er);
      unset($akro1_vr);
      unset($akro2_vr);
      unset($akro3_vr);
      unset($akro4_vr);
      unset($akro5_vr);
      unset($akro6_vr);
      unset($akro7_vr);
      unset($akro8_vr);
      unset($e_akro1_vr);
      unset($e_akro2_vr);      
      unset($wert1_vr);
      unset($wert2_vr);
      unset($wert3_vr);
      unset($wert4_vr);
      unset($wert5_vr);
      unset($wert6_vr);
      unset($wert7_vr);
      unset($wert8_vr);
      unset($e_wert1_vr);
      unset($e_wert2_vr);      
      unset($akro1_zr);
      unset($akro2_zr);
      unset($akro3_zr);
      unset($akro4_zr);
      unset($akro5_zr);
      unset($akro6_zr);
      unset($akro7_zr);
      unset($akro8_zr);
      unset($e_akro1_zr);
      unset($e_akro2_zr);      
      unset($wert1_zr);
      unset($wert2_zr);
      unset($wert3_zr);
      unset($wert4_zr);
      unset($wert5_zr);
      unset($wert6_zr);
      unset($wert7_zr);
      unset($wert8_zr);
      unset($e_wert1_zr);
      unset($e_wert2_zr);       
      unset($akro1_er);
      unset($akro2_er);
      unset($akro3_er);
      unset($akro4_er);
      unset($akro5_er);
      unset($akro6_er);
      unset($akro7_er);
      unset($akro8_er);
      unset($e_akro1_er);
      unset($e_akro2_er);      
      unset($wert1_er);
      unset($wert2_er);
      unset($wert3_er);
      unset($wert4_er);
      unset($wert5_er);
      unset($wert6_er);
      unset($wert7_er);
      unset($wert8_er);
      unset($e_wert1_er);
      unset($e_wert2_er);       
      unset($gueltig);
      unset($bw_jun);  
      unset($bw_main);
      unset($bw_senior);
      unset($bw_main_senior);
      unset($vn_dame_s);
      unset($nn_dame_s);
      unset($startbuch_nr_h);      
      unset($startbuch_nr_d);
      unset($startbuch_nr_d_s);
      unset($startklasse_dame);      
      unset($startklasse_dame_s);
      unset($startklasse_j);
      unset($startklasse_m);
      unset($startklasse_s);
      unset($gueltig_n_bw);
      unset($dame_main);
      unset($dame_hk);
      unset($dame_senior);
      unset($dame_ok);
       unset($dame_jun);
      unset($dame_jk);
      unset($start_dame);
      unset($verein_dame);
      unset($Musik_FT);  
      unset($Musik_Akro);
      
       unset($Musik_Stell);     
      unset($Musik_Form);
       unset($Musik_Sieg);
       unset($Anz_Taenzer);                
      unset($nord_sued);
           
        $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $teilnehmer_id";
        $daten = mysqli_query($db, $sqlab);
        $startnummer = $startnummer + 1;  
              
        while($meldung = mysqli_fetch_array($daten))
         {  
          $datenfeld = $meldung[0];
          $datenwert = $meldung[1];
          
//          echo"$datenfeld - $datenwert<br>";
          if($datenfeld == 28)
             $vereinsname = $datenwert;            
          if($datenfeld == 29)
             $startklasse = $datenwert;
             if($startklasse == 1) 
          $startklasse = "RR_S";         
             if($startklasse == 2) 
          $startklasse = "RR_J";
         if($startklasse == 3) 
          $startklasse = "RR_C";         
             if($startklasse == 4) 
          $startklasse = "RR_B";
          if($startklasse == 5) 
          $startklasse = "RR_A";         
             if($startklasse == 6) 
          $startklasse = "F_RR_M";  
             if($startklasse == 7) 
          $startklasse = "F_RR_J";         
             if($startklasse == 8) 
          $startklasse = "F_RR_LF";
         if($startklasse == 9) 
          $startklasse = "F_RR_GF";         
             if($startklasse == 10) 
          $startklasse = "F_RR_ST";
          if($startklasse == 11) 
          $startklasse = "F_BW_M";         
             if($startklasse == 12) 
          $startklasse = "BW_A";
             if($startklasse == 13) 
          $startklasse = "BW_B";                                    
          if($datenfeld == 31)
             $nn_herr = $datenwert;
          if($datenfeld == 32)
             $vn_herr = $datenwert;                      
          if($datenfeld == 37)
             $nn_dame = $datenwert;            
          if($datenfeld == 38)
             $vn_dame = $datenwert; 
             

// echo"Datenwert = $datenwert<br>";
        $datenwert = explode(':', $datenwert);
        $datenwert = rtrim($datenwert[0]);       
//       echo"Datenwert = :$datenwert:<br>";                  
                      
          if($datenfeld == 43)
            {        
 //            $akro1_vr = $datenwert - 1;
              $akro1_vr = $datenwert;
 // echo"Akro: $akro1_vr<br>";            
             $ergebnis = ersetzen($startklasse, $akro1_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
// print_r($ergebnis);echo"<br>";          
             $akro1_vr = $ergebnis[1];
 //            $akro1_vr = str_replace($org,$neu,$akro1_vr);   
 //             $akro1_vr = str_replace($org_1,$neu_1,$akro1_vr);
             $wert1_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }
          if($datenfeld == 44)
            { 
             $akro2_vr = $datenwert;      
             $ergebnis = ersetzen($startklasse, $akro2_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro2_vr = $ergebnis[1];
//             $akro2_vr = str_replace($org,$neu,$akro2_vr);   
//              $akro2_vr = str_replace($org_1,$neu_1,$akro2_vr);
             $wert2_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }                                                 
          if($datenfeld == 45)
            { 
             $akro3_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro3_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro3_vr = $ergebnis[1];
//             $akro3_vr = str_replace($org,$neu,$akro3_vr);   
//              $akro3_vr = str_replace($org_1,$neu_1,$akro3_vr);
             $wert3_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            } 
          if($datenfeld == 46)
            { 
             $akro4_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro4_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro4_vr = $ergebnis[1];
//             $akro4_vr = str_replace($org,$neu,$akro4_vr);   
//             $akro4_vr = str_replace($org_1,$neu_1,$akro4_vr);
             $wert4_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }                       
          if($datenfeld == 47)
            { 
             $akro5_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro5_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro5_vr = $ergebnis[1];
//             $akro5_vr = str_replace($org,$neu,$akro5_vr);   
//             $akro5_vr = str_replace($org_1,$neu_1,$akro5_vr);
             $wert5_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }              
          if($datenfeld == 48)
            { 
             $akro6_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro6_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro6_vr = $ergebnis[1];
//             $akro6_vr = str_replace($org,$neu,$akro6_vr);   
//             $akro6_vr = str_replace($org_1,$neu_1,$akro6_vr);
             $wert6_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }           
          if($datenfeld == 50)
            { 
             $akro1_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro1_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro1_zr = $ergebnis[1];
//              $akro1_zr = str_replace($org,$neu,$akro1_zr);   
//             $akro1_zr = str_replace($org_1,$neu_1,$akro1_zr);
             $wert1_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }              
          if($datenfeld == 51)
            { 
             $akro1_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro1_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro1_er = $ergebnis[1];
//             $akro1_er = str_replace($org,$neu,$akro1_er);   
//             $akro1_er = str_replace($org_1,$neu_1,$akro1_er);
             $wert1_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }          
          if($datenfeld == 53)
             $vereinsnummer = $datenwert;             
          if($datenfeld == 54)
             $team_name = $datenwert; 
          if($datenfeld == 55)
            { 
             $akro2_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro2_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro2_zr = $ergebnis[1];
//             $akro2_zr = str_replace($org_1,$neu_1,$akro2_zr);
             $wert2_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }
          if($datenfeld == 56)
            { 
             $akro3_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro3_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro3_zr = $ergebnis[1];
//             $akro3_zr = str_replace($org,$neu,$akro3_zr);   
//             $akro3_zr = str_replace($org_1,$neu_1,$akro3_zr);
             $wert3_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }             
          if($datenfeld == 57)
            { 
             $akro4_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro4_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro4_zr = $ergebnis[1];
//             $akro4_zr = str_replace($org,$neu,$akro4_zr);   
//             $akro4_zr = str_replace($org_1,$neu_1,$akro4_zr);
             $wert4_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }             
          if($datenfeld == 58)
            { 
             $akro5_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro5_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro5_zr = $ergebnis[1];
//             $akro5_zr = str_replace($org,$neu,$akro5_zr);   
//             $akro5_zr = str_replace($org_1,$neu_1,$akro5_zr);
             $wert5_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }
          if($datenfeld == 59)
            { 
             $akro6_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro6_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro6_zr = $ergebnis[1];
//             $akro6_zr = str_replace($org,$neu,$akro6_zr);   
//             $akro6_zr = str_replace($org_1,$neu_1,$akro6_zr);
             $wert6_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }                          
          if($datenfeld == 60)
            {
             $akro2_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro2_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro2_er = $ergebnis[1];
//             $akro2_er = str_replace($org,$neu,$akro2_er);   
//             $akro2_er = str_replace($org_1,$neu_1,$akro2_er);
             $wert2_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }              
          if($datenfeld == 61)
            {
             $akro3_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro3_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro3_er = $ergebnis[1];
//             $akro3_er = str_replace($org,$neu,$akro3_er);   
//             $akro3_er = str_replace($org_1,$neu_1,$akro3_er);
             $wert3_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }             
          if($datenfeld == 62)
            {
             $akro4_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro4_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro4_er = $ergebnis[1];
//             $akro4_er = str_replace($org,$neu,$akro4_er);   
//             $akro4_er = str_replace($org_1,$neu_1,$akro4_er);                      
             $wert4_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }             
          if($datenfeld == 63)
            {
             $akro5_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro5_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro5_er = $ergebnis[1];
//             $akro5_er = str_replace($org,$neu,$akro5_er);   
//             $akro5_er = str_replace($org_1,$neu_1,$akro5_er);
             $wert5_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }
          if($datenfeld == 64)
            {
             $akro6_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro6_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro6_er = $ergebnis[1];
//             $akro6_er = str_replace($org,$neu,$akro6_er);   
//             $akro6_er = str_replace($org_1,$neu_1,$akro6_er);
             $wert6_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            }  
          if($datenfeld == 65 && $datenwert == 1)            
            {
             $gueltig = 1;
            }
                      
          if($datenfeld == 66)
            { 
             if($startklasse == "BW_A" || $startklasse == "BW_B")
               {  
                if($nn_herr)
                  {   
                    // Miglied ID finden
                   $startbuch_nr_h = $datenwert;  
               
                 $sqlab = "SELECT * FROM adm_user_data WHERE usd_usf_id = '66' AND usd_value = '" . $datenwert . "' ";
                   $bw_herr = mysqli_query($db, $sqlab);
                   $daten_herr = mysqli_fetch_array($bw_herr);
                   
                   // Starktlasse auslesen
                   $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $daten_herr[1] . "' ";
                   $bw_startklasse = mysqli_query($db, $sqlab);
                   
                   while($bw_start = mysqli_fetch_array($bw_startklasse))
               {

                 // Jugendklasse
                if($bw_start[0] == 146 && $bw_start[1])  
                   $bw_jun = 4;                            
                if($bw_start[0] == 147 && $bw_jun == 4)
                  {
                   $dame_jun = $bw_start[1];
                   
                   $dame_jk = 1;
                  
                   $sqlab = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_jun . "' ";
                   $dame_jun = mysqli_query($db, $sqlab);
                   $id_dame_jun = mysqli_fetch_row($dame_jun);

                   $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_jun[0] . "' ORDER BY usd_usf_id";
                            $daten_dame_schleife = mysqli_query($db, $sqlab);
                    while($daten_dame = mysqli_fetch_array($daten_dame_schleife))
                          {
                           if($daten_dame[0] == 2)
                              $verein_dame = $daten_dame[1];            
                           if($daten_dame[0] == 29)
                             {
                                       if($daten_dame[1] == 12)
                                          $startklasse_dame_j = "BW_A";
                                       if($daten_dame[1] == 13) 
                                          $startklasse_dame_j = "BW_B";     
                                        if($startklasse != $startklasse_dame_j)
                                         {  
                                          $gueltig = 0;
                                          echo"<p>Paarung nicht möglich!<p>";  
                                         }                                         
                             }
                           if($daten_dame[0] == 37)
                                       $nn_dame = $daten_dame[1];            
                                    if($daten_dame[0] == 38)
                                       $vn_dame = $daten_dame[1];
                                     if($daten_dame[0] == 65 && $daten_dame[1] == 0)            
                                       $gueltig = 0;                  
                                   if($daten_dame[0] == 66)                                       
                                       $startbuch_nr_d = $daten_dame[1];                              
                          }                                            
                  }               
                  
                // Hauptklasse  
                 if($bw_start[0] == 138 && $bw_start[1])  
                   $bw_main = 1;                            
                if($bw_start[0] == 139 && $bw_main == 1)
                  {  
                   $dame_main = $bw_start[1];
                   
                   $dame_hk = 1;
                  
                   $sqlab = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_main . "' ";
                   $dame_main = mysqli_query($db, $sqlab);
                   $id_dame_main = mysqli_fetch_row($dame_main);

                   $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_main[0] . "' ORDER BY usd_usf_id";
                            $daten_dame_schleife = mysqli_query($db, $sqlab);
                    while($daten_dame = mysqli_fetch_array($daten_dame_schleife))
                          {
                           if($daten_dame[0] == 2)
                              $verein_dame = $daten_dame[1];                            
                           if($daten_dame[0] == 29)
                             {
                                       if($daten_dame[1] == 12)
                                          $startklasse_dame = "BW_A";
                                       if($daten_dame[1] == 13) 
                                          $startklasse_dame = "BW_B";    
                                        if($startklasse != $startklasse_dame)
                                         {  
                                          $gueltig = 0;
                                          echo"<p>Paarung nicht möglich!<p>";  
                                         }                                         
                             }
                           if($daten_dame[0] == 37)
                                       $nn_dame = $daten_dame[1];            
                                    if($daten_dame[0] == 38)
                                       $vn_dame = $daten_dame[1];
                                     if($daten_dame[0] == 65 && $daten_dame[1] == 0)            
                                       $gueltig = 0;                  
                                   if($daten_dame[0] == 66)                                       
                                       $startbuch_nr_d = $daten_dame[1];                              
                          } 
                  }

                           // Seniorklasse
                if($bw_start[0] == 140 && $bw_start[1])
                    $bw_senior = 2;               
                   
                if($bw_start[0] == 141 && $bw_senior == 2)
                  {                     
                   $dame_senior = $bw_start[1];
                   
                   $dame_ok = 1;

                   $sqlab = "SELECT usd_usr_id FROM adm_user_data WHERE usd_usf_id = '66' AND  usd_value = '" . $dame_senior . "' ";
                   $dame_senior = mysqli_query($db, $sqlab);
                   $id_dame_senior = mysqli_fetch_row($dame_senior);
                   
                   $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = '" . $id_dame_senior[0] . "' ";
                            $daten_dame_schleife = mysqli_query($db, $sqlab);
                    while($daten_dame = mysqli_fetch_array($daten_dame_schleife))
                          {
                           if($daten_dame[0] == 2)
                              $verein_dame = $daten_dame[1];                            
                           if($daten_dame[0] == 29)
                             {                               
                                       if($daten_dame[1] == 12)
                                          $startklasse_dame_s = "BW_A";
                                       if($daten_dame[1] == 13) 
                                          $startklasse_dame_s = "BW_B";    
                                       if($startklasse != $startklasse_dame_s)
                                         {
                                          $gueltig = 0;
                                          echo"<p>Paarung nicht möglich!<p>";
                                         }                                           
                             }  
                           if($daten_dame[0] == 37)
                                       $nn_dame_s = $daten_dame[1];            
                                    if($daten_dame[0] == 38)
                                       $vn_dame_s = $daten_dame[1];
                                     if($daten_dame[0] == 65 && $daten_dame[1] == 0)            
                                       $gueltig = 0;                                       
                                   if($daten_dame[0] == 66)                                       
                                       $startbuch_nr_d_s = $daten_dame[1];                                                      
                          }
                  }
                if($bw_start[0] == 148 && $bw_start[1] == 1)
                {
                 $dame_start = 1;
                 $vereinsname = $verein_dame;
// echo"Verein Dame: $dame_start - $verein_dame - $vereinsname<br>"; 
                }                                   
               }
           if($dame_jk + $dame_hk + $dame_ok < 1)
              echo"<p>Startbuch: $startbuch_nr_h Startklasse: $startklasse Verein: $vereinsname: Ungültige Startmeldung!<p>";    
               
           $bw_main_senior = $bw_jun + $bw_main + $bw_senior;  
                  }
               }
                             
               }

               if($datenfeld == 66 && !$bw_main_senior)
                  {                      
             $startbuch_nr = $datenwert;
                  }

          if($datenfeld == 68)
            { 
             $e_akro1_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro1_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro1_vr = $ergebnis[1];
             $e_akro1_vr = str_replace($org,$neu,$e_akro1_vr);   
             $e_akro1_vr = str_replace($org_1,$neu_1,$e_akro1_vr);
             $e_wert1_vr = $ergebnis[0];
            }
          if($datenfeld == 69)
            { 
             $e_akro1_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro1_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro1_zr = $ergebnis[1];
             $e_akro1_zr = str_replace($org,$neu,$e_akro1_zr);   
             $e_akro1_zr = str_replace($org_1,$neu_1,$e_akro1_zr);
             $e_wert1_zr = $ergebnis[0];
            }
          if($datenfeld == 70)
            { 
             $e_akro1_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro1_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro1_er = $ergebnis[1];
             $e_akro1_er = str_replace($org,$neu,$e_akro1_er);   
             $e_akro1_er = str_replace($org_1,$neu_1,$e_akro1_er);
             $e_wert1_er = $ergebnis[0];
            }   
          if($datenfeld == 75)
            { 
             $akro7_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro7_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro7_vr = $ergebnis[1];
             $akro7_vr = str_replace($org,$neu,$akro7_vr);   
             $akro7_vr = str_replace($org_1,$neu_1,$akro7_vr);
             $wert7_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            }  
          if($datenfeld == 76)
            { 
             $akro8_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro8_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro8_vr = $ergebnis[1];
             $akro8_vr = str_replace($org,$neu,$akro8_vr);   
             $akro8_vr = str_replace($org_1,$neu_1,$akro8_vr);
             $wert8_vr = $ergebnis[0];
             $akro_vr = $akro_vr + 1;
            } 
           if($datenfeld == 77)
            { 
             $akro7_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro7_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro7_zr = $ergebnis[1];
             $akro7_zr = str_replace($org,$neu,$akro7_zr);   
             $akro7_zr = str_replace($org_1,$neu_1,$akro7_zr);
             $wert7_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            } 
          if($datenfeld == 78)
            { 
             $akro8_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro8_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro8_zr = $ergebnis[1];
             $akro8_zr = str_replace($org,$neu,$akro8_zr);   
             $akro8_zr = str_replace($org_1,$neu_1,$akro8_zr);
             $wert8_zr = $ergebnis[0];
             $akro_zr = $akro_zr + 1;
            }            
           if($datenfeld == 79)
            {
             $akro7_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro7_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro7_er = $ergebnis[1];
             $akro7_er = str_replace($org,$neu,$akro7_er);   
             $akro7_er = str_replace($org_1,$neu_1,$akro7_er);
             $wert7_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            } 
          if($datenfeld == 80)
            {
             $akro8_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $akro8_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $akro8_er = $ergebnis[1];
             $akro8_er = str_replace($org,$neu,$akro8_er);   
             $akro8_er = str_replace($org_1,$neu_1,$akro8_er);
             $wert8_er = $ergebnis[0];
             $akro_er = $akro_er + 1;
            } 

          if($datenfeld == 149)
            { 
             $e_akro2_vr = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro2_vr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro2_vr = $ergebnis[1];
             $e_akro2_vr = str_replace($org,$neu,$e_akro2_vr);   
             $e_akro2_vr = str_replace($org_1,$neu_1,$e_akro2_vr);
             $e_wert2_vr = $ergebnis[0];
            }
          if($datenfeld == 150)
            { 
             $e_akro2_zr = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro2_zr, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro2_zr = $ergebnis[1];
             $e_akro2_zr = str_replace($org,$neu,$e_akro2_zr);   
             $e_akro2_zr = str_replace($org_1,$neu_1,$e_akro2_zr);
             $e_wert2_zr = $ergebnis[0];
            }
          if($datenfeld == 151)
            { 
             $e_akro2_er = $datenwert;
             $ergebnis = ersetzen($startklasse, $e_akro2_er, $liste_C_komp, $liste_J_komp, $liste_S_komp, $liste_B_komp, $liste_A_komp, $liste_F_komp);
             $e_akro2_er = $ergebnis[1];
             $e_akro2_er = str_replace($org,$neu,$e_akro2_er);   
             $e_akro2_er = str_replace($org_1,$neu_1,$e_akro2_er);
             $e_wert2_er = $ergebnis[0];
            }   
            
          if($datenfeld == 152)
            {
             $Musik_FT = $datenwert;
            }                                                                                                             if($datenfeld == 153)
            {
             $Musik_Akro = $datenwert;             
            }
         if($datenfeld == 154)
            {
             $Musik_Stell = $datenwert;
             echo"Musik Stell - $Musik_Stell<br>";              
            } 
         if($datenfeld == 155)
            {
             $Musik_Form = $datenwert;
             echo"Musik Form - $Musik_Form<br>";              
            }
         if($datenfeld == 156)
            {
             $Musik_Sieg = $datenwert;
              echo"Musik Sieg - $Musik_Sieg<br>";             
            }                                     
         if($datenfeld == 157)
            {
             $nord_sued = $datenwert;
             if($nord_sued == 1)
                $nord_sued = "Nord_Cup";
             if($nord_sued == 2)
                $nord_sued = "Sued-Cup";
             if($nord_sued == 3)
                $nord_sued = "Kleiner Grenzverkehr";                                            
            }
          if($datenfeld == 179)
            {
             $Anz_Taenzer = $datenwert + 3;
            }                                    
         }

// Übreprüfung der Akros

if(($startklasse == "RR_A" && $akro_vr <6) || ($startklasse == "RR_B" && $akro_vr <5) || ($startklasse == "RR_C" && $akro_vr <4) || ($startklasse == "RR_J" && $akro_vr <3) || ($startklasse == "F_RR_M" && $akro_vr <6))
 {
  echo"<p>";
  echo"Startbuch: $startbuch_nr Startklasse: $startklasse Verein: $vereinsname<br>";
  echo"Anzahl VR: $akro_vr<br>";
  echo"Anzahl ZR: $akro_zr<br>";
  echo"Anzahl ER: $akro_er<br>";
 }

      if(!$akro1_vr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro1_vr = $no_akro;
             $wert1_vr = $akro_null;
              echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 1 Vorrunde<font color=000000><br>";
            }         
      if(!$akro2_vr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro2_vr = $no_akro;
             $wert2_vr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 2 Vorrunde<font color=000000><br>";
            }
      if(!$akro3_vr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro3_vr = $no_akro;
             $wert3_vr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 3 Vorrunde<font color=000000><br>";
            } 
      if(!$akro4_vr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "F_RR_M"))
            { 
             $akro4_vr = $no_akro;
             $wert4_vr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 4 Vorrunde<font color=000000><br>";
            }         
      if(!$akro5_vr && ($startklasse == "RR_A" || $startklasse == "RR_B"  || $startklasse == "F_RR_M"))
            { 
             $akro5_vr = $no_akro;
             $wert5_vr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 5 Vorrunde<font color=000000><br>";
            }
      if(!$akro6_vr && $startklasse == "F_RR_M")
            { 
             $akro6_vr = $no_akro;
             $wert6_vr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 6 Vorrunde<font color=000000><br>";
            }
/* nicht benötigt!                       
      if(!$akro7_vr && (($startklasse == "RR_A" && $akro_vr <5) || ($startklasse == "RR_B" && $akro_vr <4) || ($startklasse == "RR_C" && $akro_vr <0) || ($startklasse == "RR_J" && $akro_vr <0) || ($startklasse == "F_RR_M" && $akro_vr <6)))
            { 
             $akro7_vr = $no_akro;
             $wert7_vr = $akro_null;
             echo"<font color=FF3333>Fehler Akro 7 Vorrunde<font color=000000><br>";
            }         
      if(!$akro8_vr && (($startklasse == "RR_A" && $akro_vr <5) || ($startklasse == "RR_B" && $akro_vr <4) || ($startklasse == "RR_C" && $akro_vr <0) || ($startklasse == "RR_J" && $akro_vr <0) || ($startklasse == "F_RR_M" && $akro_vr <6)))
            { 
             $akro8_vr = $no_akro;
             $wert8_vr = $akro_null;
             echo"<font color=FF3333>Fehler Akro 8 Vorrunde<font color=000000><br>";
            }
*/
            
      if(!$akro1_zr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro1_zr = $no_akro;
             $wert1_zr = $akro_null;
              echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 1 Zwischenrunde<font color=000000><br>";
            }         
      if(!$akro2_zr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro2_zr = $no_akro;
             $wert2_zr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 2 Zwischenrunde<font color=000000><br>";
            }
      if(!$akro3_zr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro3_zr = $no_akro;
             $wert3_zr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 3 Zwischenrunde<font color=000000><br>";
            } 
      if(!$akro4_zr && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "F_RR_M"))
            {
             $akro4_zr = $no_akro;
             $wert4_zr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 4 Zwischenrunde<font color=000000><br>";
            }         
      if(!$akro5_zr && ($startklasse == "RR_A" || $startklasse == "RR_B"  || $startklasse == "F_RR_M"))
            { 
             $akro5_zr = $no_akro;
             $wert5_zr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 5 Zwischenrunde<font color=000000><br>";
            } 
      if(!$akro6_zr && $startklasse == "F_RR_M")
            { 
             $akro6_zr = $no_akro;
             $wert6_zr = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 6 Zwischenrunde<font color=000000><br>";
            }
/* nicht benötigt!                         
      if(!$akro7_zr && (($startklasse == "RR_A" && $akro_zr <5) || ($startklasse == "RR_B" && $akro_zr <4) || ($startklasse == "RR_C" && $akro_zr <0) || ($startklasse == "RR_J" && $akro_zr <0) || ($startklasse == "F_RR_M" && $akro_zr <6)))
            { 
             $akro7_zr = $no_akro;
             $wert7_zr = $akro_null;
             echo"<font color=FF3333>Fehler Akro 7 Zwischenrunde<font color=000000><br>";
            }         
      if(!$akro8_zr && (($startklasse == "RR_A" && $akro_zr <5) || ($startklasse == "RR_B" && $akro_zr <4) || ($startklasse == "RR_C" && $akro_zr <0) || ($startklasse == "RR_J" && $akro_zr <0) || ($startklasse == "F_RR_M" && $akro_zr <6)))
            { 
             $akro8_zr = $no_akro;
             $wert8_zr = $akro_null;
             echo"<font color=FF3333>Fehler Akro 8 Zwischenrunde<font color=000000><br>";
            } 
*/

      if(!$akro1_er && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro1_er = $no_akro;
             $wert1_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 1 Endrunde<font color=000000><br>";
            }        
      if(!$akro2_er && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro2_er = $no_akro;
             $wert2_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 2 Endrunde<font color=000000><br>";
            }
      if(!$akro3_er && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "RR_J" || $startklasse == "F_RR_M"))
            { 
             $akro3_er = $no_akro;
             $wert3_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 3 Endrunde<font color=000000><br>";
            }
      if(!$akro4_er && ($startklasse == "RR_A" || $startklasse == "RR_B" || $startklasse == "RR_C" || $startklasse == "F_RR_M"))
            { 
             $akro4_er = $no_akro;
             $wert4_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 4 Endrunde<font color=000000><br>";
            }         
      if(!$akro5_er && ($startklasse == "RR_A" || $startklasse == "RR_B"  || $startklasse == "F_RR_M"))
            { 
             $akro5_er = $no_akro;
             $wert5_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 5 Endrunde<font color=000000><br>";
            } 
      if(!$akro6_er && ($startklasse == "RR_A" || $startklasse == "RR_B"  || $startklasse == "F_RR_M"))
            { 
             $akro6_er = $no_akro;
             $wert6_er = $akro_null;
             echo"<font color=FF3333>Startbuch: $startbuch_nr Fehler Akro 6 Endrunde<font color=000000><br>";
            }
            
/* nicht benötigt!                        
      if(!$akro7_er && (($startklasse == "RR_A" && $akro_er <5) || ($startklasse == "RR_B" && $akro_er <4) || ($startklasse == "RR_C" && $akro_er <0) || ($startklasse == "RR_J" && $akro_er <0) || ($startklasse == "F_RR_M" && $akro_er <6)))
            { 
             $akro7_er = $no_akro;
             $wert7_er = $akro_null;
             echo"<font color=FF3333>Fehler Akro 7 Endrunde<font color=000000><br>";
            }         
      if(!$akro8_er && (($startklasse == "RR_A" && $akro_er <5) || ($startklasse == "RR_B" && $akro_er <4) || ($startklasse == "RR_C" && $akro_er <0) || ($startklasse == "RR_J" && $akro_er <0) || ($startklasse == "F_RR_M" && $akro_er <6)))
            { 
             $akro8_er = $no_akro;
             $wert8_er = $akro_null;
             echo"<font color=FF3333>Fehler Akro 8 Endrunde<font color=000000><br>";
            }                                          
*/

     if($startklasse != "BW_A" && $startklasse != "BW_B")
        {            
         if($gueltig == 1)
           {      
           $str_body .= $valueQuotes . $startklasse . $valueQuotes . $separator . $valueQuotes . $startnummer . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator. $valueQuotes . $vereinsnummer . $valueQuotes . $separator. $valueQuotes . $vereinsname . $valueQuotes . $separator. $valueQuotes . $team_name . $valueQuotes . $separator. $valueQuotes . $startbuch_nr . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_h . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_d . $valueQuotes . $separator. $valueQuotes . $akro1_vr . $valueQuotes . $separator. $valueQuotes . $wert1_vr . $valueQuotes . $separator. $valueQuotes . $akro2_vr . $valueQuotes . $separator. $valueQuotes . $wert2_vr . $valueQuotes . $separator. $valueQuotes . $akro3_vr . $valueQuotes . $separator. $valueQuotes . $wert3_vr . $valueQuotes . $separator. $valueQuotes . $akro4_vr . $valueQuotes . $separator. $valueQuotes . $wert4_vr . $valueQuotes . $separator. $valueQuotes . $akro5_vr . $valueQuotes . $separator. $valueQuotes . $wert5_vr . $valueQuotes . $separator. $valueQuotes . $akro6_vr . $valueQuotes . $separator. $valueQuotes . $wert6_vr . $valueQuotes . $separator. $valueQuotes . $akro7_vr . $valueQuotes . $separator. $valueQuotes . $wert7_vr . $valueQuotes . $separator. $valueQuotes . $akro8_vr . $valueQuotes . $separator. $valueQuotes . $wert8_vr . $valueQuotes . $separator .

$valueQuotes . $e_akro1_vr . $valueQuotes . $separator . $valueQuotes . $e_wert1_vr . $valueQuotes . $separator . $valueQuotes . $e_akro2_vr . $valueQuotes . $separator . $valueQuotes . $e_wert2_vr . $valueQuotes . $separator .                     
           
$valueQuotes . $akro1_zr . $valueQuotes . $separator . $valueQuotes . $wert1_zr . $valueQuotes . $separator. $valueQuotes . $akro2_zr . $valueQuotes . $separator. $valueQuotes . $wert2_zr . $valueQuotes . $separator. $valueQuotes . $akro3_zr . $valueQuotes . $separator. $valueQuotes . $wert3_zr . $valueQuotes . $separator. $valueQuotes . $akro4_zr . $valueQuotes . $separator. $valueQuotes . $wert4_zr . $valueQuotes . $separator. $valueQuotes . $akro5_zr . $valueQuotes . $separator. $valueQuotes . $wert5_zr . $valueQuotes . $separator. $valueQuotes . $akro6_zr . $valueQuotes . $separator. $valueQuotes . $wert6_zr . $valueQuotes . $separator. $valueQuotes . $akro7_zr . $valueQuotes . $separator. $valueQuotes . $wert7_zr . $valueQuotes . $separator. $valueQuotes . $akro8_zr . $valueQuotes . $separator. $valueQuotes . $wert8_zr . $valueQuotes . $separator . 

$valueQuotes . $e_akro1_zr . $valueQuotes . $separator . $valueQuotes . $e_wert1_zr . $valueQuotes . $separator . $valueQuotes . $e_akro2_zr . $valueQuotes . $separator . $valueQuotes . $e_wert2_zr . $valueQuotes . $separator .
           
$valueQuotes . $akro1_er . $valueQuotes . $separator. $valueQuotes . $wert1_er . $valueQuotes . $separator. $valueQuotes . $akro2_er . $valueQuotes . $separator. $valueQuotes . $wert2_er . $valueQuotes . $separator. $valueQuotes . $akro3_er . $valueQuotes . $separator. $valueQuotes . $wert3_er . $valueQuotes . $separator. $valueQuotes . $akro4_er . $valueQuotes . $separator. $valueQuotes . $wert4_er . $valueQuotes . $separator. $valueQuotes . $akro5_er . $valueQuotes . $separator. $valueQuotes . $wert5_er . $valueQuotes . $separator. $valueQuotes . $akro6_er . $valueQuotes . $separator. $valueQuotes . $wert6_er . $valueQuotes . $separator. $valueQuotes . $akro7_er . $valueQuotes . $separator. $valueQuotes . $wert7_er . $valueQuotes . $separator. $valueQuotes . $akro8_er . $valueQuotes . $separator. $valueQuotes . $wert8_er . $valueQuotes . $separator . 
           
$valueQuotes . $e_akro1_er . $valueQuotes . $separator . $valueQuotes . $e_wert1_er . $valueQuotes . $separator . $valueQuotes . $e_akro2_er . $valueQuotes . $separator . $valueQuotes . $e_wert2_er . $valueQuotes . $separator .           
           
$valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator . 
           
$valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . $separator. $valueQuotes . $nord_sued . $valueQuotes . $separator .
           
$valueQuotes . $Anz_Taenzer . $valueQuotes . $separator . $valueQuotes . '1' . $valueQuotes . "\r\n";
           }
        }
        
     if(($bw_main_senior == 4 || $bw_main_senior == 5) && $startklasse == "BW_A")
        {
         $startklasse_j =  substr($startklasse,0,3) . "JA";
                 
         if($gueltig == 1)
           {      
           $str_body .= $valueQuotes . $startklasse_j . $valueQuotes . $separator . $valueQuotes . $startnummer . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator. $valueQuotes . $vereinsnummer . $valueQuotes . $separator. $valueQuotes . $vereinsname . $valueQuotes . $separator. $valueQuotes . $team_name . $valueQuotes . $separator. $valueQuotes . $startbuch_nr . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_h . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_d . $valueQuotes . $separator. $valueQuotes . $akro1_vr . $valueQuotes . $separator. $valueQuotes . $wert1_vr . $valueQuotes . $separator. $valueQuotes . $akro2_vr . $valueQuotes . $separator. $valueQuotes . $wert2_vr . $valueQuotes . $separator. $valueQuotes . $akro3_vr . $valueQuotes . $separator. $valueQuotes . $wert3_vr . $valueQuotes . $separator. $valueQuotes . $akro4_vr . $valueQuotes . $separator. $valueQuotes . $wert4_vr . $valueQuotes . $separator. $valueQuotes . $akro5_vr . $valueQuotes . $separator. $valueQuotes . $wert5_vr . $valueQuotes . $separator. $valueQuotes . $akro6_vr . $valueQuotes . $separator. $valueQuotes . $wert6_vr . $valueQuotes . $separator. $valueQuotes . $akro7_vr . $valueQuotes . $separator. $valueQuotes . $wert7_vr . $valueQuotes . $separator. $valueQuotes . $akro8_vr . $valueQuotes . $separator. $valueQuotes . $wert8_vr . $valueQuotes . $separator . 
           
$valueQuotes . $e_akro1_vr . $valueQuotes . $separator . $valueQuotes . $e_wert1_vr . $valueQuotes . $separator . $valueQuotes . $e_akro2_vr . $valueQuotes . $separator . $valueQuotes . $e_wert2_vr . $valueQuotes . $separator .           

$valueQuotes . $akro1_zr . $valueQuotes . $separator. $valueQuotes . $wert1_zr . $valueQuotes . $separator. $valueQuotes . $akro2_zr . $valueQuotes . $separator. $valueQuotes . $wert2_zr . $valueQuotes . $separator. $valueQuotes . $akro3_zr . $valueQuotes . $separator. $valueQuotes . $wert3_zr . $valueQuotes . $separator. $valueQuotes . $akro4_zr . $valueQuotes . $separator. $valueQuotes . $wert4_zr . $valueQuotes . $separator. $valueQuotes . $akro5_zr . $valueQuotes . $separator. $valueQuotes . $wert5_zr . $valueQuotes . $separator. $valueQuotes . $akro6_zr . $valueQuotes . $separator. $valueQuotes . $wert6_zr . $valueQuotes . $separator. $valueQuotes . $akro7_zr . $valueQuotes . $separator. $valueQuotes . $wert7_zr . $valueQuotes . $separator. $valueQuotes . $akro8_zr . $valueQuotes . $separator. $valueQuotes . $wert8_zr . $valueQuotes . $separator . 

$valueQuotes . $e_akro1_zr . $valueQuotes . $separator . $valueQuotes . $e_wert1_zr . $valueQuotes . $separator . $valueQuotes . $e_akro2_zr . $valueQuotes . $separator . $valueQuotes . $e_wert2_zr . $valueQuotes . $separator .

$valueQuotes . $akro1_er . $valueQuotes . $separator. $valueQuotes . $wert1_er . $valueQuotes . $separator. $valueQuotes . $akro2_er . $valueQuotes . $separator. $valueQuotes . $wert2_er . $valueQuotes . $separator. $valueQuotes . $akro3_er . $valueQuotes . $separator. $valueQuotes . $wert3_er . $valueQuotes . $separator. $valueQuotes . $akro4_er . $valueQuotes . $separator. $valueQuotes . $wert4_er . $valueQuotes . $separator. $valueQuotes . $akro5_er . $valueQuotes . $separator. $valueQuotes . $wert5_er . $valueQuotes . $separator. $valueQuotes . $akro6_er . $valueQuotes . $separator. $valueQuotes . $wert6_er . $valueQuotes . $separator. $valueQuotes . $akro7_er . $valueQuotes . $separator. $valueQuotes . $wert7_er . $valueQuotes . $separator. $valueQuotes . $akro8_er . $valueQuotes . $separator. $valueQuotes . $wert8_er . $valueQuotes . $separator .  

$valueQuotes . $e_akro1_er . $valueQuotes . $separator . $valueQuotes . $e_wert1_er . $valueQuotes . $separator . $valueQuotes . $e_akro2_er . $valueQuotes . $separator . $valueQuotes . $e_wert2_er . $valueQuotes . $separator . 

$valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator .

$valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . $separator. $valueQuotes . $nord_sued . $valueQuotes . $separator .

$valueQuotes . $Anz_Taenzer . $valueQuotes . $separator . $valueQuotes . '1' . $valueQuotes . "\r\n";
           }
        }
        
// Reserviert für Junior B
           
     if(($bw_main_senior == 1 || $bw_main_senior == 3 || $bw_main_senior == 5) && ($startklasse == "BW_A" || $startklasse == "BW_B"))
        {
         $startklasse_m =  substr($startklasse,0,3) . "M" . substr($startklasse, -1);
         if($bw_main_senior > 2)
            $startnummer = $startnummer + 1; 
               
         if($gueltig == 1)
           {   
           $str_body .= $valueQuotes . $startklasse_m . $valueQuotes . $separator . $valueQuotes . $startnummer . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator. $valueQuotes . $vereinsnummer . $valueQuotes . $separator. $valueQuotes . $vereinsname . $valueQuotes . $separator. $valueQuotes . $team_name . $valueQuotes . $separator. $valueQuotes . $startbuch_nr . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_h . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_d . $valueQuotes . $separator. $valueQuotes . $akro1_vr . $valueQuotes . $separator. $valueQuotes . $wert1_vr . $valueQuotes . $separator. $valueQuotes . $akro2_vr . $valueQuotes . $separator. $valueQuotes . $wert2_vr . $valueQuotes . $separator. $valueQuotes . $akro3_vr . $valueQuotes . $separator. $valueQuotes . $wert3_vr . $valueQuotes . $separator. $valueQuotes . $akro4_vr . $valueQuotes . $separator. $valueQuotes . $wert4_vr . $valueQuotes . $separator. $valueQuotes . $akro5_vr . $valueQuotes . $separator. $valueQuotes . $wert5_vr . $valueQuotes . $separator. $valueQuotes . $akro6_vr . $valueQuotes . $separator. $valueQuotes . $wert6_vr . $valueQuotes . $separator. $valueQuotes . $akro7_vr . $valueQuotes . $separator. $valueQuotes . $wert7_vr . $valueQuotes . $separator. $valueQuotes . $akro8_vr . $valueQuotes . $separator. $valueQuotes . $wert8_vr . $valueQuotes . $separator . 
           
$valueQuotes . $e_akro1_vr . $valueQuotes . $separator . $valueQuotes . $e_wert1_vr . $valueQuotes . $separator . $valueQuotes . $e_akro2_vr . $valueQuotes . $separator . $valueQuotes . $e_wert2_vr . $valueQuotes . $separator .            

$valueQuotes . $akro1_zr . $valueQuotes . $separator. $valueQuotes . $wert1_zr . $valueQuotes . $separator. $valueQuotes . $akro2_zr . $valueQuotes . $separator. $valueQuotes . $wert2_zr . $valueQuotes . $separator. $valueQuotes . $akro3_zr . $valueQuotes . $separator. $valueQuotes . $wert3_zr . $valueQuotes . $separator. $valueQuotes . $akro4_zr . $valueQuotes . $separator. $valueQuotes . $wert4_zr . $valueQuotes . $separator. $valueQuotes . $akro5_zr . $valueQuotes . $separator. $valueQuotes . $wert5_zr . $valueQuotes . $separator. $valueQuotes . $akro6_zr . $valueQuotes . $separator. $valueQuotes . $wert6_zr . $valueQuotes . $separator. $valueQuotes . $akro7_zr . $valueQuotes . $separator. $valueQuotes . $wert7_zr . $valueQuotes . $separator. $valueQuotes . $akro8_zr . $valueQuotes . $separator. $valueQuotes . $wert8_zr . $valueQuotes . $separator . 

$valueQuotes . $e_akro1_zr . $valueQuotes . $separator . $valueQuotes . $e_wert1_zr . $valueQuotes . $separator . $valueQuotes . $e_akro2_zr . $valueQuotes . $separator . $valueQuotes . $e_wert2_zr . $valueQuotes . $separator .

$valueQuotes . $akro1_er . $valueQuotes . $separator. $valueQuotes . $wert1_er . $valueQuotes . $separator. $valueQuotes . $akro2_er . $valueQuotes . $separator. $valueQuotes . $wert2_er . $valueQuotes . $separator. $valueQuotes . $akro3_er . $valueQuotes . $separator. $valueQuotes . $wert3_er . $valueQuotes . $separator. $valueQuotes . $akro4_er . $valueQuotes . $separator. $valueQuotes . $wert4_er . $valueQuotes . $separator. $valueQuotes . $akro5_er . $valueQuotes . $separator. $valueQuotes . $wert5_er . $valueQuotes . $separator. $valueQuotes . $akro6_er . $valueQuotes . $separator. $valueQuotes . $wert6_er . $valueQuotes . $separator. $valueQuotes . $akro7_er . $valueQuotes . $separator. $valueQuotes . $wert7_er . $valueQuotes . $separator. $valueQuotes . $akro8_er . $valueQuotes . $separator. $valueQuotes . $wert8_er . $valueQuotes . $separator . 

$valueQuotes . $e_akro1_er . $valueQuotes . $separator . $valueQuotes . $e_wert1_er . $valueQuotes . $separator . $valueQuotes . $e_akro2_er . $valueQuotes . $separator . $valueQuotes . $e_wert2_er . $valueQuotes . $separator . 

$valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator .

$valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . $separator. $valueQuotes . $nord_sued . $valueQuotes . $separator .

$valueQuotes . $Anz_Taenzer . $valueQuotes . $separator . $valueQuotes . '1' . $valueQuotes . "\r\n";
           }
        }
        

     if(($bw_main_senior == 2 || $bw_main_senior == 3) && ($startklasse == "BW_A" || $startklasse == "BW_B"))
        { 
         $startklasse_s = substr($startklasse,0,3) . "S" . substr($startklasse, -1);
         if($bw_main_senior > 2)
            $startnummer = $startnummer + 1;         
         
         if($gueltig == 1)
           {     
           $str_body .= $valueQuotes . $startklasse_s . $valueQuotes . $separator . $valueQuotes . $startnummer . $valueQuotes . $separator . $valueQuotes . $vn_dame_s . $valueQuotes . $separator . $valueQuotes . $nn_dame_s . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator. $valueQuotes . $vereinsnummer . $valueQuotes . $separator. $valueQuotes . $vereinsname . $valueQuotes . $separator. $valueQuotes . $team_name . $valueQuotes . $separator. $valueQuotes . $startbuch_nr . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_h . $valueQuotes . $separator. $valueQuotes . $startbuch_nr_d_s . $valueQuotes . $separator. $valueQuotes . $akro1_vr . $valueQuotes . $separator. $valueQuotes . $wert1_vr . $valueQuotes . $separator. $valueQuotes . $akro2_vr . $valueQuotes . $separator. $valueQuotes . $wert2_vr . $valueQuotes . $separator. $valueQuotes . $akro3_vr . $valueQuotes . $separator. $valueQuotes . $wert3_vr . $valueQuotes . $separator. $valueQuotes . $akro4_vr . $valueQuotes . $separator. $valueQuotes . $wert4_vr . $valueQuotes . $separator. $valueQuotes . $akro5_vr . $valueQuotes . $separator. $valueQuotes . $wert5_vr . $valueQuotes . $separator. $valueQuotes . $akro6_vr . $valueQuotes . $separator. $valueQuotes . $wert6_vr . $valueQuotes . $separator. $valueQuotes . $akro7_vr . $valueQuotes . $separator. $valueQuotes . $wert7_vr . $valueQuotes . $separator. $valueQuotes . $akro8_vr . $valueQuotes . $separator. $valueQuotes . $wert8_vr . $valueQuotes . $separator.
           
$valueQuotes . $e_akro1_vr . $valueQuotes . $separator . $valueQuotes . $e_wert1_vr . $valueQuotes . $separator . $valueQuotes . $e_akro2_vr . $valueQuotes . $separator . $valueQuotes . $e_wert2_vr . $valueQuotes .           
           
$separator. $valueQuotes . $akro1_zr . $valueQuotes . $separator. $valueQuotes . $wert1_zr . $valueQuotes . $separator. $valueQuotes . $akro2_zr . $valueQuotes . $separator. $valueQuotes . $wert2_zr . $valueQuotes . $separator. $valueQuotes . $akro3_zr . $valueQuotes . $separator. $valueQuotes . $wert3_zr . $valueQuotes . $separator. $valueQuotes . $akro4_zr . $valueQuotes . $separator. $valueQuotes . $wert4_zr . $valueQuotes . $separator. $valueQuotes . $akro5_zr . $valueQuotes . $separator. $valueQuotes . $wert5_zr . $valueQuotes . $separator. $valueQuotes . $akro6_zr . $valueQuotes . $separator. $valueQuotes . $wert6_zr . $valueQuotes . $separator. $valueQuotes . $akro7_zr . $valueQuotes . $separator. $valueQuotes . $wert7_zr . $valueQuotes . $separator. $valueQuotes . $akro8_zr . $valueQuotes . $separator. $valueQuotes . $wert8_zr . $valueQuotes . $separator .

$valueQuotes . $e_akro1_zr . $valueQuotes . $separator . $valueQuotes . $e_wert1_zr . $valueQuotes . $separator . $valueQuotes . $e_akro2_zr . $valueQuotes . $separator . $valueQuotes . $e_wert2_zr . $valueQuotes . $separator .

$valueQuotes . $akro1_er . $valueQuotes . $separator. $valueQuotes . $wert1_er . $valueQuotes . $separator. $valueQuotes . $akro2_er . $valueQuotes . $separator. $valueQuotes . $wert2_er . $valueQuotes . $separator. $valueQuotes . $akro3_er . $valueQuotes . $separator. $valueQuotes . $wert3_er . $valueQuotes . $separator. $valueQuotes . $akro4_er . $valueQuotes . $separator. $valueQuotes . $wert4_er . $valueQuotes . $separator. $valueQuotes . $akro5_er . $valueQuotes . $separator. $valueQuotes . $wert5_er . $valueQuotes . $separator. $valueQuotes . $akro6_er . $valueQuotes . $separator. $valueQuotes . $wert6_er . $valueQuotes . $separator. $valueQuotes . $akro7_er . $valueQuotes . $separator. $valueQuotes . $wert7_er . $valueQuotes . $separator. $valueQuotes . $akro8_er . $valueQuotes . $separator. $valueQuotes . $wert8_er . $valueQuotes . $separator.
           
$valueQuotes . $e_akro1_er . $valueQuotes . $separator . $valueQuotes . $e_wert1_er . $valueQuotes . $separator . $valueQuotes . $e_akro2_er . $valueQuotes . $separator . $valueQuotes . $e_wert2_er . $valueQuotes . $separator . 

$valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator .

$valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . $separator. $valueQuotes . $nord_sued . $valueQuotes . $separator .

$valueQuotes . $Anz_Taenzer . $valueQuotes . $separator . $valueQuotes . '1' . $valueQuotes . "\r\n";
           }
        } 
     }
     
    $startnummer = 500;
    $inhalt = $str_csv . $str_body; 
    
// echo"$inhalt<p>";

// Dateinamen festlegen
     $filename = "T" . $t_num . "_Anmeldung_KOPIE.txt";
     // Datei auf Server speichern
     $fn = "./kopien_startlisten/" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>";    
    }  
// Ende while          
   }  
?>