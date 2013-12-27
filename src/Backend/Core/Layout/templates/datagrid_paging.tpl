{option:pagination}
	{* there is more than 1 page *}
	{option:pagination.multiple_pages}
		<ul class="clearfix">
			<li class="previousPage">
				{option:pagination.show_previous}<a href="{$pagination.previous_url}" rel="prev nofollow" title="{$previousLabel}">{/option:pagination.show_previous}
				{option:!pagination.show_previous}<span>{/option:!pagination.show_previous}
					{$previousLabel}
				{option:!pagination.show_previous}</span>{/option:!pagination.show_previous}
				{option:pagination.show_previous}</a>{/option:pagination.show_previous}
			</li>

			{option:pagination.first}
				{iteration:pagination.first}<li><a href="{$pagination.first.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.first.label}">{$pagination.first.label}</a></li>{/iteration:pagination.first}
				<li class="ellipsis"><span>&hellip;</span></li>
			{/option:pagination.first}

			{iteration:pagination.pages}
				{option:pagination.pages.current}
					<li class="selected">
						<span>{$pagination.pages.label}</span>
					</li>
				{/option:pagination.pages.current}
				{option:!pagination.pages.current}
					<li>
						<a href="{$pagination.pages.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.pages.label}">{$pagination.pages.label}</a>
					</li>
				{/option:!pagination.pages.current}
			{/iteration:pagination.pages}

			{option:pagination.last}
				<li class="ellipsis"><span>&hellip;</span></li>
				{iteration:pagination.last}<li><a href="{$pagination.last.url}" rel="nofollow" title="{$goToLabel|ucfirst} {$pagination.last.label}">{$pagination.last.label}</a></li>{/iteration:pagination.last}
			{/option:pagination.last}

			<li class="nextPage">
				{option:pagination.show_next}<a href="{$pagination.next_url}" rel="next nofollow" title="{$nextLabel}">{/option:pagination.show_next}
				{option:!pagination.show_next}<span>{/option:!pagination.show_next}
					{$nextLabel}
				{option:!pagination.show_next}</span>{/option:!pagination.show_next}
				{option:pagination.show_next}</a>{/option:pagination.show_next}
			</li>
		</ul>
	{/option:pagination.multiple_pages}
{/option:pagination}