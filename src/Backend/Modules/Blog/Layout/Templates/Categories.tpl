{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblCategories|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    {option:showBlogAddCategory}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add_category'}" class="btn btn-default" title="{$lblAddCategory|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAddCategory|ucfirst}
        </a>
      </div>
    </div>
    {/option:showBlogAddCategory}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    {$dataGrid}
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoCategoryItems|sprintf:{$var|geturl:'add_category'}}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
