{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:import}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfile|ucfirst}</h3>
		</div>
		<div class="options">
			<fieldset class="horizontal">
				<p>
					<label for="group">{$lblGroup|ucfirst}</label>
					{$ddmGroup} {$ddmGroupError}
				</p>
			</fieldset>
			<div class="horizontal">
				<p>
					<label for="file">{$lblFile|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$fileFile} {$fileFileError}
				</p>
			</div>
			<p>
				<label>{$chkOverwriteExisting} {$msgOverwriteExisting}</label>
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>

{/form:import}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
