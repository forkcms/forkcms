{*
	variables that are available:
	- {$widgetBlogRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesList}
	<div id="blogBlogRecentArticlesListWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</div>
			<div class="bd">
				<ul>
					{iteration:widgetBlogRecentArticlesList}
						<li><a href="{$widgetBlogRecentArticlesList.full_url}" title="{$widgetBlogRecentArticlesList.title}">{$widgetBlogRecentArticlesList.title}</a></li>
					{/iteration:widgetBlogRecentArticlesList}
				</ul>
			</div>
			<div class="ft">
				<p>
					<a href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$var|geturlforblock:'blog':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</div>
		</div>
	</div>
{/option:widgetBlogRecentArticlesList}