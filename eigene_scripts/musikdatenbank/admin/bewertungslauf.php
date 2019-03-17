<?php



require_once("./dboeffnen.inc.php");

/*
echo'GET: ';print_r($_GET);echo"<br>";
echo'POST: ';print_r($_POST);echo"<br>";
echo'SESSION: ';print_r($_SESSION);echo"<br>";
*/
 

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Musikdatenbank Admin</title>
</head>
<body>

<?php


echo'<h1><center>Admin Turniermusik DRBV</h1>';

   $sqlab = 'SELECT * From turniermusik';

 echo $sqlab . '<br>';


$lieder = mysqli_query($db, $sqlab);
$anzahl_auswahl = mysqli_affected_rows($db);

while($temp = mysqli_fetch_array($lieder))
  {
unset($durchschnitt);
$durchschnitt = array();  
unset($teiler);

 print_r($temp);echo"<br>";
 
 
 if($temp['wertung_1'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_1'];
   }
 if($temp['wertung_2'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_2'];
   }
 if($temp['wertung_3'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_3'];
   }
 if($temp['wertung_4'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_4'];
   }
 if($temp['wertung_5'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_5'];
   }
 if($temp['wertung_6'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_6'];
   }
 if($temp['wertung_7'] >= 1)
   {
    $durchschnitt[] = $temp['wertung_7'];
   }

$teiler_1 = count($durchschnitt) - 2;

$schnitt = round((array_sum($durchschnitt) - max($durchschnitt) - min($durchschnitt))/$teiler_1);

//   $schnitt = round(($_POST['sterne_1'] + $_POST['sterne_2'] + $_POST['sterne_3'] + $_POST['sterne_4'] + $_POST['sterne_5'] + $_POST['sterne_6'] + $_POST['sterne_7'])/$teiler_1);

 $sqlab = "UPDATE turniermusik set wertung = '" . $schnitt . "' WHERE id='" . $temp['id'] . "'";
 echo$sqlab. '<br>';
mysqli_query($db, $sqlab);
$erfolg = mysqli_affected_rows($db);
if($erfolg == 1)
   echo'Der Datenstatz wurde geändert!<br>';
  } 
   echo'</table><br>';

echo'Gesamt: ' . $anzahl_auswahl . ' Titel<br>';

?>


</body>
</html>
