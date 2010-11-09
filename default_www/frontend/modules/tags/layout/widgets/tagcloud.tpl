{*
	variables that are available:
	- {$widgetTagCloud}:
*}

{option:widgetTagCloud}
	<div id="TagCloudWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblTags|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetTagCloud}
						<li>
							<a href="{$widgetTagCloud.url}">
								{$widgetTagCloud.name} ({$widgetTagCloud.number})
							</a>
						</li>
					{/iteration:widgetTagCloud}
				</ul>
			</div>
		</div>
	</div>
{/option:widgetTagCloud}