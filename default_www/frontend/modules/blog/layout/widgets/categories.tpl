{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<div class="widget blogCategories">
		<h3>{$lblCategories|ucfirst}</h3>
		<ul>
			{iteration:widgetBlogCategories}
				<li>
					<a href="{$widgetBlogCategories.url}">
						{$widgetBlogCategories.label}
						<small>({$widgetBlogCategories.total})</small>
					</a>
				</li>
			{/iteration:widgetBlogCategories}
		</ul>
	</div>
{/option:widgetBlogCategories}