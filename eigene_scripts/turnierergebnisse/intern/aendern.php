<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Turnierergebnisse ändern</title>
</head>
<body>

<?php

// Datenbank verbinden
$db = mysqli_connect("xxx","xxx","xxx", "xxx");

// Turniernummer eingeben
echo' <form method="POST" action="' . $_SERVER["PHP_SELF"] . '"> ';
echo'<h3>Bitte gib den Verein und die Startbuchnummer ein!</h3><br>';
echo'Verein: <input name = "verein" value="' . $_POST['verein'] . '" size="50" maxlength="100"><br>';
echo'Cup/Serie: ';

echo'<select name="cup">';
   
   if($_POST["cup"] == 'Nord_Cup')
      echo'<option value="Nord_Cup" selected>Nord_Cup</option>';
   else
      echo'<option value="Nord_Cup">Nord_Cup</option>';

   if($_POST["cup"] == 'Sued_Cup')
      echo'<option value="Sued_Cup" selected>Sued_Cup</option>';
   else
      echo'<option value="Sued_Cup">Sued_Cup</option>';
      
echo'</select><br>';

echo'Startbuch-Nr.: <input name = "sb_nummer" value="' . $_POST['sb_nummer'] . '" size="7" maxlength="7"><br>';
echo'<input type="submit" name="senden" value="Absenden"/>';
echo"</form>";

if($_POST["sb_nummer"])
   {

// Startbuch
    // $sqlab = "UPDATE paare SET verein='" . $_POST['verein'] . "', cup_serie='" . $_POST['cup'] . "' WHERE startbuch='" . $_POST['sb_nummer'] . "'"; 
// Startbuch Boogie Herr
     $sqlab = "UPDATE paare SET verein='" . $_POST['verein'] . "', cup_serie='" . $_POST['cup'] . "' WHERE boogie_sb_herr='" . $_POST['sb_nummer'] . "'"; 
    
    
 if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    echo '<br>Es waren <b>' . mysqli_affected_rows($db) . '</b> Datensätze betroffen';
   }
?>

</body>
</html>