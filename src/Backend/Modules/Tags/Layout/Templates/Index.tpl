{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-header">
	<div class="col-md-12">
		<h2>{$lblTags|ucfirst}</h2>
	</div>
</div>
<div class="row fork-module-content">
	<div class="col-md-12">
		{option:dataGrid}
		<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="tagsForm">
			{$dataGrid}
			<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<span class="modal-title h4">{$lblDelete|ucfirst}</span>
						</div>
						<div class="modal-body">
							<p>{$msgConfirmMassDelete|sprintf:{$title}}</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
							<button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
						</div>
					</div>
				</div>
			</div>
		</form>
		{/option:dataGrid}
		{option:!dataGrid}
		<p>{$msgNoItems}</p>
		{/option:!dataGrid}
	</div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
