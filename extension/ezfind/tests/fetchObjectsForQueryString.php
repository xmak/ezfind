<?php

//$languageCode = 'fre-FR';
$languageCode = null;
$groupByLanguage = true;
$queryString = 'foofoo';

$results = eZFindElevateConfiguration::fetchObjectsForQueryString( $queryString, $groupByLanguage, $languageCode );
var_dump( $results );

?>