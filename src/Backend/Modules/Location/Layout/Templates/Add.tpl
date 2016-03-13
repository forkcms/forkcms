{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{form:add}
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
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="addButton" type="submit" name="add" class="btn btn-success">
            <span class="fa fa-plus"></span>&nbsp;
            {$lblAddToMap|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
