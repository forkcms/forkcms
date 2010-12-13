{*
	variables that are available:
	- {$widgetRelated}:
*}

{option:widgetRelated}
	<div id="TagCloudWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblRelated|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetRelated}
						<li>
							<a href="{$widgetRelated.full_url}" title="{$widgetRelated.title}">
								{$widgetRelated.title}
							</a>
						</li>
					{/iteration:widgetRelated}
				</ul>
			</div>
		</div>
	</div>
{/option:widgetRelated}