{*
	variables that are available:
	- {$widgetEventsRecentArticlesList}: contains an array with all posts, each element contains data about the post
*}

{option:widgetEventsRecentArticlesList}
	<section id="eventsRecentArticlesListWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</header>
			<div class="bd content">
				<ul>
					{iteration:widgetEventsRecentArticlesList}
						<li><a href="{$widgetEventsRecentArticlesList.full_url}" title="{$widgetEventsRecentArticlesList.title}">{$widgetEventsRecentArticlesList.title}</a></li>
					{/iteration:widgetEventsRecentArticlesList}
				</ul>
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'events'}">{$lblEventsArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$var|geturlforblock:'events':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</footer>
		</div>
	</section>
{/option:widgetEventsRecentArticlesList}