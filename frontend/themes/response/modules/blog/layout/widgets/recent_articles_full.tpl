{*
	variables that are available:
	- {$widgetBlogRecentArticlesFull}: contains an array with all posts, each element contains data about the post
*}

{option:widgetBlogRecentArticlesFull}
	{iteration:widgetBlogRecentArticlesFull}
		<article class="blogItem{option:widgetBlogRecentArticlesFull.last} last{/option:widgetBlogRecentArticlesFull.last}"> 
			<header> 
				<h1><a href="{$widgetBlogRecentArticlesFull.full_url}" title="{$widgetBlogRecentArticlesFull.title}">{$widgetBlogRecentArticlesFull.title}</a></h1>
				<ul class="meta">
					<li>
						{* Date *}
						<time datetime="{$widgetBlogRecentArticlesFull.publish_on|date:'c':{$LANGUAGE}}">
							<span class="day">{$widgetBlogRecentArticlesFull.publish_on|date:'d':{$LANGUAGE}}</span>
							<span class="month">{$widgetBlogRecentArticlesFull.publish_on|date:'M':{$LANGUAGE}}</span>
							<span class="year">{$widgetBlogRecentArticlesFull.publish_on|date:'Y':{$LANGUAGE}}</span>
						</time>
					</li> 
					<li class="comments">
						{* Comments *}
						{option:!widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComment}" class="nocomments" title="{$msgBlogNoComments|ucfirst}">{$msgBlogNoComments|ucfirst}</a>{/option:!widgetBlogRecentArticlesFull.comments}
						{option:widgetBlogRecentArticlesFull.comments}<a href="{$widgetBlogRecentArticlesFull.full_url}#{$actComments}" title="{$msgBlogComments|ucfirst}">{$widgetBlogRecentArticlesFull.comments_count} comment</a>{/option:widgetBlogRecentArticlesFull.comments}
					</li>
				</ul>
			</header>
			<div class="content">
				{option:widgetBlogRecentArticlesFull.image}<p><img src="{$FRONTEND_FILES_URL}/blog/images/source/{$widgetBlogRecentArticlesFull.image}" alt="{$widgetBlogRecentArticlesFull.title}" /></p>{/option:widgetBlogRecentArticlesFull.image}
				{option:!widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.text}{/option:!widgetBlogRecentArticlesFull.introduction}
				{option:widgetBlogRecentArticlesFull.introduction}{$widgetBlogRecentArticlesFull.introduction}{/option:widgetBlogRecentArticlesFull.introduction}
			</div>
		</article>
	{/iteration:widgetBlogRecentArticlesFull}
	{include:core/layout/templates/pagination.tpl}
{/option:widgetBlogRecentArticlesFull}