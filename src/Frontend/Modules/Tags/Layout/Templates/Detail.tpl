{*
	Variables that are available:
	- {$tagsModules}: contains all tags, grouped per module
*}

<section id="tagsDetail" class="mod">
	<div class="inner">
		<header class="hd">
			<h1>{$lblItemsWithTag|sprintf:{$tag.name}|ucfirst}</h1>
		</header>
		<div class="bd">
			{option:tagsModules}
				{iteration:tagsModules}
					<section class="mod">
						<div class="inner">
							<header class="hd">
								<h3>{$tagsModules.label}</h3>
							</header>
							<div class="bd content">
								<ul>
									{iteration:tagsModules.items}
										<li><a href="{$tagsModules.items.full_url}">{$tagsModules.items.title}</a></li>
									{/iteration:tagsModules.items}
								</ul>
							</div>
						</div>
					</section>
				{/iteration:tagsModules}
			{/option:tagsModules}
			<p><a href="{$var|geturlforblock:'Tags'}" title="{$lblToTagsOverview|ucfirst}">{$lblToTagsOverview|ucfirst}</a></p>
			
		</div>
	</div>
</section>
