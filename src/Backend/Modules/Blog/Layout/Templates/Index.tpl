{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>
      {option:!filterCategory}
      {$lblArticles|ucfirst}
      {/option:!filterCategory}
      {option:filterCategory}
      {$msgArticlesFor|sprintf:{$filterCategory.title}|ucfirst}
      {/option:filterCategory}
    </h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showBlogAdd}
        {option:filterCategory}
        <a href="{$var|geturl:'add':null:'&category={$filterCategory.id}'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
        {/option:filterCategory}
        {option:!filterCategory}
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
        {/option:!filterCategory}
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAdd|ucfirst}
        </a>
        {/option:showBlogAdd}
      </div>
    </div>
  </div>
</div>
{form:filter}
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-4">
          <div class="form-group{option:ddmCategoryError} has-error{/option:ddmCategoryError}">
            <label for="category">{$msgShowOnlyItemsInCategory}</label>
            {$ddmCategory} {$ddmCategoryError}
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <div class="btn-toolbar">
        <div class="btn-group pull-right">
          <button id="search" type="submit" class="btn btn-primary" name="search">
            <span class="fa fa-refresh"></span>&nbsp;
            {$lblUpdateFilter|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:filter}
{option:dgRecent}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default jsDataGridHolder">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblRecentlyEdited|ucfirst}</h3>
      </div>
      {$dgRecent}
    </div>
  </div>
</div>
{/option:dgRecent}
{option:dgDrafts}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default jsDataGridHolder">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblDrafts|ucfirst}</h3>
      </div>
      {$dgDrafts}
    </div>
  </div>
</div>
{/option:dgDrafts}
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dgPosts}
    <div class="panel panel-default jsDataGridHolder">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblPublishedArticles|ucfirst}</h3>
      </div>
      {$dgPosts}
    </div>
    {/option:dgPosts}
    {option:!dgPosts}
    {option:filterCategory}
    <p>{$msgNoItems|sprintf:{$var|geturl:'add':null:'&category={$filterCategory.id}'}}</p>
    {/option:filterCategory}
    {option:!filterCategory}
    <p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>
    {/option:!filterCategory}
    {/option:!dgPosts}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
