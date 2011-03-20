<section id="contactFormIndex" class="mod">
	<div class="inner">
		<div class="bd">
			{option:contactHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:contactHasFormError}
			{option:contactHasError}<div class="message error"><p>{$errContactErrorWhileSending}</p></div>{/option:contactHasError}
			{option:contactIsSuccess}<div class="message success"><p>{$msgContactMessageSent}</p></div>{/option:contactIsSuccess}

			{option:!contactHideForm}
				{form:contact}
					<p{option:txtAuthorError} class="errorArea"{/option:txtAuthorError}>
						<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtAuthor} {$txtAuthorError}
					</p>
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p{option:txtMessageError} class="errorArea"{/option:txtMessageError}>
						<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtMessage} {$txtMessageError}
					</p>
					<p>
						<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSend|ucfirst}" />
					</p>
				{/form:contact}
			{/option:!contactHideForm}
		</div>
	</div>
</section>