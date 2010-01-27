<h3>{$lblRecentComments|ucfirst}</h3>
{option:recentComments}
<ul>
	{iteration:recentComments}
	<li>{$recentComments.author} {$lblCommentedOn} <a href="{$recentComments.url}">{$recentComments.entry_title}</a></li>
	{/iteration:recentComments}
</ul>
{/option:recentComments}