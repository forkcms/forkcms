{*
	variables that are available:
	- {$widgetTagsTagCloud}: contains an array with the most popular tags
*}

{option:widgetTagsTagCloud}
	<div id="TagCloudWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblTags|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
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
	</div>
{/option:widgetTagsTagCloud}