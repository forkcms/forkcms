{*
	variables that are available:
	- {$questions}: contains all questions inside this category
*}

<section id="faqCategory" class="faq">
	<header>
		<h1>{$category.title}</h1>
	</header>
	<div class="bd">
		{option:questions}
			<section>
				<div class="bd">
					<ul>
						{iteration:questions}
							<li><a href="{$questions.full_url}">{$questions.question}</a></li>
						{/iteration:questions}
					</ul>
				</div>
			</section>
		{/option:questions}

		{option:!questions}
			<p>{$msgNoQuestionsInCategory|ucfirst}</p>
		{/option:!questions}

		<ul class="pager">
			<li	class="previous">
				<a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">&larr; {$lblToFaqOverview|ucfirst}</a>
			</li>
		</ul>
	</div>
</section>