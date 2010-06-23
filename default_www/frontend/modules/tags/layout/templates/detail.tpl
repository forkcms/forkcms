{*
	variables that are available:

*}

<div id="tags" class"detail">
	{option:tagsModules}
		<ul>
			{iteration:tagsModules}
				<li>
					{$tagsModules.name} {* naam van deze module als label *}
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