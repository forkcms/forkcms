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
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</footer>
	</section>
{/option:widgetBlogRecentArticlesList}