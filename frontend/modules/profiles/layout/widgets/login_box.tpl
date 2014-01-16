
{*
	variables that are available:
	- {$widgetProfilesLoginBox}:
*}

<section>
	{option:isLoggedIn}
		<p>
			{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}} - <a href="{$profile.url.settings}">{$lblSettings|ucfirst}</a>
		</p>
	{/option:isLoggedIn}

	{option:!isLoggedIn}
		<header>
			<h2>{$lblLogin|ucfirst}</h2>
		</header>
		{form:login}
			<fieldset>
				<p{option:txtEmailError} class="form-error"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail}{$txtEmailError}
				</p>
				<p{option:txtPasswordError} class="form-error"{/option:txtPasswordError}>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtPassword}{$txtPasswordError}
				</p>
				<p>
					<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
					{$chkRememberError}
				</p>
				<p>
					<input type="submit" value="{$lblLogin|ucfirst}" />
				</p>
			</fieldset>
		{/form:login}
		<footer>
			<p>
				<a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a>
			</p>
		</footer>
	{/option:!isLoggedIn}
</section>