{*
	variables that are available:
	- {$isLoggedIn}
	- {$profile}: only available when logged in
*}
{option:isLoggedIn}
	{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}}
{/option:isLoggedIn}

{option:!isLoggedIn}
    <a href="{$var|geturlforblock:'profiles':'login'}?queryString={$var|geturlforblock:'profiles'}">{$lblLogin|ucfirst}</a>
{/option:!isLoggedIn}