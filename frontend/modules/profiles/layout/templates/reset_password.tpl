{* Success *}
{option:resetPasswordSuccess}
	<div class="alert-box success"><p>{$msgResetPasswordIsSuccess}</p></div>
{/option:resetPasswordSuccess}

{* Error *}
{option:resetPasswordHasError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section>
		{form:resetPassword}
			<fieldset>
				<p{option:txtPasswordError} class="form-error"{/option:txtPasswordError}>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtPassword}{$txtPasswordError}
				</p>
				<p>
					<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
				</p>
				<p>
					<input type="submit" value="{$lblSave|ucfirst}" />
				</p>
			</fieldset>
		{/form:resetPassword}
	</section>
{/option:!resetPasswordHideForm}