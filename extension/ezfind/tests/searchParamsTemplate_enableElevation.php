<?php

require( "kernel/common/template.php" );

// check the request sent to Solr, in Solr's output : the enableElevation parameter should be present.
// Add some configuration elevating an object returned as search result when searching for 'ez', and synchronise it with Solr.
//   The afore-mentioned object should then be the first result when 'EnableElevation' is true, and may not be the first result when 'EnableElevation' is false.

$tpl = templateInit();
echo $tpl->fetch( "file:extension/ezfind/tests/searchParamsTemplate_enableElevation.tpl" );

?>