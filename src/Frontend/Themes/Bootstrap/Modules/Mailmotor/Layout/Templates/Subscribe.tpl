<div id="mailmotorSubscribe">
	{option:subscribeHasFormError}<div class="alert alert-danger" role="alert">{$errFormError}</div>{/option:subscribeHasFormError}
	{option:subscribeHasError}<div class="alert alert-danger" role="alert">{$errSubscribeFailed}</div>{/option:subscribeHasError}
	{option:subscribeIsSuccess}<div class="alert alert-success" role="alert">{$msgSubscribeSuccess}</div>{/option:subscribeIsSuccess}

	{option:!subscribeHideForm}
		{form:subscribe}
		  <label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			<div class="input-group{option:txtEmailError} error{/option:txtEmailError}">
				{$txtEmail} {$txtEmailError}
				<div class="input-group-btn">
  				<input class="btn-primary btn" type="submit" name="send" value="{$lblSend|ucfirst}" />
  			</div>
			</div>
		{/form:subscribe}
	{/option:!subscribeHideForm}
</div>
