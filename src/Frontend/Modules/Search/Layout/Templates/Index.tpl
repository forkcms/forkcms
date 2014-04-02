{*
	variables that are available:
	- {$searchResults}: contains an array with all items, each element contains data about the item
	- {$searchTerm}: the term that has been searched for
*}

<section id="searchIndex" class="mod">
	<div class="inner">
		<header class="hd">
			<h4>{$lblSearchAgain|ucfirst}</h4>
		</header>
		<div class="bd">
			{form:search}
				<p{option:txtQError} class="errorArea"{/option:txtQError}>
					<label for="q">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtQ} {$txtQError}
				</p>
				<p>
					<input id="submit" class="inputSubmit" type="submit" name="submit" value="{$lblSearch|ucfirst}" />
				</p>
			{/form:search}
		</div>
	</div>
</section>

{* don't remove this container nor replace the id - it'll be used to populate the search results live as you type *}
<div id="searchContainer">
	{include:Modules/Search/Layout/Templates/Results.tpl}
</div>
