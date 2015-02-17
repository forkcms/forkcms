{option:navigation}

		{iteration:navigation}

			{option:!navigation.children}
			<li{option:navigation.selected} class="active"{/option:navigation.selected}>
				<a href="{$navigation.link}" title="{$navigation.navigation_title}"{option:navigation.nofollow} rel="nofollow"{/option:navigation.nofollow}>{$navigation.navigation_title}</a>
			</li>
			{/option:!navigation.children}

			{option:navigation.children}
			<li class="dropdown{option:navigation.selected} active{/option:navigation.selected}" id="dropdownNavigation">
				<a href="{$navigation.link}" class="dropdown-toggle" data-toggle="dropdown">{$navigation.navigation_title} <b class="caret"></b></a>
					<ul class="dropdown-menu">	
					{$navigation.children}
			</li>
			{/option:navigation.children}
		
		{/iteration:navigation}
	</ul>
{/option:navigation}