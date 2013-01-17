{* Error *}
{option:formError}
	<div class="alert alert-error">
		{option:loginError}
			<p>{$loginError}</p>
		{/option:loginError}

		{option:!loginError}
			<p>{$errFormError}</p>
		{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm" class="mod">
	<div class="bd">
		{form:login}
			<fieldset class="form-horizontal">
				<div class="control-group {option:txtEmailError}errorArea{/option:txtEmailError}">
					<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtEmail}
						{$txtEmailError}
					</div>
				</div>
				<div class="control-group {option:txtEmailError}errorArea{/option:txtEmailError}">
					<label class="control-label" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtPassword}{$txtPasswordError}
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<label for="remember">{$chkRemember} {$lblRememberMe|ucfirst}</label>
						{$chkRememberError}
						<input class="btn" type="submit" value="{$lblLogin|ucfirst}" />
						<a href="{$var|geturlforblock:'profiles':'forgot_password'}" title="{$msgForgotPassword}">{$msgForgotPassword}</a>
					</div>
				</div>
			</fieldset>
		{/form:login}
	</div>
</section>