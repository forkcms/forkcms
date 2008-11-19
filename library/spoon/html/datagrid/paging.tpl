{* previous page *}
{option:previous.url}<a href="{$previous.url}" title="{$previous.label}">{/option:previous.url}
	&laquo; {$previous.label} 
{option:previous.url}</a>{/option:previous.url}

{* list of pages *}
{iteration:pages}
	{option:page}
		{option:currentPage}<strong>{$pageNumber}</strong>{/option:currentPage}
		{option:otherPage}<a href="{$url}">{$pageNumber}</a>{/option:otherPage}
	{/option:page}
	
	{option:noPage}&hellip;{/option:noPage}
{/iteration:pages}

{* next page *}
{option:next.url}<a href="{$next.url}" title="{$next.label}">{/option:next.url}
	 {$next.label} &raquo; 
{option:next.url}</a>{/option:next.url}