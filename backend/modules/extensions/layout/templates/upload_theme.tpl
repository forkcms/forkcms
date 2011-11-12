{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="box">
	<div class="heading">
		<h3>{$lblExtensions|ucfirst}: {$lblUploadTheme}</h3>
	</div>

	{option:zlibIsMissing}
		<div class="options">
			<p>
				{$msgZlibIsMissing}
			</p>
		</div>
	{/option:zlibIsMissing}

	{option:notWritable}
		<div class="options">
			<p>
				{$msgThemesNotWritable}
			</p>
		</div>
	{/option:notWritable}

	{option:!zlibIsMissing}
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
	{/option:!zlibIsMissing}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}