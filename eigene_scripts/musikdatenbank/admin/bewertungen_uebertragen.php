<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Bewertung auf andere Titel übertragen</title>
</head>
<body>

<?php
session_start();

require_once("./dboeffnen.inc.php");

$sqlab = 'SELECT * FROM turniermusik';
$lieder = mysqli_query($db, $sqlab);
while($temp = mysqli_fetch_array($lieder))
  {
  $i = $i + 1;
   $bezeichnung = substr($temp['bezeichnung'],3);
   $summe_org = $temp['wertung_1'] + $temp['wertung_2'] + $temp['wertung_3'] + $temp['wertung_4'] + $temp['wertung_5'] + $temp['wertung_6'] + $temp['wertung_7'];
//   echo$i . ' ' . $bezeichnung .' ' . $summe_org . '<br>';

   $sqlab = 'SELECT * FROM turniermusik WHERE bezeichnung LIKE "%' . $bezeichnung . '%" ';
   $gleiche_lieder = mysqli_query($db, $sqlab);
   while($gleiche_titel = mysqli_fetch_array($gleiche_lieder))
     {
      $summe_akt = $gleiche_titel['wertung_1'] + $gleiche_titel['wertung_2'] + $gleiche_titel['wertung_3'] + $gleiche_titel['wertung_4'] + $gleiche_titel['wertung_5'] + $gleiche_titel['wertung_6'] + $gleiche_titel['wertung_7'];
//   echo$i . ' ' . $gleiche_titel['bezeichnung'] .' ' . $summe_akt . '<br>';
   
   if($summe_org < $summe_akt && $temp['id'] < $gleiche_titel['id'])
      {
       $sqlab = "update turniermusik set wertung_1 = '" . $temp['wertung_1'] . "', wertung_2 = '" . $temp['wertung_2'] . "', wertung_3 = '" . $temp['wertung_3'] . "', wertung_4 = '" . $temp['wertung_4'] . "', wertung_5 = '" . $temp['wertung_5'] . "', wertung_6 = '" . $temp['wertung_6'] . "', wertung_7 = '" . $temp['wertung_7'] . "', wertung = '" . $temp['wertung'] . "' WHERE id='" . $gleiche_titel['id'] . "'"; 
      echo$sqlab. '<br>';
       mysqli_query($db, $sqlab);
       if(mysqli_affected_rows($db));
          $erfolg = $erfolg + 1;
      }
     }
  }

echo'Es wurden ' . $erfolg .' Datensätze aktualisiert!<br>';

?>

</body>
</html>