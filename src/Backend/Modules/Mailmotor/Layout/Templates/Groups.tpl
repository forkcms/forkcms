{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12">
		<h2>{$lblGroups|ucfirst}</h2>
		<div class="btn-toolbar pull-right">
			<div class="btn-group" role="group">
				{option:showMailmotorAddGroup}
				<a href="{$var|geturl:'add_group'}" class="btn btn-primary" title="{$lblAddGroup|ucfirst}">
					<span class="glyphicon glyphicon-plus"></span>&nbsp;
					{$lblAddGroup|ucfirst}
				</a>
				{/option:showMailmotorAddGroup}
			</div>
		</div>
	</div>
</div>
{option:noDefaultsSet}
<div class="row fork-module-messages">
	<div class="col-md-12">
		<div class="alert alert-warning" role="alert">
			<p><strong>{$msgNoDefaultsSetTitle}</strong></p>
			<p>{$msgNoDefaultsSet}</p>
		</div>
	</div>
</div>
{/option:noDefaultsSet}
<div class="row fork-module-content">
	<div class="col-md-12">
		{option:dataGrid}
		<form action="{$var|geturl:'mass_group_action'}" method="get" class="forkForms submitWithLink" id="groups">
			{$dataGrid}
		</form>
		{/option:dataGrid}
		{option:!dataGrid}
		<p>{$msgNoItems}</p>
		{/option:!dataGrid}
	</div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
