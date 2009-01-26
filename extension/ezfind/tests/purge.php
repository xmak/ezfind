<?php

// test various combinations of the parameters below, including default values.
//  compare before and after visually with a simple 'SELECT * FROM ezfind_elevate_configuration'.

$queryString = 'bar';
// $queryString = '';

// $objectID = 100;
$objectID = null;

$languageCode = 'ger-GE';
// $languageCode = null;

eZFindElevateConfiguration::purge( $queryString, $objectID, $languageCode );

?>