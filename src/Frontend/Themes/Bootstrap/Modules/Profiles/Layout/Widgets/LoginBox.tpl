
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
						<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
							<label class="control-label col-sm-2" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="col-sm-6">
								{$txtEmail}{$txtEmailError}
							</div>
						</div>
						<div class="form-group{option:txtPasswordError} has-error{/option:txtPasswordError}">
							<label class="control-label col-sm-2" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="col-sm-6">
								{$txtPassword}{$txtPasswordError}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-6">
								<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
								{$chkRememberError}
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-6">
								<input class="btn" type="submit" value="{$lblLogin|ucfirst}" />
								<small><a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a></small>
							</div>
						</div>
					</fieldset>
				{/form:login}
			</div>
		{/option:!isLoggedIn}
</section>