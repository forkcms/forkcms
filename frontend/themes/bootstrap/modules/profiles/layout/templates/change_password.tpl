{* Success *}
{option:updatePasswordSuccess}
	<div class="alert alert-success"><p>{$msgUpdatePasswordIsSuccess}</p></div>
{/option:updatePasswordSuccess}

{* Error *}
{option:updatePasswordHasFormError}
	<div class="alert alert-error"><p>{$errFormError}</p></div>
{/option:updatePasswordHasFormError}

<section id="updatePasswordForm">
	<div class="bd">
		{form:updatePassword}
			<fieldset class="form-horizontal">
				<div class="control-group{option:txtOldPasswordError} error{/option:txtOldPasswordError}">
					<label class="control-label" for="oldPassword">{$lblOldPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
					{$txtOldPassword}{$txtOldPasswordError}
					</div>
				</div>
				<div class="control-group{option:txtNewPasswordError} error{/option:txtNewPasswordError}">
					<label class="control-label" for="newPassword">{$lblNewPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtNewPassword}{$txtNewPasswordError}
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
						<input class="btn" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updatePassword}
	</div>
</section>