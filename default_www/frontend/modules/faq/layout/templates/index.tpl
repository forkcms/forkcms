{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}
<section id="faq" class="mod">
	<div class="inner">
		<div class="bd">
			{iteration:faqCategories}
				<section class="mod">
					<div class="inner">
						<header class="hd">
							<h3>{$faqCategories.name}</h3>
						</header>
						<div class="bd content">
							<ul>
							{iteration:faqCategories.questions}
								<li><a href="#question-{$faqCategories.questions.id}">{$faqCategories.questions.question}</a></li>
							{/iteration:faqCategories.questions}
							</ul>
						</div>
					</div>
				</section>
				{iteration:faqCategories.questions}
					<section class="mod">
						<div class="inner">
							<header class="hd">
								<h4><a name="question-{$faqCategories.questions.id}">{$faqCategories.questions.question}</a></h4>
							</header>
							<div class="bd content">
								{$faqCategories.questions.answer}
							</div>
						</div>
					</section>
				{/iteration:faqCategories.questions}
			{/iteration:faqCategories}
		</div>
	</div>
</section>