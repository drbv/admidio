<?php
 session_start();

 // require_once("../intern/bereinigen.php");
 /*
 print_r($_POST);echo'<p>';
 echo ' JAVAvalues -' .  $_SESSION['javavalues'] . '-<p>';
 echo 'values_true -' .  $_SESSION['values_true'] . '-<p>';
 */

require("./turnierergebnisse/intern/dboeffnen.inc.php");

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Hardwareübersicht</title>
</head>
<body>

<h2>DRBV Hardwareübersicht Turnierleiter/Wertungsrichter</h2>
<?php

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<center><table border="1">';
echo'<tr><th>Name</th><th>Vorname</th><th>Lizenz-Nr.</th><th>Datum</th><th>Browser</th><th>Aulösung</th><th>Mobil</th><th>Java</th><th>Version</th><th>Flash</th><th>Version</th><th>Cookies</th><th>Zeitzone</th><th>Sprache</th><th>Schriften</th><th>System</th><th>Feld 13</th></tr>';
$sqlab = 'SELECT * FROM hardware';
$zeile = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($zeile)) {
    echo'<tr><td>' . $temp['name'] . '</td><td>' . $temp['vorname'] . '</td><td>' . $temp['lizenz'] . '</td><td>' . $temp['datum'] . '</td><td>' . $temp['browser'] . '</td><td>' . $temp['aufloesung'] . '</td><td>' . $temp['mobile'] . '</td><td>' . $temp['java'] . '</td><td>' . $temp['java-version'] . '</td><td>' . $temp['flash'] . '</td><td>' . $temp['flash-version'] . '</td><td>' . $temp['cookies'] . '</td><td>' . $temp['zeitzone'] . '</td><td>' . $temp['sprache'] . '</td><td>' . $temp['fonts'] . '</td><td>' . $temp['system'] . '</td><td>' . $temp['feld13'] . '</td></tr>';
}




/*
echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
echo'Vorname: <input type="text" name="vorname" value="' . $_POST['vorname'] . '"  size="20" maxlength="20"><br />';
echo'Nachname: <input type="text" name="nachname" value="' . $_POST['nachname'] . '"  size="20" maxlength="20"><br />';
echo'Lizenznummer: <input type="text" name="lizenz" value="' . $_POST['lizenz'] . '"  size="4" maxlength="4"><br />';

       //$inhalt = $_POST['vorname'] . ' - ' . $_POST['nachname'] . ' - ' . $_POST['lizenz'] . ' - ' . date('d.m.Y H:i:s') . ' - ';
       $inhalt .=  $_SESSION['javavalues'];
       //$inhalt .= "\r\n";
       echo 'Inhalt:' . $inhalt . '<br>'; 
       
    
echo'<input type="hidden" name="values" value="' . $inhalt . '"><br />';

echo'<input type="submit" name="absenden" value="Absenden"><p />';

if($_POST['absenden']) {
    if(!$_POST['vorname']) {
        echo'Bitte geben Sie Ihren Vornamen ein!<p />';
        $fehler = 1 ;
    }
    if(!$_POST['nachname']) {
       echo'Bitte geben Sie Ihren Nachnamen ein!<p />';
       $fehler = 1;
    }
    if(!$_POST['lizenz']) {
       echo'Bitte geben Sie Ihre Lizenznummer ein!<p />';
       $fehler = 1;
    }
*/

echo'</form>';
?>
</body>
</html>
