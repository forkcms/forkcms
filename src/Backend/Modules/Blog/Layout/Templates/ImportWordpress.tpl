{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblImport|ucfirst}</h2>
  </div>
</div>
{form:import}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="wordpress">{$lblFile|ucfirst}</label>
        <p class="text-info">{$msgHelpWordpress}</p>
        {$fileWordpress} {$fileWordpressError}
      </div>
      <div class="form-group">
        <label for="filter">{$lblWordpressFilter|ucfirst}</label>
        <p class="text-info">{$msgHelpWordpressFilter}</p>
        {$txtFilter} {$txtFilterError}
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="importButton" type="submit" name="save" class="btn btn-primary">{$lblImport|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:import}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
