{*
	variables that are available:
	- {$widgetEventsCategories}:
*}

{option:widgetEventsCategories}
	<div id="eventsCategoriesWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetEventsCategories}
						<li>
							<a href="{$widgetEventsCategories.url}">
								{$widgetEventsCategories.label} ({$widgetEventsCategories.total})
							</a>
						</li>
					{/iteration:widgetEventsCategories}
				</ul>
			</div>
		</div>
	</div>
{/option:widgetEventsCategories}