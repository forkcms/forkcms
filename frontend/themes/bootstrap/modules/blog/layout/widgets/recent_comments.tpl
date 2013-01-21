{*
	variables that are available:
	- {$widgetBlogRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetBlogRecentComments}
	<section id="blogRecentCommentsWidget" class="well">
		<header>
		    <h3>{$lblRecentComments|ucfirst}</h3>
		</header>
		<ul>
		    {iteration:widgetBlogRecentComments}
		    	<li>
		    		{option:widgetBlogRecentComments.website}<a href="{$widgetBlogRecentComments.website}" rel="nofollow">{/option:widgetBlogRecentComments.website}
		    			{$widgetBlogRecentComments.author}
		    		{option:widgetBlogRecentComments.website}</a>{/option:widgetBlogRecentComments.website}
		    		{$lblCommentedOn} <a href="{$widgetBlogRecentComments.full_url}">{$widgetBlogRecentComments.post_title}</a>
		    		<time class="muted" itemprop="commentTime" datetime="{$widgetBlogRecentComments.created_on|date:'Y-m-d\TH:i:s'}">{$widgetBlogRecentComments.created_on|timeago}</time>
		    	</li>
		    {/iteration:widgetBlogRecentComments}
		</ul>
	</section>
{/option:widgetBlogRecentComments}