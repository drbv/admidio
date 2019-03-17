<?php
//error_reporting (E_ALL);
        
date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");
require_once("./intern/bereinigen.php");

$separator   = ";";
$valueQuotes = '"';

// Datum festlegen
$datum_unix = time() + 777600 ; // 9 Tage
$datum = date("Y-m-d", $datum_unix);


// Datum manuell festlegen
echo' <form method="POST" action="' . $_SERVER["PHP_SELF"] . '"> ';
echo'<h3>Bitte gib das Datum im Format: JJJJ-MM-DD ein!</h3><br>';
echo'<input name = "datum" size="10" maxlength="10">';
echo'<input type="submit" name="senden" value="Absenden"/>';
echo"</form>";

if($_POST["datum"])
   {
    $man_datum = strtotime($_POST["datum"]);
    if($man_datum > $datum_unix)
       $datum = $_POST["datum"];
   }
echo $datum ."<br>";

// Turnier finden
$sqlab = "SELECT dat_cat_id, dat_rol_id, dat_begin, dat_headline, dat_turniernummer, dat_tl, dat_ansprechpartner, dat_mail FROM adm_dates";

// echo"$sqlab<br>";
$turniere = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turniere))
 {
  $kategorie = $temp[0];	
  $turnier_id = $temp[1];
  $beginn = $temp[2];
  $turnier = $temp[3];
  $t_num = $temp[4];
  $v_datum = substr($beginn,8,2) . "." . substr($beginn,5,2) . "." . substr($beginn,0,4);
  $turnierleiter = $temp[5];
  $apartner = $temp[6];
  $mail = $temp[7];
    
  unset($str_body);

$str_csv = $valueQuotes . "Startklasse" . $valueQuotes . $separator . $valueQuotes . "Vorname Dame" . $valueQuotes . $separator . $valueQuotes . "Name Dame" . $valueQuotes . $separator. $valueQuotes . "Vorname Herr" . $valueQuotes . $separator. $valueQuotes . "Name Herr" . $valueQuotes . $separator . $valueQuotes . "Verein" . $valueQuotes . $separator . $valueQuotes . "Team Name" . $valueQuotes . $separator . $valueQuotes . "E-Mail" . $valueQuotes . $separator . $valueQuotes . "Musik Fusstechnik" . $valueQuotes . $separator . $valueQuotes . "Musik Akrobatik" . $valueQuotes . $separator . $valueQuotes . "Musik Stellprobe" . $valueQuotes . $separator . $valueQuotes . "Musik Tanzmusik" . $valueQuotes . $separator . $valueQuotes . "Musik Ersatzmusik" . $valueQuotes . "\r\n";

$betreff = $turnier . " am " . $v_datum;
 
// Daten schreiben      
  if(substr($beginn,0,10) == $datum && $kategorie == 31)
    {
//     echo"$beginn, $turnier_id, $t_num<br>";
     
	 $sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = $turnier_id AND mem_leader = 0 AND mem_end > '" .  $datum . "' ";
//	 echo"$sqlab<br>";
	 $teilnehmer = mysqli_query($db, $sqlab);
	 
	 while($t_paar = mysqli_fetch_array($teilnehmer))
 	  {
 	    	  	
       $teilnehmer_id = $t_paar[0];
//	   echo"<br>Paar: $teilnehmer_id<br>";

// unset alle Variablen

 	   unset($paar_mail);	   
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
	   
	   	 $sqlab = "SELECT usd_usf_id, usd_value FROM adm_user_data WHERE usd_usr_id = $teilnehmer_id";
	   	 $daten = mysqli_query($db, $sqlab);
 	     $startnummer = $startnummer + 1;	
 	        	 
	   	 while($meldung = mysqli_fetch_array($daten))
 	      {	
 	       $datenfeld = $meldung[0];
 	       $datenwert = $meldung[1];
 	       
// 	       echo"$datenfeld - $datenwert<br>";

 	       if($datenfeld == 12)
 	          $paar_mail = $datenwert;
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
 	       if($datenfeld == 54)
 	          $team_name = $datenwert; 

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
	   	            
 	                // Startklasse auslesen
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
 	                                    $nn_dame = $daten_dame[1];	          
 	                                 if($daten_dame[0] == 38)
 	                                    $vn_dame = $daten_dame[1];
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
							  } 						      						     
 						  }
 					if($dame_jk + $dame_hk + $dame_ok < 1)
 					   echo"<p>Startbuch: $startbuch_nr_h Startklasse: $startklasse Verein: $vereinsname: Ungültige Startmeldung!<p>";	  
 						  
 					$bw_main_senior = $bw_jun + $bw_main + $bw_senior;
 					// echo"Jugend: $bw_jun, Main: $bw_main, Senior: $bw_senior, Summe: $bw_main_senior<br>"; 
 	               }
 	            }
                 	          
 	            }
 	            
 	       if($datenfeld == 152)
 	         {
 	          $Musik_FT = "http://www.drbv.de/turniermusik/index.php?file=" . $datenwert . ".mp3";
 	         } 	             	                   	          	                      	           	            	       if($datenfeld == 153)
 	         {
 	          $Musik_Akro = "http://www.drbv.de/turniermusik/index.php?file=" . $datenwert . ".mp3"; 	          
 	         }
	       if($datenfeld == 154)
 	         {
 	          $Musik_Stell = "http://www.drbv.de/turniermusik/index.php?file=". $datenwert . ".mp3"; 	          
 	         } 
	       if($datenfeld == 155)
 	         {
 	          $Musik_Form = "http://www.drbv.de/turniermusik/index.php?file=". $datenwert . ".mp3";  	          
 	         }
	       if($datenfeld == 156)
 	         {
 	          $Musik_Sieg = "http://www.drbv.de/turniermusik/index.php?file=". $datenwert . ".mp3";
 	         } 	
 	      }

     if($startklasse != "BW_A" && $startklasse != "BW_B")
        {            
         if($gueltig == 1)
           {      
 	        $str_body .= $valueQuotes . $startklasse . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator . $valueQuotes . $vereinsname . $valueQuotes . $separator . $valueQuotes . $team_name . $valueQuotes . $separator . $valueQuotes . $paar_mail . $valueQuotes . $separator . $valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator . $valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . "\r\n";
           }
        }
       
     if(($bw_main_senior == 4 || $bw_main_senior == 5) && $startklasse == "BW_A")
        {
         $startklasse_j =  substr($startklasse,0,3) . "JA";
                 
         if($gueltig == 1)
           {      
 	        $str_body .= $valueQuotes . $startklasse_j . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator . $valueQuotes . $vereinsname . $valueQuotes . $separator . $valueQuotes . $team_name . $valueQuotes . $separator . $valueQuotes . $paar_mail . $valueQuotes . $separator . $valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator . $valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . "\r\n";
           }
        }
        

     if(($bw_main_senior == 1 || $bw_main_senior == 3 || $bw_main_senior == 5) && ($startklasse == "BW_A" || $startklasse == "BW_B"))
        {
         $startklasse_m =  substr($startklasse,0,3) . "M" . substr($startklasse, -1);
                 
         if($gueltig == 1)
           {      
 	        $str_body .= $valueQuotes . $startklasse_m . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator . $valueQuotes . $vereinsname . $valueQuotes . $separator . $valueQuotes . $team_name . $valueQuotes . $separator . $valueQuotes . $paar_mail . $valueQuotes . $separator . $valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator . $valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . "\r\n";
           }
        }

     if(($bw_main_senior == 2 || $bw_main_senior == 3) && ($startklasse == "BW_A" || $startklasse == "BW_B"))
        { 
         $startklasse_s = substr($startklasse,0,3) . "S" . substr($startklasse, -1);
         if($gueltig == 1)
           {      
 	        $str_body .= $valueQuotes . $startklasse_s . $valueQuotes . $separator . $valueQuotes . $vn_dame . $valueQuotes . $separator . $valueQuotes . $nn_dame . $valueQuotes . $separator. $valueQuotes . $vn_herr . $valueQuotes . $separator. $valueQuotes . $nn_herr . $valueQuotes . $separator . $valueQuotes . $vereinsname . $valueQuotes . $separator . $valueQuotes . $team_name . $valueQuotes . $separator . $valueQuotes . $paar_mail . $valueQuotes . $separator . $valueQuotes . $Musik_FT . $valueQuotes . $separator. $valueQuotes . $Musik_Akro . $valueQuotes . $separator . $valueQuotes . $Musik_Stell . $valueQuotes . $separator. $valueQuotes . $Musik_Form . $valueQuotes . $separator . $valueQuotes . $Musik_Sieg . $valueQuotes . "\r\n";
           }
        }
         
 	  }
 	  
    $inhalt = $str_csv . $str_body; 
    
   
