{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-header">
	<div class="col-md-12">
		<h2>{$lblAddSynonym|ucfirst}</h2>
	</div>
</div>
{form:addItem}
	<div class="row fork-module-content">
		<div class="col-md-12">
			<div class="form-group">
				<label for="term">
					{$lblTerm|ucfirst}
					<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr>
				</label>
				{$txtTerm} {$txtTermError}
			</div>
			<div class="form-group">
				<div class="fakeP">
					<label for="addValue-synonym">
						{$lblSynonyms|ucfirst}
						<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr>
					</label>
					<div class="itemAdder">
						{$txtSynonym} {$txtSynonymError}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row fork-page-actions">
		<div class="col-md-12">
			<div class="btn-toolbar">
				<div class="btn-group pull-right" role="group">
					<button id="addButton" type="submit" name="add" class="btn btn-primary">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;{$lblAddSynonym|ucfirst}
					</button>
				</div>
			</div>
		</div>
	</div>
{/form:addItem}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
