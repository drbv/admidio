<?php
//error_reporting (E_ALL);

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");


$separator   = ";";
$valueQuotes = '"';
$org = "1/2";
$neu = "halb ";
$org_1 = "/";
$neu_1 = "-";
// Kopzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Nr#'. $valueQuotes.	
         $separator. $valueQuotes. 'Akrobatik'. $valueQuotes.	
         $separator. $valueQuotes. 'Langtext'. $valueQuotes.	
         $separator. $valueQuotes. 'Einstufung'. $valueQuotes.	
         $separator. $valueQuotes. 'RR_A'. $valueQuotes.	
         $separator. $valueQuotes. 'RR_B'. $valueQuotes.	
         $separator. $valueQuotes. 'RR_C'. $valueQuotes.	
         $separator. $valueQuotes. 'RR_J'. $valueQuotes.	
         $separator. $valueQuotes. 'RR_S'. $valueQuotes.	
         $separator. $valueQuotes. 'F_RR_M'. $valueQuotes.	
         $separator. $valueQuotes. 'F_RR_J'. $valueQuotes.	
         $separator. $valueQuotes. 'F_RR_LF'. $valueQuotes.	
         $separator. $valueQuotes. 'F_RR_GF'. $valueQuotes.	
         $separator. $valueQuotes. 'F_RR_ST'. $valueQuotes.	
         $separator. $valueQuotes. 'Gruppen_ID_1'. $valueQuotes.	
         $separator. $valueQuotes. 'Gruppen_ID_2'. $valueQuotes.	
         $separator. $valueQuotes. 'Gruppen_ID_3'. $valueQuotes.	
         $separator. $valueQuotes. 'Gruppen_ID_4'. $valueQuotes.	
         $separator. $valueQuotes. 'Gruppen_ID_5'. $valueQuotes.	
         $separator. $valueQuotes. 'Bemerkung'. $valueQuotes;				
$str_csv = $str_csv. "\r\n";

  	
// Akrolisten einlesen

// Akroliste RR_A
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 125"; 
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_A_komp = explode("\n", $temp[0]);
$x = count($liste_A_komp);   
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_A_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
//        print_r($werte);echo"<br>";
//        print_r($org_ids);echo"<br>";
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)      
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";
 /*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */                 
//  echo $werte[2] . " - " . $werte[3] ." <br>";     	
//  echo  "A" . $lt . " - " . $kt ." <br><p />";
// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   


 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }

// Akroliste RR_B
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 124";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_B_komp = explode("\n", $temp[0]);
$x = count($liste_B_komp);   
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_B_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)     
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";  
/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */	

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }
      
      
// Akroliste RR_C
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 121";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_C_komp = explode("\n", $temp[0]);
$x = count($liste_C_komp);     
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_C_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids); 
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)       
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";  

/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */  

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }
      

// Akroliste RR_C_int
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 145";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_C_komp = explode("\n", $temp[0]);
$x = count($liste_C_komp);     
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_C_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)       
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";  

/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */ 

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . "Int " . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }

            
// Akroliste RR_J
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 122";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_J_komp = explode("\n", $temp[0]);
$x = count($liste_J_komp);     
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_J_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)       
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";  

/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }      
      
// Akroliste RR_S
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 123";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_S_komp = explode("\n", $temp[0]);
$x = count($liste_S_komp);     
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_S_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)      
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";
         
/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */      	

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      } 
           
// Akroliste F_RR_M
$sqlab = "SELECT usf_value_list FROM adm_user_fields WHERE usf_id = 126";
$akrolisten = mysqli_query($db, $sqlab);

$temp = mysqli_fetch_array($akrolisten);

$liste_F_RR_M_komp = explode("\n", $temp[0]);
$x = count($liste_F_RR_M_komp);     
$i = 0;

while($i < $x)
      {
       $werte =	explode(":", $liste_F_RR_M_komp[$i]);
       if($werte[0] == "")
      	  break;

       unset($org_ids);
       $org_ids = explode(" ", $werte[4]);
       if(substr($werte[1],1,1) == 8 || substr($werte[1],1,1) == 9 || substr($werte[1],1,2) == 10)       
         $einstufung = "S";
       if(substr($werte[1],1,1) >= 4 && substr($werte[1],1,1) < 8)      
         $einstufung = "M";
       if(substr($werte[1],1,1) < 4 && substr($werte[1],1,2) < 10)      
         $einstufung = "L";  

/*        
        $lt = str_replace($org,$neu,$werte[2]); 
        $kt = str_replace($org,$neu,$werte[3]);   

        $lt = str_replace($org_1,$neu_1,$lt); 
        $kt = str_replace($org_1,$neu_1,$kt);	   	
 */    	

// Versuch
   $lt = $werte[2]; 
   $kt = $werte[3];   
   
 	    $str_body .= $valueQuotes . trim($werte[0]) . $valueQuotes . $separator . $valueQuotes . trim($kt) . $valueQuotes . $separator . $valueQuotes . trim($lt) . $valueQuotes . $separator . $valueQuotes . $einstufung . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($werte[1]) . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . trim($org_ids[1]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[2]) . $valueQuotes . $separator . $valueQuotes . trim($org_ids[3]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[4]) . $valueQuotes . $separator. $valueQuotes . trim($org_ids[5]) . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 	    
 	   $i++;
      }
 
     
 // Akro nicht gemeldet
 
unset($org_ids);

 	    $str_body .= $valueQuotes . 'ALL' . $valueQuotes . $separator . $valueQuotes . 'Akro n gem' . $valueQuotes . $separator . $valueQuotes .  'Akro nicht gemeldet' . $valueQuotes . $separator . $valueQuotes . 'X' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0,01' . $valueQuotes . $separator. $valueQuotes . '0'. $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator . $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
 
      
 // Akroliste ALL

for($i = 1;  $i<=8; $i++)
      {
 	    $str_body .= $valueQuotes . 'ALL' . $valueQuotes . $separator . $valueQuotes . 'Akrobatik ' . $i . $valueQuotes . $separator . $valueQuotes .  'Akrobatik ' . $i . $valueQuotes . $separator . $valueQuotes . 'X' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '10,00' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator . $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes . '0' . $valueQuotes . $separator. $valueQuotes. ''. $valueQuotes;
 	    $str_body .= "\r\n";
      }      

      
// Dateien schreiben
 	 $inhalt = $str_csv . $str_body;
    
     // Dateinamen festlegen
     $filename = "DRBV-Akrotabelle-12P.txt";
     // Datei auf Server speichern
     $fn = "../../cms/images/Download/TurnierProgramm/" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>";
     
// Kopie speichern
     // Dateinamen festlegen
     $filename = "DRBV-Akrotabelle_" . date('Y-m-d') . ".txt";
     // Datei auf Server speichern
     $fn = "./kopien_akros/" . $filename;
      if (is_file($fn)) 
         unlink($fn);
     $fp = fopen($fn,"w"); 
     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>";
 
?>