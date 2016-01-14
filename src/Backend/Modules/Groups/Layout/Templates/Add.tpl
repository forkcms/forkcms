{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabName" aria-controls="name" role="tab" data-toggle="tab">{$lblName|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabDashboard" aria-controls="dashboard" role="tab" data-toggle="tab">{$lblDashboard|ucfirst}</a>
          </li>
          <li role="presentation">
            <a href="#tabPermissions" aria-controls="permission" role="tab" data-toggle="tab">{$lblPermissions|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabName">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group{option:txtNameError} has-error{/option:txtNameError}">
                  <label for="name">{$lblName|ucfirst}&nbsp;<abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr></label>
                  {$txtName} {$txtNameError}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabDashboard">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="toggleChecksWidgets">{$lblDisplayWidgets|ucfirst}</label>
                  {option:widgets}
                  {$widgets}
                  {/option:widgets}
                  {option:!widgets}
                  {$msgNoWidgets|ucfirst}
                  {/option:!widgets}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabPermissions">
            <div class="row">
              <div class="col-md-12">
                <div class="panel-group" id="permissions" role="tablist" aria-multiselectable="true">
                  {iteration:permissions}
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="permissions-heading-{$permissions.id}">
                      <h4 class="panel-title">
                        {$permissions.chk}
                        <a data-toggle="collapse" data-parent="#permissions" href="#permissions-list-{$permissions.id}" aria-expanded="false" aria-controls="collapseOne">
                          <label for="{$permissions.id}">{$permissions.label}</label>
                        </a>
                      </h4>
                    </div>
                    <div id="permissions-list-{$permissions.id}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="permissions-heading-{$permissions.id}">
                      {$permissions.actions.dataGrid}
                    </div>
                  </div>
                  {/iteration:permissions}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="pageButtons" class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
