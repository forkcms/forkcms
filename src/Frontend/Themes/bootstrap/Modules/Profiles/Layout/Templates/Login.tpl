{* Error *}
{option:formError}
	<div class="alert alert-danger">
		{option:loginError}
			<p>{$loginError}</p>
		{/option:loginError}

		{option:!loginError}
			<p>{$errFormError}</p>
		{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm">
	{form:login}
		<fieldset>
			<p{option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
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
</section>