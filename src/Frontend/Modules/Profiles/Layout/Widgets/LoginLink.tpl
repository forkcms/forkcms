{*
	variables that are available:
	- {$isLoggedIn}
	- {$profile}: only available when logged in
*}

{option:isLoggedIn}
	{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}}
{/option:isLoggedIn}

{option:!isLoggedIn}
    <a href="{$var|geturlforblock:'Profiles':'Login'}?queryString={$var|geturlforblock:'Profiles'}">{$lblLogin|ucfirst}</a>
{/option:!isLoggedIn}