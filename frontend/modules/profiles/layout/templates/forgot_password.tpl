{* Success *}
{option:forgotPasswordSuccess}
	<div class="alert-box success"><p>{$msgForgotPasswordIsSuccess}</p></div>
{/option:forgotPasswordSuccess}

{* Error *}
{option:forgotPasswordHasError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section>
		{form:forgotPassword}
			<fieldset>
				<p{option:txtEmailError} class="error-area"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail}{$txtEmailError}
				</p>
				<p>
					<input type="submit" value="{$lblSend|ucfirst}" />
				</p>
			</fieldset>
		{/form:forgotPassword}
	</section>
{/option:!forgotPasswordHideForm}