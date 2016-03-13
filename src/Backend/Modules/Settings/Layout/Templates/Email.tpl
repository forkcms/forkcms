{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblGeneralSettings|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{form:settingsEmail}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblEmail|ucfirst}
          </h3>
        </div>
        {option:isGod}
        <div class="panel-body">
          <h4>{$lblSendingEmails|ucfirst}</h4>
          <p class="help-block">{$msgHelpSendingEmails}</p>
          <div class="form-inline">
            <div class="form-group{option:ddmMailerTypeError} has-error{/option:ddmMailerTypeError}">
              {$ddmMailerType} {$ddmMailerTypeError}
            </div>
            <small>
              <a id="testEmailConnection" href="#">{$msgSendTestMail}</a>
              <span id="testEmailConnectionSpinner" style="display: none;">
                <img style="margin-top: 3px;" src="/src/Backend/Core/Layout/images/spinner.gif" width="12px" height="12px" alt="loading" />
              </span>
              <span id="testEmailConnectionError" style="display: none;" class="alert alert-danger">{$errErrorWhileSendingEmail}</span>
              <span id="testEmailConnectionSuccess" style="display: none;" class="alert alert-success">{$msgTestWasSent}</span>
            </small>
          </div>
        </div>
        {/option:isGod}
        <div class="panel-body">
          <h4>{$lblFrom|ucfirst}</h4>
          <p class="help-block">{$msgHelpEmailFrom}</p>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{option:txtMailerFromNameError} has-error{/option:txtMailerFromNameError}">
                <label for="mailerFromName" class="control-label">
                  {$lblName|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerFromName} {$txtMailerFromNameError}
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{option:txtMailerFromEmailError} has-error{/option:txtMailerFromEmailError}">
                <label for="mailerFromEmail" class="control-label">
                  {$lblEmail|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerFromEmail} {$txtMailerFromEmailError}
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <h4>{$lblTo|ucfirst}</h4>
          <p class="help-block">{$msgHelpEmailTo}</p>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{option:txtMailerToNameError} has-error{option:txtMailerToNameError}">
                <label for="mailerToName" class="control-label">
                  {$lblName|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerToName} {$txtMailerToNameError}
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{option:txtMailerToEmailError} has-error{/option:txtMailerToEmailError}">
                <label for="mailerToEmail" class="control-label">
                  {$lblEmail|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerToEmail} {$txtMailerToEmailError}
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body">
          <h4>{$lblReplyTo|ucfirst}</h4>
          <p class="help-block">{$msgHelpReplyTo}</p>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{option:txtMailerReplyToNameError} has-error{/option:txtMailerReplyToNameError}">
                <label for="mailerReplyToName" class="control-label">
                  {$lblName|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerReplyToName} {$txtMailerReplyToNameError}
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{option:txtMailerReplyToEmailError} has-error{/option:txtMailerReplyToEmailError}">
                <label for="mailerReplyToEmail" class="control-label">
                  {$lblEmail|ucfirst}
                  <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                </label>
                {$txtMailerReplyToEmail} {$txtMailerReplyToEmailError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {option:isGod}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblSMTP|ucfirst}
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtSmtpServerError} has-error{/option:txtSmtpServerError}{option:txtSmtpPortError} has-error{/option:txtSmtpPortError}">
            <label for="smtpServer" style="float: left;" class="control-label">{$lblServer|ucfirst}</label>
            <label for="smtpPort" class="control-label">&#160;&amp; {$lblPort}</label>
            <p class="help-block">{$msgHelpSMTPServer}</p>
            <div class="form-inline">
              <div class="form-group">
                {$txtSmtpServer}
              </div>
              <span>:</span>
              <div class="form-group">
                {$txtSmtpPort}
              </div>
              <div class="form-group">
                {$txtSmtpServerError} {$txtSmtpPortError}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group{option:txtSmtpUsernameError} has-error{/option:txtSmtpUsernameError}">
                <label for="smtpUsername" class="control-label">{$lblUsername|ucfirst}</label>
                {$txtSmtpUsername} {$txtSmtpUsernameError}
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group{option:txtSmtpPasswordError} has-error{/option:txtSmtpPasswordError}">
                <label for="smtpPassword" class="control-label">{$lblPassword|ucfirst}</label>
                {$txtSmtpPassword} {$txtSmtpPasswordError}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="smtpSecureLayer" class="control-label">{$lblSmtpSecureLayer|ucfirst}</label>
                 {$ddmSmtpSecureLayer}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {/option:isGod}
  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-success"><span class="fa fa-floppy-o"></span> {$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settingsEmail}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
