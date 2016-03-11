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
        <div class="form-group{option:fileCertificateError} has-error{/option:fileCertificateError}">
          <p class="help-block">
            {$msgCertificateHelp}
          </p>
          {$fileCertificate} {$fileCertificateError}
        </div>
        <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
          <label for="email" class="control-label">{$lblEmail|ucfirst}</label>
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
  {/option:ddmWebPropertyId}


  {option:ddmProfile}
    <div class="box">
      <div class="heading">
        <h3><label for="account">{$lblChooseWebsiteProfile|ucfirst}</label></h3>
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
            <button id="save" type="submit" name="save" class="btn btn-success">
              <span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}
            </button>
          </div>
        </div>
      </div>
    </div>
  {/option:!profile}
{/form:settings}

  {option:!profile}
    <div class="fullwidthOptions">
      <div class="buttonHolderRight">
        <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
      </div>
    </div>
  {/option:!profile}
{/form:settings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
