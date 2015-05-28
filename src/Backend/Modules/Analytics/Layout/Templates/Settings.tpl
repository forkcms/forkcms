{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblSettings|ucfirst}</h2>
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
        <div class="form-group">
          <p class="text-info">
            {$msgCertificateHelp}
          </p>
          {$fileCertificate} {$fileCertificateError}
        </div>
        <div class="form-group">
          <label for="clientId">{$lblClientId|ucfirst}</label>
          {$txtClientId} {$txtClientIdError}
        </div>
        <div class="form-group">
          <label for="email">{$lblEmail|ucfirst}</label>
          {$txtEmail} {$txtEmailError}
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
{option:web_property_id}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblLinkedProfile|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <div class="form-group">
          <strong>{$web_property_id}</strong>{option:profile}: ga:{$profile}{/option:profile}
        </div>
      </div>
    </div>
  </div>
</div>
{/option:web_property_id}
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
<div class="row fork-module-actions">
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
