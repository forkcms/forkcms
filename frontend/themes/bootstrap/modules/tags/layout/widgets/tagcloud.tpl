{*
	variables that are available:
	- {$widgetTagsTagCloud}: contains an array with the most popular tag
*}
{option:widgetTagsTagCloud}
	<aside id="tagCloudWidget" class="well tags">
		<header>
			<h3>{$lblTags|ucfirst}</h3>
		</header>
		<p>
			{iteration:widgetTagsTagCloud}
				<a href="{$widgetTagsTagCloud.url}" rel="tag">{option:widgetTagsTagCloud.first}{$widgetTagsTagCloud.name|ucfirst}{/option:widgetTagsTagCloud.first}{option:!widgetTagsTagCloud.first}{$widgetTagsTagCloud.name}{/option:!widgetTagsTagCloud.first}</a>
				<span class="badge hidden-phone">{$widgetTagsTagCloud.number}</span>{option:!widgetTagsTagCloud.last}, {/option:!widgetTagsTagCloud.last}{option:widgetTagsTagCloud.last}.{/option:widgetTagsTagCloud.last}
			{/iteration:widgetTagsTagCloud}
		</p>
	</aside>
{/option:widgetTagsTagCloud}