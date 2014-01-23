{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblContentBlocks|ucfirst}: {$lblAdd}</h2>
</div>

{form:contentBlocks}
	<p>
		<label for="title">{$lblTitle|ucfirst}</label>
		{$txtTitle} {$txtTitleError}
	</p>

	<div class="content">
		<fieldset>
			<div class="box">
				<div class="heading">
					<h3>
						<label for="text">{$lblContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					</h3>
				</div>
				<div class="optionsRTE">
					{$txtText} {$txtTextError}
				</div>
			</div>

			{option:ddmTemplate}<p>{$lblTemplate|ucfirst} <label for="template">{$ddmTemplate} {$ddmTemplateError}</label></p>{/option:ddmTemplate}
			<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite|ucfirst}</label></p>
		</fieldset>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:contentBlocks}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
