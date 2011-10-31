{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<nav class="sideNavigation">
		<h4>{$lblCategories|ucfirst}</h4>
		<ul>
			{iteration:widgetBlogCategories}
				<li>
					<a href="{$widgetBlogCategories.url}">
						{$widgetBlogCategories.label} ({$widgetBlogCategories.total})
					</a>
				</li>
			{/iteration:widgetBlogCategories}
		</ul>
	</nav>
{/option:widgetBlogCategories}