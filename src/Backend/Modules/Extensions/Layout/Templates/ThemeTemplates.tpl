{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2 class="form-inline">{$lblTemplates|ucfirst} {$lblFor} {$ddmTheme}</h2>
  </div>
  <div class="col-md-6">
    {option:showExtensionsAddThemeTemplate}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'export_theme_templates'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="btn btn-default">
          <span class="fa fa-upload"></span>&nbsp;
          <span>{$lblExport|ucfirst}</span>
        </a>
        <a href="{$var|geturl:'add_theme_template'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="btn btn-primary">
          <span class="fa fa-plus"></span>&nbsp;
          <span>{$lblAddTemplate|ucfirst}</span>
        </a>
      </div>
    </div>
    {/option:showExtensionsAddThemeTemplate}
  </div>
</div>
{form:themes}
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
	<div class="pageTitle">
		<h2>
			{$lblExtensions|ucfirst}: <label for="theme" class="control-label">{$lblTemplates} {$lblFor}</label> {$ddmTheme}
		</h2>

		{option:showExtensionsAddThemeTemplate}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add_theme_template'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="button icon iconAdd" title="{$lblAddTemplate|ucfirst}">
				<span>{$lblAddTemplate|ucfirst}</span>
			</a>
			<a href="{$var|geturl:'export_theme_templates'}{option:selectedTheme}&amp;theme={$selectedTheme}{/option:selectedTheme}" class="button icon iconExport" title="{$lblExport|ucfirst}">
				<span>{$lblExport|ucfirst}</span>
			</a>
		</div>
		{/option:showExtensionsAddThemeTemplate}
	</div>

	<div class="dataGridHolder">
		{option:dataGrid}{$dataGrid}{/option:dataGrid}
		{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}
	</div>
{/form:themes}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
