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
			<div class="control-group{option:txtNameError} error{/option:txtNameError}">
				<label class="control-label" for="name">{$lblYourName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtName} {$txtNameError}
				</div>
			</div>
			<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
				<label class="control-label" for="email">{$lblYourEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtEmail} {$txtEmailError}
				</div>
			</div>
			<div class="bigInput control-group{option:txtMessageError} error{/option:txtMessageError}">
				<label class="control-label" for="message">{$lblYourQuestion|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				<div class="controls">
					{$txtMessage} {$txtMessageError}
				</div>
			</div>
			<p>
				<input class="btn" type="submit" name="send" value="{$lblSend|ucfirst}" />
			</p>
		{/form:own_question}
	</section>
{/option:widgetFaqOwnQuestion}