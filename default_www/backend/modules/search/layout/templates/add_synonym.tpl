{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:addItem}
	<div class="box">
		<div class="heading">
			<h3>{$lblSearch|ucfirst}: {$lblAddSynonym}</h3>
		</div>
		<div class="options">
			<label for="term">{$lblTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtTerm} {$txtTermError}
		</div>
		<div class="options">
			<label for="synonym">{$lblSynonyms|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtSynonym} {$txtSynonymError}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddSynonym|ucfirst}" />
		</div>
	</div>
{/form:addItem}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}