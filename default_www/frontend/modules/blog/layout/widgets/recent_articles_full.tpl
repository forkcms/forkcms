{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<div id="blogBlogRecentArticlesFullWidget" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblRecentArticles|ucfirst}</h3>
			</div>
			<div class="bd">
				{iteration:widgetBlogRecentArticlesFull}
					<div class="mod article">
						<div class="inner">
							<div class="hd">
								<h4><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h4>
								<p>{$widgetBlogRecentArticlesFull.publish_on|date:{$dateFormatLong}:{$LANGUAGE}|ucfirst} -
								{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticlesFull.comments}
								{option:widgetBlogRecentArticlesFull.comments}
									{option:widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}</a>{/option:widgetBlogRecentArticlesFull.comments_multiple}
									{option:!widgetBlogRecentArticlesFull.comments_multiple}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticlesFull.comments_multiple}
								{/option:widgetBlogRecentArticlesFull.comments}
								</p>
							</div>
							<div class="bd content">
								{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
								{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
							</div>
							<div class="ft">
								<p>
									{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}} {$lblInTheCategory}: <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_name}">{$widgetBlogRecentArticlesFull.category_name}</a>. {option:widgetBlogRecentArticlesFull.tags}{$lblTags|ucfirst}: {iteration:widgetBlogRecentArticlesFull.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:widgetBlogRecentArticlesFull.tags}{/option:widgetBlogRecentArticlesFull.tags}
								</p>
							</div>
						</div>
					</div>
				{/iteration:widgetBlogRecentArticlesFull}
			</div>
			<div class="ft">
				<p>
					<a href="{$var|geturlforblock:'blog'}">{$lblBlogArchive|ucfirst}</a>
					<a id="RSSfeed" href="{$var|geturlforblock:'blog':'rss'}">{$lblSubscribeToTheRSSFeed|ucfirst}</a>
				</p>
			</div>
		</div>
	</div>
{/option:widgetBlogRecentArticlesFull}