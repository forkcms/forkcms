{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblGeneralSettings|ucfirst}</h2>
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
          <p class="text-info">{$msgHelpSendingEmails}</p>
          <div class="form-inline">
            <div class="form-group">
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
          <p>{$msgHelpEmailFrom}</p>
          <div class="form-group">
            <label for="mailerFromName">
              {$lblName|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerFromName} {$txtMailerFromNameError}
          </div>
          <div class="form-group">
            <label for="mailerFromEmail">
              {$lblEmail|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerFromEmail} {$txtMailerFromEmailError}
          </div>
        </div>
        <div class="panel-body">
          <h4>{$lblTo|ucfirst}</h4>
          <p>{$msgHelpEmailTo}</p>
          <div class="form-group">
            <label for="mailerToName">
              {$lblName|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerToName} {$txtMailerToNameError}
          </div>
          <div class="form-group">
            <label for="mailerToEmail">
              {$lblEmail|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerToEmail} {$txtMailerToEmailError}
          </div>
        </div>
        <div class="panel-body">
          <h4>{$lblReplyTo|ucfirst}</h4>
          <div class="form-group">
            <label for="mailerReplyToName">
              {$lblName|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerReplyToName} {$txtMailerReplyToNameError}
          </div>
          <div class="form-group">
            <label for="mailerReplyToEmail">
              {$lblEmail|ucfirst}
              <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
            </label>
            {$txtMailerReplyToEmail} {$txtMailerReplyToEmailError}
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
          <div class="form-group">
            <label for="smtpServer" style="float: left;">{$lblServer|ucfirst}</label>
            <label for="smtpPort">&#160;&amp; {$lblPort}</label>
            <p class="text-info">{$msgHelpSMTPServer}</p>
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
          <div class="form-group">
            <label for="smtpUsername">{$lblUsername|ucfirst}</label>
            {$txtSmtpUsername} {$txtSmtpUsernameError}
          </div>
          <div class="form-group">
            <label for="smtpPassword">{$lblPassword|ucfirst}</label>
            {$txtSmtpPassword} {$txtSmtpPasswordError}
          </div>
          <div class="form-group">
            <label for="smtpSecureLayer">{$lblSmtpSecureLayer|ucfirst}</label>
             {$ddmSmtpSecureLayer}
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
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>
{/form:settingsEmail}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
