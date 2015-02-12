{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row">
	<div class="col-md-12">
		<h2>{$msgEditTag|sprintf:{$name}|ucfirst}</h2>
	</div>
</div>
{form:edit}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<p>
						<label for="name">
							{$lblName|ucfirst}
							<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr>
						</label>
						{$txtName} {$txtNameError}
					</p>
				</div>
				<div class="panel-heading">
					<label class="panel-title">{$lblUsedIn|ucfirst}</label>
				</div>
				{option:usage}
				{$usage}
				{/option:usage}
				{option:!usage}
				<div class="panel-body">
					<p>{$msgNoUsage}</p>
				</div>
				{/option:!usage}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="btn-group pull-right" role="group">
				<button id="editButton" type="submit" name="edit" class="btn btn-primary">{$lblSave|ucfirst}</button>
			</div>
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
