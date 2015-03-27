{* Success *}
{option:updatePasswordSuccess}
	<div class="alert alert-success"><p>{$msgUpdatePasswordIsSuccess}</p></div>
{/option:updatePasswordSuccess}

{* Error *}
{option:updatePasswordHasFormError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:updatePasswordHasFormError}

<section id="updatePasswordForm">
	{form:updatePassword}
		<fieldset>
			<p{option:txtOldPasswordError} class="alert alert-danger"{/option:txtOldPasswordError}>
				<label for="oldPassword">{$lblOldPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtOldPassword}{$txtOldPasswordError}
			</p>
			<p{option:txtNewPasswordError} class="alert alert-danger"{/option:txtNewPasswordError}>
				<label for="newPassword">{$lblNewPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtNewPassword}{$txtNewPasswordError}
			</p>
			<p>
				<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
			</p>
			<p>
				<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
			</p>
		</fieldset>
	{/form:updatePassword}
</section>