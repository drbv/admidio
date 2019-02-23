<?php 
/******************************************************************************
 * Form for Beantragung des Gütesiegels Mail
 *
 * Copyright    : (c) 2017 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

if (isset($_POST)) {
$strMailtextMod = "";
$strMailtextFb    = "";
$strMailtextMod .= "Formular - Beantragung des Gütesiegels: ".$_POST['antrag']."\n";
$strMailtextMod .= "\n";
$strMailtextMod .= "Vereinsnummer: ".$strDelimiter1x.$_POST['Vereinsnummer']."\n";
$strMailtextMod .= "Vereinsname: ".$strDelimiter1x.$_POST['Verein']."\n";
$strMailtextMod .= "Bundesland: ".$strDelimiter1x.$_POST['Bundesland']."\n";
$strMailtextMod .= "Versender: ".$strDelimiter1x.$_POST['Versender']."\n";
$strMailtextMod .= "EMail: ".$strDelimiter1x.$_POST['EMail']."\n";
$strMailtextMod .= "\n";
$strMailtextMod .= "Angaben zum Erwerb der Basispunkte:\n";
$strMailtextMod .= "Gemeldeter Mitgliederbestand mit DRBV-Zugehörigkeit vor 2 Jahren: ".$strDelimiter1x.$_POST['anz_mitglieder_alt']."\n";
$strMailtextMod .= "Letzter gemeldeter Mitgliederbestand mit DRBV-Zugehörigkeit: ".$strDelimiter1x.$_POST['anz_mitglieder_aktuell']." (100%)\n";
$strMailtextMod .= "Anzahl Kinder und Jugendlicher bis 18 Jahre aktuelle Mitgliedermeldung: ".$strDelimiter1x.$_POST['anz_mitglieder_kiju']." (30%)\n";
$strMailtextMod .= "Anzahl Startbücher/-karten (Paare): ".$strDelimiter1x.$_POST['anz_startbuecher_paare']." (2)\n";
$strMailtextMod .= "Anzahl Startbücher Formationen: ".$strDelimiter1x.$_POST['anz_startbuecher_formation']." (1)\n";
$strMailtextMod .= "Anzahl Wertungsrichter im Verein: ".$strDelimiter1x.$_POST['anz_wertungsrichter']." (1)\n";
$strMailtextMod .= "Anzahl Turnierleiter im Verein: ".$strDelimiter1x.$_POST['anz_turnierleiter']." (1)\n";
$strMailtextMod .= "Anzahl lizenzierter DRBV-Trainer/Kursleiter: ".$strDelimiter1x.$_POST['anz_lizenztrainer_drbv']." (3%)\n";
$strMailtextMod .= "Anzahl ausgerichteter, tagesverschiedener DRBV-Turniere und Wettbewerbe in den letzten zwei Kalenderjahren: ".$strDelimiter1x.$_POST['anz_turnierausrichtung']." (2)\n";
$strMailtextMod .= "Anzahl Turnierteilnahmen des Vereins im letzten Kalenderjahr (pro Turnier nur 1x mgl.): ".$strDelimiter1x.$_POST['anz_turnierteilnahmen']." (3)\n";
$strMailtextMod .= "Anzahl TSA-Abnahmen (Prüfungsveranstaltung) in den letzen zwei Kalenderjahren: ".$strDelimiter1x.$_POST['anz_tsaabnahme']." (2)\n";
$strMailtextMod .= "\n";
$strMailtextMod .= "Resultat Basispunkte: ".$_POST['res_basispkt']." (100)\n";
if(!$_POST['res_basispkt']<100){
  $strMailtextMod .= "Das Gütesiegel kann aufgrund der erreichten Basispunkte erteilt werden!\n";
  $strMailtextFb    .= $strMailtextMod;
  $strMailtextMod .= "Link für den Verein zur Validierung des Gütesiegels:\n";
  $strMailtextMod .= "https://www.drbv.de/scripts/guetesiegel?GId=_X2D451D4490C".$strDelimiter1x.$_POST['Vereinsnummer']."C3989893C".$gCurrentUser->getValue('usr_id')."\n";
} else {
  $strMailtextMod .= "Die Basispunkte reichen nicht aus, bitte Zusatzpunkte prüfen:\n";
  $strMailtextFb    .= "Die Basispunkte reichen nicht aus, daher werden die Zusatzangaben überprüft:\n";
}      
$strMailtextMod .= "\n";
$strMailtextMod .= "Angaben zum Erwerb von Zusatzpunkten:\n";
if($_POST['check_liztrainer']==1){
  $strMailtextMod .= "Andere eingesetzte Trainer mit DTV oder LSB Lizenz: Ja, ".$strDelimiter1x.$_POST['anz_lizenztrainer_dtv']." Lizenz siehe Dokument(e) im Anhang.\n";
} else {
  $strMailtextMod .= "Andere eingesetzte Trainer mit DTV oder LSB Lizenz: Nein, keine.\n";
}
$strMailtextMod .= "Durchführung Kooperation Verein/Schule/GTA (im letzten Kalenderjahr): ".$strDelimiter1x;
if($_POST['check_kooperation']==1){
  $strMailtextMod .= "ja-> check: ".$_POST['gta_url']." oder Dokument im Anhang.\n";
} else {
  $strMailtextMod .= "nein\n";
}
$strMailtextMod .= "Offene Workshops (der letzten zwei Kalenderjahre: ".$strDelimiter1x;
if($_POST['check_workshops']==1){
  $strMailtextMod .= "ja-> check: ".$_POST['workshop_url']."\n";
} else {
  $strMailtextMod .= "nein\n";
}
$strMailtextMod .= "Funktionierender und gepflegter Internetauftritt des Vereines/der Abteilung: ".$strDelimiter1x;
if($_POST['check_website']==1){
  $strMailtextMod .= "ja -> check: ".$_POST['website_url']."\n";
} else {  
  $strMailtextMod .= "nein\n";
}
$strMailtextMod .= "Aktive Nutzung von sozialen Netzwerken z.B. Facebook für Vereinsdarstellung: ".$strDelimiter1x;
if($_POST['check_socialnetworks']==1){
  $strMailtextMod .= "ja-> check: ".$_POST['social_url']."\n";
} else {
  $strMailtextMod .= "nein\n";
}
$strMailtextMod .= "Sportartübergreifende Sportangebote: ".$strDelimiter1x;
if($_POST['check_uebergreifendeangebote']==1){
  $strMailtextMod .= "ja-> check: ".$_POST['sportangebote_url']."\n";
} else {
  $strMailtextMod .= "nein\n";
}
$strMailtextMod .= "\nResultat: ";
$strMailtextMod .= "Basispunkte: ".$_POST['res_basispkt']."\n";
$strMailtextMod .= "Mögliche Zusatzspunkte: ".$_POST['res_xtrpkt']."\n";
$strMailtextMod .= "Mögliche Gesamtpunkte: ".($_POST['res_basispkt']+$_POST['res_xtrpkt'])."\n\n";
$strMailtextMod .= "Vereinsprofil bearbeiten: https://drbv.de/adm/adm_program/modules/profile/profile_new.php?user_id=".$gCurrentUser->getValue('usr_id')."\n\n";
$strMailtextMod .= "Code zum einbetten auf der Vereinshomepage:\n";
$strMailtextMod .= "[link rel=\"stylesheet\" href=\"https://drbv.de/adm/adm_themes/classic/css/gsiegel.css\" type=\"text/css\" /]\n";
$strMailtextMod .= "[a href=\"https://www.drbv.de/cms/index.php/guetesiegel?GId=_X2D451D4490C".$strDelimiter1x.$_POST['Vereinsnummer']."C3989893C".$gCurrentUser->getValue('usr_id')."\" target=\"_blank\"]\n";
$strMailtextMod .= "  [span class=\"gsglMiniLogo\" title=\"Unser Verein erfüllt die Qualitätskriterien des Deutschen Rock'n'Roll und Boogie-Woogie Verbands\"][/span]\n";
$strMailtextMod .= "[/a]\n";
$strMailtextMod .= "[ und ] durch html tag-zeichen ersetzen!\n";

//Mail zur Antwort an den Antragsteller
$strMailtextFb .= "\n";
$strMailtextFb .= "Angaben zum Erwerb von Zusatzpunkten:\n";
if($_POST['check_liztrainer']==1){
  $strMailtextFb .= "Andere eingesetzte Trainer mit DTV oder LSB Lizenz: Ja, ".$strDelimiter1x.$_POST['anz_lizenztrainer_dtv'].".\n";
} else {
  $strMailtextFb .= "Andere eingesetzte Trainer mit DTV oder LSB Lizenz: Nein, keine.\n";
}
$strMailtextFb .= "Durchführung Kooperation Verein/Schule/GTA (im letzten Kalenderjahr): ".$strDelimiter1x;
if($_POST['check_kooperation']==1){
  $strMailtextFb .= "ja-> siehe folgende Url: ".$_POST['gta_url']."\n";
} else {
  $strMailtextFb .= "nein\n";
}
$strMailtextFb .= "Offene Workshops (der letzten zwei Kalenderjahre: ".$strDelimiter1x;
if($_POST['check_workshops']==1){
  $strMailtextFb .= "ja-> siehe folgende Url: ".$_POST['workshop_url']."\n";
} else {
  $strMailtextFb .= "nein\n";
}
$strMailtextFb .= "Funktionierender und gepflegter Internetauftritt des Vereines/der Abteilung: ".$strDelimiter1x;
if($_POST['check_website']==1){
  $strMailtextFb .= "ja -> siehe folgende Url: ".$_POST['website_url']."\n";
} else {  
  $strMailtextFb .= "nein\n";
}
$strMailtextFb .= "Aktive Nutzung von sozialen Netzwerken z.B. Facebook für Vereinsdarstellung: ".$strDelimiter1x;
if($_POST['check_socialnetworks']==1){
  $strMailtextFb .= "ja-> siehe folgende Url: ".$_POST['social_url']."\n";
} else {
  $strMailtextFb .= "nein\n";
}
$strMailtextFb .= "Sportartübergreifende Sportangebote: ".$strDelimiter1x;
if($_POST['check_uebergreifendeangebote']==1){
  $strMailtextFb .= "ja-> siehe folgende Url: ".$_POST['sportangebote_url']."\n";
} else {
  $strMailtextFb .= "nein\n";
}
$strMailtextFb .= "\nDer Antrag wird in kürze bearbeitet.";

}
?>