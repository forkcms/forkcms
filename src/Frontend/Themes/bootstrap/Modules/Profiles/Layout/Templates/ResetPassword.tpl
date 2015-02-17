{* Success *}
{option:resetPasswordSuccess}
	<div class="alert alert-success"><p>{$msgResetPasswordIsSuccess}</p></div>
{/option:resetPasswordSuccess}

{* Error *}
{option:resetPasswordHasError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section id="resetPasswordForm">
		{form:resetPassword}
			<fieldset>
				<p{option:txtPasswordError} class="alert alert-danger"{/option:txtPasswordError}>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtPassword}{$txtPasswordError}
				</p>
				<p>
					<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
				</p>
				<p>
					<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
				</p>
			</fieldset>
		{/form:resetPassword}
	</section>
{/option:!resetPasswordHideForm}