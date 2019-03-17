<?php

array_walk ( $_POST, 'eingabebereinigen' );
array_walk ( $_GET, 'isint' );
array_walk ( $_REQUEST, 'eingabebereinigen' );
 
require("./intern/dboeffnen.inc.php");

// Startbuchnummer eingeben
echo'<form action="' . $_SERVER["PHP_SELF"] . '" method=post>';  

echo'<center><table>';
   
   echo'<tr><td><b> Bitte Startbuchnummer eingeben: </b> ';
   echo'<input type=text name="sb_nr" value="' . $_POST[sb_nr] . '"  size=5 maxlength=5></td></tr>';
   echo'<td colspan = 2><center><input type=submit name="absenden" value="Absenden"></td></tr>';

echo'</table>';  
  
echo'</form>';

// Tanzpaar in Datenbank suchen
  
$sqlab = 'SELECT turniernummer, paar_id_tlp FROM paare WHERE startbuch = ' . $_POST["sb_nr"]; 
// echo"$sqlab<br>"; 
$punkte =  mysqli_query($db, $sqlab);

while($wr = mysqli_fetch_array($punkte))
      {
//        print_r($wr);echo"<br>";
       echo'Turniernummer: ' . $wr["turniernummer"] .'<br>';
       
       $sqlab = 'SELECT * FROM majoritaet WHERE turniernummer = ' . $wr["turniernummer"] . ' AND TP_ID = ' . $wr["paar_id_tlp"]; // . ' ORDER BY wr_id'; 
       $punkte_wr = mysqli_query($db, $sqlab);
// echo"$sqlab<br>"; 
     
       while($wr_punkte = mysqli_fetch_array($punkte_wr))
            {
             // print_r($wr_punkte);echo'<br>';
             echo'WR 1: ' . $wr_punkte[WR1] . ' WR 2: ' . $wr_punkte[WR2] . ' WR 3: ' . $wr_punkte[WR3] . ' WR 4: ' . $wr_punkte[WR4] . ' WR 5: ' . $wr_punkte[WR5] . ' WR 6: ' . $wr_punkte[WR6] . ' WR 7: ' . $wr_punkte[WR7] . ' WR 8: ' . $wr_punkte[WR8] . '<p />';
            }
       //echo'<p />';  
      }

?>