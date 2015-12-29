{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
{form:themes}
	<div class="row fork-module-heading">
		<div class="col-md-12">
			<h2 class="form-inline">{$lblTemplates|ucfirst} {$lblFor} {$ddmTheme}</h2>
			{option:showExtensionsAddThemeTemplate}
			<div class="btn-toolbar pull-right">
				<div class="btn-group" role="group">
					<a href="{$var|geturl:'add_theme_template'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="btn btn-primary">
						<span class="glyphicon glyphicon-plus"></span>&nbsp;
						<span>{$lblAddTemplate|ucfirst}</span>
					</a>
					<a href="{$var|geturl:'export_theme_templates'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="btn btn-default">
						<span class="glyphicon glyphicon-export"></span>&nbsp;
						<span>{$lblExport|ucfirst}</span>
					</a>
				</div>
			</div>
			{/option:showExtensionsAddThemeTemplate}
		</div>
	</div>
	<div class="row fork-module-content">
		<div class="col-md-12">
			{option:dataGrid}
			{$dataGrid}
			{/option:dataGrid}
			{option:!dataGrid}
			<p>{$msgNoItems}</p>
			{/option:!dataGrid}
		</div>
	</div>
{/form:themes}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
