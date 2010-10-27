{*
	Variables that are available:
	@todo add variables that are available
*}

<div id="tagsDetail" class="mod">
	<div class="inner">
		<div class="bd content">
			{option:tagsModules}
				<ul>
					{iteration:tagsModules}
						<li>
							{$tagsModules.name} {* Module name as label *}
							<ul>
								{iteration:tagsModules.items}
									<li><a href="{$items.url}">{$items.title}</a></li>
								{/iteration:tagsModules.items}
							</ul>
						</li>
					{/iteration:tagsModules}
				</ul>
			{/option:tagsModules}
		</div>
	</div>
</div>