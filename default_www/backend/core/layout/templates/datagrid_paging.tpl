<ul>
	<li class="previousPage">
		{option:previousURL}<a href="{$previousURL}" title="{$previousLabel}">{/option:previousURL}
		{option:!previousURL}<span>{/option:!previousURL}
			{$previousLabel}
		{option:previousURL}</a>{/option:previousURL}
		{option:!previousURL}</span>{/option:!previousURL}
	</li>

{* list of pages *}
{iteration:pages}
	{option:pages.page}
		{option:pages.currentPage}
			<li class="selected"><span>{$pages.pageNumber}</span></li>
		{/option:pages.currentPage}
		{option:pages.otherPage}
			<li><a href="{$pages.url}">{$pages.pageNumber}</a></li>
		{/option:pages.otherPage}
	{/option:pages.page}
	{option:pages.noPage}<li>&hellip;</li>{/option:pages.noPage}
{/iteration:pages}

	<li class="nextPage">
		{option:nextURL}<a href="{$nextURL}" title="{$nextLabel}">{/option:nextURL}
		{option:!nextURL}<span>{/option:!nextURL}
			{$nextLabel}
		{option:nextURL}</a>{/option:nextURL}
		{option:!nextURL}</span>{/option:!nextURL}
	</li>
</ul>