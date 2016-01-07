{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAdd|ucfirst}</h2>
  </div>
</div>
{form:add}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="form-group">
        <label for="title">{$lblTitle|ucfirst}</label>
        {$txtTitle} {$txtTitleError}
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblAddress|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="street">
              {$lblStreet|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtStreet} {$txtStreetError}
          </div>
          <div class="form-group">
            <label for="number">
              {$lblNumber|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtNumber} {$txtNumberError}
          </div>
          <div class="form-group">
            <label for="zip">
              {$lblZip|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtZip} {$txtZipError}
          </div>
          <div class="form-group">
            <label for="city">
              {$lblCity|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtCity} {$txtCityError}
          </div>
          <div class="form-group">
            <label for="country">
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
          <button id="addButton" type="submit" name="add" class="btn btn-primary">
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
