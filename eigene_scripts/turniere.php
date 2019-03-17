<?php
//error_reporting (E_ALL);

echo"<h1>Turniere Exportieren!</h1>";

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

// Datum festlegen

$datum_tlp = time() - 900000 ; // 1 Tag

// echo $datum_tlp ."<br>";

$separator   = ";";
$valueQuotes = '"';

// Kopzeile schreiben
$str_csv = $str_csv. $valueQuotes. 'Terminnummer'. $valueQuotes.	
         $separator. $valueQuotes. 'Datum'. $valueQuotes.	
         $separator. $valueQuotes. 'Clubname_kurz'. $valueQuotes.	
         $separator. $valueQuotes. 'Mitgliedsnr'. $valueQuotes.
         $separator. $valueQuotes. 'Cup_Serie'. $valueQuotes.
         $separator. $valueQuotes. 'Bezeichnung'. $valueQuotes.	
         $separator. $valueQuotes. 'Raum'. $valueQuotes.	
         $separator. $valueQuotes. 'Straße'. $valueQuotes.	
         $separator. $valueQuotes. 'Ort'. $valueQuotes.	
         $separator. $valueQuotes. 'PLZ'. $valueQuotes.	
         $separator. $valueQuotes. 'Beginn'. $valueQuotes.	
         $separator. $valueQuotes. 'Ende'. $valueQuotes.
         $separator. $valueQuotes. 'Wettbewerbsart'. $valueQuotes.	
         $separator. $valueQuotes. 'Startklasse'. $valueQuotes.	
         $separator. $valueQuotes. 'Turnierleiter'. $valueQuotes.	
         $separator. $valueQuotes. 'Einschränkungen'. $valueQuotes.	
         $separator. $valueQuotes. 'Ansprechpartner'. $valueQuotes.	
         $separator. $valueQuotes. 'Tel_Ansprechpartner'. $valueQuotes.
         $separator. $valueQuotes. 'Beginn_Abendveranstaltung'. $valueQuotes;              			
$str_csv = $str_csv. "\r\n";


// Turnier finden
$sqlab = "SELECT * FROM adm_dates WHERE dat_cat_id = 31 AND dat_id > 5400";

/*2 016 ändern auf 
$sqlab = "SELECT * FROM adm_dates WHERE dat_cat_id = 31 AND dat_id > 4900";
*/
// echo"$sqlab<br>";
$turniere = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turniere))
 {
 	   unset($termine);
 	   unset($datum);
 	   unset($clubname);
 	   unset($vereinsnummer);
 	   unset($cup_serie);
 	   unset($bezeichnung);
 	   unset($raum);
 	   unset($strasse);
 	   unset($ort);
 	   unset($plz);
 	   unset($beginn);
 	   unset($ende);
       unset($str_body);
       unset($v_ort);
       unset($turnierleiter);	

//       print_r($temp);echo"<p>";          
       $termine = $temp[45];
       $datum = substr($temp[5],8,2) . "." . substr($temp[5],5,2) . "." . substr($temp[5],0,4);
       $clubname = $temp[38];
       $migliedsnr = $temp[39];
       $cup_serie = $temp[48];
       $bezeichnung = $temp[12];
       
       $beginn = substr($temp[5],11);
       $ende = substr($temp[6],11);
       $raum = $temp[49];
       $strasse = $temp[50];
       $plz = $temp[51];
       $ort = $temp[52];
       $ver_dat = strtotime($temp[5]);
       $turnierleiter = $temp[31];
       
// echo $ver_dat . "<br>";
 
// Daten schreiben    
 
  if($ver_dat >= $datum_tlp && $termine > 1)
    {            
 	    $str_body .= $valueQuotes . $termine . $valueQuotes . $separator . $valueQuotes . $datum . $valueQuotes . $separator . $valueQuotes . $clubname . $valueQuotes . $separator . $valueQuotes . $migliedsnr . $valueQuotes . $separator . $valueQuotes . $cup_serie . $valueQuotes . $separator .$valueQuotes . $bezeichnung . $valueQuotes . $separator. $valueQuotes . $raum . $valueQuotes . $separator. $valueQuotes . $strasse . $valueQuotes . $separator. $valueQuotes . $ort . $valueQuotes . $separator. $valueQuotes . $plz . $valueQuotes . $separator. $valueQuotes . $beginn . $valueQuotes . $separator. $valueQuotes . $ende . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . $turnierleiter . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes . $separator. $valueQuotes . '' . $valueQuotes;
 	    
 	    $str_body .= "\r\n";

 	 $str_str = $str_str . $str_body;
    } 	 
// 	 echo"$str_str<p>";
// Ende while   
 }

 	 $inhalt = $str_csv . $str_str;
/* 	 	 
// 	 echo $inhalt. "<br><br>";    
// Dateinamen festlegen
     $filename = "Termine-Start-Daten.csv";
     // Datei auf Server speichern
     $fn = "../../cms/images/Download/TurnierProgramm/" . $filename;
      if (is_file($fn)) 
         unlink($fn);

     $fp = fopen($fn,"w"); 

     fwrite($fp, $inhalt);
     fclose($fp);
     if(is_file($fn))
     echo"Die Datei $filename wurde gespeichert!<br><br>";   
*/     
// Dateinamen festlegen
     $filename = "Termine-Start-Daten.txt";
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