{*
	variables that are available:
	- {$widgetFaqCategoryList}: contains an array with all posts for the category, each element contains data about the post
*}

{option:widgetFaqCategoryList}
	<section>
		<header>
			<h2>{$lblFaq}: {$widgetFaqCategory.title}</h2>
		</header>
		<ul>
			{iteration:widgetFaqCategoryList}
				<li><a href="{$widgetFaqCategoryList.full_url}" title="{$widgetFaqCategoryList.question}">{$widgetFaqCategoryList.question}</a></li>
			{/iteration:widgetFaqCategoryList}
		</ul>
	</section>
{/option:widgetFaqCategoryList}
