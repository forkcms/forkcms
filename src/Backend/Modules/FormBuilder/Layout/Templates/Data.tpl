{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblFormData|sprintf:{$name}|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showFormBuilderIndex}
        <a href="{$var|geturl:'index'}" class="btn btn-default">
          <span class="fa fa-list"></span>&nbsp;
          {$lblOverview|ucfirst}
        </a>
        {/option:showFormBuilderIndex}
        {option:showFormBuilderExportData}
        <a href="{$var|geturl:'export_data'}&id={$id}&amp;start_date={$start_date}&amp;end_date={$end_date}" class="btn btn-default">
          <span class="fa fa-upload"></span>&nbsp;
          {$lblExport|ucfirst}
        </a>
        {/option:showFormBuilderExportData}
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
                <label for="startDate">{$lblStartDate|ucfirst}</label>
                {$txtStartDate} {$txtStartDateError}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="endDate">{$lblEndDate|ucfirst}</label>
                {$txtEndDate} {$txtEndDateError}
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
    {option:dataGrid}
    <form action="{$var|geturl:'mass_data_action'}" method="get" class="forkForms">
      {$dataGrid}
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
    </form>
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoData}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
