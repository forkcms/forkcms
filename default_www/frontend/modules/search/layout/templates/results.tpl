{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}

{option:searchTerm}
	<div id="searchResults" class="mod">
		<div class="inner">
			{option:!searchResults}
				<div class="bd content">
					<p>{$msgSearchNoItems}</p>
				</div>
			{/option:!searchResults}
			{option:searchResults}
				{iteration:searchResults}
					<div class="bd">
						<div class="mod result">
							<div class="inner">
								<div class="hd">
									<h3>
										<a href="{$searchResults.full_url}" title="{$searchResults.title}">
											{$searchResults.title}
										</a>
									</h3>
								</div>
								<div class="bd content">
									{option:!searchResults.introduction}{$searchResults.text|truncate:200}{/option:!searchResults.introduction}
									{option:searchResults.introduction}{$searchResults.introduction}{/option:searchResults.introduction}
								</div>
							</div>
						</div>
					</div>
				{/iteration:searchResults}
			{/option:searchResults}
		</div>
	</div>
	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
{/option:searchTerm}