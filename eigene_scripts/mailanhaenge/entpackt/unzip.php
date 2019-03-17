<?php

require("../../intern/dboeffnen.inc.php");

function unzip($file){

    $zip=zip_open(realpath(".")."/".$file);
    if(!$zip) {return("Unable to proccess file '{$file}'");}

    $e='';

    while($zip_entry=zip_read($zip)) {
       $zdir=dirname(zip_entry_name($zip_entry));
       $zname=zip_entry_name($zip_entry);

       if(!zip_entry_open($zip,$zip_entry,"r")) {$e.="Unable to proccess file '{$zname}'";continue;}
       if(!is_dir($zdir)) mkdirr($zdir,0777);

       #print "{$zdir} | {$zname} \n";

       $zip_fs=zip_entry_filesize($zip_entry);
       if(empty($zip_fs)) continue;

       $zz=zip_entry_read($zip_entry,$zip_fs);

       $z=fopen($zname,"w");
       fwrite($z,$zz);
       fclose($z);
       zip_entry_close($zip_entry);
    } 
    zip_close($zip);

    return($e);
} 

function mkdirr($pn,$mode=null) {

  if(is_dir($pn)||empty($pn)) return true;
  $pn=str_replace(array('/', ''),DIRECTORY_SEPARATOR,$pn);

  if(is_file($pn)) {trigger_error('mkdirr() File exists', E_USER_WARNING);return false;}

  $next_pathname=substr($pn,0,strrpos($pn,DIRECTORY_SEPARATOR));
  if(mkdirr($next_pathname,$mode)) {if(!file_exists($pn)) {return mkdir($pn,$mode);} }
  return false;
}

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
                   if(substr($file, -11) == $filename)
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

// Anhaenge verabeiten

for ($i = 0;$i<5;$i++)
     {
// Dateinamen finden 

     $dateiname = lastModification("../", "Zeitplan.csv", 12);
     if(substr($dateiname,-12,8) == "Zeitplan" && substr($dateiname, -4) == ".csv")
        {
         $dateiname_kurz = substr($dateiname,-20);
         copy($dateiname, "../../../../startlisten/" . $dateiname_kurz);
         if(is_file("../../../../startlisten/" . $dateiname_kurz))
            unlink($dateiname);         
        }
     }

