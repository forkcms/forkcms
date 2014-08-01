{*
	variables that are available:
	- {$widgetBlogRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesList}
	<section id="blogRecentArticlesListWidget" class="well blog">
		<header role="banner">
		    <h3>{$lblRecentArticles|ucfirst}</h3>
		</header>
		<ul>
		    {iteration:widgetBlogRecentArticlesList}
		    	<li><a href="{$widgetBlogRecentArticlesList.full_url}" title="{$widgetBlogRecentArticlesList.title}">{$widgetBlogRecentArticlesList.title}</a></li>
		    {/iteration:widgetBlogRecentArticlesList}
		</ul>
		<footer role="contentinfo">
		    <p class="btn-group">
		    	<a class="btn btn-default" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
		    	<a class="btn btn-default" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblRSSFeed|ucfirst}</a>
		    </p>
		</footer>
	</section>
{/option:widgetBlogRecentArticlesList}