{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
<div class="widget blogCategories">
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
</div>
{/option:widgetBlogCategories}