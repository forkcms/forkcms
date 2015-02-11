{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row">
	<div class="col-md-12">
		<h2>{$lblUploadTheme|ucfirst}</h2>
	</div>
</div>
{option:zlibIsMissing}
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger" role="alert">
			{$msgZlibIsMissing}
		</div>
	</div>
</div>
{/option:zlibIsMissing}
{option:notWritable}
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger" role="alert">
			{$msgThemesNotWritable}
		</div>
	</div>
</div>
{/option:notWritable}
{option:!zlibIsMissing}
{form:upload}
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-group">
						<label for="file">
							{$lblFile|ucfirst}&nbsp;
							<abbr class="glyphicon glyphicon-info-sign" title="{$lblRequiredField}"></abbr>
						</label>
						{$fileFile} {$fileFileError}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="btn-toolbar">
				<div class="btn-group pull-right" role="group">
					<button id="importButton" type="submit" name="add" class="btn btn-primary">{$lblInstall|ucfirst}</button>
				</div>
			</div>
		</div>
	</div>
{/form:upload}
{/option:!zlibIsMissing}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
