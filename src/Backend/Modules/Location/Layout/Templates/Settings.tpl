{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblSettings|ucfirst}</h2>
  </div>
  <div class="col-md-6">

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
          <div class="row">
            <div class="col-md-3">
              <div class="form-group{option:ddmZoomLevelWidgetError} has-error{/option:ddmZoomLevelWidgetError}">
                <label for="zoomLevelWidget" class="control-label">{$lblZoomLevel|ucfirst}</label>
                {$ddmZoomLevelWidget} {$ddmZoomLevelWidgetError}
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group{option:txtWidthWidgetError} has-error{/option:txtWidthWidgetError}">
                <label for="widthWidget" class="control-label">{$lblWidth|ucfirst}</label>
                {$txtWidthWidget} {$txtWidthWidgetError}
                <p class="help-block">{$msgWidthHelp|sprintf:300:800}</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group{option:txtHeightWidgetError} has-error{/option:txtHeightWidgetError}">
                <label for="heightWidget" class="control-label">{$lblHeight|ucfirst}</label>
                {$txtHeightWidget} {$txtHeightWidgetError}
                <p class="help-block">{$msgHeightHelp|sprintf:150}</p>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group{option:ddmMapTypeWidgetError} has-error{/option:ddmMapTypeWidgetError}">
                <label for="mapTypeWidget" class="control-label">{$lblMapType|ucfirst}</label>
                {$ddmMapTypeWidget} {$ddmMapTypeWidgetError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-success"><span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
