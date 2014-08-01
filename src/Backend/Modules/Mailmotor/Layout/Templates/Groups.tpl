{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}</h2>

	{option:showMailmotorAddGroup}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_group'}" class="button icon iconAdd" title="{$lblAddGroup|ucfirst}">
			<span>{$lblAddGroup|ucfirst}</span>
		</a>
	</div>
	{/option:showMailmotorAddGroup}
</div>

{option:noDefaultsSet}
<div class="generalMessage infoMessage content">
	<p><strong>{$msgNoDefaultsSetTitle}</strong></p>
	<p>{$msgNoDefaultsSet}</p>
</div>
{/option:noDefaultsSet}

{option:dataGrid}
<form action="{$var|geturl:'mass_group_action'}" method="get" class="forkForms submitWithLink" id="groups">
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
</form>
{/option:dataGrid}
{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
