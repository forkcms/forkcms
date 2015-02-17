{*
	variables that are available:
	- {$widgetFaqCategoryList}: contains an array with all posts for the category, each element contains data about the post
*}

{option:widgetFaqCategoryList}
	<section id="faqCategoryListWidget" class="mod">
		<div class="panel-group" id="accordion">
			<header>
				<h3 id="{$faqCategories.url}">{$widgetFaqCategory.title}</h3>
			</header>
				
			{iteration:widgetFaqCategoryList}
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse{$widgetFaqCategoryList.id}">{$widgetFaqCategoryList.question}</a>
						</h4>
					</div>
					<div id="collapse{$widgetFaqCategoryList.id}" class="panel-collapse collapse">
						<div class="panel-body">
							{$widgetFaqCategoryList.answer}
						</div>
					</div>
				</div>
			{/iteration:widgetFaqCategoryList}
		</div>
	</section>
{/option:widgetFaqCategoryList}