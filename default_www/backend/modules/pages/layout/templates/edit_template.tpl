{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="box horizontal labelWidthLong">
		<div class="heading">
			<h3>{$lblTemplates|ucfirst}: {$lblEditTemplate}</h3>
		</div>

		<div class="options">
			<p>
				<label for="file">{$msgPathToTemplate|ucfirst}</label>
				{$ddmTheme}<small><code>/core/templates/</code></small>{$txtFile} {$ddmThemeError} {$txtFileError}
				<span class="helpTxt">{$msgHelpTemplateLocation}</span>
			</p>
			<p>
				<label for="label">{$lblLabel|ucfirst}</label>
				{$txtLabel} {$txtLabelError}
			</p>
		</div>

		{* Don't change this ID *}
		<div id="positionsList" class="options">
			{iteration:positions}
				<div class="position clearfix" style="display: none">
					<label for="position{$positions.i}">{$lblPosition|ucfirst}</label>

					{* Position name *}
					{$positions.txtPosition}

					{* Button to delete this position *}
					<a href="#" class="deletePosition button icon iconOnly iconDelete"><span>{$lblDeletePosition|ucfirst}</span></a>

					{$positions.txtPositionError}

					<div class="defaultBlocks">
						{* Default blocks for this position *}
						{iteration:positions.blocks}
							<div class="defaultBlock">
								{$positions.blocks.ddmType}
								{$positions.blocks.ddmTypeError}

								{* @todo: button to remove block from this position *}
								<a href="#" class="deleteBlock button icon iconOnly iconDelete"><span>{$lblDeleteBlock|ucfirst}</span></a>
							</div>
						{/iteration:positions.blocks}

						{* Button to add new default block to this position *}
						<a href="#" class="addBlock button icon iconOnly iconAdd"><span>{$lblAddBlock|ucfirst}</span></a>
					</div>
				</div>
			{/iteration:positions}

			{* Button to add new position *}
			<p>
				<a href="#" id="addPosition" class="button icon iconAdd"><span>{$lblAddPosition|ucfirst}</span></a>
			</p>

			{option:formErrors}<span class="formError">{$formErrors}</span>{/option:formErrors}
		</div>

		<div class="options">
			<p>
				<label for="format">{$lblLayout|ucfirst}</label>
				{$txtFormat} {$txtFormatError}
				<span class="helpTxt">{$msgHelpTemplateFormat}</span>
			</p>
		</div>

		<div class="options">
			<div class="spacing">
				<ul class="inputList pb0">
					<li><label for="active">{$chkActive} {$lblActive|ucfirst}</label> {$chkActiveError}</li>
					<li><label for="default">{$chkDefault} {$lblDefault|ucfirst}</label> {$chkDefaultError}</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:deleteAllowed}
			<a href="{$var|geturl:'delete_template'}&amp;id={$template.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
		{/option:deleteAllowed}
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>{$msgConfirmDeleteTemplate|sprintf:{$template.label}}</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}