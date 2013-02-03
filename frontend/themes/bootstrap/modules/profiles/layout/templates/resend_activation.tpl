{option:resendActivationSuccess}<div class="alert alert-success" role="alert">{$msgResendActivationIsSuccess}</div>{/option:resendActivationSuccess}
{option:resendActivationHasError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:resendActivationHasError}

{option:!resendActivationHideForm}
	<section id="resendActivationForm" class="profiles">
		<div class="bd">
			{form:resendActivation}
				<fieldset class="control-group {option:txtEmailError} error{/option:txtEmailError}">
					<p class="form-inline">
						<label for="email">{$lblEmail|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail}
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
						{$txtEmailError}
					</p>
				</fieldset>
			{/form:resendActivation}
		</div>
	</section>
{/option:!resendActivationHideForm}