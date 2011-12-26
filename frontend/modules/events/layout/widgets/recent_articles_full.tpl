{*
	variables that are available:
	- {$widgetEventsRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetEventsRecentArticlesFull}
	<section id="eventsRecentArticlesFullWidget" class="mod">
		<div class="inner">
			<header class="hd">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</header>
			<div class="bd">
				{iteration:widgetEventsRecentArticlesFull}
					<article class="mod article">
						<div class="inner">
							<header class="hd">
								<h4><a href="{$widgetEventsRecentArticlesFull.full_url}" title="{$widgetEventsRecentArticlesFull.title}">{$widgetEventsRecentArticlesFull.title}</a></h4>
								<ul>
									<li>{$msgWrittenBy|ucfirst|sprintf:{$widgetEventsRecentArticlesFull.user_id|usersetting:'nickname'}} {$lblOn} {$widgetEventsRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</li>
									<li>
										{option:!widgetEventsRecentArticlesFull.comments}<a href="{$widgetEventsRecentArticlesFull.full_url}#{$actComment}">{$msgEventsNoComments|ucfirst}</a>{/option:!widgetEventsRecentArticlesFull.comments}
										{option:widgetEventsRecentArticlesFull.comments}
											{option:widgetEventsRecentArticlesFull.comments_multiple}<a href="{$widgetEventsRecentArticlesFull.full_url}#{$actComments}">{$msgEventsNumberOfComments|sprintf:{$widgetEventsRecentArticlesFull.comments_count}}</a>{/option:widgetEventsRecentArticlesFull.comments_multiple}
											{option:!widgetEventsRecentArticlesFull.comments_multiple}<a href="{$widgetEventsRecentArticlesFull.full_url}#{$actComments}">{$msgEventsOneComment}</a>{/option:!widgetEventsRecentArticlesFull.comments_multiple}
										{/option:widgetEventsRecentArticlesFull.comments}
									</li>
									<li><a href="{$widgetEventsRecentArticlesFull.category_full_url}" title="{$widgetEventsRecentArticlesFull.category_title}">{$widgetEventsRecentArticlesFull.category_title}</a></li>
								</ul>
							</header>
							<div class="bd content">
								{option:!widgetEventsRecentArticlesFull.introduction}{$widgetEventsRecentArticlesFull.text}{/option:!widgetEventsRecentArticlesFull.introduction}
								{option:widgetEventsRecentArticlesFull.introduction}{$widgetEventsRecentArticlesFull.introduction}{/option:widgetEventsRecentArticlesFull.introduction}
							</div>
						</div>
					</article>
				{/iteration:widgetEventsRecentArticlesFull}
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'events'}">{$lblEventsArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$var|geturlforblock:'events':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</footer>
		</div>
	</section>
{/option:widgetEventsRecentArticlesFull}