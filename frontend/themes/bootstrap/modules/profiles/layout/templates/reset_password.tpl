{* Success *}
{option:resetPasswordSuccess}
	<div class="alert alert-success"><p>{$msgResetPasswordIsSuccess}</p></div>
{/option:resetPasswordSuccess}

{* Error *}
{option:resetPasswordHasError}
	<div class="alert alert-error"><p>{$errFormError}</p></div>
{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section id="resetPasswordForm">
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
								<input class="btn" type="submit" value="{$lblSave|ucfirst}" />
							</div>
						</div>
					</fieldset>
				{/form:resetPassword}
			</div>
	</section>
{/option:!resetPasswordHideForm}