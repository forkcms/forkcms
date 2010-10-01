{*
	variables that are available:
	- {$widgetBlogRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesList}
<div class="widget widgetBlogRecentArticlesList">
	<h4>{$lblRecentArticles|ucfirst}</h4>
	<ul>
		{iteration:widgetBlogRecentArticlesList}
			<li><a href="{$widgetBlogRecentArticlesList.full_url}" title="{$widgetBlogRecentArticlesList.title}">{$widgetBlogRecentArticlesList.title}</a></li>
		{/iteration:widgetBlogRecentArticlesList}
	</ul>
</div>
{/option:widgetBlogRecentArticlesList}