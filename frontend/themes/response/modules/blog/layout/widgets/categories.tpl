{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<h3>{$lblCategories|ucfirst}</h3>
	<ul class="blognavigation">
		{iteration:widgetBlogCategories}
			<li>
				<a href="{$widgetBlogCategories.url}">
					{$widgetBlogCategories.label}&nbsp;({$widgetBlogCategories.total})
				</a>
			</li>
		{/iteration:widgetBlogCategories}
	</ul>
{/option:widgetBlogCategories}