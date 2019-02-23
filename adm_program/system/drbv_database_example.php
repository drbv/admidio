<?php

// rmenken: Admidio DB Zugang   
function ADMIDIOdb(){
  $ADMIDIOdb = mysqli_connect("mysql.db.com1","mysql.db.user1","mypasswd", "mysql.db.name1");//php7 admidio system 
  return $ADMIDIOdb;
}  

// rmenken: DRBV TLP DB Zugang
function DRBVdb(){  
  $DRBVdb    = mysqli_connect("mysql.db.com2","mysql.db.user2","mypasswd", "mysql.db.name2");//drbv ergebnisse db
  return $DRBVdb;
}
  
// rmenken: DRBV Musik DB Zugang
function MUSIKdb(){   
  $MUSIKdb   = mysqli_connect("mysql.db.com3","mysql.db.user3","mypasswd", "mysql.db.name3");//drbv musik db
  return $MUSIKdb;
}  
  
?>