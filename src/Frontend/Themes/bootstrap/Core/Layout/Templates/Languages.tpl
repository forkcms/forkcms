{option:languages}
	<ul class="nav navbar-nav navbar-right">
		{iteration:languages}
			<li{option:languages.current} class="active"{/option:languages.current}>
				<a href="{$languages.url}">{$languages.label|uppercase}</a>
			</li>
		{/iteration:languages}
	</ul>
{/option:languages}