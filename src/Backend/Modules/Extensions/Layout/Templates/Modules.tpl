{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblModules|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showExtensionsUploadModule}
        <a href="{$var|geturl:'upload_module'}" class="btn btn-default">
          <span class="fa fa-upload"></span>&nbsp;
          <span>{$lblUploadModule|ucfirst}</span>
        </a>
        {/option:showExtensionsUploadModule}
        <a href="http://www.fork-cms.com/extensions/apps" target="_blank" class="btn btn-default">
          <span class="fa fa-search"></span>&nbsp;
          <span>{$lblFindModules|ucfirst}</span>
        </a>
      </div>
    </div>
  </div>
</div>
{option:warnings}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="panel panel-warning">
      <div class="panel-heading">
        <span class="panel-title">{$msgModulesWarnings}</span>
      </div>
      <div class="panel-body">
        <ul>
          <li>
            <strong>{$warnings.module}</strong>
            <ul>
              {iteration:warnings.warnings}
                <li>- {$warnings.warnings.message}</li>
              {/iteration:warnings.warnings}
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
{/option:warnings}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblInstalledModules|ucfirst}</h3>
      </div>
      {option:dataGridInstalledModules}
      {$dataGridInstalledModules}
      {/option:dataGridInstalledModules}
      {option:!dataGridInstalledModules}
      <div class="panel-body">
        <p>{$msgNoModulesInstalled}</p>
      </div>
      {/option:!dataGridInstalledModules}
    </div>
  </div>
</div>
{option:dataGridInstallableModules}
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblInstallableModules|ucfirst}</h3>
      </div>
      {$dataGridInstallableModules}
    </div>
  </div>
</div>
{/option:dataGridInstallableModules}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
