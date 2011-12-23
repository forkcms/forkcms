<aside id="userActions">
	{option:isLoggedIn}
		<p>
			<strong>{$msgWelcomeUserX|sprintf:{$profileDisplayName}}</strong>
			<a href="{$var|geturlforblock:'profiles':'settings'}" title="{$profileDisplayName}">{$lblProfileSettings|ucfirst}</a>
			<a href="{$var|geturlforblock:'profiles':'logout'}">{$lblLogout|ucfirst}</a>
		</p>
	{/option:isLoggedIn}

	{option:!isLoggedIn}
		<p>
			<a href="{$var|geturlforblock:'profiles':'register'}"><span class="icon pencilIcon"></span><span class="iconWrapper">{$lblRegister|ucfirst}</span></a>
			<small> {$lblOr} </small>
			<a href="{$loginUrl}"><span class="icon userIcon"></span><span class="iconWrapper">{$lblLogin|ucfirst}</span></a>
		</p>
	{/option:!isLoggedIn}
</aside>
