{* Success *}
{option:resetPasswordSuccess}
	<div class="message success"><p>{$msgResetPasswordIsSuccess}</p></div>
{/option:resetPasswordSuccess}

{* Error *}
{option:resetPasswordHasError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:resetPasswordHasError}

{option:!resetPasswordHideForm}
	<section id="resetPasswordForm" class="mod">
		<div class="inner">
			<div class="bd">
				{form:resetPassword}
					<fieldset>
						<p{option:txtPasswordError} class="errorArea"{/option:txtPasswordError}>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}{$txtPasswordError}
						</p>
						<p>
							<label for="showPassword">{$chkShowPassword} {$lblShowPassword|ucfirst}</label>
						</p>
						<p>
							<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
						</p>
					</fieldset>
				{/form:resetPassword}
			</div>
		</div>
	</section>
{/option:!resetPasswordHideForm}