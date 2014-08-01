{*
	variables that are available:
	- {$questions}: contains all questions inside this category
*}

<section id="faqCategory" class="faq">
	<header role="banner">
		<h1>{$category.title}</h1>
	</header>
		{option:questions}
			<ul>
				{iteration:questions}
					<li><a href="{$questions.full_url}">{$questions.question}</a></li>
				{/iteration:questions}
			</ul>
		{/option:questions}

		{option:!questions}
			<p>{$msgNoQuestionsInCategory|ucfirst}</p>
		{/option:!questions}

		<ul class="pager">
			<li	class="previous">
				<a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">&larr; {$lblToFaqOverview|ucfirst}</a>
			</li>
		</ul>
</section>