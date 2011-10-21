{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="box">
	<div class="heading">
		<h3>{$lblExtensions|ucfirst}: {$lblUploadModule}</h3>
	</div>
	{option:!extensionIsMissing}
		{form:upload}
			<div class="options">
				<div class="horizontal">
					<p>
						<label for="file">{$lblFile|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$fileFile} {$fileFileError}
					</p>
				</div>
			</div>

			<div class="fullwidthOptions">
				<div class="buttonHolderRight">
					<input id="importButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblInstall|ucfirst}" />
				</div>
			</div>
		{/form:upload}
	{/option:!extensionIsMissing}

	{option:extensionIsMissing}
		<div class="options">
			<p>
				{$msgExtensionIsMissing}
			</p>
		</div>
	{/option:extensionIsMissing}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}