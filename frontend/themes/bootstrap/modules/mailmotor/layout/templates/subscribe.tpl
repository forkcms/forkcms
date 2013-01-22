<div id="mailmotorSubscribe">
	{option:subscribeHasFormError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:subscribeHasFormError}
	{option:subscribeHasError}<div class="alert alert-error" role="alert">{$errSubscribeFailed}</div>{/option:subscribeHasError}
	{option:subscribeIsSuccess}<div class="alert alert-success" role="alert">{$msgSubscribeSuccess}</div>{/option:subscribeIsSuccess}

	{option:!subscribeHideForm}
		{form:subscribe}
			<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
				<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtEmail} {$txtEmailError}
				</div>
			</div>

			<div class="form-actions">
				<input class="btn-primary btn" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</div>
		{/form:subscribe}
	{/option:!subscribeHideForm}
</div>