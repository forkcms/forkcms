{option:pagination}
	<!-- pagination -->
	<div class="paginationWrap">
		<div class="pagination">
			<ul class="clearfix">
				<li class="previousPage">
					{option:paginationShowPrevious}<a href="{$paginationPreviousUrl}" rel="previous nofollow" title="{$lblPreviousPage|ucfirst}">{/option:paginationShowPrevious}
					{option:!paginationShowPrevious}<span>{/option:!paginationShowPrevious}
						&lt; {$lblPreviousPage|ucfirst}
					{option:!paginationShowPrevious}</span>{/option:!paginationShowPrevious}
					{option:paginationShowPrevious}</a>{/option:paginationShowPrevious}
				</li>

				{option:paginationPagesFirst}
				{iteration:paginationPagesFirst}<li><a href="{$paginationPagesFirst.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$paginationPagesFirst.label}">{$paginationPagesFirst.label}</a></li>{/iteration:paginationPagesFirst}
				<li class="ellipsis"><span>&hellip;</span></li>
				{/option:paginationPagesFirst}

				{iteration:paginationPages}
				<li{option:paginationPages.current} class="currentPage"{/option:paginationPages.current}>
					{option:!paginationPages.current}<a href="{$paginationPages.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$paginationPages.label}">{/option:!paginationPages.current}
					{option:paginationPages.current}<span>{/option:paginationPages.current}
						{$paginationPages.label}
					{option:paginationPages.current}</span>{/option:paginationPages.current}
					{option:!paginationPages.current}</a>{/option:!paginationPages.current}
				</li>
				{/iteration:paginationPages}

				{option:paginationPagesLast}
				<li class="ellipsis"><span>&hellip;</span></li>
				{iteration:paginationPagesLast}<li><a href="{$paginationPagesLast.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$paginationPagesLast.label}">{$paginationPagesLast.label}</a></li>{/iteration:paginationPagesLast}
				{/option:paginationPagesLast}

				<li class="nextPage">
					{option:paginationShowNext}<a href="{$paginationNextUrl}" rel="next nofollow" title="{$lblNextPage|ucfirst}">{/option:paginationShowNext}
					{option:!paginationShowNext}<span>{/option:!paginationShowNext}
						{$lblNextPage|ucfirst} &gt;
					{option:!paginationShowNext}</span>{/option:!paginationShowNext}
					{option:paginationShowNext}</a>{/option:paginationShowNext}
				</li>
			</ul>
		</div>

		<!-- current page -->
		<p class="paginationLocation">({$paginationCurrentPage}/{$paginationNumPages})</p>
	</div>
{/option:pagination}