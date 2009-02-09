<?php
//
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Find
// SOFTWARE RELEASE: 2.0.x
// COPYRIGHT NOTICE: Copyright (C) 2009 eZ Systems AS
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

/**
 * File containing the elevate view of the eZFind module.
 *
 * @package eZFind
 */

require_once( "kernel/common/template.php" );

$module = $Params['Module'];
$http = eZHTTPTool::instance();
$tpl = templateInit();
$feedback = array();
$wildcard = eZFindElevateConfiguration::WILDCARD;


// Elevation was triggered from the javascript menu ( content structure menu OR subitems menu )
if ( $http->hasPostVariable( 'ObjectIDFromMenu' ) and is_numeric( $http->postVariable( 'ObjectIDFromMenu' ) ) )
{
    $elevatedObject = eZContentObject::fetch( $http->postVariable( 'ObjectIDFromMenu' ) );

    $tpl->setVariable( 'back_from_browse', true );
    $tpl->setVariable( 'elevatedObject', $elevatedObject );
    $tpl->setVariable( 'elevateSearchQuery', '' );
}

// back from browse
elseif(
    $http->hasPostVariable( 'BrowseActionName' ) and
    $http->postVariable( 'BrowseActionName' ) == ( 'ezfind-elevate-browseforobject' ) and
    $http->hasPostVariable( "SelectedNodeIDArray" )
      )
{
    if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
    {
        $selectedNodeID = $http->postVariable( "SelectedNodeIDArray" );
        $selectedNodeID = $selectedNodeID[0];
        $elevatedObject = eZContentObject::fetchByNodeID( $selectedNodeID );

        $tpl->setVariable( 'back_from_browse', true );
        $tpl->setVariable( 'elevatedObject', $elevatedObject );
        $tpl->setVariable( 'elevateSearchQuery', $http->postVariable( 'elevateSearchQuery' ) );
    }
}

// From elevate's landing page, trigger browsing for an object to elevate.
elseif ( $http->hasPostVariable( 'ezfind-elevate-browseforobject' ) )
{
    $elevateSearchQuery =  $http->hasPostVariable( 'ezfind-elevate-searchquery' ) ? $http->postVariable( 'ezfind-elevate-searchquery' ): '';
    $browseType = 'SelectObjectRelationNode';
    eZContentBrowse::browse( array( 'action_name' => 'ezfind-elevate-browseforobject',
                                    'type' =>  $browseType,
                                    'from_page' => $module->currentRedirectionURI(),
                                    'persistent_data' => array( 'elevateSearchQuery' => $elevateSearchQuery ) ),
                             $module );
}

// Store the actual Elevate configuration
elseif( $http->hasPostVariable( 'ezfind-elevate-do') )
{
    $doStorage = true;

    // Check if we have all required data
    if ( !$http->hasPostVariable( 'elevateObjectID' ) or
         ( $elevatedObject = eZContentObject::fetch( $http->postVariable( 'elevateObjectID' ) ) ) === null
       )
    {
        $feedback['missing_object'] = true;
        $doStorage = false;
    }
    else
    {
        $tpl->setVariable( 'back_from_browse', true );
        $tpl->setVariable( 'elevatedObject', $elevatedObject );
    }

    if ( !$http->hasPostVariable( 'ezfind-elevate-searchquery' ) or $http->postVariable( 'ezfind-elevate-searchquery' ) == '' )
    {
        $feedback['missing_searchquery'] = true;
        $doStorage = false;
    }
    else
    {
        $tpl->setVariable( 'elevateSearchQuery', $http->postVariable( 'ezfind-elevate-searchquery' ) );
    }

    if ( !$http->hasPostVariable( 'ezfind-elevate-language' ) )
    {
        $feedback['missing_language'] = true;
        $doStorage = false;
    }

    if ( $doStorage )
    {
        // Filter the not yet filtered fields
        $queryString = htmlspecialchars( $http->postVariable( 'ezfind-elevate-searchquery' ), ENT_QUOTES );
        $languageCode = htmlspecialchars( $http->postVariable( 'ezfind-elevate-language' ), ENT_QUOTES );

        // Do actual storage here.
        $conf = eZFindElevateConfiguration::add( $queryString, $http->postVariable( 'elevateObjectID' ), $languageCode );

        // Give feedback message
        if ( $conf instanceof eZFindElevateConfiguration )
        {
            $feedback['creation_ok'] = $conf;
            $tpl->resetVariables();
        }
        else
            $feedback['creation_error'] = array( 'elevatedObject' => $elevatedObject,
                                                 'language' => $languageCode,
                                                 'queryString' => $queryString );
    }
}

$tpl->setVariable( 'feedback', $feedback );
$tpl->setVariable( 'language_wildcard', $wildcard );

$Result = array();
$Result['content'] = $tpl->fetch( "design:ezfind/elevate.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezi18n( 'extension/ezfind', 'eZFind' ) ),
                         array( 'url' => false,
                                'text' => ezi18n( 'extension/ezfind', 'Elevation' ) ) );

$Result['left_menu'] = "design:ezfind/backoffice_left_menu.tpl";
?>