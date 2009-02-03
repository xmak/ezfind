{* def $params = hash( 'query', 'ez',
                     'sort_by', hash( 'published', 'desc' ),
                     'force_elevation', true() ) *}


{def $params = hash( 'query', 'ez',
                     'sort_by', hash( 'published', 'desc' ), 
                     'force_elevation', false() )}


{def $searchResults = fetch( 'ezfind', 'search', $params )}
{foreach $searchResults.SearchResult as $result}
    {$result.name}
    {}
{/foreach}