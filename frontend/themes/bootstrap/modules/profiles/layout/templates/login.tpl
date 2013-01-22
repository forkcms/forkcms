{* Error *}
{option:formError}
	<div class="alert alert-error">
		{option:loginError}
			{$loginError}
		{/option:loginError}

		{option:!loginError}
			{$errFormError}
		{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm" class="profiles">
	{form:login}
		<fieldset class="form-horizontal">
			<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
				<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtEmail}
					{$txtEmailError}
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
					<input class="btn btn-primary" type="submit" value="{$lblLogin|ucfirst}" />
					<small><a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a></small>
				</div>
			</div>
		</fieldset>
	{/form:login}
</section>