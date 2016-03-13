{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblSettings|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{option:noAccounts}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="alert alert-info" role="alert">
      {$msgNoAccounts|sprintf:{$email}}
    </div>
  </div>
</div>
{/option:noAccounts}
{form:settings}
  {option:fileCertificate}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblCertificate|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:fileCertificateError} has-error{/option:fileCertificateError}">
            {$fileCertificate}
            {option:fileCertificateError}
            <span class="help-block">{$fileCertificateError}</span>
            {/option:fileCertificateError}
          </div>
          <div class="form-group">
            <label for="email">{$lblEmail|ucfirst}</label>
            {$txtEmail}
            {option:txtEmailError}
            <span class="help-block">{$txtEmailError}</span>
            {/option:txtEmailError}
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:fileCertificate}
  {option:ddmAccount}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblChooseThisAccount|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            {$ddmAccount}
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:ddmAccount}
  {option:ddmWebPropertyId}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblChooseWebsiteProfile|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            {$ddmWebPropertyId}
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:ddmWebPropertyId}
  {option:ddmProfile}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblChooseWebsiteProfile|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            {$ddmProfile}
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:ddmProfile}
  {option:noAccounts}
  <div class="generalMessage infoMessage content">
    <p><strong>{$msgNoAccounts|sprintf:{$email}}</strong></p>
  </div>
  {/option:noAccounts}
  {option:profile}
  <div class="box">
    <div class="heading">
      <h3>{$lblLinkedProfile|ucfirst}</h3>
    </div>
    <div class="options">
      <p>
        <strong>{$web_property_id}</strong>{option:profile}: ga:{$profile}{/option:profile}
      </p>
      {option:showAnalyticsReset}<a href="{$var|geturl:'reset'}">{$msgRemoveAccountLink|ucfirst}</a>{/option:showAnalyticsReset}
    </div>
  </div>
  {/option:profile}
  {option:!profile}
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
  {/option:!profile}
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
