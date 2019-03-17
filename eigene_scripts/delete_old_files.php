<?php

include('../adm_program/system/drbv_database.php');

function files($dir, $currentDate)
 { 
  if(is_file($dir)) 
     return false; 
    
    if( strlen( $dir ) - 1 != '\\' || strlen( $dir ) - 1 != '/' ) 
        $dir .= '/'; 
      
    $handle = @opendir($dir); 
     
    if(!$handle) 
        return false; 
         
    while(($file = readdir($handle)) !== false)
          {
           if($file != '.' && $file != '..' && is_file($dir.$file))
              {      
               if(filemtime($dir.$file) <= $currentDate)
                  {
                   if($file != 'index.php')  
                   unlink($dir.$file);

                   // echo'Dir: ' . $dir . ' File: ' . $file.'<br>';
                  } 
              } 
           }    
     
     
    closedir( $handle );        
 }
 
 function musik($dir)
 { 
    echo'<p><h3>' . $dir . '</h3></p>';      
  $db = ADMIDIOdb();
 
  if(is_file($dir)) 
     return false; 
    
    if( strlen( $dir ) - 1 != '\\' || strlen( $dir ) - 1 != '/' ) 
        $dir .= '/'; 
       
    $handle = @opendir($dir); 
     
    if(!$handle) 
       return false; 
 
    while(($file = readdir($handle)) !== false)
          {
           if($file != '.' && $file != '..' && is_file($dir.$file))
              {     
                if($file != 'index.php') 
                   {
                    $sb_nummer = substr($file,0,5);
                    $sqlab = 'SELECT usd_usr_id From adm_user_data Where usd_value = "' . $sb_nummer . '" And usd_usf_id = "66"';
                    $startbuch = mysqli_query($db, $sqlab);
                    $temp = mysqli_fetch_row($startbuch);

                    $sqlab = 'SELECT mem_rol_id, mem_end From adm_members Where mem_usr_id = "' . $temp[0] . '"';
                    $startbuch_aktiv = mysqli_query($db, $sqlab);
                    while($temp = mysqli_fetch_array($startbuch_aktiv))
                        { 
                         if($temp[0] == 121 && $temp[1] < date('Y-m_d')) 
                           {
                            $delete = 1;
                           }
                        }
                    if($delete)
                      {
                        echo 'Das Startbuch ' . $sb_nummer . ' ist Nicht Aktiv!<br>';
                        $zaehler = $zaehler + 1; 
                        echo $zaehler . ' - ';
                        echo'Der Titel ' . $file . ' wird gelöscht!<p>';
                        // unlink($dir.$file);
                      }  
                    unset($delete);
                   }             
              } 
           }    
     echo'<p /';
     
    closedir( $handle );        
 }

 // Alte Dateien löschen
 // * Anzahl Tage
 $currentDate = time() - 86400 * 60;

 // Dateien löschen

 files('./kopien', $currentDate);
 files('./kopien_akros', $currentDate);
 files('./kopien_startlisten', $currentDate);
 files('./musikdatenbank/admin/db_backup', $currentDate);
 files('./musikzertifizierung/db_backup', $currentDate);
 files('./turnierergebnisse/DB_Backup', $currentDate);

 files('../adm_my_files/backup', $currentDate);

 files('../../db_backup/newsletter', $currentDate);
 files('../../teilnehmer', $currentDate);
 files('../../test/admidio_3/adm_my_files/backup', $currentDate);

 // Alte Musik löschen

 musik('../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Akrobatik');
 musik('../adm_my_files/download/Turniermusik/Zertifizierte-Musik/A-Klasse/Fusstechnik');
 musik('../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Akrobatik');
 musik('../adm_my_files/download/Turniermusik/Zertifizierte-Musik/B-Klasse/Fusstechnik');
 musik('../adm_my_files/download/Turniermusik/Zertifizierte-Musik/Formationen');
 musik('../../downloads/turniermp3');
 ?>