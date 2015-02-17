{* Success *}
{option:resendActivationSuccess}
	<div class="alert alert-success"><p>{$msgResendActivationIsSuccess}</p></div>
{/option:resendActivationSuccess}

{* Error *}
{option:resendActivationHasError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:resendActivationHasError}

{option:!resendActivationHideForm}
	<section id="resendActivationForm">
		{form:resendActivation}
			<fieldset>
				<p {option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
					<label for="email">{$lblEmail|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
				</p>
			</fieldset>
		{/form:resendActivation}
	</section>
{/option:!resendActivationHideForm}