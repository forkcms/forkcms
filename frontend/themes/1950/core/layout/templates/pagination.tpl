{option:pagination}
	{option:pagination.multiple_pages}
		<nav class="mod">
			<ul class="pageNavigation">
				{option:pagination.show_previous}
				<li class="previousLink">
					<a href="{$pagination.previous_url}" rel="prev nofollow" title="{$lblPreviousPage|ucfirst}">{$lblPreviousPage|ucfirst}</a>
				</li>
				{/option:pagination.show_previous}

				{option:pagination.show_next}
				<li class="nextLink">
					<a href="{$pagination.next_url}" rel="next nofollow" title="{$lblNextPage|ucfirst}">{$lblNextPage|ucfirst}</a>
				</li>
				{/option:pagination.show_next}
			</ul>
		</nav>
	{/option:pagination.multiple_pages}
{/option:pagination}