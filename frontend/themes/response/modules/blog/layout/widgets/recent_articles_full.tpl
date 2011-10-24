{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	<section id="blogRecentArticlesFullWidget" class="mod">
		<div class="inner">
			<div class="bd">
				{iteration:widgetBlogRecentArticlesFull}
					<article class="mod{option:widgetBlogRecentArticlesFull} lastChild{/option:widgetBlogRecentArticlesFull}">
						<div class="inner">
							<header class="hd">
								<h2><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h2>


								<p class="meta">
									{$msgWrittenBy|ucfirst|sprintf:{$widgetBlogRecentArticlesFull.user_id|usersetting:'nickname'}}
								 	{$widgetBlogRecentArticlesFull.publish_on|date:"M d, Y":{$LANGUAGE}}
									{$lblIn} <a href="{$widgetBlogRecentArticlesFull.category_full_url}" title="{$widgetBlogRecentArticlesFull.category_title}">{$widgetBlogRecentArticlesFull.category_title}</a>
									 -
									{option:!widgetBlogRecentArticlesFull.comments}
										<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}">
											{$msgBlogNoComments|ucfirst}
										</a>
									{/option:!widgetBlogRecentArticlesFull.comments}
									{option:widgetBlogRecentArticlesFull.comments}
										{option:widgetBlogRecentArticlesFull.comments_multiple}
											<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">
												{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticlesFull.comments_count}}
											</a>
										{/option:widgetBlogRecentArticlesFull.comments_multiple}
										{option:!widgetBlogRecentArticlesFull.comments_multiple}
											<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}">
												{$msgBlogOneComment}
											</a>
										{/option:!widgetBlogRecentArticlesFull.comments_multiple}
									{/option:widgetBlogRecentArticlesFull.comments}
								</p>
							</header>
							<div class="bd content">
								{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
								{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
							</div>
						</div>
					</article>
				{/iteration:widgetBlogRecentArticlesFull}
			</div>
		</div>
	</section>
{/option:widgetBlogRecentArticlesFull}