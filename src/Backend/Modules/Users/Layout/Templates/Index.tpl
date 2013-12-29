{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}</h2>

	{option:showUsersAdd}
	<div class="buttonHolderRight">
		<a class="button icon iconAdd" href="{$var|geturl:'add'}"><span>{$lblAdd|ucfirst}</span></a>
	</div>
	{/option:showUsersAdd}
</div>
<div class="dataGridHolder">
	{option:dataGrid}{$dataGrid}{/option:dataGrid}
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
