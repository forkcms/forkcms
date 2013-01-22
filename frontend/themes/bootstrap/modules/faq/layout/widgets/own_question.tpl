{*
	variables that are available:
	- {$widgetFaqOwnQuestion}: true or false depending on if the data can be shown
*}

{option:widgetFaqOwnQuestion}
	<section id="faqOwnQuestionForm" class="well faq">
		<header>
			<h3 id="{$actOwnQuestion}">{$msgAskOwnQuestion|ucfirst}</h3>
		</header>
		{option:errorSpam}<div class="alert alert-error">{$errOwnQuestionSpam}</div>{/option:errorSpam}
		{option:success}<div class="alert alert-success">{$msgOwnQuestionSuccess}</div>{/option:success}

		{form:own_question}
			<p{option:txtNameError} class="error"{/option:txtNameError}>
				<label for="name">{$lblYourName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtName} {$txtNameError}
			</p>
			<p{option:txtEmailError} class="error"{/option:txtEmailError}>
				<label for="email">{$lblYourEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail} {$txtEmailError}
			</p>
			<p class="bigInput{option:txtMessageError} error{/option:txtMessageError}">
				<label for="message">{$lblYourQuestion|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMessage} {$txtMessageError}
			</p>
			<p>
				<input class="btn" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</p>
		{/form:own_question}
	</section>
{/option:widgetFaqOwnQuestion}