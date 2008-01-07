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
ResultHighLight=disabled
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
FacetExtraAttributes[]
FacetExtraAttributes[]=xmp_ezdc_subject
FacetAttributes[]=xmp_ezdc_subject
FacetMeta[]
FacetMeta[]=class_name
FacetMeta[]=published
FacetMeta[]=modified
FacetMeta[]=owner_name
FacetMeta[]=author_name
FacetMeta[]=path_string
FacetMeta[]=main_parent_node
FacetMeta[]=path
FacetPrefix=
FacetMinCount=1
FacetSort=1
FacetLimit=2000
FacetOffset=0
FacetMissing=1

[FieldAliasSettings]
UseFieldAliases=enabled
FieldAliasList[]
FieldAliasList[id]=m_id
FieldAliasList[section]=m_section_id
FieldAliasList[class]=m_class_identifier
FieldAliasList[modified]=m_modified
FieldAliasList[published]=m_published
FieldAliasList[parent]=m_main_parent_node_id
FieldAliasList[publisher]=xmp_ezdc_publisher
FieldAliasList[type]=xmp_ezdc_type

[SortFieldSettings]
SortFieldList[]
SortFieldList[relevance]=score
SortFieldList[path]=m_path
SortFieldList[published]=m_published
SortFieldList[modified]=m_modified
SortFieldList[section]=m_section_id
SortFieldList[depth]=m_depth
SortFieldList[class_identifier]=m_class_identifier
SortFieldList[class_name]=m_class_name
SortFieldList[priority]=m_priority
SortFieldList[name]=m_name
SortFieldList[attribute]=attr_*
SortFieldList[xmp]=xmp_*

*/ ?>
