{* Success *}
{option:registerIsSuccess}
	<div class="alert-box success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Error *}
{option:registerHasFormError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section>
			<fieldset>
				<p{option:txtDisplayNameError} class="form-error"{/option:txtDisplayNameError}>
					<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtDisplayName}{$txtDisplayNameError}
				</p>
				<p{option:txtEmailError} class="form-error"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail}{$txtEmailError}
				</p>
				<p{option:txtPasswordError} class="form-error"{/option:txtPasswordError}>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtPassword}{$txtPasswordError}
				</p>
				<p>
					<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst} </label>
				</p>
				<p>
					<input type="submit" value="{$lblRegister|ucfirst}" />
				</p>
			</fieldset>
		</section>
	{/form:register}
{/option:!registerHideForm}