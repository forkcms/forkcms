{*
	variables that are available:
	- {$widgetBlogRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesList}
	<section id="blogRecentArticlesListWidget" class="well">
		<header>
		    <h3>{$lblRecentArticles|ucfirst}</h3>
		</header>
		<ul>
		    {iteration:widgetBlogRecentArticlesList}
		    	<li><a href="{$widgetBlogRecentArticlesList.full_url}" title="{$widgetBlogRecentArticlesList.title}">{$widgetBlogRecentArticlesList.title}</a></li>
		    {/iteration:widgetBlogRecentArticlesList}
		</ul>
		<footer>
		    <p>
		    	<a class="btn" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a><br/>
		    </p>
		    <p>
		    	<a class="btn" id="RSSfeed" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblRSSFeed|ucfirst}</a>
		    </p>
		</footer>
	</section>
{/option:widgetBlogRecentArticlesList}