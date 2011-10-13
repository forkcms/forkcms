{*
	variables that are available:
	- {$widgetBlogArticlesByCategory}:
*}
{option:widgetBlogArticlesByCategory}
	<section id="blogRecentArticlesListWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetBlogArticlesByCategory}
						<li><a href="{$widgetBlogArticlesByCategory.full_url}" title="{$widgetBlogArticlesByCategory.title}">{$widgetBlogArticlesByCategory.title}</a></li>
					{/iteration:widgetBlogArticlesByCategory}
				</ul>
			</div>
		</div>
	</section>
{/option:widgetBlogArticlesByCategory}