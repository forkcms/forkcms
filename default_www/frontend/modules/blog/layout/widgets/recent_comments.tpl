{*
	variables that are available:
	- {$widgetBlogRecentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:widgetBlogRecentComments}
<div class="widget widgetBlogRecentComments">
	<h3>{$lblRecentComments|ucfirst}</h3>
	<ul>
		{iteration:widgetBlogRecentComments}
		<li>
			{option:widgetBlogRecentComments.website}<a href="{$widgetBlogRecentComments.website}" rel="nofollow">{/option:widgetBlogRecentComments.website}
				<img src="{$FRONTEND_CORE_URL}/layout/images/default_author_avatar.gif" width="24" height="24" alt="{$widgetBlogRecentComments.author}" class="replaceWithGravatar" rel="{$widgetBlogRecentComments.gravatar_id}" />
				{$widgetBlogRecentComments.author}
			{option:widgetBlogRecentComments.website}</a>{/option:widgetBlogRecentComments.website}
			{$lblCommentedOn} <a href="{$widgetBlogRecentComments.full_url}">{$widgetBlogRecentComments.post_title}</a>
			<small>{$widgetBlogRecentComments.created_on|timeago}</small>
		</li>
		{/iteration:widgetBlogRecentComments}
	</ul>
</div>
{/option:widgetBlogRecentComments}