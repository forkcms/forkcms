{* Success *}
{option:forgotPasswordSuccess}
	<div class="alert alert-success"><p>{$msgForgotPasswordIsSuccess}</p></div>
{/option:forgotPasswordSuccess}

{* Error *}
{option:forgotPasswordHasError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section id="forgotPasswordForm">
		{form:forgotPassword}
			<fieldset>
				<p{option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail}{$txtEmailError}
				</p>
				<p>
					<input class="btn btn-primary" type="submit" value="{$lblSend|ucfirst}" />
				</p>
			</fieldset>
		{/form:forgotPassword}
	</section>
{/option:!forgotPasswordHideForm}