{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<section>
		<header>
			<h2>{$lblRecentArticles|ucfirst}</h2>
		</header>
		{iteration:widgetBlogRecentArticlesFull}
			<article>
				<header>
					<h2><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h2>
					<p>
						{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}} {$lblOn} {$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}} {$lblIn} {$lblThe} {$lblCategory} <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_title}">{$widgetBlogRecentArticlesFull.category_title}</a>
						{option:widgetBlogRecentArticlesFull.allow_comments}
						-
							{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticlesFull.comments}
							{option:widgetBlogRecentArticlesFull.comments}
								{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
								{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
							{/option:widgetBlogRecentArticlesFull.comments}
						{/option:widgetBlogRecentArticlesFull.allow_comments}
					</p>
				</header>
				<div>
					{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
					{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
				</div>
			</article>
		{/iteration:widgetBlogRecentArticlesFull}
		<footer>
			<p>
				<a href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
				<a id="RSSfeed" href="{$widgetBlogRecentArticlesFullRssLink}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
			</p>
		</footer>
	</section>
{/option:widgetBlogRecentArticlesFull}
