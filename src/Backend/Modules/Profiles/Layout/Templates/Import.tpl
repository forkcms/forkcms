{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblProfiles|ucfirst}: {$lblImport}</h2>

	<div class="buttonHolderRight">
		{option:showProfilesIndex}
		<a href="{$var|geturl:'index'}" class="button icon iconBack" title="{$lblCancel|ucfirst}"><span>{$lblCancel|ucfirst}</span></a>
		{/option:showProfilesIndex}
		<a href="{$var|geturl:'export_template'}" class="button icon iconExport" title="{$lblExportTemplate|ucfirst}"><span>{$lblExportTemplate|ucfirst}</span></a>
	</div>
</div>

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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
