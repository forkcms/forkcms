{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}

<div id="faq" class="mod">
	<div class="inner">
		<div class="bd">
			{iteration:faqCategories}
				<h3>{$faqCategories.name}</h3>
				<div id="questions">
					{iteration:faqCategories.questions}
						<h4><a href="#question-{$questions.id}">{$questions.question}</a></h4>
					{/iteration:faqCategories.questions}
				</div>

				{iteration:faqCategories.questions}
					<div class="question">
						<h4><a name="question-{$questions.id}">{$questions.question}</a></h4>
						{$questions.answer}
					</div>
				{/iteration:faqCategories.questions}
			{/iteration:faqCategories}
		</div>
	</div>
</div>