<?php
// error_reporting (E_ALL);

echo"<h1>Funktion�re zur GS Exportieren!</h1>";

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

$separator   = ";";
$valueQuotes = '"';

// Kopfzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Nachname'. $valueQuotes.	
         $separator. $valueQuotes. 'Vorname'. $valueQuotes.
         $separator. $valueQuotes. 'Clubnr' . $valueQuotes.	
         $separator. $valueQuotes. 'Stra�e'. $valueQuotes.
         $separator. $valueQuotes. 'PLZ'. $valueQuotes.	
         $separator. $valueQuotes. 'Ort'. $valueQuotes.
         $separator. $valueQuotes. 'TelG'. $valueQuotes.         				
         $separator. $valueQuotes. 'TelP'. $valueQuotes.
         $separator. $valueQuotes. 'e-mail_privat'. $valueQuotes.
         $separator. $valueQuotes. 'Handy'. $valueQuotes.		     
$str_csv = $str_csv. "\r\n";

// Pr�sidium

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 4";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];
   	       
 //  	       print_r($feld);echo"<br>";

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert; 
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes; 
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Sportausschu�

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 5";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Hauptausschu�

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 6";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Dozenten

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 10";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Trainer-A

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 11";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;
 	 
// Turnierleiter

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 101";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;   
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Wertungsrichter

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 124";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Jugendausschu�

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 307";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      { 		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;   
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;


// Trainer-B

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 347";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;   
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Trainer C

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 367";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Trainer B BW

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 366";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Trainer C BW

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 368";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Trainer C Breitensport

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 369";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Trainer C BW Breitensport

$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 4";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;

// Kopiervorlage

// Rolle
/*
$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = 4";
    
$verein = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($verein))
   {
   	$verein_id = $temp[0];
   	$sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $verein_id";
   	$ergebnis = mysqli_query($db, $sqlab);
   	while($feld = mysqli_fetch_array($ergebnis))
   	      {	   		   			
   	       $temp_id = $feld[0];
   	       $temp_wert = $feld[1];

           if($temp_id == 1)
   	          $name = $temp_wert;
           if($temp_id == 2)
   	          $vorname = $temp_wert; 	
   	       if($temp_id == 7)
   	          $tel_g = $temp_wert;         	         
   	       if($temp_id == 8)
   	          $handy = $temp_wert;  
           if($temp_id == 12)
   	          $mail_priv = $temp_wert;    	          
   	       if($temp_id == 53)
   	          $clubnummer = $temp_wert;     
    	   if($temp_id == 72)
   	          $strasse = $temp_wert;   	         	         
   	       if($temp_id == 73)
   	          $plz = $temp_wert;
   	       if($temp_id == 74)
   	          $ort = $temp_wert;  	           
   	       if($temp_id == 137)
   	          $tel_p = $temp_wert;
   	         } 
  	             
 	    $str_body .= $valueQuotes . $name . $valueQuotes . $separator . $valueQuotes . $vorname . $valueQuotes . $separator . $valueQuotes . $clubnummer . $valueQuotes . $separator . $valueQuotes . $strasse . $valueQuotes . $separator . $valueQuotes . $plz . $valueQuotes . $separator . $valueQuotes . $ort . $valueQuotes . $separator . $valueQuotes . $tel_p . $valueQuotes . $separator. $valueQuotes . $tel_g . $valueQuotes . $separator . $valueQuotes . $mail_priv . $valueQuotes . $separator . $valueQuotes . $handy . $valueQuotes;  
 	    
 	    $str_body .= "\r\n";
 	    

 	 $str_str = $str_str . $str_body;
 	 
     unset($name);
     unset($vorname);
     unset($clubnummer);
     unset($strasse);   
     unset($plz);
     unset($ort);
     unset($tel_p);
     unset($tel_g);
     unset($handy);
     unset($mail);
     unset($mail_priv);     
     unset($str_body);	        
   }  	    
          	      
 	 $inhalt = $str_csv . $str_str;
*/ 	 
// Ende Kopiervorlage 	 
 	 	 
// 	 echo $inhalt . "<br><br>";        
 
     // Dateinamen festlegen
    
     $filename = "./gs/funktionaere.txt";
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