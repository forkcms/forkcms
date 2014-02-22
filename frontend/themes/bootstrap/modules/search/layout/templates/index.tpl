{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}
<section id="searchIndex" class="search" role="search">
	{form:search}
	  <label for="q">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr>&nbsp;</label>
		<div class="input-group well{option:txtQError} error{/option:txtQError}">
			{$txtQ}
			<span class="input-group-btn">
			  <input id="submit" class="btn btn-primary" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
			</span>
		</div>
		{$txtQError}
	{/form:search}
</section>

{* don't remove this container nor replace the id - it'll be used to populate the search results live as you type *}
<div id="searchContainer">
	{include:modules/search/layout/templates/results.tpl}
</div>