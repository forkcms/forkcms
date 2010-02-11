{*
	variables that are available:
	- {$tags}: contains an array with all tags that are used on the site, each element contains data about the tag
*}

{option:!tags}<div class="message warning"><p>{$msgTagsNoItems}</p></div>{/option:!tags}
{option:tags}
	<ul>
		{iteration:tags}
			<li><a href="{$var|geturlforblock:'tags':'detail'}/{$tags.url}">{$tags.name}</a></li>
		{/iteration:tags}
	</ul>
{/option:tags}