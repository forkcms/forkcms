<section>
	{option:unsubscribeHasFormError}<div class="alert-box error"><p>{$errFormError}</p></div>{/option:unsubscribeHasFormError}
	{option:unsubscribeHasError}<div class="alert-box error"><p>{$errUnsubscribeFailed}</p></div>{/option:unsubscribeHasError}
	{option:unsubscribeIsSuccess}<div class="alert-box success"><p>{$msgUnsubscribeSuccess}</p></div>{/option:unsubscribeIsSuccess}

	{option:!unsubscribeHideForm}
		{form:unsubscribe}
			<p{option:txtEmailError} class="error-area"{/option:txtEmailError}>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail} {$txtEmailError}
			</p>
			<p>
				<input id="send" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</p>
		{/form:unsubscribe}
	{/option:!unsubscribeHideForm}
</section>
