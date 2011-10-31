{*
	variables that are available:
	- {$widgetTagsTagCloud}: contains an array with the most popular tags
*}

{option:widgetTagsTagCloud}
	<section class="mod sideMod">
		<div class="inner">
			<header>
				<h4>{$lblTags|ucfirst}</h4>
			</header>
			<div class="bd">
				<ul class="doubleLineList">
					{iteration:widgetTagsTagCloud}
						<li>
							<a href="{$widgetTagsTagCloud.url}">
								{$widgetTagsTagCloud.name} ({$widgetTagsTagCloud.number})
							</a>
						</li>
					{/iteration:widgetTagsTagCloud}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetTagsTagCloud}