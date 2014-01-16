{* Error *}
{option:formError}
	<div class="alert-box error">
		{option:loginError}
			<p>{$loginError}</p>
		{/option:loginError}

		{option:!loginError}
			<p>{$errFormError}</p>
		{/option:!loginError}
	</div>
{/option:formError}

<section>
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
</section>