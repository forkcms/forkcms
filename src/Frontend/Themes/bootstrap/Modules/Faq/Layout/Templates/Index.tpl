{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}

{option:!faqCategories}
	<section id="faqIndex">
		<p>{$msgFaqNoItems}</p>
	</section>
{/option:!faqCategories}

{option:faqCategories}
	<section id="faqIndex">
		<div class="panel-group" id="accordion">
			{iteration:faqCategories}
				{option:allowMultipleCategories}
				<header>
					<h3 id="{$faqCategories.url}">{$faqCategories.title}</h3>
				</header>
				{/option:allowMultipleCategories}

				{iteration:faqCategories.questions}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse{$faqCategories.questions.id}">{$faqCategories.questions.question}</a>
						</h4>
					</div>
					<div id="collapse{$faqCategories.questions.id}" class="panel-collapse collapse">
						<div class="panel-body">
							{$faqCategories.questions.answer}
						</div>
					</div>
				</div>
				{/iteration:faqCategories.questions}
			{/iteration:faqCategories}
		</div>
	</section>
{/option:faqCategories}