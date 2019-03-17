<?php

require_once  '../../../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf(['tempDir' => $_SERVER["DOCUMENT_ROOT"] . '/tmp/mpdf']);

$filename = 'Test.pdf';
$html = '<bookmark content="Start of the Document" /><div>Section 1 text</div>';

//$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

if(is_file($filename))
   echo'Die Datei wurde gespeichert!';

?>