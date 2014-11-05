{* Success *}
{option:registerIsSuccess}
	<div class="message success"><p>{$msgRegisterIsSuccess}</p></div>
{/option:registerIsSuccess}

{* Error *}
{option:registerHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:registerHasFormError}

{option:!registerHideForm}
	{form:register}
		<section id="registerForm" class="mod">
			<div class="inner">
				<div class="bd">
					<fieldset>
						<p{option:txtDisplayNameError} class="errorArea"{/option:txtDisplayNameError}>
							<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtDisplayName}{$txtDisplayNameError}
						</p>
						<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
							<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtEmail}{$txtEmailError}
						</p>
						<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
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
				</div>
			</div>
		</section>
	{/form:register}
{/option:!registerHideForm}