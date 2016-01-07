{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblFormBuilder|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showFormBuilderAdd}
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAdd|ucfirst}
        </a>
        {/option:showFormBuilderAdd}
      </div>
    </div>
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
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
