{*
	Variables that are available:
	- {$tagsModules}: contains all tags, grouped per module
*}

<section>
	<header>
		<h1>{$lblItemsWithTag|sprintf:{$tag.name}|ucfirst}</h1>
	</header>
	{option:tagsModules}
		{iteration:tagsModules}
			<header>
				<h2>{$tagsModules.label}</h2>
			</header>
			{iteration:tagsModules.items}
				<p><a href="{$tagsModules.items.full_url}">{$tagsModules.items.title}</a></p>
			{/iteration:tagsModules.items}
		{/iteration:tagsModules}
	{/option:tagsModules}
	<p><a href="{$var|geturlforblock:'tags'}" title="{$lblToTagsOverview|ucfirst}">{$lblToTagsOverview|ucfirst}</a></p>
</section>
