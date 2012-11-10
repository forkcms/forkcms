{*
	Variables that are available:
	- {$tagsModules}: contains all tags, grouped per module
*}
<section id="tagsDetail">
	<header class="hd">
		<h1>{$lblItemsWithTag|sprintf:{$tag.name}|ucfirst}</h1>
	</header>
	{option:tagsModules}
		{iteration:tagsModules}
			<section>
				<header class="hd">
					<h2>{$tagsModules.label|ucfirst}</h2>
				</header>
				<ul>
					{iteration:tagsModules.items}
						<li>
							<a href="{$tagsModules.items.full_url}">
								{$tagsModules.items.title}
							</a>
						</li>
					{/iteration:tagsModules.items}
				</ul>
			</section>
		{/iteration:tagsModules}
	{/option:tagsModules}

	<p>
		<a href="{$var|geturlforblock:'tags'}" class="btn">
			{$lblToTagsOverview|ucfirst}
		</a>
	</p>
</section>