<?php

require_once("./intern/tickets.inc.php");
echo'<h2>Zertifizierungen</h2>';

// print_r($_POST);echo"<br>";

// print_r($_POST['datei']);

  

  
// Tabelle und Variablen erstellen

$gewaehlt = $_POST['datei'];

echo'<Form action="' . $_SERVER['PHP_SELF'] . '" method="post">';

echo'<table border="1">';
echo'<tr><th>Auswahl</th><th>Startbuch</th><th>Startklasse</th><th>Ticket</th><th>Dateiname</th><th>Klarname</th><th>Titelname Ticket</th></tr>';

$i = 1;

$sqlab = "SELECT * FROM hesk_attachments";
    
$daten = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($daten))
   {
  // echo $temp['ticket_id'];
    $sqlab = "SELECT custom7, custom8, custom10, custom11, custom12, custom13, custom14 FROM hesk_tickets WHERE trackid LIKE '" . $temp['ticket_id'] . "'";

    $startklasse = mysqli_query($db, $sqlab);
    $ds = mysqli_fetch_array($startklasse);
    if($ds['custom10'] !="FT_")
       {
        $ticket_titel = $ds['custom10'];
        $art = 'FT_';
       }
    if($ds['custom11'] !="AK_")
       {
        $ticket_titel = $ds['custom11'];
        $art = 'AK_';
       }
    if($ds['custom12'] !="SP_")
       {
        $ticket_titel = $ds['custom12'];
        $art = 'SP_';
       }
    if($ds['custom13'] !="TM_")
       {
        $ticket_titel = $ds['custom13'];
        $art = 'TM_';
       }  
    if($ds['custom14'] !="ST_")
       {
        $ticket_titel = $ds['custom14'];
        $art = 'ST_';
       }
    
//    print_r($ds);echo '<br>';

    if($gewaehlt[$i] == 1)
       {
      echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1" checked></td><td>' . $ds['custom7'] . '</td><td>' . $ds['custom8'] . '</td><td>' . $temp['ticket_id'] . '</td><td>' . $temp['saved_name'] . '</td><td>' . $temp['real_name'] . '</td><td>' .  $ticket_titel . '</td></tr>';
        $dateiname[$i][0] = $temp['saved_name'];
        $dateiname[$i][1] = $art . $ds['custom8'] . '_' . substr($temp['saved_name'],0 ,12);
        $dateiname[$i][2] = $ds['custom7'];
        $dateiname[$i][3] = $ds['custom7'] . '_' . date("Y") . '_' . $ds['custom8'] . $art . substr($temp['saved_name'],0 ,12);
        $dateiname[$i][4] = $ds['custom8'];
        $dateiname[$i][5] = $art;
        // print_r($dateiname[$i]) . '<br>';
       }
    else 
       {
    echo'<tr><td align="center"><input type="checkbox" name="datei[' . $i . ']" value="1"></td><td>' . $ds['custom7'] . '</td><td>' . $ds['custom8'] . '</td><td>' . $temp['ticket_id'] . '</td><td>' . $temp['saved_name'] . '</td><td>' . $temp['real_name'] . '</td><td>' .  $ticket_titel . '</td></tr>';
       }      
    $i++;
   }
   
echo'<tr><td colspan="4" align="center"><input type="submit" name="zertifizieren" value="Zertifizieren"></td><td>&nbsp</td><td colspan="2" align="center"><input type="submit" name="kopieren" value="Zur Turniermusik"></td></tr>';
echo'</table>';
echo'</form>';



// Benachrichtigung per E-Mail
/*
    $inhalt_mail =  $inhalt_kopf . $inhalt_mail . "\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "webmaster@drbv.de";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

   @mail($absender,"Musik ist zertifiziert", $inhalt_mail, $absender_mail, $absender_path);
//@mail("info@oberleonline.de","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
*/
  
  
  ?>