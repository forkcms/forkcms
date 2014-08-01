{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}
{option:!faqCategories}
	<div id="faqIndex" class="faq">
		<div class="alert" role="alert">
			{$msgFaqNoItems}
		</div>
	</div>
{/option:!faqCategories}

{option:faqCategories}
	<div id="faqIndex" class="faq">
		{iteration:faqCategories}
			<section>
            	{option:allowMultipleCategories}
					<header role="banner">
						<h2 id="{$faqCategories.url}"><a href="{$faqCategories.full_url}" title="{$faqCategories.title}">{$faqCategories.title}</a></h2>
					</header>
                {/option:allowMultipleCategories}
				<ul>
					{iteration:faqCategories.questions}
						<li><a href="{$faqCategories.questions.full_url}">{$faqCategories.questions.question}</a></li>
					{/iteration:faqCategories.questions}
				</ul>
			</section>
		{/iteration:faqCategories}
	</div>
{/option:faqCategories}