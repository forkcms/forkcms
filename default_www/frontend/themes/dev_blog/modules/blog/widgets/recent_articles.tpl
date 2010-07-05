{*
	variables that are available:
	- {$blogRecentArticles}: contains an array with all posts, each element contains data about the post
*}

{option:blogRecentArticles}
<div id="blog" class="home recentArticles">
		{iteration:blogRecentArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$blogRecentArticles.full_url}" title="{$blogRecentArticles.title}">{$blogRecentArticles.title}</a></h2>
					<ul>
						<li>
							{$msgPublishedBy|sprintf:{$blogRecentArticles.user_id|usersetting:'nickname'}},
							{$blogRecentArticles.publish_on|date:'l F jS Y':{$LANGUAGE}},
							{$blogRecentArticles.publish_on|date:'H:i':{$LANGUAGE}}
						</li>
						{option:!blogRecentArticles.comments}<li class="lastChild"><a href="{$blogRecentArticles.full_url}#{$actReact}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!blogRecentArticles.comments}
						{option:blogRecentArticles.comments}
							{option:blogRecentArticles.comments_multiple}<li class="lastChild"><a href="{$blogRecentArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogRecentArticles.comments_count}}</a></li>{/option:blogRecentArticles.comments_multiple}
							{option:!blogRecentArticles.comments_multiple}<li class="lastChild"><a href="{$blogRecentArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!blogRecentArticles.comments_multiple}
						{/option:blogRecentArticles.comments}
					</ul>
				</div>
				<div class="content">
					{option:!blogRecentArticles.introduction}{$blogRecentArticles.text}{/option:!blogRecentArticles.introduction}
					{option:blogRecentArticles.introduction}{$blogRecentArticles.introduction}{/option:blogRecentArticles.introduction}
				</div>
			</div>
		{/iteration:blogRecentArticles}
	{/option:blogRecentArticles}
</div>