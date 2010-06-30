{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblPages}</h2>
</div>

{form:settings}
	<div class="box">
		<div class="heading">
			<h3>{$lblHasMetaNavigation|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpMetaNavigation}</p>
			<ul class="inputList p0">
				<li>{$chkHasMetaNavigation} <label for="hasMetaNavigation">{$msgHasMetaNavigation|ucfirst}</label></li>
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:settings}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}