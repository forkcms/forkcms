{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblTranslations|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    {option:showLocaleExportAnalyse}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'ExportAnalyse'}&amp;language={$language}" class="btn btn-default" title="{$lblExport|ucfirst}">
          <span class="fa fa-upload"></span>
          {$lblExport|ucfirst}
        </a>
      </div>
    </div>
    {/option:showLocaleExportAnalyse}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dgFrontend}
    <h4>{$lblFrontend|ucfirst}</h4>
    {$dgFrontend}
    {/option:dgFrontend}
    {option:!dgFrontend}
    <h4>{$lblFrontend|ucfirst}</h4>
    <p>{$msgNoItemsAnalyse}</p>
    {/option:!dgFrontend}
    {option:dgBackend}
    <h4>{$lblBackend|ucfirst}</h4>
    {$dgBackend}
    {/option:dgBackend}
    {option:!dgBackend}
    <h4>{$lblBackend|ucfirst}</h4>
    <p>{$msgNoItemsAnalyse}</p>
    {/option:!dgBackend}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
