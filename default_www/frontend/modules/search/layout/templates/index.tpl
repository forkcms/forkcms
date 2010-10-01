{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
*}

{cache:{$cacheName}}
	{form:search}
		<p>
			<label for="q">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtQ} {$txtQError}
		</p>
		<p>
			<input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
		</p>
	{/form:search}
	{option:searchTerm}
		<div id="search" class="index">
			{option:!searchResults}<p>{$msgSearchNoItems}</p>{/option:!searchResults}
			{option:searchResults}
				{iteration:searchResults}
					<div class="article">
						<h2>
							<a href="{$searchResults.full_url}" title="{$searchResults.title}">
								{$searchResults.title}
							</a>
						</h2>
						<div class="content">
							{option:!searchResults.introduction}{$searchResults.text|truncate:200}{/option:!searchResults.introduction}
							{option:searchResults.introduction}{$searchResults.introduction}{/option:searchResults.introduction}
						</div>
					</div>
				{/iteration:searchResults}
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
			{/option:searchResults}
		</div>
	{/option:searchTerm}
{/cache:{$cacheName}}