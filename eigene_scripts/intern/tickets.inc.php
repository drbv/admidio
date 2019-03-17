<?php

function music_zert_mail($inhalt_mail) {
    $inhalt_kopf = "Hallo,\n\nEin Musiktitel wurde zertifiziert.\n\n";
    $inhalt_kopf .= "Bitte gehe auf http://drbv.de/adm/eigene_scripts/musikzertifizierung/musikzertifizierung.php\nund bearbeite folgende Titel:\n\n";
    
    $inhalt_mail =  $inhalt_kopf . $inhalt_mail . "\n\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "webmaster@drbv.de";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

    @mail($absender,"Ein Musiktitel wurde zertifiziert", $inhalt_mail, $absender_mail, $absender_path);

}

function music_zert_mail_zert($inhalt_kopf, $inhalt_mail) {
    $inhalt_mail =  $inhalt_kopf . $inhalt_mail . "\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "xxx@xxx.xx";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

   @mail($absender,"Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
   @mail("xxx@xxx.xx","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
}

function music_zert_mail_zert_bw($inhalt_kopf, $inhalt_mail) {
    $inhalt_mail =  $inhalt_kopf . $inhalt_mail . "\nFrohes Schaffen\n\nder Webmaster\n";
    $absender = "xxx@xxx.xx";

    $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
    $absender_path = "-f $absender"; 

   @mail($absender,"Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
   @mail("xxx@xxx.xx","Bitte Musik zertifizieren", $inhalt_mail, $absender_mail, $absender_path);
}

//DB Zugangsdaten

$db = mysqli_connect("XXX","XXX","XXX", "XXX");

?>
