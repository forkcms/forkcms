{*
	variables that are available:
	- {$widgetFaqOwnQuestion}: true or false depending on if the data can be shown
*}

{option:widgetFaqOwnQuestion}
	<section id="faqOwnQuestionForm" class="well faq">
		<header role="banner">
			<h3 id="{$actOwnQuestion}">{$msgAskOwnQuestion|ucfirst}</h3>
		</header>
		{option:ownQuestionerrorSpam}<div class="alert alert-error" role="alert">{$errOwnQuestionSpam}</div>{/option:ownQuestionerrorSpam}
		{option:ownQuestionsuccess}<div class="alert alert-success" role="alert">{$msgOwnQuestionSuccess}</div>{/option:ownQuestionsuccess}

		{form:own_question}
			<div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
				<label class="control-label" for="name">{$lblYourName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtName} {$txtNameError}
			</div>
			<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
				<label class="control-label" for="email">{$lblYourEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail} {$txtEmailError}
			</div>
			<div class="bigInput form-group{option:txtMessageError} has-error{/option:txtMessageError}">
				<label class="control-label" for="message">{$lblYourQuestion|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMessage} {$txtMessageError}
			</div>
			<input class="btn btn-default" type="submit" name="send" value="{$lblSend|ucfirst}" />
		{/form:own_question}
	</section>
{/option:widgetFaqOwnQuestion}