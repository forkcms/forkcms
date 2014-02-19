<ul>
	{option:widgetPagesNavigation.previous}
		<li>
			<a href="{$widgetPagesNavigation.previous.url}" rel="prev">{$lblPreviousPage|ucfirst}: {$widgetPagesNavigation.previous.title}</a>
		</li>
	{/option:widgetPagesNavigation.previous}

	{option:widgetPagesNavigation.parent}
		<li>
			<a href="{$widgetPagesNavigation.parent.full_url}" rel="prev">{$lblParentPage|ucfirst}: {$widgetPagesNavigation.parent.title}</a>
		</li>
	{/option:widgetPagesNavigation.parent}

	{option:widgetPagesNavigation.next}
		<li>
			<a href="{$widgetPagesNavigation.next.url}" rel="next">{$lblNextPage|ucfirst}: {$widgetPagesNavigation.next.title}</a>
		</li>
	{/option:widgetPagesNavigation.next}
</ul>
