	{*
	variables that are available:
	- {$widgetFaqOwnQuestion}: true or false depending on if the data can be shown
*}

{option:widgetFaqOwnQuestion}
<section>
	<header>
		<h2 id="{$actOwnQuestion}">{$msgAskOwnQuestion|ucfirst}</h2>
	</header>
	{option:errorSpam}<div class="alert-box error"><p>{$errOwnQuestionSpam}</p></div>{/option:errorSpam}
	{option:success}<div class="alert-box success"><p>{$msgOwnQuestionSuccess}</p></div>{/option:success}

	{form:own_question}
		<p{option:txtNameError} class="error-area"{/option:txtNameError}>
			<label for="name">{$lblYourName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtName} {$txtNameError}
		</p>
		<p{option:txtEmailError} class="error-area"{/option:txtEmailError}>
			<label for="email">{$lblYourEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtEmail} {$txtEmailError}
		</p>
		<p{option:txtMessageError} class="error-area"{/option:txtMessageError}>
			<label for="message">{$lblYourQuestion|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtMessage} {$txtMessageError}
		</p>
		<p>
			<input type="submit" name="send" value="{$lblSend|ucfirst}" />
		</p>
	{/form:own_question}
</section>
{/option:widgetFaqOwnQuestion}
