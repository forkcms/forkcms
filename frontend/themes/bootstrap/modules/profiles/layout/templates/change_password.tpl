{option:updatePasswordSuccess}<div class="alert alert-success" role="alert">{$msgUpdatePasswordIsSuccess}</div>{/option:updatePasswordSuccess}
{option:updatePasswordHasFormError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:updatePasswordHasFormError}

<section id="updatePasswordForm" class="profiles">
	<div class="bd">
		{form:updatePassword}
			<fieldset class="form-horizontal">
				<div class="form-group{option:txtOldPasswordError} has-error{/option:txtOldPasswordError}">
					<label class="control-label col-sm-2" for="oldPassword">{$lblOldPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
					{$txtOldPassword}{$txtOldPasswordError}
					</div>
				</div>
				<div class="control-group{option:txtNewPasswordError} has-error{/option:txtNewPasswordError}">
					<label class="control-label col-sm-2" for="newPassword">{$lblNewPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
						{$txtNewPassword}{$txtNewPasswordError}
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-6">
						<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updatePassword}
	</div>
</section>