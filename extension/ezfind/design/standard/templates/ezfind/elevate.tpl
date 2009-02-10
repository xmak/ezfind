<form name="ezfindelevateform" method="post" action={"/ezfind/elevate/"|ezurl}>

{* Title. *}
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Elevation'|i18n( 'extension/ezfind/elevate' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content" style="padding-top: 2px;">
	{* 
    Feedbacks. 
	*}
	
	{if or( $feedback.missing_searchquery, is_set( $feedback.missing_object ) )}
	    <div class="message-warning">
	    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
	    {if is_set( $feedback.missing_searchquery )}
	         {'Please enter a search query.'|i18n( 'extension/ezfind/elevate' )}<br />
	    {/if}
	    {if is_set( $feedback.missing_object )}
	        {'Please choose a valid object.'|i18n( 'extension/ezfind/elevate' )}
	    {/if}
	    </h2>
	    </div>
	{/if}
	
	{if is_set( $feedback.missing_language )}
	    <div class="message-warning">
	    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
	        {'Missing language information.'|i18n( 'extension/ezfind/elevate' )}
	    </h2>
	    </div>
	{/if}
	
	{if is_set( $feedback.creation_ok )}
	    <div class="message-feedback">
	    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
	        {def $obj = fetch( 'content', 'object', hash( 'object_id', $feedback.creation_ok.contentobject_id ) )}
	        {'Successful creation of the following Elevate configuration:'|i18n( 'extension/ezfind/elevate' )}
	        </h2>
	        <ul>
	            <li>{'Search query'|i18n( 'extension/ezfind/elevate' )}: {$feedback.creation_ok.search_query}</li>
	            <li>{'Content object'|i18n( 'extension/ezfind/elevate' )}: <a href={$obj.main_node.url_alias|ezurl}>{$obj.name|wash}</a></li>
	            <li>{'Language'|i18n( 'extension/ezfind/elevate' )}: {if eq( $feedback.creation_ok.language_code, $language_wildcard )}
														                 <em>{'All'|i18n( 'extension/ezfind/elevate' )}</em>
														             {else}
														                 {$feedback.creation_ok.language_code}
														             {/if}
	            </li>
	        </ul>
	    </div>
	{/if}

    {if is_set( $feedback.synchronisation_ok )}
		<div class="message-feedback">
		<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
		    {"Successful synchronization of the local Elevate configuration with Solr's."|i18n( 'extension/ezfind/elevate' )}
		</h2>
		</div>
    {/if}

    {if is_set( $feedback.synchronisation_fail )}
        <div class="message-warning">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span>
            {$feedback.synchronisation_fail_message}
        </h2>
        </div>
    {/if}
        
    {* Synchronise configuration witih Solr *}
    <table class="list cache" cellspacing="0">
    <tr class="bglight">
        <th>
            {'Synchronise Elevate configuration with Solr'|i18n( 'extension/ezfind/elevate' )}
        </th>
        <th>
            <input class="button" type="submit" name="ezfind-elevate-synchronise" value="{'Synchronise'|i18n( 'extension/ezfind/elevate' )}" title="{'Synchronise the Elevate configuration with Solr.'|i18n( 'extension/ezfind/elevate' )}"/>
        </th>
    </tr>
    </table>

{* DESIGN: Content END *}</div></div></div></div></div></div>
</div>



<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Elevate an object'|i18n( 'extension/ezfind/elevate' )}</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<table class="list cache" cellspacing="0">
	<tr>
	    <th>
            <div style="white-space: normal; padding-bottom: 10px;">
			<em>
			{'"Elevating an object" for a given search query means that this object will be returned among the very first search results when a search is triggered using this search query.'|i18n( 'extension/ezfind/elevate' )}
			<em>
		    </div>
	    </th>
	</tr>
	<tr>
	    <th>
	    {if $back_from_browse|not}
		    {'Search query'|i18n( 'extension/ezfind/elevate' )}:
		    <input type="text" name="ezfind-elevate-searchquery" size="15" value="" title="{'Search query to elevate the object for.'|i18n( 'extension/ezfind/elevate' )}"/>&nbsp;
		    <input class="button" type="submit" name="ezfind-elevate-browseforobject" value="{'Elevate object'|i18n( 'extension/ezfind/elevate' )}" title="{'Browse for the object to associate elevation to.'|i18n( 'extension/ezfind/elevate' )}"/>
	    {else}
	        {'Elevate %objectlink with &nbsp;  %searchquery &nbsp;  for language:'|i18n( 'extension/ezfind/elevate', '', 
	                                                                                     hash( '%objectlink',  concat( '<a href=', $elevatedObject.main_node.url_alias|ezurl, '>', $elevatedObject.main_node.name, '</a>' ),
	                                                                                           '%searchquery', concat( '<input type="text" name="ezfind-elevate-searchquery" size="15" value="', $elevateSearchQuery|wash, '" title="', 'Search query to elevate the object for.'|i18n( 'extension/ezfind/elevate' ) , '"/>' ) ))}
	                                                                                                           
            <select name="ezfind-elevate-language">
                   <option value="{$language_wildcard}"><em>{'All'|i18n( 'extension/ezfind/elevate' )}</em></option>
                   {foreach $elevatedObject.languages as $lang}
                       <option value="{$lang.locale}">{$lang.name}</option>
                   {/foreach}
            </select>     
            
            <input type="hidden" name="elevateObjectID" value="{$elevatedObject.id}">            
            <input class="button" type="submit" name="ezfind-elevate-do" value="{'Elevate'|i18n( 'extension/ezfind/elevate' )}" title="{'Store elevation'|i18n( 'extension/ezfind/elevate' )}"/>
            <input class="button" type="submit" name="ezfind-elevate-cancel" value="{'Cancel'|i18n( 'extension/ezfind/elevate' )}" title="{'Cancel elevation'|i18n( 'extension/ezfind/elevate' )}"/>
	    {/if}
	       
	    </th>
	</tr>
