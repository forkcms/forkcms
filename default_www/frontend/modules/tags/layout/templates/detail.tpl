{*
	Variables that are available:
	@todo add variables that are available
*}

<div id="tagsDetail" class="mod">
	<div class="inner">
		<div class="bd">
			{option:tagsModules}
				{iteration:tagsModules}
					<h2>{$tagsModules.name}</h2>
					<ul>
						{iteration:tagsModules.items}
							<li><a href="{$items.url}">{$items.title}</a></li>
						{/iteration:tagsModules.items}
					</ul>
				{/iteration:tagsModules}
			{/option:tagsModules}
		</div>
	</div>
</div>