{*
	variables that are available:
	- {$blogRecentArticles}: contains an array with all posts, each element contains data about the post
*}

{option:blogRecentArticles}
<div id="blog" class="recentArticles">
		{iteration:blogRecentArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$blogRecentArticles.full_url}" title="{$blogRecentArticles.title}">{$blogRecentArticles.title}</a></h2>
					<p class="date">{$lblWrittenOn|ucfirst} {$blogRecentArticles.publish_on|date:'j F Y':{$LANGUAGE}} {$lblOn} {$blogRecentArticles.publish_on|date:'H:i:s':{$LANGUAGE}}</p>
				</div>
				<div class="content">
					{option:!blogRecentArticles.introduction}{$blogRecentArticles.text}{/option:!blogRecentArticles.introduction}
					{option:blogRecentArticles.introduction}{$blogRecentArticles.introduction}{/option:blogRecentArticles.introduction}
				</div>
				<div class="meta">
					<ul>
						<!-- Permalink -->
						<li><a href="{$blogRecentArticles.full_url}" title="{$blogRecentArticles.title}">{$blogRecentArticles.title}</a> {$msgWroteBy|sprintf:{$blogRecentArticles.user_id|userSetting:'nickname'}}</li>

						<!-- Category -->
						<li>{$lblCategory|ucfirst}: <a href="{$blogRecentArticles.category_full_url}" title="{$blogRecentArticles.category_name}">{$blogRecentArticles.category_name}</a></li>

						{option:blogRecentArticles.tags}
						<!-- Tags -->
						<li>{$lblTags|ucfirst}: {iteration:blogRecentArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:blogRecentArticles.tags}</li>
						{/option:blogRecentArticles.tags}

						<!-- Comments -->
						{option:!blogRecentArticles.comments}<li><a href="{$blogRecentArticles.full_url}#{$actReact}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!blogRecentArticles.comments}
						{option:blogRecentArticles.comments}
							{option:blogRecentArticles.comments_multiple}<li><a href="{$blogRecentArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$blogRecentArticles.comments_count}}</a></li>{/option:blogRecentArticles.comments_multiple}
							{option:!blogRecentArticles.comments_multiple}<li><a href="{$blogRecentArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!blogRecentArticles.comments_multiple}
						{/option:blogRecentArticles.comments}
					</ul>
				</div>
			</div>
		{/iteration:blogRecentArticles}
	{/option:blogRecentArticles}
</div>