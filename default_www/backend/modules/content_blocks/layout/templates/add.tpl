{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblContentBlocks|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<p>
		{$txtTitle} {$txtTitleError}
	</p>

	<div class="box">
		<div class="heading">
			<h3>{$lblContent|ucfirst}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p style="position: relative;">
					{$txtText} {$txtTextError}
				</p>
				<p><label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite}</label></p>
			</fieldset>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}