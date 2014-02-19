{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}

<section>
	{option:!faqCategories}
		<p>{$msgFaqNoItems}</p>
	{/option:!faqCategories}

	{option:faqCategories}
		{option:allowMultipleCategories}
			<header>
				<h2>{$lblCategories}</h2>
			</header>
			<ul>
			{iteration:faqCategories}
				<li><a href="#{$faqCategories.url}" title="{$faqCategories.title}">{$faqCategories.title}</a></li>
			{/iteration:faqCategories}
			</ul>
		{/option:allowMultipleCategories}
		{iteration:faqCategories}
			<section>
				{option:allowMultipleCategories}
					<header>
						<h3 id="{$faqCategories.url}"><a href="{$faqCategories.full_url}" title="{$faqCategories.title}">{$faqCategories.title}</a></h3>
					</header>
				{/option:allowMultipleCategories}
				<ul>
					{iteration:faqCategories.questions}
					<li><a href="{$faqCategories.questions.full_url}">{$faqCategories.questions.question}</a></li>
					{/iteration:faqCategories.questions}
				</ul>
			</section>
		{/iteration:faqCategories}
	{/option:faqCategories}
</section>
