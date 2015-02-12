{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-header">
	<div class="col-md-12">
		<h2>{$lblAdd|ucfirst}</h2>
	</div>
</div>
{form:add}
	<div class="row fork-module-content">
		<div class="col-md-12">
			<div class="form-group">
				<label for="title">{$lblTitle|ucfirst}</label>
				{$txtTitle} {$txtTitleError}
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<label for="text">
							{$lblContent|ucfirst}
							<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr>
						</label>
					</h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						{$txtText} {$txtTextError}
					</div>
					{option:ddmTemplate}
					<div class="form-group">
						<label for="template">{$lblTemplate|ucfirst}</label>
						{$ddmTemplate} {$ddmTemplateError}
					</div>
					{/option:ddmTemplate}
					<div class="form-group">
						<label for="hidden">{$chkHidden} {$chkHiddenError} {$lblVisibleOnSite|ucfirst}</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row fork-module-actions">
		<div class="col-md-12">
			<div class="btn-group pull-right" role="group">
				<button id="addButton" type="submit" name="add" class="btn btn-primary">{$lblAdd|ucfirst}</button>
			</div>
		</div>
	</div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
