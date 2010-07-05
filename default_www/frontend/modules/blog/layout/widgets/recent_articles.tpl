{*
	variables that are available:
	- {$widgetBlogRecentArticles}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticles}
<div class="widget widgetBlogRecentArticles">
		{iteration:widgetBlogRecentArticles}
			<div class="article">
				<div class="heading">
					<h2><a href="{$widgetBlogRecentArticles.full_url}" title="{$widgetBlogRecentArticles.title}">{$widgetBlogRecentArticles.title}</a></h2>
					<p class="date">{$lblWrittenOn|ucfirst} {$widgetBlogRecentArticles.publish_on|date:'j F Y':{$LANGUAGE}} {$lblOn} {$widgetBlogRecentArticles.publish_on|date:'H:i:s':{$LANGUAGE}}</p>
				</div>
				<div class="content">
					{option:!widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.text}{/option:!widgetBlogRecentArticles.introduction}
					{option:widgetBlogRecentArticles.introduction}{$widgetBlogRecentArticles.introduction}{/option:widgetBlogRecentArticles.introduction}
				</div>
				<div class="meta">
					<ul>
						<!-- Permalink -->
						<li><a href="{$widgetBlogRecentArticles.full_url}" title="{$widgetBlogRecentArticles.title}">{$widgetBlogRecentArticles.title}</a> {$msgWroteBy|sprintf:{$widgetBlogRecentArticles.user_id|usersetting:'nickname'}}</li>

						<!-- Category -->
						<li>{$lblCategory|ucfirst}: <a href="{$widgetBlogRecentArticles.category_full_url}" title="{$widgetBlogRecentArticles.category_name}">{$widgetBlogRecentArticles.category_name}</a></li>

						{option:widgetBlogRecentArticles.tags}
						<!-- Tags -->
						<li>{$lblTags|ucfirst}: {iteration:widgetBlogRecentArticles.tags}<a href="{$tags.full_url}" rel="tag" title="{$tags.name}">{$tags.name}</a>{option:!tags.last}, {/option:!tags.last}{/iteration:widgetBlogRecentArticles.tags}</li>
						{/option:widgetBlogRecentArticles.tags}

						<!-- Comments -->
						{option:!widgetBlogRecentArticles.comments}<li><a href="{$widgetBlogRecentArticles.full_url}#{$actReact}">{$msgBlogNoComments|ucfirst}</a></li>{/option:!widgetBlogRecentArticles.comments}
						{option:widgetBlogRecentArticles.comments}
							{option:widgetBlogRecentArticles.comments_multiple}<li><a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogNumberOfComments|sprintf:{$widgetBlogRecentArticles.comments_count}}</a></li>{/option:widgetBlogRecentArticles.comments_multiple}
							{option:!widgetBlogRecentArticles.comments_multiple}<li><a href="{$widgetBlogRecentArticles.full_url}#{$actComments}">{$msgBlogOneComment}</a></li>{/option:!widgetBlogRecentArticles.comments_multiple}
						{/option:widgetBlogRecentArticles.comments}
					</ul>
				</div>
			</div>
		{/iteration:widgetBlogRecentArticles}
	{/option:widgetBlogRecentArticles}
</div>