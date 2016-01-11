{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSettings|ucfirst}</h2>
  </div>
</div>
{form:settings}
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblIndividualMap|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="zoomLevelWidget">{$lblZoomLevel|ucfirst}</label>
            {$ddmZoomLevelWidget} {$ddmZoomLevelWidgetError}
          </div>
          <div class="form-group">
            <label for="widthWidget">{$lblWidth|ucfirst}</label>
            <p class="help-block">{$msgWidthHelp|sprintf:300:800}</p>
            {$txtWidthWidget} {$txtWidthWidgetError}
          </div>
          <div class="form-group">
            <label for="heightWidget">{$lblHeight|ucfirst}</label>
            <p class="help-block">{$msgHeightHelp|sprintf:150}</p>
            {$txtHeightWidget} {$txtHeightWidgetError}
          </div>
          <div class="form-group">
            <label for="mapTypeWidget">{$lblMapType|ucfirst}</label>
            {$ddmMapTypeWidget} {$ddmMapTypeWidgetError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
