{*
	variables that are available:
	- {$faqQuestions}: contains all questions inside this category
*}

<div id="faq" class="index">
	{option:faqQuestions}
		{* First display all the questions in group *}
		<div id="questions">
			{iteration:faqQuestions}
				<h4><a href="#question-{$faqQuestions.id}">{$faqQuestions.question}</a></h4>
			{/iteration:faqQuestions}
		</div>

		{* Question - Answer *}
		{iteration:faqQuestions}
			<div class="question">
				<h4><a name="question-{$faqQuestions.id}">{$faqQuestions.question}</a></h4>
				{$faqQuestions.answer}
			</div>
		{/iteration:faqQuestions}
	{/option:faqQuestions}

	{option:!faqQuestions}
		<p>{$msgNoQuestionsInCategory|ucfirst}</p>
	{/option:!faqQuestions}
</div>