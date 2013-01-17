{* Success *}
{option:forgotPasswordSuccess}
	<div class="alert alert-success"><p>{$msgForgotPasswordIsSuccess}</p></div>
{/option:forgotPasswordSuccess}

{* Error *}
{option:forgotPasswordHasError}
	<div class="alert alert-error"><p>{$errFormError}</p></div>
{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section id="forgotPasswordForm">
		<div class="bd">
			{form:forgotPassword}
				<fieldset class="control-group {option:txtEmailError} error{/option:txtEmailError}">
					<p class="form-inline">
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail}
						<input class="btn" type="submit" value="{$lblSend|ucfirst}" />
						{$txtEmailError}
					</p>
				</fieldset>
			{/form:forgotPassword}
		</div>
	</section>
{/option:!forgotPasswordHideForm}