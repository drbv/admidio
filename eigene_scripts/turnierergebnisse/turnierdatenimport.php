<?php

// error_reporting (E_ALL);

require("./intern/dboeffnen.inc.php");

// Turnier

$verzeichnis = "./csv_dateien";
echo "<ol>";
 
// Text, ob ein Verzeichnis angegeben wurde
if ( is_dir ( $verzeichnis ))
{
    // öffnen des Verzeichnisses
    if ( $handle = opendir($verzeichnis) )
    {
        // einlesen der Verzeichnisses
        while (($file = readdir($handle)) !== false)
        {   
            if($file !="." && $file !="..")
              {
                if(substr($file,0,1) == "T")
                  $nummer_des_turniers = substr($file,0,8);


echo'Turniernummer: ' . $nummer_des_turniers . '<br>';

// Prüfen ob Turnier in DB schon vorhanden

$tuniernummer_kurz = substr($nummer_des_turniers,1,7);

$sqlab = 'SELECT turniernummer FROM Turnier WHERE turniernummer = ' . $tuniernummer_kurz;
// echo $sqlab .' sqlab<br>';
$vorhanden = mysqli_query($db, $sqlab);
$ergebnis = mysqli_fetch_row($vorhanden);

// print_r($ergebnis);echo' Ergenis<br>';
if(!$ergebnis[0] && substr($tuniernummer_kurz,0,1) != 2)
   {
    // Turnier wird importiert
    echo'import<br>';

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Turnier.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($rtab = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
       if($rtab[0] == "1")
         { 
          $turniername = mysqli_real_escape_string($db, $rtab[1]); 
          $t_num = $rtab[2]; // Turniernummer
         	
          $dat = explode('.', $rtab[3]);
              if($dat[0] < 10)
                 $datum_1 = "0" . $dat[0];
               else
                 $datum_1 = $dat[0];
               if($dat[1] < 10)
                 $datum_2 = "0" . $dat[1];
               else
                 $datum_2 = $dat[1];
                          
          $datum = substr($dat[2],0,4) . "-" . $datum_2 . "-" .  $datum_1;
          
          $veranstalter = mysqli_real_escape_string($db, $rtab[5]);             
                        
         $sqlab = "insert Turnier set turniernummer = '$rtab[2]', turniername = '$turniername', datum = '$datum', veranstalter_nr = '$rtab[4]', veranstalter_name = '$veranstalter', veranstaltung_ort = '$rtab[6]' ";
         
           // echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Turnierleitung

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Turnierleitung.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($rtab = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
       // print_r($rtab);echo"<br><br>";

       if($rtab[0] != "TL_ID")
         {                              
          $sqlab = "insert T_Leiter set tl_id_tlp = '$rtab[0]', turniernummer = '$t_num', lizenznummer = '$rtab[2]', name = '$rtab[3] $rtab[4]', funktion = '$rtab[6]'";
          
        // echo"$sqlab<br><br>";        
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Wertungsrichter

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Wert_Richter.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($rtab = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
       if($rtab[0] != "WR_ID")
         {                              
          $sqlab = "insert wertungsrichter set wr_id_tlp = '$rtab[0]', turniernummer = '$t_num', lizenznummer = '$rtab[3]', name = '$rtab[4] $rtab[5]', kuerzel = '$rtab[2]'";
         
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Rundentab

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Rundentab.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($rtab = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
       if($rtab[0] != "RT_ID" && strlen($rtab[2]) > 2 && $rtab[3] !="Sieger" && $rtab[3] !="Runde")
        {
         $sqlab = "insert rundentab set rt_id_tlp = '$rtab[0]', turniernummer = '$t_num', startklasse = '$rtab[2]', runde = '$rtab[3]', runden_rf = '$rtab[4]'";
         
         //  echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Tanzpaare

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Paare.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($paar = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
       if($paar[0] != "TP_ID" && $paar[16] == 1)
        {

         $verein_name = mysqli_real_escape_string($db, $paar[11]); 
//         echo 'Team_name: ' .$verein_name . '<br>';    
         $team_name = mysqli_real_escape_string($db, $paar[12]); 
//         echo 'Team_name: ' .$team_name . '<br>';     


         $sqlab = "insert paare set paar_id_tlp = '$paar[0]', turniernummer = '$t_num', startklasse = '$paar[2]', startnummer = '$paar[3]', dame = '" . $paar[4] . " " . $paar[5] . "', herr = '" . $paar[7] . " " . $paar[8] . "', team = '$team_name', startbuch = '$paar[13]', boogie_sb_herr = '$paar[14]', boogie_sb_dame = '$paar[15]', platz = '$paar[17]', punkte = '" . str_replace(',', '.', $paar[18]) . "', rl_punkte = '" . str_replace(',', '.', $paar[19]) . "', RT_ID_Ausgeschieden = '" . $paar[20] . "', Akro1_VR = '$paar[25]', Wert1_VR = '" . str_replace(',', '.', $paar[26]) . "', Akro2_VR = '$paar[27]', Wert2_VR = '" . str_replace(',', '.', $paar[28]) . "', Akro3_VR = '$paar[29]', Wert3_VR = '" . str_replace(',', '.', $paar[30]) . "', Akro4_VR = '$paar[31]', Wert4_VR = '" . str_replace(',', '.', $paar[32]) . "', Akro5_VR = '$paar[33]', Wert5_VR = '" . str_replace(',', '.', $paar[34]) . "', Akro6_VR = '$paar[35]', Wert6_VR = '" . str_replace(',', '.', $paar[36]) . "', Akro7_VR = '$paar[37]', Wert7_VR = '" . str_replace(',', '.', $paar[38]) . "', Akro8_VR = '$paar[39]', Wert8_VR = '" . str_replace(',', '.', $paar[40]) . "', Akro1_ZR = '$paar[41]', Wert1_ZR = '" . str_replace(',', '.', $paar[42]) . "', Akro2_ZR = '$paar[43]', Wert2_ZR = '" . str_replace(',', '.', $paar[44]) . "', Akro3_ZR = '$paar[45]', Wert3_ZR = '" . str_replace(',', '.', $paar[46]) . "', Akro4_ZR = '$paar[47]', Wert4_ZR = '" . str_replace(',', '.', $paar[48]) . "', Akro5_ZR = '$paar[49]', Wert5_ZR = '" . str_replace(',', '.', $paar[50]) . "', Akro6_ZR = '$paar[51]', Wert6_ZR = '" . str_replace(',', '.', $paar[52]) . "', Akro7_ZR = '$paar[53]', Wert7_ZR = '" . str_replace(',', '.', $paar[54]) . "', Akro8_ZR = '$paar[55]', Wert8_ZR = '" . str_replace(',', '.', $paar[56]) . "', Akro1_ER = '$paar[57]', Wert1_ER = '" . str_replace(',', '.', $paar[58]) . "', Akro2_ER = '$paar[59]', Wert2_ER = '" . str_replace(',', '.', $paar[60]) . "', Akro3_ER = '$paar[61]', Wert3_ER = '" . str_replace(',', '.', $paar[62]) . "', Akro4_ER = '$paar[63]', Wert4_ER = '" . str_replace(',', '.', $paar[64]) . "', Akro5_ER = '$paar[65]', Wert5_ER = '" . str_replace(',', '.', $paar[66]) . "', Akro6_ER = '$paar[67]', Wert6_ER = '" . str_replace(',', '.', $paar[68]) . "', Akro7_ER = '$paar[69]', Wert7_ER = '" . str_replace(',', '.', $paar[70]) . "', Akro8_ER = '$paar[71]', Wert8_ER = '" . str_replace(',', '.', $paar[72]) . "', anzahl_taenzer = '$paar[91]', verein = '$verein_name', cup_serie = '$paar[90]' ";

// echo"<br>$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Wertungen

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Abgegebene_Wertungen.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($wertung = fgetcsv($fp, 1000, ";",'"')) !== FALSE)
      {
//       print_r($wertung);echo"<br><br>";
       if($wertung[0] != "ID")
        {
         $sqlab = "insert wertungen set turniernummer = '$t_num', paar_id_tlp = '$wertung[1]', rh = '$wertung[2]', wr_id = '$wertung[3]', rund_tab_id = '$wertung[4]', herr_gt = '" . str_replace(',','.',$wertung[5]) . "', herr_halt_dt = '" . str_replace(',','.',$wertung[6]) . "', dame_gt = '" . str_replace(',','.',$wertung[7]) . "', dame_halt_dt = '" . str_replace(',','.',$wertung[8]) . "', choreo = '" . str_replace(',','.',$wertung[9]) . "', tanzfiguren = '" . str_replace(',','.',$wertung[10]) . "', taenz_darbietung = '" . str_replace(',','.',$wertung[11]) . "', grobfehler_text = '$wertung[12]', grobfehler_summe = '" . str_replace(',','.',$wertung[13]) . "', akro1 = '" . str_replace(',','.',$wertung[14]) . "', akro1_grobfehler_text = '$wertung[15]', akro1_grobfehler_summe = '" . str_replace(',','.',$wertung[16]) . "', akro2 = '" . str_replace(',','.',$wertung[17]) . "', akro2_grobfehler_text = '$wertung[18]', akro2_grobfehler_summe = '" . str_replace(',','.',$wertung[19]) . "', akro3 = '" . str_replace(',','.',$wertung[20]) . "', akro3_grobfehler_text = '$wertung[21]', akro3_grobfehler_summe = '" . str_replace(',','.',$wertung[22]) . "', akro4 = '" . str_replace(',','.',$wertung[23]) . "', akro4_grobfehler_text = '$wertung[24]', akro4_grobfehler_summe = '" . str_replace(',','.',$wertung[25]) . "', akro5 = '" . str_replace(',','.',$wertung[26]) . "', akro5_grobfehler_text = '$wertung[27]', akro5_grobfehler_summe = '" . str_replace(',','.',$wertung[28]) . "', akro6 = '" . str_replace(',','.',$wertung[29]) . "', akro6_grobfehler_text = '$wertung[30]', akro6_grobfehler_summe = '" . str_replace(',','.',$wertung[31]) . "', akro7 = '" . str_replace(',','.',$wertung[32]) . "', akro7_grobfehler_text = '$wertung[33]', akro7_grobfehler_summe = '" . str_replace(',','.',$wertung[34]) . "', akro8 = '" . str_replace(',','.',$wertung[35]) . "', akro8_grobfehler_text = '$wertung[36]', akro8_grobfehler_summe = '" . str_replace(',','.',$wertung[37]) . "' ";
         
// echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
           
fclose($fp);
unlink($filename);

// Majorität

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Majoritaet.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($majoritaet = fgetcsv($fp, 1000, ";")) !== FALSE)
      {
//        print_r($majoritaet);echo" - $lz <br><br>";
       if($majoritaet[0] != "RT_ID")
        {
         $sqlab = "insert majoritaet set turniernummer = '$t_num', RT_ID = '$majoritaet[0]', TP_ID = '$majoritaet[1]', DQ_ID = '$majoritaet[2]', PA_ID = '$majoritaet[3]', Anmerkung = '$majoritaet[4]', WR1_ID = '$majoritaet[5]', WR1_Orig_Punkte = '" . str_replace(',','.',$majoritaet[6]) . "', WR1_Orig_Platz = '$majoritaet[7]', WR1_Punkte = '" . str_replace(',','.',$majoritaet[8]) . "', WR1_Platz = '$majoritaet[9]', WR1 = '" . str_replace(',','.',$majoritaet[10]) . "', WR2_ID = '$majoritaet[11]', WR2_Orig_Punkte = '" . str_replace(',','.',$majoritaet[12]) . "', WR2_Orig_Platz = '$majoritaet[13]', WR2_Punkte = '" . str_replace(',','.',$majoritaet[14]) . "', WR2_Platz = '$majoritaet[15]', WR2 = '" . str_replace(',','.',$majoritaet[16]) . "', WR3_ID = '$majoritaet[17]', WR3_Orig_Punkte = '" . str_replace(',','.',$majoritaet[18]) . "', WR3_Orig_Platz = '$majoritaet[19]', WR3_Punkte = '" . str_replace(',','.',$majoritaet[20]) . "', WR3_Platz = '$majoritaet[21]', WR3 = '" . str_replace(',','.',$majoritaet[22]) . "', WR4_ID = '$majoritaet[23]', WR4_Orig_Punkte = '" . str_replace(',','.',$majoritaet[24]) . "', WR4_Orig_Platz = '$majoritaet[25]', WR4_Punkte = '" . str_replace(',','.',$majoritaet[26]) . "', WR4_Platz = '$majoritaet[27]', WR4 = '" . str_replace(',','.',$majoritaet[28]) . "', WR5_ID = '$majoritaet[29]', WR5_Orig_Punkte = '" . str_replace(',','.',$majoritaet[30]) . "', WR5_Orig_Platz = '$majoritaet[31]', WR5_Punkte = '" . str_replace(',','.',$majoritaet[32]) . "', WR5_Platz = '$majoritaet[33]', WR5 = '" . str_replace(',','.',$majoritaet[34]) . "', WR6_ID = '$majoritaet[35]', WR6_Orig_Punkte = '" . str_replace(',','.',$majoritaet[36]) . "', WR6_Orig_Platz = '$majoritaet[37]', WR6_Punkte = '" . str_replace(',','.',$majoritaet[38]) . "', WR6_Platz = '$majoritaet[39]', WR6 = '" . str_replace(',','.',$majoritaet[40]) . "', WR7_ID = '$majoritaet[41]', WR7_Orig_Punkte = '" . str_replace(',','.',$majoritaet[42]) . "', WR7_Orig_Platz = '$majoritaet[43]', WR7_Punkte = '" . str_replace(',','.',$majoritaet[44]) . "', WR7_Platz = '$majoritaet[45]', WR7 = '" . str_replace(',','.',$majoritaet[46]) . "',  Platz = '$majoritaet[47]', Platz_Orig = '$majoritaet[48]', RT_ID_weiter = '$majoritaet[49]', Runde_Report = '$majoritaet[50]', KO_Sieger = '$majoritaet[51]' ";
         
//          echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
          
fclose($fp);
unlink($filename);


// Rundenqualifikation

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Paare_Rundenqualifikation.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($rquali = fgetcsv($fp, 1000, ";")) !== FALSE)
      {
 //       print_r($rquali);echo" - $lz <br><br>";
       if($rquali[0] != "PR_ID")
        {
        if($rquali[7] == 'FALSCH')
           $rquali[7] = 0;
        else
           $rquali[7] = 1;
        if($rquali[8] == 'FALSCH')
           $rquali[8] = 0;
        else
           $rquali[8] = 1;  
                    
        $sqlab = "insert rundenquali set turniernummer = '$t_num', pr_id = '$rquali[0]', rt_id = '$rquali[1]', tp_id = '$rquali[2]', auslosung = '$rquali[3]', rundennummer = '$rquali[4]', anwesend = '$rquali[5]', nochmal = '$rquali[7]', ko_sieger = '$rquali[8]'";
         
//echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
          
fclose($fp);
unlink($filename);


// Auswertung

$filename = "./csv_dateien/" . $nummer_des_turniers . "/" . $nummer_des_turniers . "_Auswertung.csv";

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($auswertung = fgetcsv($fp, 1000, ";")) !== FALSE)
      {
//        print_r($auswertung);echo" - $lz <br><br>";
       if($auswertung[0] != "AUS_ID")
        {
         $sqlab = "insert auswertung set turniernummer = '$t_num', aus_id = '$auswertung[0]', pr_id = '$auswertung[1]', wr_id = '$auswertung[2]', punkte = '" . str_replace(',','.',$auswertung[3]) . "', platz = '" . str_replace(',','.',$auswertung[4]) . "', reihenfolge = '$auswertung[5]' ";
         

//          echo"$sqlab<br><br>";
         mysqli_query($db, $sqlab);
        }

      }
          
fclose($fp);
unlink($filename);

echo"<h1>Der Import vom Turnier $t_num ist abgeschlossen!</h1>";

   // Ende Turnierimport
   }

   else
       echo"<h1>Das Turnier $t_num ist schon in der Datenbank vorhanden oder ein Breitensportturnier!</h1>";
       
// Dateien und Verzeichnis löschen

if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Turnier.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Turnier.csv');
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Turnierleitung.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Turnierleitung.csv');
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Wert_Richter.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Wert_Richter.csv');      
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Rundentab.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Rundentab.csv'); 
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Paare.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Paare.csv'); 
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Abgegebene_Wertungen.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Abgegebene_Wertungen.csv'); 
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Majoritaet.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Majoritaet.csv'); 
   
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Paare_Rundenqualifikation.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Paare_Rundenqualifikation.csv'); 
            
if(file('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Auswertung.csv'))
   unlink('./csv_dateien/' . $nummer_des_turniers . '/' . $nummer_des_turniers . '_Auswertung.csv');
   
   
$verz_loesch = 'csv_dateien/' . $nummer_des_turniers;
rmdir($verz_loesch); 
              }
        }
        closedir($handle);
    }
}
echo "</ol>";

echo"<p>Turnierdatenimport abgeschlossen";
?>