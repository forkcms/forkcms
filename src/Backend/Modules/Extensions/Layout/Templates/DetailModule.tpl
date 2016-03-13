{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$msgModule|sprintf:{$name}|ucfirst}</h2>
  </div>
  <div class="col-md-6">

  </div>
</div>
{option:warnings}
<div class="row fork-module-messages">
  <div class="col-md-12">
    {iteration:warnings}
    <div class="alert alert-warning" role="alert">
      {$warnings.message}
    </div>
    {/iteration:warnings}
  </div>
</div>
{/option:warnings}
{option:information}
<div class="row fork-module-content">
  <div class="col-md-8">
    {option:information.description}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblDescription|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <p>{$information.description}</p>
      </div>
    </div>
    {/option:information.description}
    {option:dataGridCronjobs}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblCronjobs|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <p>{$msgCronjobsInfo|sprintf:{$PATH_WWW}:{$SITE_URL}}</p>
      </div>
      {$dataGridCronjobs}
    </div>
    {/option:dataGridCronjobs}
    {option:dataGridEvents}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblEvents|ucfirst}
        </h3>
      </div>
      {$dataGridEvents}
    </div>
    {/option:dataGridEvents}
  </div>
  <div class="col-md-4">
    {option:information.version}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblVersion|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <p>{$information.version}</p>
      </div>
    </div>
    {/option:information.version}
    {option:information.authors}
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblAuthors|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <ul>
          {iteration:information.authors}
          <li>
            {option:information.authors.url}
            <a href="{$information.authors.url}" target="_blank" title="{$information.authors.name}">
              {/option:information.authors.url}
              {$information.authors.name}
              {option:information.authors.url}
            </a>
            {/option:information.authors.url}
          </li>
          {/iteration:information.authors}
        </ul>
      </div>
    </div>
    {/option:information.authors}
  </div>
</div>
{/option:information}
{option:showExtensionsInstallModule}
<div class="row fork-module-actions">
  <div class="col-md-12">
    <div class="btn-toolbar">
      <div class="btn-group pull-right" role="group">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#confirmInstall">
          <span class="fa fa-download"></span>&nbsp;
          {$lblInstall|ucfirst}
        </button>
      </div>
    </div>
    <div class="modal fade" id="confirmInstall" tabindex="-1" role="dialog" aria-labelledby="{$lblInstall|ucfirst}" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <span class="modal-title h4">{$lblInstall|ucfirst}</span>
          </div>
          <div class="modal-body">
            <p>{$msgConfirmModuleInstall|sprintf:{$name}}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-times"></span> {$lblCancel|ucfirst}</button>
            <a href="{$var|geturl:'install_module'}&amp;theme={$name}" class="btn btn-success">
              <span class="fa fa-check"></span> {$lblOK|ucfirst}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{/option:showExtensionsInstallModule}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
