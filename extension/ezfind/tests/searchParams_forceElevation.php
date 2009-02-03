<?php
/*
 * Check the request sent to Solr, in Solr's output : the forceElevation parameter should be present.
 *
 * Change the sort criteria to the non default one, normally cancelling the effect of Elevation.
 * Spot a result, not the first one. Create an elevate configuration for this object, and synchronise the conf with Solr.
 *
 * Set 'ForceElevation' to false, the elevate object ( mentionned above ) should NOT be the first result
 * Then, set 'ForceElevation' to true, the elevate object ( mentionned above ) SHOULD be the first result
 *
 * WARNING : 'ForceElevation' is supported at runtime from Solr@rev:735117
 */

$params = array( 'ForceElevation' => true );
// $params = array( 'ForceElevation' => false );

// use the non-default sort criteria
$params['SortBy'] = array( 'published' => 'desc' );

$solr = new eZSolr();
$results = $solr->search( 'ez', $params );

foreach( $results['SearchResult'] as $result )
{
    var_dump( $result->attribute( 'name' ) );
    echo "\n";
}

?>