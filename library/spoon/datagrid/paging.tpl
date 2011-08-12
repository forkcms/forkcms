{* previous page *}
{option:previousURL}<a href="{$previousURL}" title="{$previousLabel}">{/option:previousURL}
	&laquo; previous
{option:previousURL}</a>{/option:previousURL}

{* list of pages *}
{iteration:pages}
	{option:pages.page}
		{option:pages.currentPage}<strong>{$pages.pageNumber}</strong>{/option:pages.currentPage}
		{option:pages.otherPage}<a href="{$pages.url}">{$pages.pageNumber}</a>{/option:pages.otherPage}
	{/option:pages.page}

	{option:pages.noPage}&hellip;{/option:pages.noPage}
{/iteration:pages}

{* next page *}
{option:nextURL}<a href="{$nextURL}" title="{$nextLabel}">{/option:nextURL}
	 next &raquo;
{option:nextURL}</a>{/option:nextURL}
