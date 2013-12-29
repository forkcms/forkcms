{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:add}
	<div class="box">
		<div class="heading">
			<h3>{$lblTranslations|ucfirst}: {$lblAdd}</h3>
		</div>
		<div class="options">
			<div class="horizontal">
				<p>
					<label for="name">{$lblReferenceCode|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtName} {$txtNameError}
					<span class="helpTxt">{$msgHelpAddName}</span>
				</p>
				<p>
					<label for="value">{$lblTranslation|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					{$txtValue} {$txtValueError}
					<span class="helpTxt">{$msgHelpAddValue}</span>
				</p>
				<p>
					<label for="language">{$lblLanguage|ucfirst}</label>
					{$ddmLanguage} {$ddmLanguageError}
				</p>
				<p>
					<label for="application">{$lblApplication|ucfirst}</label>
					{$ddmApplication} {$ddmApplicationError}
				</p>
				<p>
					<label for="module">{$lblModule|ucfirst}</label>
					{$ddmModule} {$ddmModuleError}
				</p>
				<p>
					<label for="type">{$lblType|ucfirst}</label>
					{$ddmType} {$ddmTypeError}
				</p>
			</div>
		</div>

		<div class="fullwidthOptions">
			<div class="buttonHolderRight">
				<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
			</div>
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
