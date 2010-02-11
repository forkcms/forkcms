{*
	variables that are available:
	- {$recentComments}: contains an array with the recent comments. Each element contains data about the comment.
*}

{option:recentComments}
<h3>{$lblRecentComments|ucfirst}</h3>
<ul>
	{iteration:recentComments}
	<li>{$recentComments.author} {$msgCommentedOn} <a href="{$recentComments.full_url}">{$recentComments.post_title}</a></li>
	{/iteration:recentComments}
</ul>
{/option:recentComments}