<?php

date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");

$zeit = time() - 600000;
 $datum = date("Y-m-d", $zeit);
 $heute = date("Y-m-d");
// echo"$datum<br>";
// echo"$heute<br>";
// Neuen Verein suchen
$sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = '102' AND mem_begin > '$datum'"; 
$neu_verein = mysqli_query($db, $sqlab);

while($gefunden = mysqli_fetch_array($neu_verein))
{
 $neu_verein_id = $gefunden[0];
 echo"Neuer Verein = $neu_verein_id<br>";
 
// Turniere suchen
$sqlab = "SELECT dat_rol_id FROM adm_dates WHERE dat_cat_id = '31' AND dat_begin > '$heute' AND dat_rol_id > '1' ";

$turnier = mysqli_query($db, $sqlab);

   while($suche_turnier_id = mysqli_fetch_array($turnier))
       {
      	$turnier_id = $suche_turnier_id[0];
        echo"$turnier_id<br>";
 // Vereine im Turnier suchen         
        $sqlab = "SELECT mem_usr_id FROM adm_members WHERE mem_rol_id = '$turnier_id' AND mem_leader = '1' "; 
        $gef_turnier = mysqli_query($db, $sqlab);
     
        while($vorhanden = mysqli_fetch_array($gef_turnier))
              {
               $ist_turnier_id = $vorhanden[0];
               echo"$vorhanden<br>";
               
               if($ist_turnier_id == $neu_verein_id)
                  $treffer = 1;
              }
              if(!$treffer) 
                {
              	 $sqlab = "INSERT INTO adm_members (mem_rol_id, mem_usr_id, mem_begin, mem_end, mem_leader, mem_usr_id_create) VALUES ('" . $turnier_id . "', '$neu_verein_id', '$heute', '9999-12-31', '1', '2') ";
                 mysqli_query($db, $sqlab); 
                }
              else 
                {
      	         unset($treffer);
                }
          
// Ende Schleife Vereine im Turnier
        }
// Ende Schleife Turniere        
    //  }
// Ende Schleife neuer Verein
}

?>