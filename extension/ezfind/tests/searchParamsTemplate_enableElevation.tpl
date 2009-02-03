{*
{def $params = hash( 'query', 'ez',
                     'enable_elevation', true() )}
*}

{def $params = hash( 'query', 'ez',
                     'enable_elevation', false() )}


{def $searchResults = fetch( 'ezfind', 'search', $params )}
{foreach $searchResults.SearchResult as $result}
    {$result.name}
    {}
{/foreach}