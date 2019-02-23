<?php
/******************************************************************************
 * Form for Vereinsname/Zustelladresse ändern
 *
 * Copyright    : (c) 2016 DRBV WebTeam
 * Homepage     : http://www.drbv.de
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 *****************************************************************************/

echo '         <form name="Formular" autocomplete="off">
                  <!-- Hier die eigentlichen Formularfelder eintragen. Die folgenden sind Beispielangaben. -->                  
                  <font face="Verdana" size="3" color="#000080">
                  <h3>&nbsp;Änderung Vereinsname</h3>
                  <br />
                  <p>                    
                  <div style="text-align: justify;">
                    Für Änderungen des Vereinsnamens wird weiterhin ein vom Verein mit Unterschrift bestätigtes Formular benötigt, welches an die Geschäftsstelle zu senden ist.
                    <br /><br />
                    Bitte dazu diesen <a href="http://www.drbv.de/cms/images/PDF/Formulare/Aenderung-Vereinsname-Zustelladresse.pdf" target="_blank">Formularvordruck</a> verwenden!
                    <br /><br />
                    Die Zustelladresse des Vereins, kann einfach über das ';
                    if($gCurrentUser->editProfile($user)){
                      echo '<a href="'. $g_root_path. '/adm_program/modules/profile/profile_new.php?user_id='. $user->getValue('usr_id'). '">eigene Vereinsprofil</a>';
                    };
                  echo' online geändert werden.                  
                  </div>
                  </p>
                  <!-- Ende der Beispielangaben -->
              </form>';  
    
?>