{*
	variables that are available:
	- {$widgetBlogCategories}:
*}

{option:widgetBlogCategories}
	<section id="blogCategoriesWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblCategories|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetBlogCategories}
						<li>
							<a href="{$widgetBlogCategories.url}">
								{$widgetBlogCategories.label}&nbsp;({$widgetBlogCategories.total})
							</a>
						</li>
					{/iteration:widgetBlogCategories}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetBlogCategories}