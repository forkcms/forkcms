{*
	variables that are available:
	- {$questions}: contains all questions inside this category
*}

<section id="faqCategory" class="mod">
	<div class="inner">
		<header class="hd">
			<h1>{$category.title}</h1>
		</header>
		<div class="bd">
			{option:questions}
				<section class="mod">
					<div class="inner">
						<div class="bd">
							<ul>
								{iteration:questions}
									<li><a href="{$questions.full_url}">{$questions.question}</a></li>
								{/iteration:questions}
							</ul>
						</div>
					</div>
				</section>
			{/option:questions}

			{option:!questions}
				<p>{$msgNoQuestionsInCategory|ucfirst}</p>
			{/option:!questions}

			<p><a href="{$var|geturlforblock:'Faq'}" title="{$lblToFaqOverview|ucfirst}">{$lblToFaqOverview|ucfirst}</a></p>
		</div>
	</div>
</section>
