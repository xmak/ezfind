<?php /*

[SolrBaseSettings]
# Base URI of the Solr server
SearchServerURI=http://localhost:8983/solr
# Realm is used to differentiate between "multiple indexes"
# You can have several of them combined into the same index
# To set up physically separated indexes, you should run multiple instances of Solr
# See the Solr wiki on how to do this (Jetty, Tomcat)
Realm=nfpro
# This setting will forward the Solr Debug output to the search template.
DebugQueryRequest=enabled

[HighLightSettings]
ResultHighLight=enabled
SnippetLength=2
FragmentSize=100
RequireFieldMatch=true
PreText=<b>
PostText=</b>
# If Fields is left empty, all seachable attributes are used
HighLightFields[]



[FacetSettings]
UseFacets=enabled
FacetAttributes[]
#FacetAttributes[]=keywords
FacetMeta[]
#FacetMeta[]=class_name
#FacetMeta[]=published
#FacetMeta[]=modified
#FacetMeta[]=owner_name
#FacetMeta[]=author_name
FacetMeta[]=path_string
FacetPrefix=
FacetMinCount=1
FacetSort=1
FacetLimit=100
FacetOffset=0
FacetMissing=1

*/ ?>