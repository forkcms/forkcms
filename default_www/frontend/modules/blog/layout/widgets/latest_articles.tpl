{*
	variables that are available:
	- {$widgetBlogLatestArticles}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogLatestArticles}
	<div class="widget widgetBlogLatestArticles">
		<h4>{$lblRecentArticles|ucfirst}</h4>
		<ul>
			{iteration:widgetBlogLatestArticles}
				<li><a href="{$widgetBlogLatestArticles.full_url}" title="{$widgetBlogLatestArticles.title}">{$widgetBlogLatestArticles.title}</a></li>
			{/iteration:widgetBlogLatestArticles}
		</ul>
	</div>
{/option:widgetBlogLatestArticles}