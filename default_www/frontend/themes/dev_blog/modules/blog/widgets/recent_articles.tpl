{*
	variables that are available:
	- {$widgetBlogRecentArticles}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticles}
	<div id="blog" class="home">
		{iteration:widgetBlogRecentArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$widgetBlogRecentArticles.full_url}" title="{$widgetBlogRecentArticles.title}">{$widgetBlogRecentArticles.title}</a></h2>
					<ul>
						<li>Published by {$widgetBlogRecentArticles.user_id|usersetting:'nickname'}, {$widgetBlogRecentArticles.publish_on|date:{$dateFormatLong}:{$LANGUAGE}}</li>
						<li class="lastChild">
							{option:!widgetBlogRecentArticles.comments}<a href="{$widgetBlogRecentArticles.full_url}#{$actComment}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticles.comments}
							{option:widgetBlogRecentArticles.comments}
								{option:widgetBlogRecentArticles.comments_multiple}<a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticles.comments_count}}</a>{/option:widgetBlogRecentArticles.comments_multiple}
								{option:!widgetBlogRecentArticles.comments_multiple}<a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a>{/option:!widgetBlogRecentArticles.comments_multiple}
							{/option:widgetBlogRecentArticles.comments}
						</li>
					</ul>
				</div>
				<div class="content">
					{option:!widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.text}{/option:!widgetBlogRecentArticles.introduction}
					{option:widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.introduction}{/option:widgetBlogRecentArticles.introduction}
				</div>
			</div>
		{/iteration:widgetBlogRecentArticles}
	</div>
{/option:widgetBlogRecentArticles}