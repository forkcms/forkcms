
{*
	variables that are available:
	- {$widgetProfilesLoginBox}:
*}

<section id="profilesLoginBoxWidget" class="mod">
	<div class="inner">
		{option:isLoggedIn}
			<div class="bd content">
				{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}} -
				<a href="{$profile.url.settings}">{$lblSettings|ucfirst}</a>
			</div>
		{/option:isLoggedIn}

		{option:!isLoggedIn}
			<header class="hd">
				<h3>{$lblLogin|ucfirst}</h3>
			</header>
			<div class="bd content">
				{form:login}
					<fieldset>
						<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}{$txtPasswordError}
						</p>
						<p>
							<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
							{$chkRememberError}
						</p>
						<p>
							<input class="inputSubmit" type="submit" value="{$lblLogin|ucfirst}" />
						</p>
					</fieldset>
				{/form:login}
			</div>
			<footer class="ft">
				<p>
					<a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a>
				</p>
			</footer>
		{/option:!isLoggedIn}
	</div>
</section>