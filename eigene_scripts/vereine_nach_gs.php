<?php
// error_reporting (E_ALL);

echo"<h1>Vereinsdaten zur GS Exportieren!</h1>";

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

$separator   = ";";
$valueQuotes = '"';

// Kopfzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Clubnr' . $valueQuotes.
         $separator. $valueQuotes. 'Internet_Adresse'. $valueQuotes.	
         $separator. $valueQuotes. 'Name'. $valueQuotes.	
         $separator. $valueQuotes. 'Vorname'. $valueQuotes.
         $separator. $valueQuotes. 'Titel'. $valueQuotes.	
         $separator. $valueQuotes. 'Straße'. $valueQuotes.
         $separator. $valueQuotes. 'PLZ'. $valueQuotes.	
         $separator. $valueQuotes. 'Ort'. $valueQuotes.
         $separator. $valueQuotes. 'Staat'. $valueQuotes.	
         $separator. $valueQuotes. 'TelC'. $valueQuotes.	
         $separator. $valueQuotes. 'TelG'. $valueQuotes.		
         $separator. $valueQuotes. 'TelP'. $valueQuotes.
         $separator. $valueQuotes. 'Handy'. $valueQuotes.	
         $separator. $valueQuotes. 'Mail'. $valueQuotes.		
         $separator. $valueQuotes. 'VS_Vorname'. $valueQuotes.
         $separator. $valueQuotes. 'VS_Nachname'. $valueQuotes.         
         $separator. $valueQuotes. 'VS_Tel'. $valueQuotes.          
         $separator. $valueQuotes. 'VS_Datum'. $valueQuotes;        
$str_csv = $str_csv. "\r\n";


//startbücher

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 102";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {
//  print_r($feld);echo"<br>";	   
		   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];
  	       
   	       if($temp_id == 7)
   	          $tel_c = $temp_wert;   	         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;
   	       if($temp_id == 12)
   	          $mail = $temp_wert;   	            	             
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert; 
   	       if($temp_id == 127)
   	          $name = $temp_wert; 
           if($temp_id == 128)
   	          $vorname = $temp_wert;   	          
           if($temp_id == 130)
   	          $website = $temp_wert;    	                  
           if($temp_id == 132)
   	          $vs_nachname = $temp_wert;
   	       if($temp_id == 133)
   	          $vs_vorname = $temp_wert;
   	       if($temp_id == 134)
   	          $vs_tel = $temp_wert;
           if($temp_id == 135)
   	          $vs_datum = $temp_wert;
   	          
   	         } 
  	             
 	    $str_body .= $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $website . $valueQuotes . $separator . $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $titel . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $staat . $valueQuotes . $separator . $valueQuotes . $tel_c . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes . $separator . $valueQuotes . $mail . $valueQuotes . $separator . $valueQuotes . $vs_vorname . $valueQuotes . $separator . $valueQuotes . $vs_nachname . $valueQuotes . $separator . $valueQuotes . $vs_tel . $valueQuotes . $separator . $valueQuotes . $vs_datum . $valueQuotes; 
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
 	 unset($clubnummer);
 	 unset($website);
     unset($name);
     unset($vorname);	 	 
     unset($titel);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($staat);		   
     unset($tel_c);
     unset($tel_g);
     unset($tel_p);
     unset($handy);
     unset($mail);
     unset($vs_vorname);		   
     unset($vs_nachname);
     unset($vs_datum);
     unset($vs_tel);
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;
 	 	 
// 	 echo $inhalt . "<br><br>";        
 
     // Dateinamen festlegen
    
     $filename = "./gs/vereine.txt";
     // Datei auf Server speichern
     $fn = $filename;
      if (is_file($fn)) 
         unlink($fn);

     $fp = fopen($fn,"w"); 

     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>"; 
        
?>