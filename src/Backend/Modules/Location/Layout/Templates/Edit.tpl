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
      <div class="form-group{option:txtTitleError} has-error{/option:txtTitleError}">
        <label for="title" class="control-label">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblAddress|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtStreetError} has-error{/option:txtStreetError}">
            <label for="street" class="control-label">
              {$lblStreet|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtStreet} {$txtStreetError}
          </div>
          <div class="form-group{option:txtNumberError} has-error{/option:txtNumberError}">
            <label for="number" class="control-label">
              {$lblNumber|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtNumber} {$txtNumberError}
          </div>
          <div class="form-group{option:txtZipError} has-error{/option:txtZipError}">
            <label for="zip" class="control-label">
              {$lblZip|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtZip} {$txtZipError}
          </div>
          <div class="form-group{option:txtCityError} has-error{/option:txtCityError}">
            <label for="city" class="control-label">
              {$lblCity|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtCity} {$txtCityError}
          </div>
          <div class="form-group{option:ddmCountryError} has-error{/option:ddmCountryError}">
            <label for="country" class="control-label">
              {$lblCountry|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$ddmCountry} {$ddmCountryError}
          </div>
          <div class="hide">
            {$hidMapId} {$hidRedirect}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-group pull-right" role="group">
        <button id="editButton" type="submit" name="edit" class="btn btn-success">{$lblUpdateMap|ucfirst}</button>
      </div>
    </div>
  </div>
{/form:edit}
{form:settings}
  <div class="row fork-module-content">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblMap|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div id="map" style="width: 100%; height: {$settings.height}px;"></div>
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
          <div class="form-group{option:ddmZoomLevelError} has-error{/option:ddmZoomLevelError}">
            <label for="zoomLevel" class="control-label">{$lblZoomLevel|ucfirst}</label>
            {$ddmZoomLevel} {$ddmZoomLevelError}
          </div>
          <div class="form-group{option:txtWidthError} has-error{/option:txtWidthError}"{option:!godUser} style="display:none;"{/option:!godUser}>
            <label for="width" class="control-label">{$lblWidth|ucfirst}</label>
            <p class="help-block">{$msgWidthHelp|sprintf:300:800}</p>
            {$txtWidth} {$txtWidthError}
          </div>
          <div class="form-group{option:txtHeightError} has-error{/option:txtHeightError}"{option:!godUser} style="display:none;"{/option:!godUser}>
            <label for="height" class="control-label">{$lblHeight|ucfirst}</label>
            <p class="help-block">{$msgHeightHelp|sprintf:150}</p>
            {$txtHeight} {$txtHeightError}
          </div>
          <div class="form-group{option:ddmMapTypeError} has-error{/option:ddmMapTypeError}">
            <label for="mapType" class="control-label">{$lblMapType|ucfirst}</label>
            {$ddmMapType} {$ddmMapTypeError}
          </div>
          <div class="form-group">
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="fullUrl">{$chkFullUrl} {$msgShowMapUrl}</label>
              </li>
              <li class="checkbox">
                <label for="directions">{$chkDirections} {$msgShowDirections}</label>
              </li>
              <li class="checkbox">
                <label for="markerOverview">{$chkMarkerOverview} {$msgShowMarkerOverview}</label>
              </li>
            </ul>
          </div>
          <div class="btn-toolbar">
            <div class="btn-group pull-right" role="group">
              <a href="#" id="saveLiveData" class="btn btn-success">
                <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
              </a>
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
          {option:showLocationDelete}
          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">
            <span class="fa fa-trash-o"></span>
            {$lblDelete|ucfirst}
          </button>
          {/option:showLocationDelete}
        </div>
        <div class="btn-group pull-right" role="group">
          <button id="saveLiveData" type="button" name="edit" class="btn btn-success">
            <span class="fa fa-floppy-o"></span>&nbsp;{$lblSave|ucfirst}
          </button>
        </div>
      </div>
      {option:showLocationDelete}
      <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="{$lblDelete|ucfirst}" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <span class="modal-title h4">{$lblDelete|ucfirst}</span>
            </div>
            <div class="modal-body">
              <p>{$msgConfirmDelete|sprintf:{$item.title}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times"></span> {$lblCancel|ucfirst}</button>
              <a href="{$var|geturl:'delete'}&amp;id={$item.id}" class="btn btn-success">
                <span class="fa fa-trash-o"></span> {$lblDelete|ucfirst}
              </a>
            </div>
          </div>
        </div>
      </div>
      {/option:showLocationDelete}
    </div>
  </div>
{/form:settings}
<script type="text/javascript">
  var mapOptions =
  {
    zoom: '{$settings.zoom_level}' == 'auto' ? 0 : {$settings.zoom_level},
    type: '{$settings.map_type}',
    center:
    {
      lat: {$settings.center.lat},
      lng: {$settings.center.lng}
    }
  };
  var markers = [];
  {option:item.lat}
    {option:item.lng}
      markers.push(
      {
        lat: {$item.lat},
        lng: {$item.lng},
        title: '{$item.title}',
        text: '<p>{$item.street} {$item.number}</p><p>{$item.zip} {$item.city}</p>',
        dragable: true
      });
    {/option:item.lng}
  {/option:item.lat}
</script>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
