{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblEdit|ucfirst}</h2>
  </div>
</div>
{form:edit}
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
          <li role="presentation">
            <a href="#tabUsers" aria-controls="users" role="tab" data-toggle="tab">{$lblUsers|ucfirst}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="tabName">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblName|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="name">
                    {$lblName|ucfirst}&nbsp;
                    <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                  {$txtName} {$txtNameError}
                </div>
              </div>
            </div>
          </div>
          <div role="tabpanel" class="tab-pane" id="tabDashboard">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblDashboard|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group jsGroupsWidgets">
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
                <h3>{$lblModules|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="panel-group" id="permissions" role="tablist" aria-multiselectable="true">
                  {iteration:permissions}
                  <div class="panel panel-default jsGroupsPermissionsModule">
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
          <div role="tabpanel" class="tab-pane" id="tabUsers">
            <div class="row">
              <div class="col-md-12">
                <h3>{$lblUsers|ucfirst} in {$groupName|ucfirst}</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                {option:dataGridUsers}
                {$dataGridUsers}
                {/option:dataGridUsers}
                {option:!dataGridUsers}
                <p>{$msgNoUsers|ucfirst}</p>
                {/option:!dataGridUsers}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-page-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-left" role="group">
          {option:showGroupsDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>&nbsp;
            {$lblDelete|ucfirst}
          </button>
          {/option:showGroupsDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="editButton" type="submit" name="edit" class="btn btn-primary">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
      {option:showGroupsDelete}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDelete|sprintf:{$item.name}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete'}&amp;id={$item.id}" class="btn btn-primary">
                {$lblOK|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showGroupsDelete}
    </div>
  </div>
{/form:edit}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