// Dateinamen festlegen
     $filename = $t_num . "_" . time() . ".csv";
     // Datei auf Server speichern
     $fn = "../../teilnehmer/" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
//     fwrite($fp, mb_convert_encoding($inhalt, "UTF-8", "ISO-8859-1"));
     fwrite($fp,$inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br>";
    
     // Dateinamen festlegen
     $datei = "Startlisten.txt";
     $nl = chr(13) . chr(10);     // neue Zeile
     $link = '<a href="http://drbv.de/teilnehmer/index.php?file=' . $filename . '">Teilnehmerliste ' . $t_num . '</a> '. $turnier ;
     // Datei auf Server speichern
     $fn =  $datei;
     $fp = fopen($fn,"a"); 
     fwrite($fp,$link);
     fwrite($fp,$nl);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $datei wurde gespeichert!<br>";
     
// Turnierleiter Mail finden

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
   	       if($temp_id == 12)
   	          $email = $temp_wert;          	          	          
   	      }
    if(($v_name . " " . $n_name) == $turnierleiter)
        $turnierleiter_mail = $email; 
     
   }    
     
//     echo"$mail - Betreff: $betreff<br>";
     $mail_verein = "Hallo $apartner,\n\n";
     $mail_tl = "Hallo $turnierleiter,\n\n";
          
     $mail_inhalt = "unter dem Link: http://drbv.de/teilnehmer/index.php?file=$filename\n";
     $mail_inhalt .= "steht die Teilnehmerliste inclusive E-Mail-Adressen fuer Dich zum Download bereit.\n\n";
     $mail_inhalt .= "Diese Datei ist nicht fuer den oeffentlichen Gebrauch bestimmt sondern nur fuer Dich\n";    
     $mail_inhalt .= "falls Du mit den Tanzpaaren wegen Unstimmigkeiten oder zeitlichen Aenderungen\n";
     $mail_inhalt .= "kommunizieren musst.\n\n";
     $mail_inhalt .= "Den Musikdownloader findest Du hier:\nhttp://www.drbv.de/cms/images/Download/TurnierProgramm/Musiktool/MusicDownloader4TL.zip\n\n";
     $mail_inhalt .= "Mit freundlichen Gruessen\n\n";
     $mail_inhalt .= "DRBV\n";

// E-Mails versenden

    $absender = "xxx@xxx.xx";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 


    $inhalt_verein = $mail_verein . $mail_inhalt;
    $inhalt_tl = $mail_tl . $mail_inhalt; 

@mail($absender, $betreff, $inhalt_tl, $absender_mail, $absender_path);
@mail($mail, $betreff, $inhalt_verein, $absender_mail, $absender_path);
@mail($turnierleiter_mail, $betreff, $inhalt_tl, $absender_mail, $absender_path);

    }  
// Ende while          
   }
  
?>