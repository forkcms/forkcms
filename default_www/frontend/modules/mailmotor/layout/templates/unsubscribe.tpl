<div id="unsubscribeForm">
	{option:unsubscribeHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:unsubscribeHasFormError}
	{option:unsubscribeHasError}<div class="message error"><p>{$errUnsubscribeFailed}</p></div>{/option:unsubscribeHasError}
	{option:unsubscribeIsSuccess}<div class="message success"><p>{$msgUnsubscribeSuccess}</p></div>{/option:unsubscribeIsSuccess}

	{option:!unsubscribeHideForm}
		{form:unsubscribe}
			<div class="horizontal">
				<div class="options">
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
				</div>
				<div class="options">
					<p>
						<input id="send" class="inputButton button mainButton" type="submit" name="send" value="{$lblSend|ucfirst}" />
					</p>
				</div>
			</div>
		{/form:unsubscribe}
	{/option:!unsubscribeHideForm}
</div>