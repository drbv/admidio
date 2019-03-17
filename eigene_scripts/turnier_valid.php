<?php
//error_reporting (E_ALL);
        
date_default_timezone_set("Europe/Berlin");

require("./intern/dboeffnen.inc.php");
require_once("./intern/bereinigen.php");

// Datum festlegen
$beginn = date('Y-m-d',time() + 43 * 86400); // echo $beginn; 42 Tage
$ende = date('Y-m-d',time() + 44 * 86400); // echo $ende;

// Datum manuell festlegen
echo' <form method="POST" action="' . $_SERVER["PHP_SELF"] . '"> ';
echo'<h3>Bitte gib das Datum im Format: JJJJ-MM-DD ein!</h3><br>';
echo'<input name = "datum" size="10" maxlength="10">';
echo'<input type="submit" name="senden" value="Absenden"/>';
echo"</form>";

if($_POST["datum"])
   {
    if($man_datum != $beginn)
		 $beginn = $_POST["datum"];
		 $ende = date('Y-m-d', strtotime($_POST["datum"]) + 86500);
   }

echo $beginn . ' - ' . $ende . "<br>";
 
// Turnier finden
$sqlab = "SELECT dat_begin, dat_location, dat_headline, dat_turniernummer, dat_tl, dat_ansprechpartner, dat_mail,dat_link, dat_tform, dat_verein, dat_sk_s, dat_sk_j, dat_sk_c, dat_sk_b, dat_sk_a, dat_sk_bwh, dat_sk_bwo, dat_sk_bwj, dat_sk_frm, dat_sk_frj, dat_sk_frl, dat_sk_frg, dat_sk_fbm,dat_sk_bsp, dat_sk_frs, dat_sk_bwh_b, dat_sk_bwo_b  FROM adm_dates WHERE dat_cat_id = 31 AND dat_begin >= '" . $beginn ."' AND dat_begin < '" . $ende . "'";

// echo"$sqlab<p />";
$turniere = mysqli_query($db, $sqlab);

