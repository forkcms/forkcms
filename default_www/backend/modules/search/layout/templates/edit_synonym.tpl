{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblSearch|ucfirst}: {$lblEditSynonym}</h2>
</div>

{form:editItem}
	<div id="tabContent">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td id="leftColumn">
					<div class="box">
						<div class="heading">
							<h3>{$lblSearchTerm|ucfirst}</h3>
						</div>
						<div class="options">
							<label for="term">{$lblSearchTerm|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtTerm} {$txtTermError}
						</div>
					</div>
				</td>

				<td id="sidebar">
					<div id="synonymBox" class="box">
						<div class="heading">
							<h4>{$lblSynonyms|ucfirst}</h4>
						</div>

						<div class="options">
							{$txtSynonym} {$txtSynonymError}
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div class="fullwidthOptions">
		<a href="{$var|geturl:'delete_synonym'}&amp;id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDeleteSynonym|sprintf:{$term}}
			</p>
		</div>
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editItem}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}