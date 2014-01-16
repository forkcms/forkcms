{* Success *}
{option:resendActivationSuccess}
	<div class="alert-box success"><p>{$msgResendActivationIsSuccess}</p></div>
{/option:resendActivationSuccess}

{* Error *}
{option:resendActivationHasError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:resendActivationHasError}

{option:!resendActivationHideForm}
	<section>
		{form:resendActivation}
			<fieldset>
				<p {option:txtEmailError} class="form-error"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<input type="submit" value="{$lblSave|ucfirst}" />
				</p>
			</fieldset>
		{/form:resendActivation}
	</section>
{/option:!resendActivationHideForm}