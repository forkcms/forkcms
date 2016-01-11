{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblProfiles|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAdd|ucfirst}
        </a>
        {option:showProfilesImport}
        <a href="{$var|geturl:'import'}" class="btn btn-default" title="{$lblImport|ucfirst}">
          <span class="fa fa-download"></span>&nbsp;
          {$lblImport|ucfirst}
        </a>
        {/option:showProfilesImport}
      </div>
    </div>
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
                <label for="email">{$lblEmail|ucfirst}</label>
                {$txtEmail} {$txtEmailError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="status">{$lblStatus|ucfirst}</label>
                {$ddmStatus} {$ddmStatusError}
              </div>
            </div>
            <div class="col-md-4">
              {option:ddmGroup}
              <div class="form-group">
                <label for="group">{$lblGroup|ucfirst}</label>
                {$ddmGroup} {$ddmGroupError}
              </div>
              {/option:ddmGroup}
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
    {option:dgProfiles}
    <form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink">
      <div>
        <input type="hidden" name="offset" value="{$offset}" />
        <input type="hidden" name="order" value="{$order}" />
        <input type="hidden" name="sort" value="{$sort}" />
        <input type="hidden" name="email" value="{$email}" />
        <input type="hidden" name="status" value="{$status}" />
        <input type="hidden" name="newGroup" value="" />
      </div>
      {$dgProfiles}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmMassDelete}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="confirmAddToGroup" tabindex="-1" role="dialog" aria-labelledby="{$lblAddToGroup|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblAddToGroup|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmMassAddToGroup}</p>
              <div class="form-group">
                <div class="jsMassActionAddToGroupSelectGroup"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">{$lblCancel|ucfirst}</button>
              <button type="submit" class="btn btn-primary">{$lblOK|ucfirst}</button>
            </div>
          </div>
        </div>
      </div>
    </form>
    {/option:dgProfiles}
    {option:!dgProfiles}
    <p>{$msgNoItems}</p>
    {/option:!dgProfiles}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
