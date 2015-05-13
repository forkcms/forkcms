{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
  <h2>{$lblModuleSettings|ucfirst}: {$lblAnalytics|ucfirst}</h2>
</div>

{form:settings}
  {option:fileSecretFile}
    <div class="box">
      <div class="heading">
        <h3><label for="secretFile">{$lblSecretFile|ucfirst}</label></h3>
      </div>
      <div class="options">
        {$fileSecretFile}
      </div>
      <div class="options longHelpTxt">
        {$msgSecretFileHelp|sprintf:'{$SITE_URL}/private/{$LANGUAGE}/analytics/settings'}
      </div>
    </div>
  {/option:fileSecretFile}

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

  {option:web_property_id}
    <div class="box">
      <div class="heading">
        <h3>{$lblLinkedProfile|ucfirst}</h3>
      </div>
      <div class="options">
        {$web_property_id}
      </div>
    </div>
  {/option:web_property_id}

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

  <div class="fullwidthOptions">
    <div class="buttonHolderRight">
      <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
    </div>
  </div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
