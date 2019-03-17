<?php 

date_default_timezone_set("Europe/Berlin");

$inhalt = "Beginn des Scripts!\n\n";           
$inhalt .= "Kontrollmail!\n";
   	
// mail('webmaster@drbv.de','Startlisten für TLP',$inhalt,'From: webmaster@drbv.de');
   		 
require("./intern/dboeffnen.inc.php");

function lastModification ( $dir, $todo = 'new', $format = 'd.m.Y H:i:s' )
 { 
     
    if ( is_file ( $dir ) ) 
        return false; 
         
    $lastfile = ''; 
     
    if( strlen( $dir ) - 1 != '\\' || strlen( $dir ) - 1 != '/' ) 
        $dir .= '/'; 
         
    $handle = @opendir( $dir ); 
     
    if( !$handle ) 
        return false; 
         
    while ( ( $file = readdir( $handle ) ) !== false ) { 
         
        if( $file != '.' && $file != '..' && is_file ( $dir.$file ) ) { 
             
            if ( $todo == 'old' ) { 
                 
                if( filemtime( $dir.$file ) <= filemtime( $dir.$lastfile ) ) { 
                     
                       $lastfile = $file; 
                        
                 } 
                  
            } 
            else { 
                 
                if( filemtime( $dir.$file ) >= filemtime( $dir.$lastfile ) ) { 
                     
                       $lastfile = $file; 
                        
                 } 
                 
            } 
             
            if ( empty( $lastfile ) ) 
                $lastfile = $file; 
             
        } 
       
    } 
     
    $fileInfo['formattime'] = date( $format, filemtime( $dir.$lastfile ) ); 
     
     
    closedir( $handle ); 

      return $fileInfo; 
         
} 

$datum = date("dmY");
$datum_unix = time() + 777600 ; // 9 Tage
$turnier_datum = date("Y-m-d", $datum_unix);
// $turnier_datum = date("Y-m-d");
// $turnier_datum = "2015-10-31";
echo"Datum: $datum Turnier-Datum: $turnier_datum<br>";

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

  
// Daten schreiben      
  if(substr($beginn,0,10) == $turnier_datum && $kategorie == 31)
    {
     echo substr($beginn,0,10) ."<br>";
    	
     $directory = '../../cms/images/Download/TurnierProgramm/startlisten'; 
     $neueste_datei = lastModification( $directory, 'new', 'dmY');
     $letzte_datei = $neueste_datei["formattime"];
     echo"Letzte Datei: $letzte_datei <br>";

     if($datum == $letzte_datei)
        {
         $inhalt = "Die Startlisten wurden geschrieben!\n\n";           
         $inhalt .= "Bitte kontrollieren!\n";
   	
   		 // mail('webmaster@drbv.de','Startlisten für TLP',$inhalt,'From: webmaster@drbv.de');
        }
     else
        {
         $inhalt = "Beim schreiben der Startlisten ist ein Fehler aufgetreten!\n\n";           
         $inhalt .= "Bitte kontrollieren!\n";
   	
   		 mail('xxx@xxx.xx','Fehler Startlisten für TLP',$inhalt,'From: webmaster@drbv.de');
        }
    }
 }
  
  
    
?>