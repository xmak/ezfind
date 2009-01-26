<?php

// $languageCode = 'fre-FR';
$languageCode = null;
$sortByLanguage = true;
$objectID = 200;

$results = eZFindElevateConfiguration::fetchQueryStringsForObject( $objectID, $sortByLanguage, $languageCode );
var_dump( $results );


?>