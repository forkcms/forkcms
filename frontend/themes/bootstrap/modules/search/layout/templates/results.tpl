{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}
{option:searchTerm}
	<section id="searchResults">
		{option:!searchResults}
			<div class="alert">
				{$msgSearchNoItems}
			</div>
		{/option:!searchResults}
		{option:searchResults}
			<div class="media">
				{iteration:searchResults}
					<section class="media-body">
						<header>
							<h3 class="media-heading">
								<a href="{$searchResults.full_url}">
									{$searchResults.title}
								</a>
							</h3>
						</header>
						{option:!searchResults.introduction}<div>{$searchResults.text|truncate:200|cleanupplaintext}</div>{/option:!searchResults.introduction}
						{option:searchResults.introduction}<div>{$searchResults.introduction}</div>{/option:searchResults.introduction}
					</section>
				{/iteration:searchResults}
			</div>
		{/option:searchResults}
	</section>

	{include:core/layout/templates/pagination.tpl}
{/option:searchTerm}