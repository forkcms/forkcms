{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add}
	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblTemplates|ucfirst}: {$lblAddTemplate}</h3>
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
			<p>
				<label for="numBlocks">{$lblNumberOfBlocks|ucfirst}</label>
				{$ddmNumBlocks} {$ddmNumBlocksError}
			</p>
		</div>
		{* Don't change this ID *}
		<div id="metaData" class="options">
			{iteration:names}
				<p>
					<label for="name{$names.i}">{$lblName|ucfirst} {$names.i}</label>
					{$names.txtName} {$names.ddmType}
					{$names.txtNameError} {$names.ddmTypeError}
				</p>
			{/iteration:names}
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
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}