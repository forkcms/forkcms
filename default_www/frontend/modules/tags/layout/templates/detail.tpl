{*
	Variables that are available:
	- {$tagsModules}: {* @todo: add info *}
*}

<div id="tags" class"detail">
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