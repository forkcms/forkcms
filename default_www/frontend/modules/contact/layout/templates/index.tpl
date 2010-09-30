<div id="contactForm">
	{option:contactHasFormError}<div class="formMessage errorMessage"><p>{$errFormError}</p></div>{/option:contactHasFormError}
	{option:contactHasError}<div class="formMessage errorMessage"><p>{$errContactErrorWhileSending}</p></div>{/option:contactHasError}
	{option:contactIsSuccess}<div class="formMessage successMessage"><p>{$msgContactMessageSent}</p></div>{/option:contactIsSuccess}

	{option:!contactHideForm}
	{form:contact}
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="author">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtAuthor} {$txtAuthorError}
				</p>
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
			</div>
			<div class="options">
				<p>
					<label for="message">{$lblMessage|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtMessage} {$txtMessageError}
				</p>
			</div>
			<div class="spacing">
				<p>
					<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSend|ucfirst}" />
				</p>
			</div>
		</div>
	{/form:contact}
	{/option:!contactHideForm}
</div>