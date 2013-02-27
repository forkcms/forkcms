<ul>
	{option:widgetPagesNavigation.previous}
		<li class="previousLink">
			<a href="{$widgetPagesNavigation.previous.url}" rel="prev">{$lblPreviousPage|ucfirst}: {$widgetPagesNavigation.previous.title}</a>
		</li>
	{/option:widgetPagesNavigation.previous}

	{option:widgetPagesNavigation.parent}
		<li class="parentLink">
			<a href="{$widgetPagesNavigation.parent.full_url}" rel="prev">{$lblParentPage|ucfirst}: {$widgetPagesNavigation.parent.title}</a>
		</li>
	{/option:widgetPagesNavigation.parent}

	{option:widgetPagesNavigation.next}
		<li class="nextLink">
			<a href="{$widgetPagesNavigation.next.url}" rel="next">{$lblNextPage|ucfirst}: {$widgetPagesNavigation.next.title}</a>
		</li>
	{/option:widgetPagesNavigation.next}
</ul>