{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblGroups|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    {option:showProfilesAddGroup}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add_group'}" class="btn btn-default" title="{$lblAddGroup|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAddGroup|ucfirst}
        </a>
      </div>
    </div>
    {/option:showProfilesAddGroup}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {form:filter}
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="name">{$lblName|ucfirst}</label>
              {$txtName} {$txtNameError}
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
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dgGroups}
    <form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
      <div>
        <input type="hidden" name="offset" value="{$offset}" />
        <input type="hidden" name="order" value="{$order}" />
        <input type="hidden" name="sort" value="{$sort}" />
      </div>
      {$dgGroups}
    </form>
    {/option:dgGroups}
    {option:!dgGroups}
    <p>{$msgNoItems}</p>
    {/option:!dgGroups}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
