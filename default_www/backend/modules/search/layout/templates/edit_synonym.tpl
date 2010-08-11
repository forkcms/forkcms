{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:editItem}
	<div class="box">
		<div class="heading">
			<h3>{$lblSearch|ucfirst}: {$lblEditSynonym}</h3>
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
		<a href="{$var|geturl:'delete_synonym'}&amp;id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>

		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>{$msgConfirmDeleteSynonym|sprintf:{$term}}</p>
		</div>
	</div>
{/form:editItem}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}