{option:registerIsSuccess}<div class="alert alert-success" role="alert">{$msgRegisterIsSuccess}</div>{/option:registerIsSuccess}
{option:registerHasFormError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm" class="profiles">
			<div class="bd">
				<fieldset class="form-horizontal">
					<div class="form-group{option:txtDisplayNameError} has-error{/option:txtDisplayNameError}">
						<label class="control-label col-sm-2" for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="col-sm-6">
							{$txtDisplayName}{$txtDisplayNameError}
						</div>
					</div>
					<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
						<label class="control-label col-sm-2" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="col-sm-6">
							{$txtEmail}{$txtEmailError}
						</div>
					</div>
					<div class="form-group{option:txtPasswordError} has-error{option:txtPasswordError}">
						<label class="control-label col-sm-2" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="col-sm-6">
							{$txtPassword}{$txtPasswordError}
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-6">
							<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst} </label>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-6">
							<input class="btn btn-primary" type="submit" value="{$lblRegister|ucfirst}" />
						</div>
					</div>
				</fieldset>
			</div>
		</section>
	{/form:register}
{/option:!registerHideForm}