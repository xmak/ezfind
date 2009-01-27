<?php
/*
 * Compare elevate.xml before and after the call. Make sure it is empty before the call
 * and has some content after.
 * The file is stored in solr.ini[SolrBase].DataDirFullPath
 *
 */

var_dump( eZFindElevateConfiguration::synchroniseWithSolr() );

?>