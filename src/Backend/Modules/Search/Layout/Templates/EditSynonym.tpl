{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:editItem}
<div class="box">
	<div class="heading">
		<h3>{$lblSearch|ucfirst}: {$lblAddSynonym}</h3>
	</div>
	<div class="options horizontal">
		<p>
			<label for="term">{$lblTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			{$txtTerm} {$txtTermError}
		</p>
		<div class="fakeP">
			<label for="addValue-synonym">{$lblSynonyms|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			<div class="itemAdder">
				{$txtSynonym} {$txtSynonymError}
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showSearchDeleteSynonym}
		<a href="{$var|geturl:'delete_synonym'}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>{$msgConfirmDeleteSynonym|sprintf:{$term}}</p>
		</div>
		{/option:showSearchDeleteSynonym}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editItem}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
