<?php

$queryString = 'foo bar';
$objectID = 77;
$languageCode = 'eng-GB';

$row = array( 'search_query'     => $queryString,
              'contentobject_id' => $objectID,
              'language_code'    => $languageCode );

$conf = new eZFindElevateConfiguration( $row );
$conf->store();

var_dump( $conf->attribute( 'search_query' ) );
var_dump( $conf->attribute( 'contentobject_id' ) );
var_dump( $conf->attribute( 'language_code' ) );

?>