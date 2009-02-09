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
	         {'Please enter a search query to elevate the object for.'|i18n( 'extension/ezfind/elevate' )}<br />
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

{* DESIGN: Content END *}</div></div></div></div></div></div>
</div>



{* Action window. *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{'Elevate an object'|i18n( 'extension/ezfind/elevate' )}</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<table class="list" cellspacing="0">
	<tr>
	    <td>
	    <em>
	    {'"Elevating an object" for a given search query means that it will be returned among the very first search results when a search is triggered
	       using this search query.'|i18n( 'extension/ezfind/elevate' )}
	    <em><br /><br />
	    </td>
	</tr>
	<tr>
	    <td>
	    {if $back_from_browse|not}
		    {'Search query'|i18n( 'extension/ezfind/elevate' )}:
		    <input type="text" name="ezfind-elevate-searchquery" size="15" value="" />&nbsp;
		    <input class="button" type="submit" name="ezfind-elevate-browseforobject" value="{'Elevate object'|i18n( 'extension/ezfind/elevate' )}" />
	    {else}
	        {'Elevate %objectlink with &nbsp;  %searchquery &nbsp;  for language:'|i18n( 'extension/ezfind/elevate', '', 
	                                                                                     hash( '%objectlink',  concat( '<a href=', $elevatedObject.main_node.url_alias|ezurl, '>', $elevatedObject.main_node.name, '</a>' ),
	                                                                                           '%searchquery', concat( '<input type="text" name="ezfind-elevate-searchquery" size="15" value="', $elevateSearchQuery|wash, '"/>' ) ))}
	                                                                                                           
            <select name="ezfind-elevate-language">
                   <option value="{$language_wildcard}"><em>{'All'|i18n( 'extension/ezfind/elevate' )}</em></option>
                   {foreach $elevatedObject.languages as $lang}
                       <option value="{$lang.locale}">{$lang.name}</option>
                   {/foreach}
            </select>     
            
            <input type="hidden" name="elevateObjectID" value="{$elevatedObject.id}">            
            <input class="button" type="submit" name="ezfind-elevate-do" value="{'Elevate'|i18n( 'extension/ezfind/elevate' )}" />	                                                                                                           
	    {/if}
	       
	    </td>
	</tr>
</table>

{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<div class="block">

</div>
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
<th width="60%">{'Regenerate static content cache'|i18n( 'design/admin/setup/cache' )}:</th>
<td width="40%"><input class="button" type="submit" name="RegenerateStaticCacheButton" value="{'Create new'|i18n( 'design/admin/setup/cache' )}" title="{'This operation will regenerate all the static content caches that are configured. This action can take  some time depending on the specifications of the server and the number of locations that are configured to be statically cached. If you encounter time-out problems, use the &quot;bin/php/makestaticcache.php&quot; shell script.'|i18n( 'design/admin/setup/cache' )}" /></td>
</tr>
</table>

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>


{* Existing configurations *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h2 class="context-title">{'Existing configurations'|i18n( 'extension/ezfind/elevate' )}</h2>

{* DESIGN: Mainline *}<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{* DESIGN: Content END *}</div></div></div></div></div></div>

</div>
</form>