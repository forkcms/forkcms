{*
	variables that are available:
	- {$faqCategories}: contains all categories, along with all questions inside a category
*}


{option:!faqCategories}
	<div id="faqIndex">
		<section class="mod">
			<div class="inner">
				<div class="bd content">
					<p>{$msgFaqNoItems}</p>
				</div>
			</div>
		</section>
	</div>
{/option:!faqCategories}

{option:faqCategories}
	<section id="faqIndex" class="mod">
		<div class="inner">
			{option:allowMultipleCategories}
			<div class="hd">
				<ul>
					{iteration:faqCategories}
						<li><a href="#{$faqCategories.url}" title="{$faqCategories.title}">{$faqCategories.title}</a></li>
					{/iteration:faqCategories}
				</ul>
			</div>
			{/option:allowMultipleCategories}
			<div class="bd">
				{iteration:faqCategories}
					<section class="mod">
						<div class="inner">
							{option:allowMultipleCategories}
							<header class="hd">
								<h3 id="{$faqCategories.url}"><a href="{$faqCategories.full_url}" title="{$faqCategories.title}">{$faqCategories.title}</a></h3>
							</header>
							{/option:allowMultipleCategories}

							<div class="bd content">
								<ul>
									{iteration:faqCategories.questions}
										<li><a href="{$faqCategories.questions.full_url}">{$faqCategories.questions.question}</a></li>
									{/iteration:faqCategories.questions}
								</ul>
							</div>
						</div>
					</section>
				{/iteration:faqCategories}
			</div>
		</div>
	</section>
{/option:faqCategories}