{option:resendActivationSuccess}<div class="alert alert-success" role="alert">{$msgResendActivationIsSuccess}</div>{/option:resendActivationSuccess}
{option:resendActivationHasError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:resendActivationHasError}

{option:!resendActivationHideForm}
	<section id="resendActivationForm" class="profiles">
		<div class="bd">
			{form:resendActivation}
			  <label for="email">{$lblEmail|ucfirst} <abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="input-group {option:txtEmailError} has-error{/option:txtEmailError}">
						{$txtEmail}
						<span class="input-group-btn">
  						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
						</span>
				</div>
				{$txtEmailError}
			{/form:resendActivation}
		</div>
	</section>
{/option:!resendActivationHideForm}