<ul class="pager">
	{option:widgetPagesNavigation.previous}
		<li class="previous">
			<a href="{$widgetPagesNavigation.previous.url}" rel="prev">
				← {$widgetPagesNavigation.previous.title}
			</a>
		</li>
	{/option:widgetPagesNavigation.previous}

	{option:widgetPagesNavigation.parent}
		<li class="parentLink">
			<a href="{$widgetPagesNavigation.parent.url}">
				↑ {$widgetPagesNavigation.parent.title}
			</a>
		</li>
	{/option:widgetPagesNavigation.parent}

	{option:widgetPagesNavigation.next}
		<li class="next">
			<a href="{$widgetPagesNavigation.next.url}" rel="next">
				{$widgetPagesNavigation.next.title} ➝
			</a>
		</li>
	{/option:widgetPagesNavigation.next}
</ul>