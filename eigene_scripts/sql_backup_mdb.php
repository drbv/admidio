<?php
require("./intern/backup_config_mdb.php");

// echo "Start dump\n";
passthru("mysqldump --user=$dbuser --password=$dbpassword --host=$dbhost $dbname | gzip -c  > $dumpfile");
echo "-- Dump completed -- ";
// echo $dumpfile;

?>