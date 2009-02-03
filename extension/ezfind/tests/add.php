<?php

$queryString = 'ez';
// eZ Awards crowd [Image]
$objectID = 66;
$languageCode = eZFindElevateConfiguration::WILDCARD;
// $languageCode = 'eng-GB';

eZFindElevateConfiguration::add( $queryString, $objectID, $languageCode );

?>