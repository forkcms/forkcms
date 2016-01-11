{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblLocation|ucfirst}</h2>
  </div>
  <div class="col-md-6">
    {option:showLocationAdd}
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAdd|ucfirst}">
          <span class="fa fa-plus"></span>
          {$lblAdd|ucfirst}
        </a>
      </div>
    </div>
    {/option:showLocationAdd}
  </div>
</div>
{option:dataGrid}
<div class="row fork-module-content">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblMap|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        {option:items}
        <div id="map" style="width: 100%; height: {$settings.height}px;"></div>
        {/option:items}
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblSettings|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <label for="zoomLevel">{$lblZoomLevel|ucfirst}</label>
          {$ddmZoomLevel} {$ddmZoomLevelError}
        </div>
        <div class="form-group"{option:!godUser} style="display:none;"{/option:!godUser}>
          <label for="width">{$lblWidth|ucfirst}</label>
          <p class="help-block">{$msgWidthHelp|sprintf:300:800}</p>
          {$txtWidth} {$txtWidthError}
        </div>
        <div class="form-group"{option:!godUser} style="display:none;"{/option:!godUser}>
          <label for="height">{$lblHeight|ucfirst}</label>
          <p class="help-block">{$msgHeightHelp|sprintf:150}</p>
          {$txtHeight} {$txtHeightError}
        </div>
        <div class="form-group">
          <label for="mapType">{$lblMapType|ucfirst}</label>
          {$ddmMapType} {$ddmMapTypeError}
        </div>
        <div class="btn-toolbar">
          <div class="btn-group pull-right" role="group">
            <a href="#" id="saveLiveData" class="btn btn-primary">{$lblSave|ucfirst}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {$dataGrid}
  </div>
</div>
{/option:dataGrid}
{option:!dataGrid}
<div class="row fork-module-content">
  <div class="col-md-12">
    <p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>
  </div>
</div>
{/option:!dataGrid}
<script type="text/javascript">
  //@todo BUG: data below should come from action
  var mapOptions = {
    zoom: '{$settings.zoom_level}' == 'auto' ? 0 : {$settings.zoom_level},
    type: '{$settings.map_type}',
    center: {
      lat: {$settings.center.lat},
      lng: {$settings.center.lng}
    }
  };
  var markers = [];
  {iteration:items}
    {option:items.lat}
      {option:items.lng}
        markers.push({
          lat: {$items.lat},
          lng: {$items.lng},
          title: '{$items.title}',
          text: '<p>{$items.street} {$items.number}</p><p>{$items.zip} {$items.city}</p>'
        });
      {/option:items.lng}
    {/option:items.lat}
  {/iteration:items}
</script>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
