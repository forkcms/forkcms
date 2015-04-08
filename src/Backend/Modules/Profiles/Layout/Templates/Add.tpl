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
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblProfile|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="email">
              {$lblEmail|ucfirst}
              <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
            </label>
            {$txtEmail} {$txtEmailError}
          </div>
          <div class="form-group">
            <label for="displayName">
              {$lblDisplayName|ucfirst}
              <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
            </label>
            {$txtDisplayName} {$txtDisplayNameError}
          </div>
          <div class="form-group">
            <label for="password">
              {$lblPassword|ucfirst}
              <abbr class="glyphicon glyphicon-asterisk" title="{$lblRequiredField|ucfirst}"></abbr>
            </label>
            {$txtPassword} {$txtPasswordError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblSettings|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label for="firstName">{$lblFirstName|ucfirst}</label>
            {$txtFirstName} {$txtFirstNameError}
          </div>
          <div class="form-group">
            <label for="lastName">{$lblLastName|ucfirst}</label>
            {$txtLastName} {$txtLastNameError}
          </div>
          <div class="form-group">
            <label for="gender">{$lblGender|ucfirst}</label>
            {$ddmGender} {$ddmGenderError}
          </div>
          <div class="form-group">
            <label for="day">{$lblBirthDate|ucfirst}</label>
            <div class="form-inline">
              <div class="form-group">{$ddmDay}</div>
              <div class="form-group">{$ddmMonth}</div>
              <div class="form-group">{$ddmYear}</div>
              {$ddmYearError}
            </div>
          </div>
          <div class="form-group">
            <label for="city">{$lblCity|ucfirst}</label>
            {$txtCity} {$txtCityError}
          </div>
          <div class="form-group">
            <label for="country">{$lblCountry|ucfirst}</label>
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
            <span class="glyphicon glyphicon-plus"></span>&nbsp;
            {$lblAdd|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:add}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
