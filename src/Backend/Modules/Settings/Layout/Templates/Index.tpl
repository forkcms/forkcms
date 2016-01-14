{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblGeneralSettings|ucfirst}</h2>
  </div>
</div>
{form:settingsIndex}
  {option:warnings}
  <div class="row fork-module-messages">
    <div class="col-md-12">
      <div class="alert alert-warning" role="alert">
        <p><strong>{$msgConfigurationError}</strong></p>
        <ul>
          {iteration:warnings}
          <li>{$warnings.message}</li>
          {/iteration:warnings}
        </ul>
      </div>
    </div>
  </div>
  {/option:warnings}
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            {$lblWebsiteTitle|ucfirst}
            <abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
          </h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtSiteTitleError} has-error{/option:txtSiteTitleError}">
            {$txtSiteTitle} {$txtSiteTitleError}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblScripts|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtSiteHtmlHeaderError} has-error{/option:txtSiteHtmlHeaderError}">
            <label for="siteHtmlHeader"><code>&lt;head&gt;</code> script(s)</label>
            {$txtSiteHtmlHeader} {$txtSiteHtmlHeaderError}
            <span class="help-block">{$msgHelpScriptsHead}</span>
          </div>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtSiteStartOfBodyScriptsError} has-error{/option:txtSiteStartOfBodyScriptsError}">
            <label for="siteStartOfBodyScripts">{$msgHelpScriptsStartOfBodyLabel}</label>
            {$txtSiteStartOfBodyScripts} {$txtSiteStartOfBodyScriptsError}
            <span class="help-block">{$msgHelpScriptsStartOfBody}</span>
          </div>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtSiteHtmlFooterError} has-error{/option:txtSiteHtmlFooterError}">
            <label for="siteHtmlFooter">End of <code>&lt;body&gt;</code> script(s)</label>
            {$txtSiteHtmlFooter} {$txtSiteHtmlFooterError}
            <span class="help-block">{$msgHelpScriptsFoot}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblLanguages|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <p>{$msgHelpLanguages}</p>
            <ul id="activeLanguages" class="list-unstyled">
              {iteration:activeLanguages}
              <li class="checkbox">
                <label for="{$activeLanguages.id}">
                  {$activeLanguages.chkActiveLanguages} {$activeLanguages.label|ucfirst}{option:activeLanguages.default} ({$lblDefault}){/option:activeLanguages.default}
                </label>
              </li>
              {/iteration:activeLanguages}
            </ul>
          </div>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <p>{$msgHelpRedirectLanguages}</p>
            <ul id="redirectLanguages" class="list-unstyled">
              {iteration:redirectLanguages}
              <li class="checkbox">
                <label for="{$redirectLanguages.id}">
                  {$redirectLanguages.chkRedirectLanguages} {$redirectLanguages.label|ucfirst}{option:redirectLanguages.default} ({$lblDefault}){/option:redirectLanguages.default}
                </label>
              </li>
              {/iteration:redirectLanguages}
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblDateAndTime|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:ddmTimeFormatError} has-error{/option:ddmTimeFormatError}">
            <label for="timeFormat">{$lblTimeFormat|ucfirst}</label>
            {$ddmTimeFormat} {$ddmTimeFormatError}
            <span class="help-block">{$msgHelpTimeFormat}</span>
          </div>
          <div class="form-group{option:ddmDateFormatShortError} has-error{/option:ddmDateFormatShortError}">
            <label for="dateFormatShort">{$lblShortDateFormat|ucfirst}</label>
            {$ddmDateFormatShort} {$ddmDateFormatShortError}
            <span class="help-block">{$msgHelpDateFormatShort}</span>
          </div>
          <div class="form-group{option:ddmDateFormatLongError} has-error{/option:ddmDateFormatLongError}">
            <label for="dateFormatLong">{$lblLongDateFormat|ucfirst}</label>
            {$ddmDateFormatLong} {$ddmDateFormatLongError}
            <span class="help-block">{$msgHelpDateFormatLong}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblNumbers|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:ddmNumberFormatError} has-error{/option:ddmNumberFormatError}">
            <label for="numberFormat">{$lblNumberFormat|ucfirst}</label>
            {$ddmNumberFormat} {$ddmNumberFormatError}
            <span class="help-block">{$msgHelpNumberFormat}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div id="settingsApiKeys" class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblAPIKeys|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <p>{$msgHelpAPIKeys}</p>
          <table class="table table-striped">
            <thead>
              <tr>
                <th class="title" style="width: 20%;"><span>{$lblName|ucfirst}</span></th>
                <th style="width: 40%;"><span>{$lblAPIKey|ucfirst}</span></th>
                <th style="width: 60%;"><span>{$lblAPIURL|ucfirst}</span></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th><label for="forkApiPublicKey">Fork public key</label></td>
                <td>{$txtForkApiPublicKey} {$txtForkApiPublicKeyError}</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <th><label for="forkApiPrivateKey">Fork private key</label></td>
                <td>{$txtForkApiPrivateKey} {$txtForkApiPrivateKeyError}</td>
                <td>&nbsp;</td>
              </tr>
              {option:needsGoogleMaps}
              <tr>
                <th>
                  <label for="googleMapsKey">
                    Google maps key<abbr data-toggle="tooltip" title="{$lblRequiredField|ucfirst}">*</abbr>
                  </label>
                </td>
                <td>{$txtGoogleMapsKey} {$txtGoogleMapsKeyError}</td>
                <td><a href="http://code.google.com/apis/maps/signup.html">http://code.google.com/apis/maps/signup.html</a></td>
              </tr>
              {/option:needsGoogleMaps}
              {option:needsAkismet}
              <tr>
                <th><label for="akismetKey">Akismet key</label></td>
                <td>{$txtAkismetKey} {$txtAkismetKeyError}</td>
                <td><a href="http://akismet.com/personal">http://akismet.com/personal</a></td>
              </tr>
              {/option:needsAkismet}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">CKFinder</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtCkfinderLicenseNameError} has-error{/option:txtCkfinderLicenseNameError}">
            <label for="ckfinderLicenseName">{$lblLicenseName|ucfirst}</label>
            {$txtCkfinderLicenseName} {$txtCkfinderLicenseNameError}
          </div>
          <div class="form-group{option:txtCkfinderLicenseKeyError} has-error{/option:txtCkfinderLicenseKeyError}">
            <label for="ckfinderLicenseKey">{$lblLicenseKey|ucfirst}</label>
            {$txtCkfinderLicenseKey} {$txtCkfinderLicenseKeyError}
          </div>
          <div class="form-group{option:txtCkfinderImageMaxWidthError} has-error{/option:txtCkfinderImageMaxWidthError}">
            <label for="ckfinderImageMaxWidth">{$lblMaximumWidth|ucfirst}</label>
            {$txtCkfinderImageMaxWidth} {$txtCkfinderImageMaxWidthError}
            <span class="help-block">{$msgHelpCkfinderMaximumWidth}</span>
          </div>
          <div class="form-group{option:txtCkfinderImageMaxHeightError} has-error{/option:txtCkfinderImageMaxHeightError}">
            <label for="ckfinderImageMaxHeight">{$lblMaximumHeight|ucfirst}</label>
            {$txtCkfinderImageMaxHeight} {$txtCkfinderImageMaxHeightError}
            <span class="help-block">{$msgHelpCkfinderMaximumHeight}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Facebook</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtFacebookAdminIdsError} has-error{/option:txtFacebookAdminIdsError}">
            <label for="addValue-facebookAdminIds">{$lblAdminIds|ucfirst}</label>
            {$txtFacebookAdminIds} {$txtFacebookAdminIdsError}
            <span class="help-block">{$msgHelpFacebookAdminIds}</span>
          </div>
          <div class="form-group{option:txtFacebookApplicationIdError} has-error{/option:txtFacebookApplicationIdError}">
            <label for="facebookApplicationId">{$lblApplicationId|ucfirst}</label>
            {$txtFacebookApplicationId} {$txtFacebookApplicationIdError}
            <span class="help-block">{$msgHelpFacebookApplicationId}</span>
          </div>
          <div class="form-group{option:txtFacebookApplicationSecretError} has-error{option:txtFacebookApplicationSecretError}">
            <label for="facebookApplicationSecret">{$lblApplicationSecret|ucfirst}</label>
            {$txtFacebookApplicationSecret} {$txtFacebookApplicationSecretError}
            <span class="help-block">{$msgHelpFacebookApplicationSecret}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Twitter</h3>
        </div>
        <div class="panel-body">
          <div class="form-group{option:txtTwitterSiteNameError} has-error{/option:txtTwitterSiteNameError}">
            <label for="twitterSiteName">{$lblTwitterSiteName|ucfirst}</label>
            <div class="form-inline">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-addon">@</div>
                  {$txtTwitterSiteName}
                </div>
                {$txtTwitterSiteNameError}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">{$lblCookies|ucfirst}</h3>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <p>{$msgHelpCookies}</p>
            <ul class="list-unstyled">
              <li class="checkbox">
                <label for="showCookieBar">{$chkShowCookieBar} {$msgShowCookieBar}</label>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row fork-module-actions">
    <div class="col-md-12">
      <div class="btn-toolbar">
        <div class="btn-group pull-right" role="group">
          <button id="save" type="submit" name="save" class="btn btn-primary">{$lblSave|ucfirst}</button>
        </div>
      </div>
    </div>
  </div>

{/form:settingsIndex}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
