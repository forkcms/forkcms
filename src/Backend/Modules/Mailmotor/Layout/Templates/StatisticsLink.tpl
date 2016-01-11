{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$url}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorStatistics}
        <a href="{$var|geturl:'statistics'}&amp;id={$mailing.id}" class="btn btn-default" title="{$msgBackToStatistics|sprintf:{$mailing.name}}">
          <span class="fa fa-link"></span>
          {$msgBackToStatistics|sprintf:{$mailing.name}}
        </a>
        {/option:showMailmotorStatistics}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {form:add}
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="group">{$lblGroup|ucfirst}</label>
                {$txtGroup} {$txtGroupError}
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
    {/form:add}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    {$dataGrid}
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoItems}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
