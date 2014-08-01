<aside id="userActions">
	{option:isLoggedIn}
		<p>
			<strong>{$msgWelcomeUserX|sprintf:{$profileDisplayName}}</strong>
			<a href="{$var|geturlforblock:'Profiles':'Settings'}" title="{$profileDisplayName}">{$lblProfileSettings|ucfirst}</a>
			<a href="{$var|geturlforblock:'Profiles':'Logout'}">{$lblLogout|ucfirst}</a>
		</p>
	{/option:isLoggedIn}

	{option:!isLoggedIn}
		<p>
			<a href="{$var|geturlforblock:'Profiles':'Register'}"><span class="icon pencilIcon"></span><span class="iconWrapper">{$lblRegister|ucfirst}</span></a>
			<small> {$lblOr} </small>
			<a href="{$loginUrl}"><span class="icon userIcon"></span><span class="iconWrapper">{$lblLogin|ucfirst}</span></a>
		</p>
	{/option:!isLoggedIn}
</aside>