</table>

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block"></div>
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>
</div>



{* Search for elevated objects window *}
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Search for elevated objects'|i18n( 'extension/ezfind/elevate' )}</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<table class="list cache" cellspacing="0">
<tr class="bgdark">
    <th>
    {'Search query'|i18n( 'extension/ezfind/elevate' )}:
    <input type="text" name="ezfind-searchelevateconfigurations-searchquery" size="15" value="{$view_parameters.search_query}" title="{'Search query to elevate the object for.'|i18n( 'extension/ezfind/elevate' )}" />&nbsp;
            
    {'Language'|i18n( 'extension/ezfind/elevate' )}:        
    <select name="ezfind-searchelevateconfigurations-language" title="{'Select a translation to narrow down the search.'|i18n( 'extension/ezfind/elevate' )}">
		<option value="{$language_wildcard}" {if is_set( $view_parameters.language )|not}selected="selected"{/if} ><em>{'All'|i18n( 'extension/ezfind/elevate' )}</em></option>
		{foreach $available_translations as $translation}
		    <option value="{$translation.locale}" {if and( is_set( $view_parameters.language ), eq( $view_parameters.language, $translation.locale ))}selected="selected"{/if}>{$translation.name}</option>
		{/foreach}
	</select>
    </th>
    <th>   
    	<input class="button" type="submit" name="ezfind-searchelevateconfigurations-do" value="{'Find matching elevate configurations'|i18n( 'extension/ezfind/elevate' )}" title="{'Find elevate configurations matching the search query entered.'|i18n( 'extension/ezfind/elevate' )}"/>
    </th>	    
</tr>
</table>

{* DESIGN: Content END *}</div></div></div></div></div></div>
</div>


{* Existing configurations *}

{* TODO : make $limit a user_preference *}
{def $limit = 10
     $params = hash( 'offset', $view_parameters.offset,
                     'limit',  $limit )
     $paramsForCount = hash( 'countOnly', true() )}
     
{* Searching for elevate configurations for a given search query, alter the fetch parameters *}
{if is_set( $view_parameters.search_query )}
    {set $params = $params|merge( hash( 'searchQuery', $view_parameters.search_query ) )}
    {set $paramsForCount = $paramsForCount|merge( hash( 'searchQuery', $view_parameters.search_query ) )}
{/if}

{* Searching for elevate configurations, filtering on a given languageCode, alter the fetch parameters *}
{if is_set( $view_parameters.language )}
    {set $params = $params|merge( hash( 'languageCode', $view_parameters.language ) )}
    {set $paramsForCount = $paramsForCount|merge( hash( 'languageCode', $view_parameters.language ) )}
{/if}

{def $configurations = fetch( 'ezfind', 'elevateConfiguration',  $params )
     $configurations_count = fetch( 'ezfind', 'elevateConfiguration', $paramsForCount )}

<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">
    {if is_set( $view_parameters.search_query )}
        <span style="color: orange;">{'Objects elevated by "%search_query"'|i18n( 'extension/ezfind/elevate', '', hash( '%search_query', $view_parameters.search_query ) )}</span>
    {else}
        {'Existing configurations'|i18n( 'extension/ezfind/elevate' )}    
    {/if}
</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

<table class="list" cellspacing="0">
{if eq( $configurations_count, 0 )}
    <tr class="bgdark">
        <th>{'No existing Elevate configuration.'|i18n( 'extension/ezfind/elevate' )}</th>
    </tr>
    </table>
{else}
	{if is_set( $view_parameters.search_query )}
        {foreach $configurations as $conf sequence array( 'bglight', 'bgdark' ) as $tdClass }
        <tr class="{$tdClass}">
        <td>
            {node_view_gui content_node=$conf.main_node view='line'}
        </td>
        </tr>
        {/foreach}	
	{else}
		<tr class="bgdark">
		    <th>{'Search query'|i18n( 'extension/ezfind/elevate' )}</th>
		    <th>{'Content object'|i18n( 'extension/ezfind/elevate' )}</th>
		    <th>{'Language'|i18n( 'extension/ezfind/elevate' )}</th>
		</tr>
		
		{def $tmp_obj=false()}
		{foreach $configurations as $conf sequence array( 'bglight', 'bgdark' ) as $tdClass }
		   {set $tmp_obj=fetch( 'content', 'object', hash( 'object_id', $conf.contentobject_id ) )}
		   <tr class="{$tdClass}">
		       <td>{$conf.search_query}</td>
	           <td><a href={$tmp_obj.main_node.url_alias|ezurl}>{$tmp_obj.name|wash}</a></td>
		       <td>
		           {if eq( $conf.language_code, $language_wildcard )}
				      <em>{'All'|i18n( 'extension/ezfind/elevate' )}</em>
				   {else}
				      {$conf.language_code}
				   {/if}
		       </td>
		   </tr>	    
		{/foreach}
	{/if}
    </table>
        
    
	<div class="context-toolbar">
	{include name=navigator
	         uri='design:navigator/alphabetical.tpl'
	         page_uri='/ezfind/elevate'
	         item_count=$configurations_count
	         view_parameters=$view_parameters
	         item_limit=$limit}
	</div>
{/if}

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>
</form>