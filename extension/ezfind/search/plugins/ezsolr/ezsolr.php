<?php
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Find
// SOFTWARE RELEASE: 1.0.x
// COPYRIGHT NOTICE: Copyright (C) 2007 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//



include_once( eZExtension::baseDirectory() . '/ezfind/lib/ezsolrbase.php' );
include_once( eZExtension::baseDirectory() . '/ezfind/lib/ezsolrdoc.php' );
include_once( eZExtension::baseDirectory() . '/ezfind/lib/ezfindresultobject.php' );

class eZSolr
{
    /*!
     \brief Constructor
    */
    function eZSolr()
    {
        eZDebug::createAccumulatorGroup( 'solr', 'Solr search plugin' );
        $this->SolrINI = eZINI::instance( 'solr.ini' );
        $this->FindINI = eZINI::instance( 'ezfind.ini' );
        $this->SearchServerURI = $this->SolrINI->variable( 'SolrBaseSettings', 'SearchServerURI' );
        $this->Solr = new eZSolrBase( $this->SearchServerURI );
        $realm = $this->SolrINI->variable( 'SolrBaseSettings', 'Realm' );
        if ( $realm == 'default' )
        {
            $SiteINI = eZINI::instance( 'site.ini' );
            $this->Realm = $SiteINI->variable( 'DatabaseSettings', 'Database' );
        }
        else
        {
            $this->Realm = $realm;
        }

        $this->resultHighLight = 'no';
        if ( $this->SolrINI->variable( 'HighLightSettings', 'ResultHighLight' ) == 'enabled' )
        {
            $this->ResultHighLight            = 'yes';
            $this->HighLightFields            = array();
            $this->HighLightSnipplets         = 2;
            $this->HighLightFragmentSize      = 100;
            $this->HighLightRequireFieldMatch = 'true';
            $this->HighLightPreText           = '<b>';
            $this->HighLightPostText          = '</b>';

            if ( count( $this->SolrINI->variable( 'HighLightSettings', 'HighLightFields' ) ) )
            {
                $this->HighLightFields = $this->SolrINI->variable( 'HighLightSettings', 'HighLightFields' );
            }
            if ( $this->SolrINI->hasVariable( 'HighLightSettings', 'SnippetLength' ) )
            {
                $this->HighLightSnipplets = $this->SolrINI->variable( 'HighLightSettings', 'SnippetLength' );
            }
            if ( $this->SolrINI->hasVariable( 'HighLightSettings', 'FragmentSize' ) )
            {
                $this->HighLightFragmentSize = $this->SolrINI->variable( 'HighLightSettings', 'FragmentSize' );
            }
            if ( $this->SolrINI->hasVariable( 'HighLightSettings', 'RequireFieldMatch' ) )
            {
                $this->HighLightRequireFieldMatch = $this->SolrINI->variable( 'HighLightSettings', 'RequireFieldMatch' );
            }
            if ( $this->SolrINI->hasVariable( 'HighLightSettings', 'PreText' ) )
            {
                $this->HighLightPreText = $this->SolrINI->variable( 'HighLightSettings', 'PreText' );
            }
            if ( $this->SolrINI->hasVariable( 'HighLightSettings', 'PostText' ) )
            {
                $this->HighLightPostText = $this->SolrINI->variable( 'HighLightSettings', 'PostText' );
            }
        }

        $this->UseFacets = 'false';
        if ( $this->SolrINI->variable( 'FacetSettings', 'UseFacets' ) == 'enabled' )
        {
            $this->UseFacets     = 'true';
            $this->FacetFields   = array();
            $this->FacetMinCount = 1;
            $this->FacetSort     = 'true';
            $this->FacetLimit    = 100;
            $this->FacetOffset   = 0;
            $this->FacetMissing  = 'false';
            $this->FacetPrefix   = '';

            if ( count( $this->SolrINI->variable( 'FacetSettings', 'FacetAttributes' ) ) )
            {
                $facetAttributes = $this->SolrINI->variable( 'FacetSettings', 'FacetAttributes' );
                foreach ( $facetAttributes as $attr )
                {
                    $this->FacetFields[] = 'attr_' . $attr;
                }
            }
            if ( count( $this->SolrINI->variable( 'FacetSettings', 'FacetMeta' ) ) )
            {
                $facetAttributes = $this->SolrINI->variable( 'FacetSettings', 'FacetMeta' );
                foreach ( $facetAttributes as $attr )
                {
                    $this->FacetFields[] = 'm_' . $attr;
                }
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetMinCount' ) )
            {
                $this->FacetMinCount = $this->SolrINI->variable( 'FacetSettings', 'FacetMinCount' );
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetSort' ) )
            {
                $this->FacetSort = $this->SolrINI->variable( 'FacetSettings', 'FacetSort' ) ? 'true' : 'false';
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetLimit' ) )
            {
                $this->FacetLimit = $this->SolrINI->variable( 'FacetSettings', 'FacetLimit' );
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetOffset' ) )
            {
                $this->FacetOffset = $this->SolrINI->variable( 'FacetSettings', 'FacetOffset' );
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetMissing' ) )
            {
                $this->FacetMissing = $this->SolrINI->variable( 'FacetSettings', 'FacetMissing' ) ? 'true' : 'false';
            }
            if ( $this->SolrINI->hasVariable( 'FacetSettings', 'FacetPrefix' ) )
            {
                $this->FacetPrefix = $this->SolrINI->variable( 'FacetSettings', 'FacetPrefix' );
            }
        }

        $this->UseFieldAliases = false;
        if ( $this->SolrINI->variable( 'FieldAliasSettings', 'UseFieldAliases' ) == 'enabled' )
        {
            $this->UseFieldAliases = true;
            if ( count( $this->SolrINI->variable( 'FieldAliasSettings', 'FieldAliasList' ) ) )
            {
                $this->FieldAliasList = $this->SolrINI->variable( 'FieldAliasSettings', 'FieldAliasList' );
            }
        }

        if ( $this->SolrINI->variable( 'SolrBaseSettings', 'DebugQueryRequest' ) == 'enabled' )
        {
            $this->DebugQueryRequest = true;
        }
        else
        {
            $this->DebugQueryRequest = false;
        }
    }

    /*!
     Returns a list of meta attributes to post to the search server
    */
    function metaAttributes()
    {
        $metaAttributes = array(
            'id',
            'class_name',
            'section_id',
            'owner_id',
            'contentclass_id',
            'current_version',
            'remote_id',
            'class_identifier',
            'main_node_id',
            'modified',
            'published',
            'main_parent_node_id'
            );
        return $metaAttributes;
    }

    /*!
     \brief Adds a content object to the Solr search server
    */
    function addObject( $contentObject, $doCommit = true )
    {
        $ini = eZINI::instance();

        // Add all translations to the document list
        $docList = array();

        // Get global object values
        $mainNode = $contentObject->attribute( 'main_node' );
        if ( !$mainNode )
        {
            eZDebug::writeError( 'Unable to fetch main node for object: ' . $contentObject->attribute( 'id' ), 'eZSolr::addObject()' );
            return;
        }
        $pathArray = $mainNode->attribute( 'path_array' );
        $currentVersion = $contentObject->currentVersion();
        $metaAttributeValues = array();
        foreach( eZSolr::metaAttributes() as $attributeName )
        {
            $metaAttributeValues[$attributeName] = $contentObject->attribute( $attributeName );
        }

        // Check anonymous user access.
        if ( $this->FindINI->variable( 'SiteSettings', 'IndexPubliclyAvailable' ) == 'enabled' )
        {
            $anonymousUserID = $ini->variable( 'UserSettings', 'AnonymousUserID' );
            $currentUserID = eZUser::currentUserID();
            $user = eZUser::instance( $anonymousUserID );
            eZUser::setCurrentlyLoggedInUser( $user, $anonymousUserID );
            $anonymousAccess = $contentObject->attribute( 'can_read' );
            $user = eZUser::instance( $currentUserID );
            eZUser::setCurrentlyLoggedInUser( $user, $currentUserID );
            $anonymousAccess = $anonymousAccess ? 'true' : 'false';
        }
        else
        {
            $anonymousAccess = 'false';
        }

        foreach( $currentVersion->translationList( false, false ) as $languageCode )
        {
            $doc = new eZSolrDoc();


            // Set global unique object ID
            $doc->addField( 'm_guid', $this->guid( $contentObject, $languageCode ) );

            // Set realm and installation ident.
            $doc->addField( 'm_realm', $this->Realm );
            $doc->addField( 'm_installation_id', $this->installationID() );
            $doc->addField( 'm_installation_url',
                            $this->FindINI->variable( 'SiteSettings', 'URLProtocol' ) . $ini->variable( 'SiteSettings', 'SiteURL' ) . '/' );

            // Set Object attributes
            $doc->addField( 'm_name', $contentObject->name( false, $languageCode ) );
            $doc->addField( 'm_anon_access', $anonymousAccess );
            $doc->addField( 'm_language_code', $languageCode );

            foreach ( $metaAttributeValues as $name => $value )
            {
                $doc->addField ( 'm_' . $name, $value );
            }

            // Add main url_alias
            $doc->addField( 'm_main_url_alias', $mainNode->attribute( 'url_alias' ) );

            $parentNode = $mainNode->attribute( 'parent' );
            $doc->addField( 'm_path_string', $mainNode->attribute( 'path_string' ) );

            $doc->addField( 'm_parent_path_string', $parentNode->attribute( 'path_string' ) );

            $doc->addField( 'm_depth', $parentNode->attribute( 'depth' ) );

            foreach ( $pathArray as $pathNodeID )
            {
                $doc->addField( 'm_path', $pathNodeID );
            }

            eZContentObject::recursionProtectionStart();

            foreach ( $currentVersion->contentObjectAttributes( $languageCode ) as $attribute )
            {
                $metaData = array();
                $classAttribute = $attribute->contentClassAttribute();
                if ( $classAttribute->attribute( 'is_searchable' ) == 1 )
                {
                    $tmpMetaData = $attribute->metaData();
                    if ( !is_array( $tmpMetaData ) )
                    {
                        $tmpMetaData = array( array( 'id' => 'attr_' . $classAttribute->attribute( 'identifier' ),
                                                     'text' => $tmpMetaData ) );
                    }
                    elseif ( is_array( $tmpMetaData ) && array_key_exists( 'text', $tmpMetaData ) )
                    {
                        $metaDataText = '';
                        foreach( $metaData as $metaDataElement )
                        {
                            $metaDataText .= ' ' . $metaDataElement['text'];
                        }
                        $tmpMetaData = array( array( 'id' => 'attr_' . $classAttribute->attribute( 'identifier' ),
                                                     'text' => $tmpMetaData ) );
                    }
                    $metaData = array_merge( $metaData, $tmpMetaData );

                    foreach( $metaData as $metaDataPart )
                    {
                        $fieldName = $metaDataPart['id'];
                        if ( is_array( $metaDataPart['text'] ) )
                        {
                            foreach ( $metaDataPart['text'] as $text )
                            {
                                if ( is_array( $text ) )
                                {
                                    $resultText = '';
                                    foreach ( $text as $textPart )
                                    {
                                        $resultText .= ' ' . $textPart;
                                    }
                                    $text = $resultText;
                                }
                                $doc->addField( $fieldName, $text );
                            }
                        }
                        else
                        {
                            $doc->addField( $fieldName, $metaDataPart['text'] );
                        }
                    }
/*                    if ( count( $metaData ) )
                    {
                        foreach( $metaData as $metaDataElement )
                        {
                            $metaDataText .= ' ' . $metaDataElement['text'];
                        }
                    }
                    else
                    {
                        $metaDataText = $metaData;
                    }
                    $doc->addField( 'attr_' . $classAttribute->attribute( 'identifier' ),
                                    $metaDataText );*/
                }
            }
            eZContentObject::recursionProtectionEnd();

            $docList[] = $doc;
        }

        $this->Solr->addDocs( $docList );
    }

    /*!
     \return a Lucene/Solr query string which can be used as filter query for Solr
     \todo Handle "group" value of Owner limitation
     \todo Investigate if we can group multiple clauses to a single field: http://lucene.apache.org/java/docs/queryparsersyntax.html#Field%20Grouping
    */
    function policyLimitationFilterQuery()
    {
        include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
        $currentUser = eZUser::currentUser();
        $accessResult = $currentUser->hasAccessTo( 'content', 'read' );

        $filterQuery = false;

        // Add limitations for filter query based on local permissions.
        if ( !in_array( $accessResult['accessWord'], array( 'yes', 'no' ) ) )
        {
            $policies = $accessResult['policies'];

            $limitationHash = array(
                'Class'        => 'm_contentclass_id',
                'Section'      => 'm_section_id',
                'User_Section' => 'm_section_id',
                'Subtree'      => 'm_path_string',
                'User_Subtree' => 'm_path_string',
                'Node'         => 'm_main_node_id',
                'Owner'        => 'm_owner_id' );

            $filterQueryPolicies = array();

            // policies are concatenated with OR
            foreach ( $policies as $limitationList )
            {
                // policy limitations are concatenated with AND
                $filterQueryPolicyLimitations = array();

                foreach ( $limitationList as $limitationType => $limitationValues )
                {
                    // limitation values of one type in a policy are concatenated with OR
                    $filterQueryPolicyLimitationParts = array();

                    switch ( $limitationType )
                    {
                        case 'User_Subtree':
                        case 'Subtree':
                        {
                            foreach ( $limitationValues as $limitationValue )
                            {
                                $pathString = trim( $limitationValue, '/' );
                                $pathArray = explode( '/', $pathString );
                                // we only take the last node ID in the path identification string
                                $subtreeNodeID = array_shift( $pathArray );
                                $filterQueryPolicyLimitationParts[] = 'm_path:' . $subtreeNodeID;
                            }
                        } break;

                        case 'Node':
                        {
                            foreach ( $limitationValues as $limitationValue )
                            {
                                $pathString = trim( $limitationValue, '/' );
                                $pathArray = explode( '/', $pathString );
                                // we only take the last node ID in the path identification string
                                $nodeID = array_shift( $pathArray );
                                $filterQueryPolicyLimitationParts[] = 'm_main_node_id:' . $nodeID;
                            }
                        } break;


                        case 'Owner':
                        {
                            $filterQueryPolicyLimitationParts[] = 'm_owner_id:' . $currentUser->attribute ( 'contentobject_id' );
                        } break;

                        case 'Class':
                        case 'Section':
                        case 'User_Section':
                        {
                            foreach ( $limitationValues as $limitationValue )
                            {
                                $filterQueryPolicyLimitationParts[] = $limitationHash[$limitationType] . ':' . $limitationValue;
                            }
                        } break;

                        default :
                        {
                            eZDebug::writeDebug( $limitationType, 'eZSolr::policyLimitationFilterQuery unknown limitation type: ' . $limitationType );
                            continue;
                        }
                    }

                    $filterQueryPolicyLimitations[] = '( ' . implode( ' OR ', $filterQueryPolicyLimitationParts ) . ' )';
                }

                if ( count( $filterQueryPolicyLimitations ) > 0 )
                {
                    $filterQueryPolicies[] = '( ' . implode( ' AND ', $filterQueryPolicyLimitations ) . ')';
                }
            }

            if ( count( $filterQueryPolicies ) > 0 )
            {
                $filterQuery = implode( ' OR ', $filterQueryPolicies );
            }
        }

        // Add limitations for allowing search of other installations.
        $anonymousPart = '';
        if ( $this->FindINI->variable( 'SiteSettings', 'SearchOtherInstallations' ) == 'enabled' )
        {
            $anonymousPart = ' OR m_anon_access:true ';
        }

        if ( !empty( $filterQuery ) )
        {
            $filterQuery = '( ( m_installation_id:' . $this->installationID() . ' AND ' . $filterQuery . ' ) ' . $anonymousPart . ' )';
        }
        else
        {
            $filterQuery = '( m_installation_id:' . $this->installationID() . $anonymousPart . ' )';
        }

        // Add limitations based on allowed languages.
        $ini = eZINI::instance();
        if ( $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) )
        {
            $filterQuery = '( ' . $filterQuery . ' AND ( m_language_code:' .
                implode( ' OR m_language_code:', $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) ) . ' ) )';
        }

        eZDebug::writeDebug( $filterQuery, 'eZSolr::policyLimitationFilterQuery' );

        return $filterQuery;
    }

    function commit()
    {
        //$updateURI = $this->SearchServerURI . '/update';
        $this->Solr->commit();
    }


    function optimize( $withCommit = false )
    {
        $this->Solr->optimize( $withCommit );
    }


    /*!
     \brief Removes an object from the Solr search server
    */
    function removeObject( $contentObject )
    {
        $this->Solr->deleteDocs( array(),
                                 'm_id:' . $contentObject->attribute( 'id' ) . ' AND '.
                                 'm_installation_id:' . $this->installationID() );
    }

    /*!
     \brief get an array of class attribute identifiers based on a list of class ids, prepended with attr_

     \param $classIDArray ( if set to false, fetch for all existing classes )
     \param $classAttributeID ( if set to false, fetch for all )
    */
    function getClassAttributes( $classIDArray = false, $classAttributeIDArray = false )
    {
        eZDebug::createAccumulator( 'solr_classattribute_list', 'solr' );
        eZDebug::accumulatorStart( 'solr_classattribute_list' );
        include_once( 'kernel/classes/ezcontentclass.php' );
        $fieldArray = array();

        $classAttributeArray = array();

        if ( is_numeric( $classAttributeIDArray ) and $classAttributeIDArray > 0 )
        {
            $classAttributeArray[] = eZContentClassAttribute::fetch( $classAttributeIDArray );
        }
        else if ( is_array( $classAttributeIDArray ) )
        {
            foreach( $classAttributeIDArray as $classAttributeID )
            {
                $classAttributeArray[] = eZContentClassAttribute::fetch( $classAttributeID );
            }
        }

        if ( !empty( $classAttributeArray ) )
        {
            foreach( $classAttributeArray as $classAttribute )
            {
                $fieldArray[] = 'attr_' . $classAttribute->attribute( 'identifier' );
            }
        }
        else
        {
            $classArray = array();
            // Fetch class list.
            if ( is_numeric( $classIDArray ) and  $classIDArray > 0 )
            {
                $classArray[] = eZContentClass::fetch( $classIDArray );
            }
            else if ( is_array( $classIDArray ) )
            {
                foreach ( $classIDArray as $sccID )
                {
                    $classArray[] = eZContentClass::fetch( $sccID );
                }
            }
            else
            {
                $classArray = eZContentClass::fetchList();
            }

            // Fetch class attribute list.
            foreach ( $classArray as $class )
            {
                //eZDebug::writeDebug( $class, ' In class array loop ' );
                if ( is_object( $class ) )
                {
                    $attribs = $class->fetchSearchableAttributes();
                    foreach( $attribs as $attrib )
                    {
                        $fieldArray[] = 'attr_' . $attrib->attribute( 'identifier' );
                    }
                }
            }
        }
        //eZDebug::writeDebug( $fieldArray, ' Field array ' );
        eZDebug::accumulatorStop( 'solr_classattribute_list' );
        return $fieldArray;
    }


    /*!
     \brief Search on the Solr search server
     \todo see if we can use eZHTTPTool::sendHTTPRequest instead
    */
    function search( $searchText, $params = array(), $searchTypes = array() )
    {
        eZDebug::writeDebug( $params, 'search params' );
        $searchCount = 0;

        $offset = ( isset( $params['SearchOffset'] ) && $params['SearchOffset'] ) ? $params['SearchOffset'] : 0;
        $limit = ( isset( $params['SearchLimit']  ) && $params['SearchLimit'] ) ? $params['SearchLimit'] : 20;
        $subtrees = isset( $params['SearchSubTreeArray'] ) ? $params['SearchSubTreeArray'] : array();
        $contentClassID = ( isset( $params['SearchContentClassID'] ) && $params['SearchContentClassID'] <> -1 ) ? $params['SearchContentClassID'] : false;
        $contentClassAttributeID = ( isset( $params['SearchContentClassAttributeID'] ) && $params['SearchContentClassAttributeID'] <> -1 ) ? $params['SearchContentClassAttributeID'] : false;
        $sectionID = isset( $params['SearchSectionID'] ) && $params['SearchSectionID'] > 0 ? $params['SearchSectionID'] : false;
        $filterQuery = array();

        $sortArray = isset( $params['SortArray'] ) ? $params['SortArray'] : array();
var_dump($sortArray);
        //FacetFields and FacetQueries not used yet! Need to add it to the module as well
//         $facetFields = ( isset( $params['FacetFields'] ) && $params['FacetFields'] ) ? $params['FacetFields'] : array('m_class_name');
//         $facetQueries = ( isset( $params['FacetQueries'] ) && $params['FacetQueries'] ) ? $params['FacetQueries'] : array();

        if ( count( $subtrees ) > 0 )
        {
            $subtreeQueryParts = array();
            foreach ( $subtrees as $subtreeNodeID )
            {
                $subtreeQueryParts[] = 'm_path:' . $subtreeNodeID;
            }

            $filterQuery[] = implode( ' OR ', $subtreeQueryParts );
        }

        $policyLimitationFilterQuery = $this->policyLimitationFilterQuery();

        if ( $policyLimitationFilterQuery !== false )
        {
            $filterQuery[] = $policyLimitationFilterQuery;
        }

        if ( $contentClassID )
        {
            if ( is_array( $contentClassID ) )
            {
                $classQueryParts = array();
                foreach ( $contentClassID as $classID )
                {
                    if ( is_int( (int)$classID ) and  (int)$classID != 0 )
                    {
                        $classQueryParts[] = 'm_contentclass_id:' . $classID;
                    }
                    elseif ( is_string( $classID ) and  $classID != "" )
                    {
                        $class = eZContentClass::fetchByIdentifier( $classID );
                        if ( $class )
                        {
                            $classID = $class->attribute( 'id' );
                            $classQueryParts[] = 'm_contentclass_id:' . $classID;
                        }
                        else
                        {
                            eZDebug::writeError( 'No valid content class', 'eZSolr::search' );
                        }
                    }
                    else
                    {
                        eZDebug::writeError( 'No valid content class', 'eZSolr::search' );
                    }
                }
                $filterQuery[] = implode( ' OR ', $classQueryParts );
            }
            elseif ( is_int( (int)$contentClassID ) and  (int)$contentClassID != 0 )
            {
                $filterQuery[] = 'm_contentclass_id:' . $contentClassID;
            }
            elseif ( is_string( $contentClassID ) and  $contentClassID != "" )
            {
                $class = eZContentClass::fetchByIdentifier( $contentClassID );
                if ( $class )
                {
                    $contentClassID = $class->attribute( 'id' );
                    $filterQuery[] = 'm_contentclass_id:' . $contentClassID;
                }
                else
                {
                    eZDebug::writeError( 'No valid content class', 'eZSolr::search' );
                }
            }
            else
            {
                eZDebug::writeError( 'No valid content class', 'eZSolr::search' );
            }
        }

        if ( $sectionID )
        {
            $filterQuery[] = 'm_section_id:' . $sectionID;
        }

        if ( $this->Realm )
        {
        }

        if ( $this->UseFieldAliases )
        {
            $fieldNames = array();
            $result = preg_match_all( '/([a-z]*):\"?[a-z]*\"?/U', $searchText, $fields );
            if ( $result > 0 )
            {
                foreach ( $fields[1] as $field )
                {
                    if ( array_key_exists( $field, $this->FieldAliasList ) )
                    {
                        $searchText = str_replace( $field, $this->FieldAliasList[$field], $searchText );
                    }
                }
            }
        }

        $sortString = '';
        if ( count( $sortArray ) )
        {
            $searchAttributeMappingList = array();
            if ( count( $this->SolrINI->variable( 'SortFieldSettings', 'SortFieldList' ) ) )
            {
                $searchAttributeMappingList = $this->SolrINI->variable( 'SortFieldSettings', 'SortFieldList' );
            }

            $sortList = false;
            if ( isset( $sortArray ) && is_array( $sortArray ) &&  count( $sortArray ) > 0 )
            {
                $sortList = $sortArray;
                if ( count( $sortList ) > 1 && !is_array( $sortList[0] ) )
                {
                    $sortList = array( $sortList );
                }
            }
            if ( $sortList !== false )
            {
                $sortingFieldList = array();
                foreach ( $sortList as $sortBy )
                {
                    $sortOrder = true; // true is ascending
                    if ( isset( $sortBy[1] ) )
                        $sortOrder = $sortBy[1];
                    $sortOrder .= $sortOrder ? " asc" : " desc";

                    $sortField = $sortBy[0];
                    if ( count( $searchAttributeMappingList ) && array_key_exists( $sortField, $searchAttributeMappingList ) )
                    {
                        $sortFieldName = $searchAttributeMappingList[$sortField];
                        if ( strpos( $sortFieldName, '*' ) && count( $sortBy ) == 3 )
                        {
                                $sortClassID = $sortBy[2];
                                if ( strpos( $sortClassID, '/' )  )
                                {
                                    list( $class, $attr ) = split("/", $sortClassID, 2 );
                                    if ( is_int( $attr ) )
                                    {
                                        include_once( 'kernel/classes/ezcontentclassattributenamelist.php' );
                                        $contentClass = eZContentClassAttribute::fetch( $attr );
                                        $sortFieldName = str_replace( '*', $contentClass->attribute( 'identifier' ), $sortFieldName );
                                    }
                                }
                                elseif ( is_int( $sortClassID ) )
                                {
                                    include_once( 'kernel/classes/ezcontentclassattributenamelist.php' );
                                    $contentClass = eZContentClassAttribute::fetch( $attr );
                                    $attrName = $contentClass->attribute( 'identifier' );
                                    $sortFieldName = str_replace( '*', $attrName, $sortFieldName );
                                }
                                else
                                {
                                    $sortFieldName = str_replace( '*', $sortClassID, $sortFieldName );
                                }
                                $sortingFieldList[] = $sortFieldName . $sortOrder;
                        }
                        else
                        {
                            $sortingFieldList[] = $searchAttributeMappingList[$sortField] . $sortOrder;
                        }
                    }
                }
                if ( count( $sortingFieldList ) )
                {
                    $sortString = implode( ",", $sortingFieldList );
                }
            }

            
        }
var_dump($sortingFieldList);



        //the array_unique below is necessary because attribute identifiers are not unique .. and we get as
        //much highlight snippets as there are duplicate attribute identifiers
        //these are also in the list of query fields (dismax, ezpublish) request handlers
        $queryFields = array_unique( $this->getClassAttributes( $contentClassID, $contentClassAttributeID ) );

        //$queryFields = array ('df_attr');
        //highlighting only in the attributes, otherwise the object name is repeated in the highlight, which is already
        //partly true as it is mostly composed of one or more attributes.
        //maybe we should add meta data to the index to filter them out.

        $queryFields[] = 'm_name^2.0';
        $queryFields[] = 'm_owner_name^1.5';
        $queryParams = array(
            'start' => $offset,
            'rows' => $limit,
            'indent' => 'on',
            'version' => '2.2',
            'qt' => 'ezpublish',
            'qf' => implode( ' ', $queryFields ),
            'bq' => $this->boostQuery(),
            'fl' =>
            'm_guid m_installation_id m_main_url_alias m_installation_url m_id m_main_node_id ' .
            'm_language_code m_name score m_published',
            'q' => $searchText,
            'fq' => $filterQuery,
            'wt' => 'php',
            'sort' => $sortString
            );

        if ( $this->UseFacets == 'true' )
        {
            $facetParams = array( );
            $facetParams['facet'] = 'true';
            $facetParams['facet.field'] = $this->FacetFields;
            $facetParams['facet.sort'] = $this->FacetSort;
            $facetParams['facet.limit'] = $this->FacetLimit;
            $facetParams['facet.offset'] = $this->FacetOffset;
            $facetParams['facet.mincount'] = $this->FacetMinCount;
            $facetParams['facet.missing'] = $this->FacetMissing;
            $facetParams['facet.prefix'] = $this->FacetPrefix;

            $queryParams = array_merge( $queryParams, $facetParams );

         }

        if ( $this->ResultHighLight == 'yes' )
        {
            $highLightFields = array();
            if ( count( $this->HighLightFields ) )
            {
                $highLightFields = $this->HighLightFields;
            }
            else
            {
                $highLightFields = $queryFields;
            }
            $hightLightParams = array(
                'hl'                   => $this->ResultHighLight,
                'hl.fl'                => $highLightFields,
                'hl.snippets'          => $this->HighLightSnipplets,
                'hl.fragsize'          => $this->HighLightFragmentSize,
                'hl.requireFieldMatch' => $this->HighLightRequireFieldMatch,
                'hl.simple.pre'        => $this->HighLightPreText,
                'hl.simple.post'       => $this->HighLightPostText
            );

            $queryParams = array_merge( $queryParams, $hightLightParams );
        }
        if ( $this->DebugQueryRequest )
        {
            $queryParams = array_merge( $queryParams, array( 'debugQuery' => 'on' ) );
        }

        eZDebug::writeDebug( $queryParams );
        $resultArray = $this->Solr->rawSearch( $queryParams );
        if (! $resultArray )
        {
            return array(
                'SearchResult' => false,
                'SearchCount' => 0,
                'StopWordArray' => array(),
                'SearchExtras' => array(
                    'DocExtras' => array(),
                    'FacetArray' => $resultArray['facet_counts'],
                    'Debug' => $resultArray['debug'],
                    'ResponseHeader' => $resultArray['responseHeader'],
                    'Error' => ezi18n( 'ezfind', 'Server not running' ),
                    'Engine' => $this->engineText() ) );
        }

        $highLights = array();
        if ( !empty( $resultArray['highlighting'] ) )
        {
            foreach ( $resultArray['highlighting'] as $id => $highlight)
            {
                $highLightStrings = array();
                //implode apparently does not work on associative arrays that contain arrays
                //$element being an array as well
                foreach ($highlight as $key => $element)
                {
                    $highLightStrings[] = implode(' ', $element);
                }
                $highLights[$id] = implode(' ...  ', $highLightStrings);

            }
        }
//         if ( count($resultArray) > 0 )
//         {
//             $result = $resultArray['response'];
//             $searchCount = $result['numFound'];
//             $maxScore = $result['maxScore'];
//             $docs = $result['docs'];
//             $objectRes = array();
//             $docExtras = array();
//             foreach ( $docs as $idx => $doc )
//             {
//                 if ( $doc['m_installation_id'] == $this->installationID() )
//                 {
//                     // Search result document is from current installation
//                     $docExtras[$idx]['is_local_installation'] = true;
//                     $objectTreeRow = eZPersistentObject::fetchObject( eZContentObjectTreeNode::definition(),
//                                                                       null,
//                                                                       array( 'node_id' => $doc['m_main_node_id'] ),
//                                                                       false );
//                     $resultTree = new eZFindResultNode( $objectTreeRow );
//                     $resultTree->setAttribute( 'is_local_installation', true );
// 
//                     $globalURL = $doc['m_main_url_alias'] . '/(language)/' . $doc['m_language_code'];
//                     eZURI::transformURI( $globalURL );
// 
//                 }
//                 else
//                 {
//                     $resultTree = new eZFindResultNode();
//                     $resultTree->setAttribute( 'is_local_installation', false );
//                     $globalURL = $doc['m_installation_url'] . $doc['m_main_url_alias'] .
//                         '/(language)/' . $doc['m_language_code'];
//                 }
// 
//                 $resultTree->setAttribute( 'name', $doc['m_name'] );
//                 $resultTree->setAttribute( 'published', $doc['m_published'] );
//                 $resultTree->setAttribute( 'global_url_alias', $globalURL );
//                 $resultTree->setAttribute( 'highlight', isset( $highLights[$doc['m_guid']] ) ? $highLights[$doc['m_guid']] : null );
//                 $resultTree->setAttribute( 'score_percent', (int) ( ( $doc['score'] / $maxScore ) * 100 ) );
//                 $resultTree->setAttribute( 'language_code', $doc['m_language_code'] );
//                 $objectRes[] = $resultTree;
//             }
//         }

        if ( count($resultArray) > 0 )
        {
            $result = $resultArray['response'];
            $searchCount = $result['numFound'];
            $maxScore = $result['maxScore'];
            $docs = $result['docs'];
            $objectRes = array();
            $docExtras = array();

            $nodeArray = array();
            $tmpDocList = array();
            if ( $resultArray['response']['numFound'] > 0 )
            {
                foreach ( $docs as $idx => $doc )
                {
                    $nodeArray[] = $doc['m_main_node_id'];
                    $tmpDocList[$doc['m_main_node_id']] = $doc;
                }
    
                $objectTreeRow = eZPersistentObject::fetchObjectList( eZContentObjectTreeNode::definition(),
                                                                    null,
                                                                    array( 'node_id' => array( $nodeArray ) ),
                                                                    null,
                                                                    null,
                                                                    false );
                if ( count($objectTreeRow) )
                {
                    foreach ( $objectTreeRow as $idx => $doc )
                    {
                        if ( $tmpDocList[$doc['main_node_id']]['m_installation_id'] == $this->installationID() )
                        {
                            //Search result document is from current installation
                            $docExtras[$idx]['is_local_installation'] = true;
        
                            $resultTree = new eZFindResultNode( $doc );
                            $resultTree->setAttribute( 'is_local_installation', true );
        
                            $globalURL = $tmpDocList[$doc['main_node_id']]['m_main_url_alias'] . '/(language)/' . $tmpDocList[$doc['main_node_id']]['m_language_code'];
                            eZURI::transformURI( $globalURL );
        
                        }
                        else
                        {
                            $resultTree = new eZFindResultNode();
                            $resultTree->setAttribute( 'is_local_installation', false );
                            $globalURL = $tmpDocList[$doc['main_node_id']]['m_installation_url'] . $tmpDocList[$doc['main_node_id']]['m_main_url_alias'] .
                                '/(language)/' . $tmpDocList[$doc['main_node_id']]['m_language_code'];
                        }
                        $resultTree->setAttribute( 'name', $tmpDocList[$doc['main_node_id']]['m_name'] );
                        $resultTree->setAttribute( 'published', $tmpDocList[$doc['main_node_id']]['m_published'] );
                        $resultTree->setAttribute( 'global_url_alias', $globalURL );
                        $resultTree->setAttribute( 'highlight', isset( $highLights[$tmpDocList[$doc['main_node_id']]['m_guid']] ) ? $highLights[$tmpDocList[$doc['main_node_id']]['m_guid']] : null );
// var_dump( $tmpDocList[$doc['main_node_id']] );
// echo "<pre>" .$doc['main_node_id']. "-" . (int) ( ( $tmpDocList[$doc['main_node_id']]['score'] / $maxScore ) * 100 ) . "</pre>";
                        $resultTree->setAttribute( 'score_percent', (int) ( ( $tmpDocList[$doc['main_node_id']]['score'] / $maxScore ) * 100 ) );
                        $resultTree->setAttribute( 'language_code', $tmpDocList[$doc['main_node_id']]['m_language_code'] );
                        $objectRes[] = $resultTree;
        
                    }
                }
            }
        }
        $stopWordArray = array();
        eZDebug::writeDebug( isset( $resultArray['highlighting'] ) ? $resultArray['highlighting'] : 'No hightlights returned', ' Highlights ' );
//      eZDebug::writeDebug( $resultArray['facet_counts'], ' Facets ' );


        //rewrite highlight array into one string

        eZDebug::writeDebug( $highLights, ' Highlight massage ' );
        return array(
            'SearchResult' => $objectRes,
            'SearchCount' => $searchCount,
            'StopWordArray' => $stopWordArray,
            'SearchExtras' => array(
                'DocExtras' => $docExtras,
                'FacetArray' =>  array_key_exists( 'facet_counts', $resultArray ) ?  $resultArray['facet_counts'] : false,
                'ResponseHeader' => $resultArray['responseHeader'],
                'Debug' => array_key_exists( 'debug', $resultArray ) ?  $resultArray['debug'] : false,
                'Error' => '',
                'Engine' => $this->engineText() )
            );
    }

    /*!
     Generate boost query on search. This boost is configured boost the following criterias:
     - local installation
     - Language priority

     \return boostQuery
    */
    function boostQuery()
    {
        // Local installation boost
        $boostQuery = 'm_installation_id:' . $this->installationID() . '^1.5';
        $ini = eZINI::instance();

        // Language boost. Only boost 3 first languages.
        $languageBoostList = array( '1.2', '1.0', '0.8' );
        foreach ( $ini->variable( 'RegionalSettings', 'SiteLanguageList' ) as $idx => $languageCode )
        {
            $boostQuery .= ' m_language_code:' . $languageCode . '^' . $languageBoostList[$idx];
        }

        return $boostQuery;
    }

    function supportedSearchTypes()
    {
        $searchTypes = array( array( 'type' => 'attribute',
                                     'subtype' =>  'fulltext',
                                     'params' => array( 'classattribute_id', 'value' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' =>  'patterntext',
                                     'params' => array( 'classattribute_id', 'value' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' =>  'integer',
                                     'params' => array( 'classattribute_id', 'value' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' =>  'integers',
                                     'params' => array( 'classattribute_id', 'values' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' =>  'byrange',
                                     'params' => array( 'classattribute_id' , 'from' , 'to'  ) ),
                              array( 'type' => 'attribute',
                                     'subtype' => 'byidentifier',
                                     'params' => array( 'classattribute_id', 'identifier', 'value' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' => 'byidentifierrange',
                                     'params' => array( 'classattribute_id', 'identifier', 'from', 'to' ) ),
                              array( 'type' => 'attribute',
                                     'subtype' => 'integersbyidentifier',
                                     'params' => array( 'classattribute_id', 'identifier', 'values' ) ),
                              array( 'type' => 'fulltext',
                                     'subtype' => 'text',
                                     'params' => array( 'value' ) ) );
        $generalSearchFilter = array( array( 'type' => 'general',
                                             'subtype' => 'class',
                                             'params' => array( array( 'type' => 'array',
                                                                       'value' => 'value'),
                                                                'operator' ) ),
                                      array( 'type' => 'general',
                                             'subtype' => 'publishdate',
                                             'params'  => array( 'value', 'operator' ) ),
                                      array( 'type' => 'general',
                                             'subtype' => 'subtree',
                                             'params'  => array( array( 'type' => 'array',
                                                                        'value' => 'value'),
                                                                 'operator' ) ) );
        return array( 'types' => $searchTypes,
                      'general_filter' => $generalSearchFilter );
    }


/*
    function initSpellChecker()
    {

	$return = $this->Solr->rawSearch( array( 'q' => 'solr', 'qt' => 'spellchecker', 'wt' => 'php', 'cmd' => 'rebuild') );

    }



    function spellCheck ( $string, $onlyMorePopular = false, $suggestionCount = 1, $accuracy=0.5 )
    {
	$onlyMorePopularString = $onlyMorePopular ? 'true' : 'false';
	return $this->Solr->rawSearch( '/select', array( 'q' => $string, 'qt' => 'spellchecker',
						 'suggestionCount' => $suggestionCount, 'wt' => 'php',
						 'accuracy' => $accuracy, 'onlyMorePopular' => $onlyMorePopularString ) );

    }


    function moreLikeThis ( $objectID )
    {
	return $this->rawSolrRequest ( '/mlt' , array( 'q' => 'm_id:' . $objectID, 'wt' => 'php', 'fl' => 'm_id' ) );
    }
*/

    /*!
     Get eZFind installation ID

     \return installaiton ID.
     */
    function installationID()
    {
        if ( !empty( $this->InstallationID ) )
        {
            return $this->InstallationID;
        }
        $db = eZDB::instance();

        $resultSet = $db->arrayQuery( 'SELECT value FROM ezsite_data WHERE name=\'ezfind_site_id\'' );

        if ( count( $resultSet ) == 1 )
        {
            $this->InstallationID = $resultSet[0]['value'];
        }
        else
        {
            $this->InstallationID = md5( mktime() . '-' . mt_rand() );
            $db->query( 'INSERT INTO ezsite_data ( name, value ) values( \'ezfind_site_id\', \'' . $this->InstallationID . '\' )' );
        }

        return $this->InstallationID;
    }

    /*!
     Get GlobalID of contentobject

     \param \a $contentObject
     \param \a $languageCode ( optional )

     \return guid
    */
    function guid( $contentObject, $languageCode = '' )
    {
        return md5( $this->installationID() . '-' . $contentObject->attribute( 'id' ) . '-' . $languageCode );
    }

    /*!
     Clean up search index for current installation.
    */
    function cleanup()
    {
        $this->Solr->deleteDocs( array(), 'm_installation_id:' . $this->installationID() );
    }

    /*!
     Get engine text

     \return engine text
    */
    function engineText()
    {
        return ezi18n( 'ezfind', 'eZ Find search plugin &copy; 2007 eZ Systems AS, eZ Labs' );
    }

    /// Object vars
    var $InstallationID;
    var $SolrINI;
    var $SearchSeverURI;
    var $Realm;
    var $FindINI;


    var $ResultHighLight;
    var $HighLightFields;
    var $HighLightSnipplets;
    var $HighLightFragmentSize;
    var $HighLightRequireFieldMatch;
    var $HighLightPreText;
    var $HighLightPostText;

    var $UseFacets;
    var $FacetFields;
    var $FacetPrefix;
    var $FacetMinCount;
    var $FacetSort;
    var $FacetLimit;
    var $FacetOffset;
    var $FacetMissing;

    var $UseFieldAliases;
    var $FieldAliasList;

    var $DebugQueryRequest;

}

?>
