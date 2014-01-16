{* Error *}
{option:formError}
	<div class="message error">
		{option:loginError}
			<p>{$loginError}</p>
		{/option:loginError}

		{option:!loginError}
			<p>{$errFormError}</p>
		{/option:!loginError}
	</div>
{/option:formError}

<section id="loginForm" class="mod">
	<div class="inner">
		<div class="bd">
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
	</div>
</section>