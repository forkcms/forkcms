{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="box">
	<div class="heading">
		<h3>{$lblExtensions|ucfirst}: {$lblUploadModule}</h3>
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
				{$msgModulesNotWritable}
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