while($temp = mysqli_fetch_array($turniere))
 {
	// print_r($temp); echo'<p />';
   unset($str_body);
   unset($apartner);
   unset($mail_verein);
   unset($mail);   
   unset($errors);
   unset($gs_errors);
   unset($inhalt_verein);
   unset($inhalt_gs);
   unset($mail_signatur);

   $turnier = $temp['dat_headline'];
	
	if(substr($temp['dat_begin'], 11, 2) == '00') {
	    $str_body = "- Der Veranstaltungsbeginn ist nicht korrekt\n";
	    $errors = $errors + 1;
      }
      
   $v_datum = substr($temp['dat_begin'], 8, 2) . '.' . substr($temp['dat_begin'], 5, 2) . '.' . substr($temp['dat_begin'], 0, 4);
	   
	if(!$temp['dat_location']) {
	    $str_body .= "- Der Veranstaltungsort ist nicht angegeben\n";
	    $errors = $errors + 1;
	   }	   

	if(!$temp['dat_turniernummer']) {
	    $str_body .= "- Die Turniernummer ist nicht angegeben\n";
	    $errors = $errors + 1;
	   }	   

   if(!$temp['dat_tl'] && $temp['dat_tform'] != 'Breitensportwettbewerb') {
	    $str_body .= "- Der Turnierleiter ist nicht angegeben\n";
	    $errors = $errors + 1;
	   }
	   
	if(!$temp['dat_ansprechpartner']) {
	    $str_body .= "- Der Ansprechpartner ist nicht angegeben\n";
	    $errors = $errors + 1;
      }
   else {
      $apartner = $temp['dat_ansprechpartner'];
      }  

	if(!$temp['dat_mail']) {
	    $str_body .= "- Die E-Mail-Adresse ist nicht angegeben\n";
	    $errors = $errors + 1;
      }
   else {
      $mail = $temp['dat_mail'];
      }      

   if(!$temp['dat_link']) {
	    $str_body .= "- Die Veranstaltungshomepage ist nicht angegeben\n";
	    $errors = $errors + 1;
      }
  
   if(!$temp['dat_verein']) {
	    $str_body_gs = "- Der Verein ist nicht angegeben\n\n";
	    $gs_errors = $gs_errors + 1;
      }
  
   if(!$temp['dat_tform']) {
         $str_body .= "- Die Turnierform ist nicht angegeben\n";
         $errors = $errors + 1;
        }
      
   if(!$temp['dat_tform'] || $temp['dat_tform'] != 'Breitensportwettbewerb') {
            if($temp['dat_sk_s'] == 0 && $temp['dat_sk_j'] == 0 && $temp['dat_sk_c'] == 0 && $temp['dat_sk_b'] == 0 && $temp['dat_sk_a'] == 0 && $temp['dat_sk_bwh'] == 0 && $temp['dat_sk_bwo'] == 0 && $temp['dat_sk_bwj'] == 0 && $temp['dat_sk_frm'] == 0 && $temp['dat_sk_frj'] == 0 && $temp['dat_sk_frl'] == 0 && $temp['dat_sk_g'] == 0 && $temp['dat_sk_fbm'] == 0 && $temp['dat_sk_bsp'] == 0 && $temp['dat_sk_frs'] == 0 && $temp['dat_sk_bwh_b'] == 0 && $temp['dat_sk_bwo_b'] == 0) {
            $str_body .= "- Es wurden keine Startklassen angegeben\n";
            $errors = $errors + 1;
            }
         }

   $betreff = $turnier . " am " . $v_datum;
 
   echo"----------------------<br>$mail_verein - Betreff: $betreff<p />";
   echo $str_body . $str_body_gs . '<br>-------------------------<br>';

   $mail_verein = "Hallo $apartner,\n\n";
   $mail_gs = "Zur Information!\n\nHallo,\n\n";
          
   $mail_inhalt = "die Daten fuer den Termin: $betreff\n";
   $mail_inhalt .= "sind fehlerhaft bzw. unvollstaendig.\n\n";
   $mail_inhalt .= "Folgende Daten sind zu ueberpruefen und an die geschaeftsstelle@drbv.de zu melden:\n";    
   $mail_inhalt .= $str_body . "\n";
   $mail_signatur .= "\nMit freundlichen Gruessen\n\n";
   $mail_signatur .= "Deutscher Rock'n'Roll und Boogie-Woogie Verband e.V.\n";
   $mail_signatur .= "Kirchbergstrasse 2\n";
   $mail_signatur .= "86157 Augsburg\n\n";
   $mail_signatur .= "Telefon: +49 (0)821 - 2 29 12 55\n";
   $mail_signatur .= "xxx@xx.xx\n";

// E-Mails versenden

   $absender = "xxx@xxx.xx";

   $absender_mail = "From: $absender" . "\r\n" .  "Reply-To: $absender" . "\r\n" . "Return-Path: $absender";
   $absender_path = "-f $absender"; 

   $inhalt_verein = $mail_verein . $mail_inhalt . $mail_signatur;
   if($gs_errors && !$errors) {
      $inhalt_gs = $mail_gs . $betreff . "\n" . $str_body_gs . $mail_signatur;
   }
   elseif($errors) {
      $inhalt_gs = $mail_gs . $str_body_gs . $mail_inhalt . $mail_signatur;
   }
   

   if($errors) {
      if($mail)
         mail($mail, $betreff, $inhalt_verein, $absender_mail, $absender_path);
   }
   if($errors || $gs_errors) {   
       mail("xxx@xxx.xx", $betreff, $inhalt_gs, $absender_mail, $absender_path);
       mail($absender, $betreff, $inhalt_gs, $absender_mail, $absender_path);
   }
    
// Ende while          
}
  
?>
