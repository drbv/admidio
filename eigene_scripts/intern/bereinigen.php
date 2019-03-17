<?php

function eingabebereinigen(&$value, $key)
 { 
 	// keine HTML-Tags erlaubt, außer p und br 
 	$value = strip_tags($value);
 	
 	// HTML-Tags maskieren 
 	$value = htmlspecialchars($value, ENT_QUOTES);

 	// Leerzeichen am Anfang und Ende beseitigen 
 	$value = trim($value);
 }

array_walk ( $_POST, 'eingabebereinigen' );
array_walk ( $_GET, 'isint' );
array_walk ( $_REQUEST, 'eingabebereinigen' );

?>