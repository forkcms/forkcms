{*
	Variables that are available:
	- {$tagsModules}: contains all tags group per module
*}

<div id="tagsDetail" class="mod">
	<div class="inner">
		<div class="hd">
			<h1>{$lblItemsWithTag|sprintf:{$tag['name']}|ucfirst}</h1>
		</div>
		<div class="bd">
			{option:tagsModules}
				{iteration:tagsModules}
					<h2>{$tagsModules.label}</h2>
					<ul>
						{iteration:tagsModules.items}
							<li><a href="{$items.full_url}">{$items.title}</a></li>
						{/iteration:tagsModules.items}
					</ul>
				{/iteration:tagsModules}
			{/option:tagsModules}
		</div>
	</div>
</div>

<div id="tagsNavigation" class="mod">
	<div class="inner">
		<div class="bd">
			<ul>
				<li><a href="{$var|geturlforblock:'tags'}" title="{$lblToTagsOverview|ucfirst}">{$lblToTagsOverview|ucfirst}</a></li>
			</ul>
		</div>
	</div>
</div>
