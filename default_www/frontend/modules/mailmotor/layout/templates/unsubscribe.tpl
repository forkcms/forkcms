<section id="unsubscribeIndex" class="mod">
	<div class="inner">
		<div class="bd">
			{option:unsubscribeHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:unsubscribeHasFormError}
			{option:unsubscribeHasError}<div class="message error"><p>{$errUnsubscribeFailed}</p></div>{/option:unsubscribeHasError}
			{option:unsubscribeIsSuccess}<div class="message success"><p>{$msgUnsubscribeSuccess}</p></div>{/option:unsubscribeIsSuccess}

			{option:!unsubscribeHideForm}
				{form:unsubscribe}
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSend|ucfirst}" />
					</p>
				{/form:unsubscribe}
			{/option:!unsubscribeHideForm}
		</div>
	</div>
</section>