{option:tags}
	<ul>
		{iteration:tags}
			<li><a href="{$var|geturlforblock:'tags':'detail'}/{$tags.url}">{$tags.name}</a></li>
		{/iteration:tags}
	</ul>
{/option:tags}

{option:!tags}Er zijn nog géén tags{/option:!tags}