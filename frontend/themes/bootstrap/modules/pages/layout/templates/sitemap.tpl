{option:navigation}
	<ul>
		{iteration:navigation}
			<li>
				<a href="{$navigation.link}" title="{$navigation.navigation_title}"{option:navigation.nofollow} rel="nofollow"{/option:navigation.nofollow}>{$navigation.navigation_title}</a>
				{$navigation.children}
			</li>
		{/iteration:navigation}
	</ul>
{/option:navigation}