{option:resetPasswordSuccess}<div class="alert alert-success" role="alert">{$msgResetPasswordIsSuccess}</div>{/option:resetPasswordSuccess}
{option:resetPasswordHasError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section id="resetPasswordForm" class="profiles">
			<div class="bd">
				{form:resetPassword}
					<fieldset class="form-horizontal">
						<div class="control-group{option:txtPasswordError} error{/option:txtPasswordError}">
							<label class="control-label" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="controls">
								{$txtPassword}{$txtPasswordError}
								<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
							</div>
						</div>
					</fieldset>
				{/form:resetPassword}
			</div>
	</section>
{/option:!resetPasswordHideForm}