
{*
	variables that are available:
	- {$widgetProfilesLoginBox}:
*}

<section id="profilesLoginBoxWidget">
		{option:isLoggedIn}
				{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}} -
				<a href="{$profile.url.settings}">{$lblSettings|ucfirst}</a>
		{/option:isLoggedIn}

		{option:!isLoggedIn}
			<header>
				<h3>{$lblLogin|ucfirst}</h3>
			</header>
				{form:login}
					<fieldset>
						<p{option:txtEmailError} class="alert alert-danger" {/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p{option:txtPasswordError} class="alert alert-danger"{/option:txtPasswordError}>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}{$txtPasswordError}
						</p>
						<p>
							<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
							{$chkRememberError}
						</p>
						<p>
							<input class="btn btn-primary" type="submit" value="{$lblLogin|ucfirst}" />
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