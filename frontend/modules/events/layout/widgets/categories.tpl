{*
	variables that are available:
	- {$widgetEventsCategories}:
*}

{option:widgetEventsCategories}
	<section id="eventsCategoriesWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetEventsCategories}
						<li>
							<a href="{$widgetEventsCategories.url}">
								{$widgetEventsCategories.label}&nbsp;({$widgetEventsCategories.total})
							</a>
						</li>
					{/iteration:widgetEventsCategories}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetEventsCategories}