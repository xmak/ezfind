<?php

$queryString = 'foo bar';
$objectID = 100;
// $languageCode = eZFindElevateConfiguration::WILDCARD;
$languageCode = 'eng-GB';

eZFindElevateConfiguration::add( $queryString, $objectID, $languageCode );

?>