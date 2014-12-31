{option:pagination}
	{option:pagination.multiple_pages}
		<nav role="navigation">
			<ul class="pagination pagination-centered">
				<li{option:!pagination.show_previous} class="disabled"{/option:!pagination.show_previous}>
					{option:pagination.show_previous}<a href="{$pagination.previous_url}" rel="prev nofollow" title="{$lblPreviousPage|ucfirst}">{/option:pagination.show_previous}
					{option:!pagination.show_previous}<span>{/option:!pagination.show_previous}
						←<span class="hide"> {$lblPreviousPage|ucfirst}</span>
					{option:!pagination.show_previous}</span>{/option:!pagination.show_previous}
					{option:pagination.show_previous}</a>{/option:pagination.show_previous}
				</li>

				{option:pagination.first}
					{iteration:pagination.first}
						<li>
							<a href="{$pagination.first.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$pagination.first.label}">{$pagination.first.label}</a>
						</li>
					{/iteration:pagination.first}
					<li><span>…</span></li>
				{/option:pagination.first}

				{iteration:pagination.pages}
					<li{option:pagination.pages.current} class="active"{/option:pagination.pages.current}>
						<a href="{$pagination.pages.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$pagination.pages.label}">
							{$pagination.pages.label}
						</a>
					</li>
				{/iteration:pagination.pages}

				{option:pagination.last}
					<li><span>…</span></li>
					{iteration:pagination.last}
						<li>
							<a href="{$pagination.last.url}" rel="nofollow" title="{$lblGoToPage|ucfirst} {$pagination.last.label}">
								{$pagination.last.label}
							</a>
						</li>
					{/iteration:pagination.last}
				{/option:pagination.last}

				<li{option:!pagination.show_next} class="disabled"{/option:!pagination.show_next}>
					{option:pagination.show_next}<a href="{$pagination.next_url}" rel="next nofollow" title="{$lblNextPage|ucfirst}">{/option:pagination.show_next}
					{option:!pagination.show_next}<span>{/option:!pagination.show_next}
						<span class="hide">{$lblNextPage|ucfirst} </span>→
					{option:!pagination.show_next}</span>{/option:!pagination.show_next}
					{option:pagination.show_next}</a>{/option:pagination.show_next}
				</li>
			</ul>
		</nav>
	{/option:pagination.multiple_pages}
{/option:pagination}