{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}

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

{* don't remove this container nor replace the id - it'll be used to populate the search results live as you type *}
<div id="searchContainer">
	{include:{$FRONTEND_MODULES_PATH}/search/layout/templates/results.tpl}
</div>