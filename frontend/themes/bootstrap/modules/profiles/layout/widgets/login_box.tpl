
{*
	variables that are available:
	- {$widgetProfilesLoginBox}:
*}

<section id="profilesLoginBoxWidget" class="profiles">
		{option:isLoggedIn}
			<div class="bd content">
				{$msgProfilesLoggedInAs|sprintf:{$profile.display_name}:{$profile.url.dashboard}} -
				<a href="{$profile.url.settings}">{$lblSettings|ucfirst}</a>
			</div>
		{/option:isLoggedIn}

		{option:!isLoggedIn}
			<header role="banner">
				<h3>{$lblLogin|ucfirst}</h3>
			</header>
			<div class="bd content">
				{form:login}
					<fieldset class="form-horizontal">
						<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
							<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="controls">
								{$txtEmail}{$txtEmailError}
							</div>
						</div>
						<div class="control-group{option:txtPasswordError} error{/option:txtPasswordError}">
							<label class="control-label" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="controls">
								{$txtPassword}{$txtPasswordError}
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
								{$chkRememberError}
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input class="btn" type="submit" value="{$lblLogin|ucfirst}" />
								<small><a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a></small>
							</div>
						</div>
					</fieldset>
				{/form:login}
			</div>
		{/option:!isLoggedIn}
</section>