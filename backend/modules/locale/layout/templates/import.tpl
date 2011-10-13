{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}
{form:import}
	<div class="box">
		<div class="heading">
			<h3>{$lblTranslations|ucfirst}: {$lblImport}</h3>
		</div>
		<div class="options">
			<div class="horizontal">
				<p>
					<label for="file">{$lblFile|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$fileFile} {$fileFileError}
				</p>
				<ul class="inputList">
					<li><label for="overwrite">{$chkOverwrite} {$msgOverwriteConflicts}</label></li>
				</ul>
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="importButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblImport|ucfirst}" />
			</div>
		</div>
	</div>
{/form:import}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}