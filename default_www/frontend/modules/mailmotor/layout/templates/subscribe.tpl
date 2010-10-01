<div id="subscribeForm">
	{option:subscribeHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:subscribeHasFormError}
	{option:subscribeHasError}<div class="message error"><p>{$errSubscribeFailed}</p></div>{/option:subscribeHasError}
	{option:subscribeIsSuccess}<div class="message success"><p>{$msgSubscribeSuccess}</p></div>{/option:subscribeIsSuccess}

	{option:!subscribeHideForm}
	{form:subscribe}
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title=" {$lblRequired}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
			</div>
			<div class="options">
				<p>
					<input id="send" class="inputButton button mainButton" type="submit" name="send" value="{$lblSend|ucfirst}" />
				</p>
			</div>
		</div>
	{/form:subscribe}
	{/option:!subscribeHideForm}
</div>