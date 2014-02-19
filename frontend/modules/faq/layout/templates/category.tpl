{*
	variables that are available:
	- {$questions}: contains all questions inside this category
*}

<section>
	<header>
		<h1>{$category.title}</h1>
	</header>

	{option:!questions}
		<p>{$msgNoQuestionsInCategory|ucfirst}</p>
	{/option:!questions}

	{option:questions}
		<ul>
			{iteration:questions}
				<li><a href="{$questions.full_url}">{$questions.question}</a></li>
			{/iteration:questions}
		</ul>
	{/option:questions}

	<p><a href="{$var|geturlforblock:'faq'}" title="{$lblToFaqOverview|ucfirst}">{$lblToFaqOverview|ucfirst}</a></p>
</section>
