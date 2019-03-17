<?php

$akt_zeit = time() - 28900; // 8 Stunden
// $akt_zeit = time() - 86400; // 24 Stunden
$db = mysqli_connect("XXX","XXX","XXX", "XXX"); // Joomla Datenbank

$sqlab = "SELECT commentDate FROM jcms_k2_comments ORDER BY id DESC LIMIT 10";

$kommentare = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($kommentare))
 {
  $datum_zeit = $temp['commentDate'];
  $zeit = strtotime($datum_zeit);
//  echo 'zeit: ' . date('d.m.Y H:i:s', $zeit) . '<br>';

  if($zeit > $akt_zeit)
     $marker = $marker+1;
 }
 
 if($marker > 0)
   {
    $inhalt = "Hallo,\n\nein neuer Kommentar wurde verfasst.\n\n";
    $inhalt .= "Bitte gehe auf http://drbv.de/cms/administrator\nund bearbeite diesen.";
    
    $inhalt .= "\n\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "XXX@XXX.XX";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

 @mail($absender,"Ein neuer Kommentar wurde verfasst", $inhalt, $absender_mail, $absender_path);

   }
?>