{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblCustomFields|ucfirst} {$lblFor} {$lblGroup} &ldquo;{$group.name}&rdquo; <abbr class="help">?</abbr> <span class="tooltip" style="display: none;">{$msgHelpCustomFields}</span></h2>

	{option:showMailmotorAddCustomField}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_custom_field'}&amp;group_id={$group.id}" class="button icon iconAdd" title="{$lblAddCustomField|ucfirst}">
			<span>{$lblAddCustomField|ucfirst}</span>
		</a>
	</div>
	{/option:showMailmotorAddCustomField}
</div>

<form action="{$var|geturl:'mass_custom_field_action'}" method="get" class="forkForms submitWithLink" id="massCustomFieldAction">
	<fieldset>
		<input type="hidden" name="group_id" value="{$group.id}" />
		{option:dataGrid}
			<div class="dataGridHolder">
				{$dataGrid}
			</div>
		{/option:dataGrid}
		{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}
	</fieldset>
</form>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}