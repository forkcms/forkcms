{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSynonyms|ucfirst}</h2>
    {option:showSearchAddSynonym}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add_synonym'}" class="btn btn-default" title="{$lblAddSynonym|ucfirst}">
          <span class="glyphicon glyphicon-plus"></span>
          {$lblAddSynonym|ucfirst}
        </a>
      </div>
    </div>
    {/option:showSearchAddSynonym}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    {$dataGrid}
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoSynonyms|sprintf:{$var|geturl:'add_synonym'}}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
