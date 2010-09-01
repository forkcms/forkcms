{*
	variables that are available:
	- {$widgetBlogRecentArticles}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticles}
	<div class="widget widgetBlogRecentArticles">
		{iteration:widgetBlogRecentArticles}
			<div class="article">
				<div class="heading">
					<h3><a href="{$widgetBlogRecentArticles.full_url}" title="{$widgetBlogRecentArticles.title}">{$widgetBlogRecentArticles.title}</a></h3>
					<p class="date">{$widgetBlogRecentArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}|ucfirst} -
					{option:!widgetBlogRecentArticles.comments}<a href="{$widgetBlogRecentArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticles.comments}
					{option:widgetBlogRecentArticles.comments}
						{option:widgetBlogRecentArticles.comments_multiple}<a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticles.comments_count}}</a>{/option:widgetBlogRecentArticles.comments_multiple}
						{option:!widgetBlogRecentArticles.comments_multiple}<a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticles.comments_multiple}
					{/option:widgetBlogRecentArticles.comments}
					</p>
				</div>
				<div class="content">
					{option:!widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.text}{/option:!widgetBlogRecentArticles.introduction}
					{option:widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.introduction}{/option:widgetBlogRecentArticles.introduction}
				</div>
				<p class="meta">
					{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticles.user_id|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$widgetBlogRecentArticles.category_full_url}" title="{$widgetBlogRecentArticles.category_name}">{$widgetBlogRecentArticles.category_name}</a>. {option:widgetBlogRecentArticles.tags}{$lblTags|ucfirst}: {iteration:widgetBlogRecentArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:widgetBlogRecentArticles.tags}{/option:widgetBlogRecentArticles.tags}
				</p>
			</div>
		{/iteration:widgetBlogRecentArticles}
	</div>

	<div class="buttonHolder">
		<a class="button" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
		<a class="button" id="RSSfeed" href="{$var|geturlforblock:'blog':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
	</div>

{/option:widgetBlogRecentArticles}