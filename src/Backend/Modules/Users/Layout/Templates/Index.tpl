{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblUsers|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    {option:showUsersAdd}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>
          {$lblAdd|ucfirst}
        </a>
      </div>
    </div>
    {/option:showUsersAdd}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    {$dataGrid}
    {/option:dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
