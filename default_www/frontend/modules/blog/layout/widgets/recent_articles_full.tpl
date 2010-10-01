{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
<div class="widget widgetBlogRecentArticlesFull">
	{iteration:widgetBlogRecentArticlesFull}
		<div class="article">
			<div class="heading">
				<h3><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h3>
				<p class="date">{$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}|ucfirst} -
				{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticlesFull.comments}
				{option:widgetBlogRecentArticlesFull.comments}
					{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
					{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
				{/option:widgetBlogRecentArticlesFull.comments}
				</p>
			</div>
			<div class="content">
				{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
				{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
			</div>
			<p class="meta">
				{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_name}">{$widgetBlogRecentArticlesFull.category_name}</a>. {option:widgetBlogRecentArticlesFull.tags}{$lblTags|ucfirst}: {iteration:widgetBlogRecentArticlesFull.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:widgetBlogRecentArticlesFull.tags}{/option:widgetBlogRecentArticlesFull.tags}
			</p>
		</div>
	{/iteration:widgetBlogRecentArticlesFull}
</div>
<div class="buttonHolder">
	<a class="button" href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
	<a class="button" id="RSSfeed" href="{$var|geturlforblock:'blog':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
</div>
{/option:widgetBlogRecentArticlesFull}