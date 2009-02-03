<?php

// check the request sent to Solr, in Solr's output : the enableElevation parameter should be present.
// Add some configuration elevating an object returned as search result when searching for 'ez', and synchronise it with Solr.
//   The afore-mentioned object should then be the first result when 'EnableElevation' is true, and may not be the first result when 'EnableElevation' is false.

$params = array( 'EnableElevation' => true );
// $params = array( 'EnableElevation' => false );
$solr = new eZSolr();
$results = $solr->search( 'ez', $params );

foreach( $results['SearchResult'] as $result )
{
    var_dump( $result->attribute( 'name' ) );
    echo "\n";
}

?>