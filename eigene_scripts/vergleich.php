<?php

function lastModification($dir, $filename, $laenge )
 { 
  if(is_file($dir)) 
     return false; 
         
    $lastfile = ''; 
     
    if( strlen( $dir ) - 1 != '\\' || strlen( $dir ) - 1 != '/' ) 
        $dir .= '/'; 
         
    $handle = @opendir($dir); 
     
    if(!$handle) 
        return false; 
         
    while(($file = readdir($handle)) !== false)
          {

           if($file != '.' && $file != '..' && is_file($dir.$file))
              {      
               if(filemtime($dir.$file) >= filemtime($dir.$lastfile))
                  {
                   if(substr($file, 0, $laenge) == $filename)
                    $lastfile = $file;
                  } 
   
            if(empty($lastfile)) 
               $lastfile = $file;
              } 
           }    
 
    $fileInfo = $dir.$lastfile; 

     
     
    closedir( $handle ); 

      return $fileInfo; 
         
 }

$datum = date("d-m-Y");

// Startbücher Boogie

// Dateinamen festlegen

$filename = (lastModification("./kopien", "startbuch-Export-boogie", 23) );

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file .= $Daten;
      }
           
fclose($fn);  
   
      
$filename_1 = "./gs/startbuch-Export-boogie" . ".txt";
$fn_1 = $filename_1;
$fp_1 = fopen($fn_1,"r");
     
$filename_w = "./unbearbeitet/startbuch-Export-boogie_" . $datum . ".txt";
$fn_w = $filename_w;
     if (is_file($fn_w)) 
         unlink($fn_w);      
$fp_w = fopen($fn_w,"w");
             
echo "<h2>Startbücher Boogie</h2>";

$Daten_1 = fgets($fp_1, 1000);
fwrite($fp_w, $Daten_1);
    
       while(($Daten_1 = fgets($fp_1, 1000)) !== FALSE)
             {	
               if(substr_count($gesamt_file, $Daten_1) < 1)	
              	 {
              	  echo"$zeilen $Daten_1<br>";	
                  fwrite($fp_w, $Daten_1);
                  $bw = $bw + 1;
              	 }

             }                 
fclose($fn_1);
fclose($fn_w);

if($bw == 0)
   unlink($fn_w);              
unset($gesamt_file);

if($bw != 0)
copy($filename_1, "./kopien/startbuch-Export-boogie_" . $datum . ".txt");

unset($filename_1);

// Startbücher Rock´n´Roll

// Dateinamen festlegen

$filename = (lastModification("./kopien", "startbuch-Export-RR", 19) );

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file .= $Daten;
      }     
fclose($fn);    
      
$filename_1 = "./gs/startbuch-Export-RR" . ".txt";
$fn_1 = $filename_1;
$fp_1 = fopen($fn_1,"r");
     
$filename_w = "./unbearbeitet/startbuch-Export-RR_" . $datum . ".txt";
$fn_w = $filename_w;
     if (is_file($fn_w)) 
         unlink($fn_w);      
$fp_w = fopen($fn_w,"w");
             
echo "<h2>Startbücher Rock´n´Roll</h2>";

$Daten_1 = fgets($fp_1, 1000);
fwrite($fp_w, $Daten_1);
    
       while(($Daten_1 = fgets($fp_1, 1000)) !== FALSE)
             {		
               if(substr_count($gesamt_file, $Daten_1) < 1)	
              	 {
              	  echo"$zeilen $Daten_1<br>";	
                  fwrite($fp_w, $Daten_1);
                  $rr = $rr + 1;
              	 }

             }                 

fclose($fn_1);
fclose($fn_w);

if($rr == 0)
   unlink($fn_w);              
unset($gesamt_file);

if($rr != 0)
copy($filename_1, "./kopien/startbuch-Export-RR_" . $datum . ".txt");

unset($filename_1);

// Startbücher Formationen

// Dateinamen festlegen

$filename = (lastModification("./kopien", "startbuch-Export-formationen", 28) );

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file .= $Daten;
      }     
fclose($fn);     
      
