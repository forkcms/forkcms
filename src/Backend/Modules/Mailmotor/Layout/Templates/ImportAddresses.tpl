{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblImportAddresses|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{form:import}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblAddressList|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:fileCsvError} has-error{/option:fileCsvError}">
            {option:showMailmotorImportAddresses}
            <label for="download" class="control-label">Download <a href="{$var|geturl:'import_addresses'}&amp;example=1" class="control-label">{$lblExampleFile}</a>.</label>
            {/option:showMailmotorImportAddresses}
            {$fileCsv} {$fileCsvError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblGroup|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            {option:chkGroupsError}
              <p class="text-danger">{$chkGroupsError}</p>
            {/option:chkGroupsError}
            <ul class="list-unstyled">
              {iteration:groups}
              <li class="radio">
                <label for="{$groups.id}">{$groups.rbtGroups} {$groups.label|ucfirst}</label>
              </li>
              {/iteration:groups}
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="importButton" type="submit" name="add" class="btn btn-success"><span class="fa fa-download"></span> {$lblImport|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:import}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
