
<ul id="pageNavigation">
	{option:widgetPagesNavigation.previous}
		<li class="previousLink">
			<a href="/{$widgetPagesNavigation.previous.full_url}" rel="prev" title="{$lblPreviousPage|ucfirst}: {$widgetPagesNavigation.previous.title}">{$lblPreviousPage|ucfirst}: {$widgetPagesNavigation.previous.title}</a>
		</li>
	{/option:widgetPagesNavigation.previous}

	{option:widgetPagesNavigation.parent}
		<li class="parentLink">
			<a href="/{$widgetPagesNavigation.parent.full_url}" title="{$lblOverview|ucfirst}">{$lblOverview|ucfirst}</a>
		</li>
	{/option:widgetPagesNavigation.parent}

	{option:widgetPagesNavigation.next}
		<li class="nextLink">
			<a href="/{$widgetPagesNavigation.next.full_url}" rel="next" title="{$lblNextPage|ucfirst}: {$widgetPagesNavigation.next.title}">{$lblNextPage|ucfirst}: {$widgetPagesNavigation.next.title}</a>
		</li>
	{/option:widgetPagesNavigation.next}
</ul>