{*
	variables that are available:
	- {$widgetFaqCategories}: contains an array with all posts, each element contains data about the post
*}

{option:widgetFaqCategories}
	<section id="faqCategoriesWidget" class="mod">
		<h3>{$lblAllCategories|ucfirst}</h3>
		<ul>
			{iteration:widgetFaqCategories}
				<li><a href="{$widgetFaqCategories.full_url}" title="{$widgetFaqCategories.title}">{$widgetFaqCategories.title}</a></li>
			{/iteration:widgetFaqCategories}
		</ul>
	</section>
{/option:widgetFaqCategories}