<ul>
	<li class="previousPage">
		{option:previousURL}<a href="{$previousURL}" rel="prev" title="{$previousLabel}">{/option:previousURL}
		{option:!previousURL}<span>{/option:!previousURL}
			{$previousLabel}
		{option:previousURL}</a>{/option:previousURL}
		{option:!previousURL}</span>{/option:!previousURL}
	</li>

	{option:pagesfirst}
		{iteration:pagesfirst}
			<li>
				<a href="{$pagesfirst.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$pagesfirst.pageNumber}">
					{$pagesfirst.label}
				</a>
			</li>
		{/iteration:pagesfirst}
		<li class="ellipsis"><span>&hellip;</span></li>
	{/option:pagesfirst}

	{* list of pages *}
	{iteration:pages}
		{option:pages.page}
			{option:pages.currentPage}
				<li class="selected">
					<span>
						{$pages.pageNumber}
					</span>
				</li>
			{/option:pages.currentPage}
			{option:pages.otherPage}
				<li>
					<a href="{$pages.url}" rel="nofollow" title="{$lblGoToPage} {$pages.pageNumber}">
						{$pages.pageNumber}
					</a>
				</li>
			{/option:pages.otherPage}
		{/option:pages.page}
		{option:pages.noPage}<li>&hellip;</li>{/option:pages.noPage}
	{/iteration:pages}

	{option:pageslast}
		<li class="ellipsis"><span>&hellip;</span></li>
		{iteration:pageslast}
			<li>
				<a href="{$paginationLast.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$paginationLast.label}">
					{$paginationLast.label}
				</a>
			</li>
		{/iteration:pageslast}
	{/option:pageslast}

	<li class="nextPage">
		{option:nextURL}<a href="{$nextURL}" rel="next" title="{$nextLabel}">{/option:nextURL}
			{option:!nextURL}<span>{/option:!nextURL}
			{$nextLabel}
		{option:nextURL}</a>{/option:nextURL}
		{option:!nextURL}</span>{/option:!nextURL}
	</li>
</ul>