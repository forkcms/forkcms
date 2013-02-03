{option:forgotPasswordSuccess}<div class="alert alert-success" role="alert">{$msgForgotPasswordIsSuccess}</div>{/option:forgotPasswordSuccess}
{option:forgotPasswordHasError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section id="forgotPasswordForm" class="profiles">
		<div class="bd">
			{form:forgotPassword}
				<fieldset class="control-group {option:txtEmailError} error{/option:txtEmailError}">
					<p class="form-inline">
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail}
						<input class="btn btn-primary" type="submit" value="{$lblSend|ucfirst}" />
						{$txtEmailError}
					</p>
				</fieldset>
			{/form:forgotPassword}
		</div>
	</section>
{/option:!forgotPasswordHideForm}