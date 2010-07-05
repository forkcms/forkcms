{option:pagination}
	{* there is more than 1 page *}
	{option:pagination['multiple_pages']}
		<ul class="clearfix">
			<li class="previousPage">
				{option:pagination['show_previous']}<a href="{$pagination['previous_url']}" rel="prev nofollow" title="{$previousLabel}">{/option:pagination['show_previous']}
				{option:!pagination['show_previous']}<span>{/option:!pagination['show_previous']}
					{$previousLabel}
				{option:!pagination['show_previous']}</span>{/option:!pagination['show_previous']}
				{option:pagination['show_previous']}</a>{/option:pagination['show_previous']}
			</li>

			{option:pagination['first']}
				{iteration:pagination['first']}<li><a href="{$paginationFirst.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$paginationFirst.label}">{$paginationFirst.label}</a></li>{/iteration:pagination['first']}
				<li class="ellipsis"><span>&hellip;</span></li>
			{/option:pagination['first']}

			{iteration:pagination['pages']}
				{option:paginationPages.current}
					<li class="selected">
						<span>{$paginationPages.label}</span>
					</li>
				{/option:paginationPages.current}
				{option:!paginationPages.current}
					<li>
						<a href="{$paginationPages.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$paginationPages.label}">{$paginationPages.label}</a>
					</li>
				{/option:!paginationPages.current}
			{/iteration:pagination['pages']}

			{option:pagination['last']}
				<li class="ellipsis"><span>&hellip;</span></li>
				{iteration:pagination['last']}<li><a href="{$paginationLast.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$paginationLast.label}">{$paginationLast.label}</a></li>{/iteration:pagination['last']}
			{/option:pagination['last']}

			<li class="nextPage">
				{option:pagination['show_next']}<a href="{$pagination['next_url']}" rel="next nofollow" title="{$nextLabel}">{/option:pagination['show_next']}
				{option:!pagination['show_next']}<span>{/option:!pagination['show_next']}
					{$nextLabel}
				{option:!pagination['show_next']}</span>{/option:!pagination['show_next']}
				{option:pagination['show_next']}</a>{/option:pagination['show_next']}
			</li>
		</ul>
	{/option:pagination['multiple_pages']}
{/option:pagination}