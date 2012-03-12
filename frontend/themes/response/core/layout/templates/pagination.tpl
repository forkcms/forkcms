{option:pagination}
	{option:pagination.multiple_pages}
		<ul class="pageNavigation clearfix">
			{option:pagination.show_previous}
			<li class="old">
				<a href="{$pagination.previous_url}" rel="prev nofollow" title="{$lblPreviousPage|ucfirst}">{$lblPreviousPage|ucfirst}</a>
			</li>
			{/option:pagination.show_previous}

			{option:pagination.show_next}
			<li class="new">
				<a href="{$pagination.next_url}" rel="next nofollow" title="{$lblNextPage|ucfirst}">{$lblNextPage|ucfirst}</a>
			</li>
			{/option:pagination.show_next}
		</ul>
	{/option:pagination.multiple_pages}
{/option:pagination}