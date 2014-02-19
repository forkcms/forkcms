{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}

<section>
	<header>
		<h1>{$lblSearch|ucfirst}</h1>
	</header>
	{form:search}
		<p{option:txtQError} class="errorArea"{/option:txtQError}>
			<label for="q">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtQ} {$txtQError}
		</p>
		<p>
			<input id="submit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
		</p>
	{/form:search}
</section>

{* don't remove this container nor replace the id - it'll be used to populate the search results live as you type *}
<div id="searchContainer">
	{include:modules/search/layout/templates/results.tpl}
</div>
