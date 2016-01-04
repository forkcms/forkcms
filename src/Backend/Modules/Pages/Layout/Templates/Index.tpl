{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureStart.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>
      {option:dgDrafts}{$lblDrafts|ucfirst}{/option:dgDrafts}
      {option:!dgDrafts}{$lblRecentlyEdited|ucfirst}{/option:!dgDrafts}
    </h2>
    {option:showPagesAdd}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="glyphicon glyphicon-plus"></span>
          {$lblAdd|ucfirst}
        </a>
      </div>
    </div>
    {/option:showPagesAdd}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dgDrafts}
      {$dgDrafts}
    {/option:dgDrafts}
    {option:dgRecentlyEdited}
      {$dgRecentlyEdited}
    {/option:dgRecentlyEdited}
    {option:!dgRecentlyEdited}
      <p>{$msgNoItems}</p>
    {/option:!dgRecentlyEdited}
  </div>
</div>
{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/StructureEnd.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
