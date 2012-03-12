{*
	variables that are available:
	- {$widgetBlogRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesList}
	<h3>{$lblRecentArticles|ucfirst}</h3>
	<ul class="blognavigation">
	{iteration:widgetBlogRecentArticlesList}
		<li><a href="{$widgetBlogRecentArticlesList.full_url}" title="{$widgetBlogRecentArticlesList.title}">{$widgetBlogRecentArticlesList.title}</a></li>
	{/iteration:widgetBlogRecentArticlesList}
	</ul>
{/option:widgetBlogRecentArticlesList}