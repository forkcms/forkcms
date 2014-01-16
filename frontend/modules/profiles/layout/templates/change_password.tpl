{* Success *}
{option:updatePasswordSuccess}
	<div class="alert-box success"><p>{$msgUpdatePasswordIsSuccess}</p></div>
{/option:updatePasswordSuccess}

{* Error *}
{option:updatePasswordHasFormError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:updatePasswordHasFormError}

<section>
	{form:updatePassword}
		<fieldset>
			<p{option:txtOldPasswordError} class="form-error"{/option:txtOldPasswordError}>
				<label for="oldPassword">{$lblOldPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtOldPassword}{$txtOldPasswordError}
			</p>
			<p{option:txtNewPasswordError} class="form-error"{/option:txtNewPasswordError}>
				<label for="newPassword">{$lblNewPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtNewPassword}{$txtNewPasswordError}
			</p>
			<p>
				<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
			</p>
			<p>
				<input type="submit" value="{$lblSave|ucfirst}" />
			</p>
		</fieldset>
	{/form:updatePassword}
</section>