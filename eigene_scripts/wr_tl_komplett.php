<?php
// error_reporting (E_ALL);

echo"<h1>Turniere Exportieren!</h1>";

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

$separator   = ";";
$valueQuotes = '"';

// Kopzeile schreiben

$str_csv = $str_csv. $valueQuotes. 'WName' . $valueQuotes.
         $separator. $valueQuotes. 'WVorname'. $valueQuotes.
         $separator. $valueQuotes. 'Lizenz'. $valueQuotes.         
         $separator. $valueQuotes. 'Lizenzn'. $valueQuotes.         
         $separator. $valueQuotes. 'club'. $valueQuotes.         
         $separator. $valueQuotes. 'e-mail'. $valueQuotes;
         $separator. $valueQuotes. 'Strasse'. $valueQuotes;
         $separator. $valueQuotes. 'PLZ'. $valueQuotes;
         $separator. $valueQuotes. 'Ort'. $valueQuotes;
$str_csv = $str_csv. "\r\n";         

// Datum festlegen
$datum_unix = time() + 864000 ; // 9 Tage
$datum = date("Y-m-d", $datum_unix);
                 
//Turnierleiter

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 101 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";	   
		   		   			
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
   	       if($temp_id == 159) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;  	             
   	       if($temp_id == 170)
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0000" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4);       	          	          	          
   	      }
   	      
     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";  	      
   	        
     if($gueltig == 1)
       {  
 	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'TL' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes; 	       
 	    $str_body .= "\r\n";
       }    

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);  	        
   }  	    
     
// Wertungsrichter BWE

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 123 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";
		   
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
   	       if($temp_id == 163) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;
   	       if($temp_id == 172)
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0000" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4);     	          	          	          
   	      }  

     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";
           	      
     if($gueltig == 1)
       { 
 	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'WRE-BW' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes;   
 	    $str_body .= "\r\n";
       } 	    

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);   	        
   } 

// Wertungsrichter RRE

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 122 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";
		   
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
   	       if($temp_id == 161) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;   	          
   	       if($temp_id == 171)
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0000" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4);   	          	          	          
   	      }  

     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";
 
      if($gueltig == 1)
       { 
  	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'WRE-RR' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes;
 	    $str_body .= "\r\n";
       } 

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);   	        
   } 

// Wertungsrichter BWF

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 303 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);	
		   
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";
		   
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
   	       if($temp_id == 163) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;   	           
   	       if($temp_id == 172)   	       
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0000" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4); 	          	          	          
   	      }  
 
     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";
 
     if($gueltig == 1)
       {  
  	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'WRF-BW' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes; 
 	    $str_body .= "\r\n";
       }    

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);      
   } 

// Akro-Wertungsrichter Trainer A

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 11 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);	
		   
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";
		   
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
    	   if($temp_id == 164)
    	      $trainer_a = $temp_wert;    	          
   	       if($temp_id == 165) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;   	           
   	       if($temp_id == 173)   	       
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0007" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4);          	          	          
   	      }  
 
     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";
 
     if($gueltig == 1 && $trainer_a == 1)
       {  
  	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'WRA-RR' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes; 
 	    $str_body .= "\r\n";
       }    

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);      
   } 

// Akro-Wertungsrichter Trainer B

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 347 AND mem_end > '" .  $datum . "'";
    
$turnier_leiter = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turnier_leiter))
   {
   	$t_leiter_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $t_leiter_id";
   	$ergebnis = mysqli_query($db, $sqlab);	
		   
   	while($name = mysqli_fetch_array($ergebnis))
   	      {
//   	      print_r($name);echo"<br>";
		   
   	       $temp_id = $name[0];
   	       $temp_wert = $name[1];
   	       if($temp_id == 1)
   	          $n_name = $temp_wert;
   	       if($temp_id == 2)
   	          $v_name = $temp_wert;
   	       if($temp_id == 8)
   	          $telefon = $temp_wert;
   	       if($temp_id == 12)
   	          $email = $temp_wert;
   	       if($temp_id == 53)
   	          $ve_num = $temp_wert;  	           
   	       if($temp_id == 72)
   	          $strasse = $temp_wert;
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;
    	   if($temp_id == 164)
    	      $trainer_b = $temp_wert;   	          
   	       if($temp_id == 165) 
   	          $bis = strtotime($temp_wert);
   	          if($bis > $heute) 
   	             $gueltig = 1;   	           
   	       if($temp_id == 173)   	       
   	          $lizenz_nr = $temp_wert;
   	          if(strlen($lizenz_nr) < 4)
   	             $lizenz_nr = "0008" . $lizenz_nr;
   	          $lizenz_nr = substr($lizenz_nr, -4);        	          	          
   	      }  
 
     if($gueltig == 0)
        echo"$n_name $v_name hat keine gültige Lizenz<br>";
 
     if($gueltig == 1 && $trainer_b == 3)
       {  
  	    $str_body .= $valueQuotes . $n_name . $valueQuotes . $separator . $valueQuotes . $v_name . $valueQuotes . $separator . $valueQuotes . 'WRA-RR' . $valueQuotes . $separator . $valueQuotes . $lizenz_nr . $valueQuotes . $separator . $valueQuotes . $ve_num . $valueQuotes . $separator . $valueQuotes . $email . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes; 
 	    $str_body .= "\r\n";
       }    

 	 $str_str = $str_str . $str_body;
 	 unset($str_body);
     unset($n_name);
     unset($v_name);
     unset($telefon);
     unset($email);		   
     unset($ve_num);
     unset($lizenz_nr);
     unset($strasse);
     unset($plz);
     unset($ort);
     unset($gueltig);      
   }   
       	      
 	 $inhalt = $str_csv . $str_str;
 	 	 

     // Dateinamen festlegen
    
     $filename = "WR-TL-komplett.txt";
     // Datei auf Server speichern
     $fn = "./" . $filename;
      if (is_file($fn)) 
         unlink($fn);

     $fp = fopen($fn,"w"); 

     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>";    
?>