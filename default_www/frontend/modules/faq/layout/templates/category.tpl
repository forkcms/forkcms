{*
	variables that are available:
	- {$faqQuestions}: contains all questions inside this category
*}

<section id="faqCategory" class="mod">
	<div class="inner">
		<div class="bd">
			{option:faqQuestions}
				<section class="mod">
					<div class="inner">
						<div class="bd">
							<ul>
								{iteration:faqQuestions}
									<li><a href="#question-{$faqQuestions.id}">{$faqQuestions.question}</a></li>
								{/iteration:faqQuestions}
							</ul>
						</div>
					</div>
				</section>
				{iteration:faqQuestions}
					<section class="mod">
						<div class="inner">
							<header class="hd">
								<h4><a name="question-{$faqQuestions.id}">{$faqQuestions.question}</a></h4>
							</header>
							<div class="bd content">
								{$faqQuestions.answer}
							</div>
						</div>
					</section>
				{/iteration:faqQuestions}
			{/option:faqQuestions}

			{option:!faqQuestions}
				<p>{$msgNoQuestionsInCategory|ucfirst}</p>
			{/option:!faqQuestions}
		</div>
	</div>
</section>