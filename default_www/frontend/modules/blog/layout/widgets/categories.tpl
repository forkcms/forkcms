{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<div id="blogCategoriesWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</div>
			<div class="bd">
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
		</div>
	</div>
{/option:widgetBlogCategories}