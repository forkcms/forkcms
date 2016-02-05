{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
  <h2>{$lblModuleSettings|ucfirst}: {$lblAnalytics|ucfirst}</h2>
</div>

{form:settings}
  {option:fileCertificate}
    <div class="box">
      <div class="heading">
        <h3><label for="jsonKey">{$lblCertificate|ucfirst}</label></h3>
      </div>
      <div class="options horizontal">
        <p>
          <label for="certificate">{$lblCertificate|ucfirst}</label>
          {$fileCertificate} {$fileCertificateError}
        </p>
        <p>
          <label for="email">{$lblEmail|ucfirst}</label>
          {$txtEmail} {$txtEmailError}
        </p>
      </div>
      <div class="options longHelpTxt">
        {$msgCertificateHelp}
      </div>
    </div>
  {/option:fileCertificate}

  {option:ddmAccount}
    <div class="box">
      <div class="heading">
        <h3><label for="account">{$lblChooseThisAccount|ucfirst}</label></h3>
      </div>
      <div class="options">
        {$ddmAccount}
      </div>
    </div>
  {/option:ddmAccount}

  {option:ddmWebPropertyId}
    <div class="box">
      <div class="heading">
        <h3><label for="account">{$lblChooseWebsiteProfile|ucfirst}</label></h3>
      </div>
      <div class="options">
        {$ddmWebPropertyId}
      </div>
    </div>
  {/option:ddmWebPropertyId}


  {option:ddmProfile}
    <div class="box">
      <div class="heading">
        <h3><label for="account">{$lblChooseWebsiteProfile|ucfirst}</label></h3>
      </div>
      <div class="options">
        {$ddmProfile}
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
    <div class="fullwidthOptions">
      <div class="buttonHolderRight">
        <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
      </div>
    </div>
  {/option:!profile}
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
