<section>
	{option:isLoggedIn}
		<p>
			<strong>{$msgWelcomeUserX|sprintf:{$profileDisplayName}}</strong>
			<a href="{$var|geturlforblock:'profiles':'settings'}" title="{$profileDisplayName}">{$lblProfileSettings|ucfirst}</a>
			<a href="{$var|geturlforblock:'profiles':'logout'}">{$lblLogout|ucfirst}</a>
		</p>
	{/option:isLoggedIn}

	{option:!isLoggedIn}
		<p>
			<a href="{$var|geturlforblock:'profiles':'register'}">{$lblRegister|ucfirst}</a> {$lblOr} <a href="{$loginUrl}">{$lblLogin|ucfirst}</a>
		</p>
	{/option:!isLoggedIn}
</section>
