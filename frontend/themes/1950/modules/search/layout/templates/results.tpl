{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}

{option:searchTerm}
	<section id="searchResults" class="mod">
		<div class="inner">
			{option:!searchResults}
				<div class="bd content">
					<p>{$msgSearchNoItems}</p>
				</div>
			{/option:!searchResults}
			{option:searchResults}
				{iteration:searchResults}
					<div class="bd">
						<section class="mod">
							<div class="inner">
								<header class="hd">
									<h3>
										<a href="{$searchResults.full_url|htmlentities}" title="{$searchResults.title}">
											{$searchResults.title}
										</a>
									</h3>
									<a href="{$searchResults.full_url|htmlentities}" class="url">{$SITE_URL}{$searchResults.full_url|truncate:50}</a>
								</header>
								<div class="bd content">
									<a href="{$searchResults.full_url|htmlentities}">{option:!searchResults.introduction}{$searchResults.text|truncate:200}{/option:!searchResults.introduction}
									{option:searchResults.introduction}{$searchResults.introduction|truncate:200}{/option:searchResults.introduction}</a>
								</div>
							</div>
						</section>
					</div>
				{/iteration:searchResults}
			{/option:searchResults}
		</div>
	</section>
	{include:core/layout/templates/pagination.tpl}
{/option:searchTerm}