$filename_1 = "./gs/startbuch-Export-formationen" . ".txt";
$fn_1 = $filename_1;
$fp_1 = fopen($fn_1,"r");
     
$filename_w = "./unbearbeitet/startbuch-Export-formationen_" . $datum . ".txt";
$fn_w = $filename_w;
     if (is_file($fn_w)) 
         unlink($fn_w);      
$fp_w = fopen($fn_w,"w");
             
echo "<h2>Startbücher Formationen</h2>";

$Daten_1 = fgets($fp_1, 1000);
fwrite($fp_w, $Daten_1);
    
       while(($Daten_1 = fgets($fp_1, 1000)) !== FALSE)
             {		
               if(substr_count($gesamt_file, $Daten_1) < 1)	
              	 {
              	  echo"$zeilen $Daten_1<br>";	
                  fwrite($fp_w, $Daten_1);
                  $fo = $fo + 1;
              	 }

             }                 

fclose($fn_1);
fclose($fn_w);

if($fo == 0)
   unlink($fn_w);              
unset($gesamt_file);

if($fo != 0)
copy($filename_1, "./kopien/startbuch-Export-formationen_" . $datum . ".txt");

unset($filename_1);

// Adressen Vereine

// Dateinamen festlegen

$filename = (lastModification("./kopien", "adressen-vereine", 16) );

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file .= $Daten;
      }     
fclose($fn);  

// echo"$gesamt_file<br>";    
      
$filename_1 = "./gs/adressen-vereine" . ".txt";
$fn_1 = $filename_1;
$fp_1 = fopen($fn_1,"r");
     
$filename_w = "./unbearbeitet/adressen-vereine_" . $datum . ".txt";
$fn_w = $filename_w;
     if (is_file($fn_w)) 
         unlink($fn_w);      
$fp_w = fopen($fn_w,"w");
             
echo "<h2>Vereine</h2>";

$Daten_1 = fgets($fp_1, 1000);
fwrite($fp_w, $Daten_1);
    
       while(($Daten_1 = fgets($fp_1, 1000)) !== FALSE)
             {		
               if(substr_count($gesamt_file, $Daten_1) < 1)	
              	 {
              	  echo"$zeilen $Daten_1<br>";	
                  fwrite($fp_w, $Daten_1);
                  $ve = $ve + 1;
              	 }

             }                 

fclose($fn_1);
fclose($fn_w);

if($ve == 0)
   unlink($fn_w);              
unset($gesamt_file);

if($ve != 0)
copy($filename_1, "./kopien/adressen-vereine_" . $datum . ".txt");

unset($filename_1);

// Funktionäre

// Dateinamen festlegen

$filename = (lastModification("./kopien", "Funktionaere-Export", 19) );

$fn = $filename;
$fp = fopen($fn,"r"); 

while(($Daten = fgets($fp, 1000)) !== FALSE)
      {
       $gesamt_file .= $Daten;
      }     
fclose($fn);  

// echo"$gesamt_file<br>";    
      
$filename_1 = "./gs/Funktionaere-Export" . ".txt";
$fn_1 = $filename_1;
$fp_1 = fopen($fn_1,"r");
     
$filename_w = "./unbearbeitet/Funktionaere-Export_" . $datum . ".txt";
$fn_w = $filename_w;
     if (is_file($fn_w)) 
         unlink($fn_w);      
$fp_w = fopen($fn_w,"w");
             
echo "<h2>Funktionäre</h2>";

$Daten_1 = fgets($fp_1, 1000);
fwrite($fp_w, $Daten_1);
    
       while(($Daten_1 = fgets($fp_1, 1000)) !== FALSE)
             {		
               if(substr_count($gesamt_file, $Daten_1) < 1)	
              	 {
              	  echo"$zeilen $Daten_1<br>";	
                  fwrite($fp_w, $Daten_1);
                  $fu = $fu + 1;
              	 }

             }                 

fclose($fn_1);
fclose($fn_w);

if($fu == 0)
   unlink($fn_w);              
unset($gesamt_file);

if($fu != 0)
copy($filename_1, "./kopien/Funktionaere-Export_" . $datum . ".txt");

unset($filename_1);

?>

