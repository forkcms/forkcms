{* Success *}
{option:registerIsSuccess}
	<div class="alert alert-success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Error *}
{option:registerHasFormError}
	<div class="alert alert-error"><p>{$errFormError}</p></div>
{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm" class="profiles">
			<div class="bd">
				<fieldset class="form-horizontal">
					<div class="control-group{option:txtDisplayNameError} error{/option:txtDisplayNameError}">
						<label class="control-label" for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="controls">
							{$txtDisplayName}{$txtDisplayNameError}
						</div>
					</div>
					<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
						<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="controls">
							{$txtEmail}{$txtEmailError}
						</div>
					</div>
					<div class="control-group{option:txtPasswordError} error{option:txtPasswordError}">
						<label class="control-label" for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						<div class="controls">
							{$txtPassword}{$txtPasswordError}
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst} </label>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input class="btn btn-primary" type="submit" value="{$lblRegister|ucfirst}" />
						</div>
					</div>
				</fieldset>
			</div>
		</section>
	{/form:register}
{/option:!registerHideForm}