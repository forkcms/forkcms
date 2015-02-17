{* Success *}
{option:registerIsSuccess}
	<div class="alert alert-success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Error *}
{option:registerHasFormError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm">
			<fieldset>
				<p{option:txtDisplayNameError} class="alert alert-danger"{/option:txtDisplayNameError}>
					<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtDisplayName}{$txtDisplayNameError}
				</p>
				<p{option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail}{$txtEmailError}
				</p>
				<p{option:txtPasswordError} class="alert alert-danger"{/option:txtPasswordError}>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtPassword}{$txtPasswordError}
				</p>
				<p>
					<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst} </label>
				</p>
				<p>
					<input class="inputSubmit" type="submit" value="{$lblRegister|ucfirst}" />
				</p>
			</fieldset>
		</section>
	{/form:register}
{/option:!registerHideForm}