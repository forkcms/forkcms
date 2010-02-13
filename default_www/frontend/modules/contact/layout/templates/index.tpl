{*
	variables that are available:
	- {$tags}: contains an array with all tags that are used on the site, each element contains data about the tag
*}

<div id="contactForm">
	{option:contactHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:contactHasFormError}
	{option:contactHasError}<div class="message error"><p>{$errContactErrorWhileSending}</p></div>{/option:contactHasError}
	{option:contactIsSuccess}<div class="message error"><p>{$msgContactMessageSent}</p></div>{/option:contactIsSuccess}

	{option:!contactHideForm}
	{form:contact}
		<div class="horizontal">
			<div class="options">
				<p>
					<label for="author">{$lblName|ucfirst}<abbr title=" {$lblRequired}">*</abbr></label>
					{$txtAuthor} {$txtAuthorError}
				</p>
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title=" {$lblRequired}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
			</div>
			<div class="options">
				<p>
					<label for="message">{$lblMessage|ucfirst}<abbr title=" {$lblRequired}">*</abbr></label>
					{$txtMessage} {$txtMessageError}
				</p>
			</div>
			<div class="options">
				<p>
					<input id="send" class="inputButton button mainButton" type="submit" name="send" value="{$lblSend|ucfirst}" />
				</p>
			</div>
		</div>
	{/form:contact}
	{/option:!contactHideForm}
</div>