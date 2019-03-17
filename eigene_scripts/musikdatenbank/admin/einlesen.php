<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8" />
    <title>Musikdatenbank Admin</title>
</head>
<body>

<?php
session_start();

require_once("./dboeffnen.inc.php");

/*
echo’POST: ’;print_r($_POST);echo"<br>";
echo’SESSION: ’;print_r($_SESSION);echo"<br>";
*/

echo'<br>';


echo"<form action='$PHP_SELF' method='post'>";

if(!$_POST['datum'])
    $_POST['datum'] = date("d.m.Y");

echo'<input type="text" name="datum" value="' . $_POST['datum'] . '">'. ' (DD.MM.JJJJ, Nach diesem Datum hinzugefügt!)<br>';
echo'<input type="text" name="pfad" value="' . $_POST['pfad'] . '">'. ' (rocknroll/xx_Takte/)<br>';
if($_POST['rr'])
   echo'<input type="checkbox" name="rr" value="1" checked><label for="rr">Rock´n´Roll</label>' ;
else
   echo'<input type="checkbox" name="rr" value="1"><label for="rr">Rock´n´Roll</label>' ;
if($_POST['bw'])
   echo' <input type="checkbox" name="bw" value="1" checked><label for="bw">Boogie Woogie</label><br>';
else
   echo' <input type="checkbox" name="bw" value="1"><label for="bw">Boogie Woogie</label><br>';
echo'<input type="submit" value="Absenden">'. '<br>';;

if($_POST['pfad'])
{
$tag = substr($_POST['datum'],0,2);
$monat = substr($_POST['datum'],3,2);
$jahr = substr($_POST['datum'],6);
// echo $tag . ' ' . $monat . ' ' . $jahr . '<br>';;
$zeit = mktime(0,0,0,$monat,$tag,$jahr);
// echo $zeit . date(" d.m.Y H:i",$zeit) .'<br>';
$verzeichnis = "../../../../downloads/" . $_POST['pfad'];
 
// Text, ob ein Verzeichnis angegeben wurde
if ( is_dir ( $verzeichnis ))
{
    // öffnen des Verzeichnisses
    if ( $handle = opendir($verzeichnis) )
    {
        // einlesen der Verzeichnisses
        while (($file = readdir($handle)) !== false)
        {   
            if($file !="." && $file !="..")
              {
               if(filemtime($verzeichnis.'/'.$file) > $zeit)
               {
                $teilstring = explode('-', $file);
                $interpret_org = explode('.', $teilstring[1]);
                $interpret = str_replace('_', ' ', $interpret_org[0]);
                $titel = substr($teilstring[0],3);
                $titel =str_replace('_', ' ', $titel);
                $takte = substr($file,0,2);
//    echo'File: ' . $file . ' $Teilsring: '; print_r($teilstring);echo"<br>";
if($_POST['bw'])
  {
   $genre_org = explode('/', $_POST['pfad']);
   $genre = substr($genre_org[1],0,-3);
   if(substr($genre,2,1) == '_')
      $genre ='';
  }

                $sqlab = "insert turniermusik set bezeichnung = '" . $file . "', pfad = '" . $_POST['pfad'] . "/', titel = '" . $titel . "', interpret = '" . $interpret . "', takte = '" . $takte . "', rocknroll = '" . $_POST['rr'] . "', boogiewoogie = '" . $_POST['bw'] . "', genre = '" . $genre . "'";
               mysqli_query($db, $sqlab);
// echo'SQL_Query: ' . $sqlab. '<br>';
                if(mysqli_affected_rows($db));
                   $erfolg = $erfolg + 1;echo$erfolg. '<br>';

 echo$sqlab. '<br>';   
               }
              }
        }
    }
}
echo'Es wurden ' . $erfolg .' Datensätze hinzugefügt!<br>';

echo'</form>';
}
else
  echo'Bitte geben Sie das Verzeichnis ein!<p>';
?>

</body>
</html>