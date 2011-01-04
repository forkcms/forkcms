{*
	variables that are available:
	- {$widgetTagsRelated}: contains an array with all related items
*}

{option:widgetTagsRelated}
	<div id="TagCloudWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblRelated|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetTagsRelated}
						<li>
							<a href="{$widgetTagsRelated.full_url}" title="{$widgetTagsRelated.title}">
								{$widgetTagsRelated.title}
							</a>
						</li>
					{/iteration:widgetTagsRelated}
				</ul>
			</div>
		</div>
	</div>
{/option:widgetTagsRelated}