<?php

// Datenbank verbinden
$db = mysqli_connect("xxx","xxx","xxx", "xxx");

// Turniernummer eingeben
echo' <form method="POST" action="' . $_SERVER["PHP_SELF"] . '"> ';
echo'<h3>Bitte gib Die Turniernummer ein!</h3><br>';
echo'<input name = "t_nummer" size="7" maxlength="7">';
echo'<input type="submit" name="senden" value="Absenden"/>';
echo"</form>";

if($_POST["t_nummer"])
   {
    echo'Die Daten vom Turnier ' . $_POST["t_nummer"] . ' werden gelöscht.<br>';
       
    $sqlab = 'DELETE FROM auswertung WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';

    $sqlab = 'DELETE FROM majoritaet WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM paare WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM rundenquali WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM rundentab WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
                    
    $sqlab = 'DELETE FROM Turnier WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM T_Leiter WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM wertungen WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
    
    $sqlab = 'DELETE FROM wertungsrichter WHERE turniernummer = ' . $_POST["t_nummer"];
    if(mysqli_query($db,$sqlab))
    echo $sqlab . ' wurde ausgeführt.<br>';
                
   }
?>