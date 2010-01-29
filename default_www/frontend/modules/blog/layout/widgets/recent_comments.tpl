<h3>{$lblRecentComments|ucfirst}</h3>
{option:recentComments}
<ul>
	{iteration:recentComments}
	<li>{$recentComments.author} {$msgCommentedOn} <a href="{$recentComments.full_url}">{$recentComments.post_title}</a></li>
	{/iteration:recentComments}
</ul>
{/option:recentComments}