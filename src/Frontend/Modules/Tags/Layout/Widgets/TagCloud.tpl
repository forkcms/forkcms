{*
	variables that are available:
	- {$widgetTagsTagCloud}: contains an array with the most popular tags
*}

{option:widgetTagsTagCloud}
	<section id="tagCloudWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblTags|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetTagsTagCloud}
						<li>
							<a href="{$widgetTagsTagCloud.url}">
								{$widgetTagsTagCloud.name}&nbsp;({$widgetTagsTagCloud.number})
							</a>
						</li>
					{/iteration:widgetTagsTagCloud}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetTagsTagCloud}