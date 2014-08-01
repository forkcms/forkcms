{option:forgotPasswordSuccess}<div class="alert alert-success" role="alert">{$msgForgotPasswordIsSuccess}</div>{/option:forgotPasswordSuccess}
{option:forgotPasswordHasError}<div class="alert alert-danger" role="alert">{$errFormError}</div>{/option:forgotPasswordHasError}

{option:!forgotPasswordHideForm}
	<section id="forgotPasswordForm" class="profiles">
		<div class="bd">
			{form:forgotPassword}
			  <label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="input-group {option:txtEmailError} has-error{/option:txtEmailError}">
					{$txtEmail}
					<span class="input-group-btn">
  					<input class="btn btn-primary" type="submit" value="{$lblSend|ucfirst}" />
					</span>
				</div>
				{$txtEmailError}
			{/form:forgotPassword}
		</div>
	</section>
{/option:!forgotPasswordHideForm}
