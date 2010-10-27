{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
*}

{cache:{$cacheName}}
	<div id="searchIndex" class="mod">
		<div class="inner">
			<div class="hd">
				<h3>{$lblSearchAgain|ucfirst}</h3>
			</div>
			<div class="bd">
				{form:search}
					<p>
						<label for="q">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtQ} {$txtQError}
					</p>
					<p>
						<input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
					</p>
				{/form:search}
			</div>
		</div>
	</div>

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
						<div class="bd content">
							<h3>
								<a href="{$searchResults.full_url}" title="{$searchResults.title}">
									{$searchResults.title}
								</a>
							</h3>
							{option:!searchResults.introduction}{$searchResults.text|truncate:200}{/option:!searchResults.introduction}
							{option:searchResults.introduction}{$searchResults.introduction}{/option:searchResults.introduction}
						</div>
					{/iteration:searchResults}
				{/option:searchResults}
			</div>
		</div>
		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/pagination.tpl'}
	{/option:searchTerm}
{/cache:{$cacheName}}