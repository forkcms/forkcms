<div id="mailmotorUnsubscribe">
	{option:unsubscribeHasFormError}<div class="alert alert-danger" role="alert">{$errFormError}</div>{/option:unsubscribeHasFormError}
	{option:unsubscribeHasError}<div class="alert alert-danger" role="alert">{$errUnsubscribeFailed}</div>{/option:unsubscribeHasError}
	{option:unsubscribeIsSuccess}<div class="alert alert-success" role="alert">{$msgUnsubscribeSuccess}</div>{/option:unsubscribeIsSuccess}

	{option:!unsubscribeHideForm}
		{form:unsubscribe}
			<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
				<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtEmail} {$txtEmailError}
				</div>
			</div>

			<div class="form-actions">
				<input class="btn-primary btn" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</div>
		{/form:unsubscribe}
	{/option:!unsubscribeHideForm}
</div>
