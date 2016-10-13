<section id="mailMotorUnsubscribeIndex" class="mod">
	<div class="inner">
		<div class="hd">
			<h3>{$lblUnsubscribeFromNewsletter|ucfirst}</h3>
		</div>
		<div class="bd">
			{option:mailMotorUnsubscribeHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:mailMotorUnsubscribeHasFormError}
			{option:mailMotorUnsubscribeHasError}<div class="message error"><p>{$errUnsubscribeFailed}</p></div>{/option:mailMotorUnsubscribeHasError}
			{option:mailMotorUnsubscribeIsSuccess}<div class="message success"><p>{$msgUnsubscribeSuccess}</p></div>{/option:mailMotorUnsubscribeIsSuccess}

			{option:!mailMotorUnsubscribeHideForm}
				{form:mailMotorUnsubscribeForm}
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<input id="send" class="inputSubmit btn" type="submit" name="send" value="{$lblUnsubscribe|ucfirst}" />
					</p>
				{/form:mailMotorUnsubscribeForm}
			{/option:!mailMotorUnsubscribeHideForm}
		</div>
	</div>
</section>