for ($i = 0;$i<5;$i++)
     {
// Dateinamen finden 

     $dateiname = lastModification("../", "Versand.zip", 11);

     if(substr($dateiname,-20,2) == "T1" && substr($dateiname, -4) == ".zip")
        {      
         unzip("$dateiname");
        
        // Turniernummer ermitteln

         $turnier = substr($dateiname, -20, 8);
         $t_nummer = substr($dateiname, -19, 7);
             
// echo"Turnier: $turnier - Turniernummer: $t_nummer<br>";
         // nicht benötigte Dateien löschen

         $e_liste = "./" . $turnier . "_Ergebnisliste.html";
         unlink($e_liste);
         $rangliste = "./" . $turnier . "_Rangliste.xls";
         unlink($rangliste);

         // CSV Datei erzeugen

         // Hier kommt der Code für das lesen der Text-Datei und speichern der CSV-Datei

         // Dateinamen festlegen

         $txt_datei =  "./$turnier" . "_Ergebnisliste.txt";

         $fn = $txt_datei;
         $fp = fopen($fn,"r"); 

         while(($Daten = fgets($fp, 1000)) !== FALSE)
               {
                $laenge = strlen($Daten) - 2;
                $lz = $lz + 1;
                $gesamt_file .= substr($Daten, 0, $laenge);
       
                if($lz == 1)
                   $inhalt = substr($Daten, 0, $laenge) . "\r\n";
         
                if($lz > 4)
                   {
                    if(substr($Daten,0,3) == "---")
                       $flag = $flag + 1;
            
                    if(substr($Daten,0,3) != "---")
                      {
                      if($flag == 2)
                         unset($flag);
                
                      if($flag)
                         {
                          $inhalt .= substr($Daten, 0, $laenge) . "\r\n";

                          if($flag == 2)
                             unset($flag);
                         }
                      else   
                           {
                          if(substr($Daten, 0, $laenge) > 0)
                             {
                              $platz = explode('.', $Daten);
                              $paar = explode('(', $platz[1]);
                              $paar_kurz = ltrim($paar[0]);
                              $paar_kurz = rtrim($paar_kurz);
                              $verein = explode('(', $Daten);
                              $verein_l = strlen($verein[1]) - 3;
                              $verein_kurz = substr($verein[1], 0, $verein_l ) ;               
                          
                            $inhalt .= $platz[0] . ".;" . $paar_kurz . ";" . $verein_kurz . "\r\n";
                             }
                           }
             
                      }            
              
                    }       

               }
           
         fclose($fn);  

// echo"$gesamt_file<br><br>$inhalt<br>";   

         // Dateinamen festlegen und Datei speichern
         $csv_name = $t_nummer . ".csv";
         // Datei auf Server speichern
         $fn = "../../../../cms/images/tabulizer_turnier-ergebnisse/" . $csv_name;
              if (is_file($fn))
                  unlink($fn);

         $fp = fopen($fn,"w");
         fwrite($fp, $inhalt);
         fclose($fp);

         unset($inhalt);
         
         // Quelle kopieren und löschen
         $t_num = substr($turnier, 1);
         $sqlab = "SELECT dat_begin, dat_turniernummer FROM adm_dates WHERE dat_turniernummer = $t_num";
         $datum = mysqli_query($db, $sqlab);
         $t_datum = mysqli_fetch_array($datum);
         $ordner = substr($t_datum[0], 0,4) . "_" . substr($t_datum[0], 5,2) . "_" . substr($t_datum[0], 8,2) . "_" . $turnier;
         
         $e_liste_txt = "./" . $turnier . "_Ergebnisliste.txt";
         $bericht = "./" . $turnier . "_Turnierbericht.rtf";
         $org_datenbank = $turnier . "_TDaten.mdb";
         $zip_name = $turnier . '_TDaten.zip';
         $te_turnier = $turnier . '_Turnier.csv';
         $te_turnierleitung = $turnier . '_Turnierleitung.csv';
         $te_wertungsrichter = $turnier . '_Wert_Richter.csv';
         $te_rundentab = $turnier . '_Rundentab.csv';
         $te_paare = $turnier . '_Paare.csv';
         $te_wertungen = $turnier . '_Abgegebene_Wertungen.csv';
         $te_majoritaet = $turnier . '_Majoritaet.csv';
         $te_auswertung = $turnier . '_Auswertung.csv';
         $te_rundenquali = $turnier . '_Paare_Rundenqualifikation.csv';     
         $jahr = "20" . substr($turnier,2,2);

         // Ergebnisliste.txt
         //copy($e_liste_txt, "../../../adm_my_files/download/Turnierergebnisse/Jahrgang_" . $jahr . "/". $turnier . "_Ergebnisliste.txt");
         //if(is_file("../../../adm_my_files/download/Turnierergebnisse/Jahrgang_" . $jahr . "/". $turnier . "_Ergebnisliste.txt"))
         //unlink($e_liste_txt);
         
         // DB,RTF-Datei,Ergebnisliste.txt
         mkdir("../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner, 0705);
         
         copy($bericht, "../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $turnier . "_Turnierbericht.rtf");
         if(is_file("../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $turnier . "_Turnierbericht.rtf"))
         unlink($bericht);
         copy($e_liste_txt, "../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $turnier . "_Ergebnisliste.txt");
         if(is_file("../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $turnier . "_Ergebnisliste.txt"))
         unlink($e_liste_txt);
 
         $zip = new ZipArchive();
         $zip->open($zip_name, ZipArchive::CREATE);
         $zip->addFile($org_datenbank);
         $zip->close();
         if(is_file("$zip_name"))
         copy($zip_name, "../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $zip_name);
         if(is_file("../../../adm_my_files/download/Turnierdatenbank/Jahrgang_" . $jahr . "/" . $ordner . "/" . $zip_name))
         unlink($zip_name);                 
         $mdb = "./" . $turnier . "_TDaten.mdb";
         unlink($mdb);
     
        // Turnier.csv
         mkdir("../../turnierergebnisse/csv_dateien/" . $turnier, 0705);
         copy($te_turnier, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_turnier);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_turnier))
         unlink($te_turnier);   
    
        // Turnierleitung.csv
         copy($te_turnierleitung, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_turnierleitung);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_turnierleitung))
         unlink($te_turnierleitung);
         
        // Turnierleitung.csv
         copy($te_wertungsrichter, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_wertungsrichter);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_wertungsrichter))
         unlink($te_wertungsrichter);  
         
        // Rundentab.csv
         copy($te_rundentab, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_rundentab);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_rundentab))
         unlink($te_rundentab);
         
        // Paare.csv
         copy($te_paare, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_paare);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_paare))
         unlink($te_paare);
         
        // Wertungen.csv
         copy($te_wertungen, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_wertungen);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_wertungen))
         unlink($te_wertungen);

        // Majorität.csv
         copy($te_majoritaet, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_majoritaet);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_majoritaet))
         unlink($te_majoritaet);
         
        // Auswertung.csv
         copy($te_auswertung, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_auswertung);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_auswertung))
         unlink($te_auswertung);
         
         // Paare_Rundenqualifikation.csv
         copy($te_rundenquali, "../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_rundenquali);
         if(is_file("../../turnierergebnisse/csv_dateien/" . $turnier . "/" . $te_rundenquali))
         unlink($te_rundenquali);
         
        }
       
       unlink($dateiname);
     }
     
      header("Location: ../../turnierergebnisse/turnierdatenimport.php");
?>