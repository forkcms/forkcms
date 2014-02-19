{*
	variables that are available:
	- {$widgetTagsTagCloud}: contains an array with the most popular tags
*}

{option:widgetTagsTagCloud}
	<section>
		<header>
			<h2>{$lblTags|ucfirst}</h2>
		</header>
		<p>
			{iteration:widgetTagsTagCloud}
				<a href="{$widgetTagsTagCloud.url}">
					{$widgetTagsTagCloud.name}&nbsp;({$widgetTagsTagCloud.number})
				</a>
			{/iteration:widgetTagsTagCloud}
		</p>
	</section>
{/option:widgetTagsTagCloud}
