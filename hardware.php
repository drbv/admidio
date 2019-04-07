<?php

 session_start();

 require_once("./eigene_scripts/intern/bereinigen.php");
 /*
 print_r($_POST);echo'<p>';
 echo ' JAVAvalues -' .  $_SESSION['javavalues'] . '-<p>';
 echo 'values_true -' .  $_SESSION['values_true'] . '-<p>';
 */

require("./eigene_scripts/turnierergebnisse/intern/dboeffnen.inc.php");
/*
print_r($_GET);echo' GET<p />';
print_r($_SESSION);echo' SESSION<p />';
*/
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Hardwaretest</title>
    <script src="client.min.js"></script>
</head>
<body>

<script>
    'use strict';
    let client = new ClientJS();
    
    let BrowserData = client.getBrowserData(); 
    let aufloesung = client.getScreenPrint();
    let mobile = client.isMobile();
    let java = client.isJava();
    let java_version = client.getJavaVersion();
    let flash = client.isFlash();
    let flash_version = client.getFlashVersion();
    let cookie = client.isCookie();
    let zeitzone = client.getTimeZone();
    let sprache = client.getLanguage();
    let fonts = client.getFonts();
    let getOS = client.getOS();
    let OSVersion = client.getOSVersion();

// Werte zusammensetzen
    // let values = BrowserData.ua + " - " + getScreenPrint + " - " + isMobile + " - " + isJava + " - " + getJavaVersion + " - " + isFlash + " - " + getFlashVersion + " - " + isCookie + " - " + getTimeZone + " - " + getLanguage + " - " + getFonts;
    // document.write(values);
    let browser = BrowserData.ua;

 </script>
 
 <?php
   if($_GET['browser']) {
      $_SESSION['browser'] = $_GET['browser'];
      $_SESSION['aufloesung'] = $_GET['aufloesung'];
      $_SESSION['mobile'] = $_GET['mobile'];
      $_SESSION['java'] = $_GET['java'];
      $_SESSION['java_version'] = $_GET['java_version'];
      $_SESSION['flash'] = $_GET['flash'];
      $_SESSION['flash_version'] = $_GET['flash_version'];
      $_SESSION['cookies'] = $_GET['cookies'];
      $_SESSION['zeitzone'] = $_GET['zeitzone'];
      $_SESSION['sprache'] = $_GET['sprache'];
      $_SESSION['fonts'] = $_GET['fonts'];
      $_SESSION['system'] = $_GET['system'];    
    }
    
   if(!$_SESSION['browser'])
      echo'<script>window.location = "https://drbv.de/adm/hardware.php?browser=" + browser + "&aufloesung=" + aufloesung + "&mobile=" + mobile + "&java=" + java + "&java_version=" + java_version + "&flash=" + flash + "&flash_version=" + flash_version + "&cookies=" + cookie + "&zeitzone=" + zeitzone + "&sprache=" + sprache + "&fonts=" + fonts + "&system=" + getOS + " Version: " + OSVersion;</script>'; 
?>


<h2>DRBV Hardwaretest</h2>
<?php

echo'<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
echo'Vorname: <input type="text" name="vorname" value="' . $_POST['vorname'] . '"  size="20" maxlength="20"><br />';
echo'Nachname: <input type="text" name="nachname" value="' . $_POST['nachname'] . '"  size="20" maxlength="20"><br />';
echo'Lizenznummer: <input type="text" name="lizenz" value="' . $_POST['lizenz'] . '"  size="4" maxlength="4"> Wenn bekannt.<br />';
/*
       //$inhalt = $_POST['vorname'] . ' - ' . $_POST['nachname'] . ' - ' . $_POST['lizenz'] . ' - ' . date('d.m.Y H:i:s') . ' - ';
       $inhalt .=  $_SESSION['javavalues'];
       //$inhalt .= "\r\n";
       echo 'Inhalt:' . $inhalt . '<br>'; 
       
    
echo'<input type="hidden" name="values" value="' . $inhalt . '"><br />';
*/
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
    /*
    if(!$_POST['lizenz']) {
       echo'Bitte geben Sie Ihre Lizenznummer ein!<p />';
       $fehler = 1;
    }
    */
if(!$fehler && $_SESSION['browser']) {
    echo $fehler;
       
    echo'<h3>Ihre Daten:</h3><hr>';
    echo'<b>Browser: </b><script>
    document.write(BrowserData.ua);    
    </script><br>';

    echo'<b>Auflösung: </b><script>
    document.write(aufloesung);
    </script><br>';
    
    echo'<b>Mobil: </b><script>
    document.write(mobile);
    </script><br>';

    echo'<b>Java: </b><script>
    document.write(java);</script><b> Version: </b><script>
    document.write(java_version);
    </script><br>';

    echo'<b>Flash: </b><script>
    document.write(flash);</script><b> Version: </b><script>
    document.write(flash_version);
    </script><br>';
    
    echo'<b>Cookies: </b><script>
    document.write(cookie);
    </script><br>';

    echo'<b>Zeitzone: </b><script>
    document.write(zeitzone);
    </script><br>';

    echo'<b>Sprache: </b><script>
    document.write(sprache);
    </script><br>';

    echo'<b>Schriften: </b><script>
    document.write(fonts);
    </script><br>';

    echo'<b>OS: </b><script>
    document.write(getOS);</script><b> OS Version: </b><script>
    document.write(OSVersion);
    </script><br>';
/*
    let variable = client.isIE();
    let variable = client.isChrome();
    let variable = client.isFirefox();
    let variable = client.isSafari();
    let variable = client.isOpera();

    let variable = client.getOS();
    let variable = client.getOSVersion();
    let variable = client.isWindows();
    let variable = client.isMac();
    let variable = client.isLinux();
    let variable = client.isUbuntu();
    let variable = client.isSolaris();

    let variable = client.getDevice();
    let variable = client.getDeviceType();
    let variable = client.getDeviceVendor();

    let variable = client.getCPU();

    let variable = client.isMobile();
    let variable = client.isMobileMajor();
    let variable = client.isMobileAndroid();
    let variable = client.isMobileOpera();
    let variable = client.isMobileWindows();
    let variable = client.isMobileBlackBerry();

    let variable = client.isMobileIOS();
    let variable = client.isIphone();
    let variable = client.isIpad();
    let variable = client.isIpod();

    let variable = client.getScreenPrint();
    let variable = client.getColorDepth();
    let variable = client.getCurrentResolution();
    let variable = client.getAvailableResolution();
    let variable = client.getDeviceXDPI();
    let variable = client.getDeviceYDPI();

    let variable = client.getPlugins();
    let variable = client.isJava();
    let variable = client.getJavaVersion();
    let variable = client.isFlash();
    let variable = client.getFlashVersion();
    let variable = client.isSilverlight();
    let variable = client.getSilverlightVersion();

    let variable = client.getMimeTypes();
    let variable = client.isMimeTypes();

    let variable = client.isFont();
    let variable = client.getFonts();

    let variable = client.isLocalStorage();
    let variable = client.isSessionStorage();
    let variable = client.isCookie();

    let variable = client.getTimeZone();

    let variable = client.getLanguage();
    let variable = client.getSystemLanguage();

    let variable = client.isCanvas();
    let variable = client.getCanvasPrint();
*/  
    $sqlab = 'INSERT INTO hardware (name,vorname,lizenz,browser,aufloesung,mobile,java,java_version,flash,flash_version,cookies,zeitzone,sprache,fonts,system) VALUES ("' . $_POST['nachname'] . '","' . $_POST['vorname'] . '","' . $_POST['lizenz'] . '","' . $_SESSION['browser'] . '","' . $_SESSION['aufloesung'] . '","' . $_SESSION['mobile'] . '","' . $_SESSION['java'] . '","' . $_SESSION['java_version'] . '","' . $_SESSION['flash'] . '","' . $_SESSION['flash_version'] . '","' . $_SESSION['cookies'] . '","' . $_SESSION['zeitzone'] . '","' . $_SESSION['sprache'] . '","' . $_SESSION['fonts'] . '","' . $_SESSION['system'] . '")';

    mysqli_query($db,$sqlab);

    echo'<hr><br>Danke für die Teilnahme am Hardwaretest!<p />';
    session_destroy();
        
    }
}
echo'</form>';
?>
</body>
</html>
