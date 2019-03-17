<?php
// error_reporting (E_ALL);

echo"<h1>RR Startb�cher Exportieren!</h1>";

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

$separator   = ";";
$valueQuotes = '"';

// Kopfzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Buchnr' . $valueQuotes.
         $separator. $valueQuotes. 'Anrede1'. $valueQuotes.	
         $separator. $valueQuotes. 'Vorname1'. $valueQuotes.	
         $separator. $valueQuotes. 'Nachname1'. $valueQuotes.
         $separator. $valueQuotes. 'Anrede2'. $valueQuotes.	
         $separator. $valueQuotes. 'Vorname2'. $valueQuotes.	
         $separator. $valueQuotes. 'Nachname2'. $valueQuotes.
         $separator. $valueQuotes. 'Startklasse'. $valueQuotes.	
         $separator. $valueQuotes. 'Clubnr'. $valueQuotes.	
         $separator. $valueQuotes. 'Clubname_kurz'. $valueQuotes.		
         $separator. $valueQuotes. 'LRRVERB'. $valueQuotes.
         $separator. $valueQuotes. 'RFID1'. $valueQuotes.
         $separator. $valueQuotes. 'RFID2'. $valueQuotes;
$str_csv = $str_csv. "\r\n";

// Datum festlegen
$datum_unix = time() + 864000 ; // 9 Tage
$datum = date("Y-m-d", $datum_unix);

//startb�cher

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 103 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//  print_r($name);echo"<br>";	   
		   		   			
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
  	       
   	       if($temp_id == 28)
   	          $ve_name = $temp_wert;
   	       if($temp_id == 29)
   	          $startklasse = $temp_wert;
   	          if($startklasse == 1)
   	             $startklasse = "S";
   	          if($startklasse == 2)
   	             $startklasse = "J";
   	          if($startklasse == 3)
   	             $startklasse = "C";
   	          if($startklasse == 4)
   	             $startklasse = "B";
   	          if($startklasse == 5)
   	             $startklasse = "A";
   	       if($temp_id == 31)
   	          $n_name_2 = $temp_wert;
   	          if(strlen($n_name_2) > 2)
   	             $anrede2 = "Herr";
   	       if($temp_id == 32)
   	          $v_name_2 = $temp_wert;
   	       if($temp_id == 37)
   	          $n_name_1 = $temp_wert;
   	          if(strlen($n_name_1) > 2 && !$anrede)
   	             $anrede1 = "Frau";
   	       if($temp_id == 38)
   	          $v_name_1 = $temp_wert;

   	       if($temp_id == 49)
   	         {
   	          if($temp_wert == 1)
                 $b_land = "BWRRV";
   	          if($temp_wert == 2)
                 $b_land = "BVRR";   
   	          if($temp_wert == 3)
                 $b_land = "BRRT";
   	          if($temp_wert == 4)
                 $b_land = "BRA";        
   	          if($temp_wert == 5)
                 $b_land = "BR";
   	          if($temp_wert == 6)
                 $b_land = "HARRV";
   	          if($temp_wert == 7)
                 $b_land = "HRBV";
   	          if($temp_wert == 8)
                 $b_land = "MV";       
   	          if($temp_wert == 9)
                 $b_land = "NRBV";
   	          if($temp_wert == 10)
                 $b_land = "NWRRV";       
   	          if($temp_wert == 11)
                 $b_land = "RRRV";
   	          if($temp_wert == 12)
                 $b_land = "SAAR";     
   	          if($temp_wert == 13)
                 $b_land = "SRRA";
              if($temp_wert == 14)
                 $b_land = "SARRA"; 
   	          if($temp_wert == 15)
                 $b_land = "SLH";
   	          if($temp_wert == 16)
                 $b_land = "TH";
   	         }
   	         
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;
   	       if($temp_id == 65)
   	          $gueltig = $temp_wert;
   	       if($temp_id == 66)
   	          $sb_num = $temp_wert;
   	       if($temp_id == 188)
   	          $rfid2 = $temp_wert;
   	       if($temp_id == 189)
   	          $rfid1 = $temp_wert;
   	         } 
  	             
    	      
 	    $str_body .= $valueQuotes . $sb_num . $valueQuotes . $separator . $valueQuotes . $anrede1 . $valueQuotes . $separator . $valueQuotes . $v_name_1 . $valueQuotes . $separator . $valueQuotes . $n_name_1 . $valueQuotes . $separator . $valueQuotes . $anrede2 . $valueQuotes . $separator . $valueQuotes . $v_name_2 . $valueQuotes . $separator . $valueQuotes . $n_name_2 . $valueQuotes . $separator . $valueQuotes . $startklasse . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $ve_name . $valueQuotes . $separator. $valueQuotes . $b_land . $valueQuotes . $separator . $valueQuotes . $rfid1 . $valueQuotes . $separator . $valueQuotes . $rfid2 . $valueQuotes; 
 	    $str_body .= "\r\n";


    if($gueltig == 1)
 	 $str_str = $str_str . $str_body;
 	 
 	 unset($str_body);
 	 unset($sb_num);
     unset($anrede1);
     unset($anrede2);	 	 
     unset($n_name_1);
     unset($n_name_2);   
     unset($v_name_1);
     unset($v_name_2);
     unset($startklasse);		   
     unset($ve_num);
     unset($ve_name);
     unset($b_land);
     unset($gueltig); 	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;
 	 	 
// 	 echo $inhalt . "<br><br>";        
 
     // Dateinamen festlegen
    
     $filename = "RR-Start-Daten-Paare.txt";
     // Datei auf Server speichern
     $fn = "../../cms/images/Download/TurnierProgramm/" . $filename;
      if (is_file($fn)) 
         unlink($fn);

     $fp = fopen($fn,"w"); 

     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>"; 
        
?>