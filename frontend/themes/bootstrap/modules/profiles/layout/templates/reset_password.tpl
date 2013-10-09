{option:resetPasswordSuccess}<div class="alert alert-success" role="alert">{$msgResetPasswordIsSuccess}</div>{/option:resetPasswordSuccess}
{option:resetPasswordHasError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section id="resetPasswordForm" class="profiles">
			<div class="bd">
				{form:resetPassword}
					<fieldset class="form-horizontal">
						<div class="form-group{option:txtPasswordError} has-error{/option:txtPasswordError}">
							<label class="control-label col-sm-2" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							<div class="col-sm-6">
								{$txtPassword}{$txtPasswordError}
							</div>
						</div>
						<div class="form-group">
						  <div class="col-sm-offset-2 col-sm-6">
  						  <div class="checkbox">
    						  <label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
  						  </div>
						  </div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-6">
								<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
							</div>
						</div>
					</fieldset>
				{/form:resetPassword}
			</div>
	</section>
{/option:!resetPasswordHideForm}