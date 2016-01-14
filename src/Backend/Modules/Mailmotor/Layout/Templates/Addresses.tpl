{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblEmailAddresses|ucfirst}{option:group} {$lblFor} {$lblGroup} &ldquo;{$group.name}&rdquo;{/option:group}</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorImportAddresses}
        <a href="{$var|geturl:'import_addresses'}{option:group}&amp;group_id={$group.id}{/option:group}" class="btn btn-default" title="{$lblImportAddresses|ucfirst}">
          <span class="fa fa-upload"></span>
          {option:!group}
          &nbsp;{$lblImportAddresses|ucfirst}
          {/option:!group}
        </a>
        {/option:showMailmotorImportAddresses}
        {option:showMailmotorExportAddresses}
        <a href="{$var|geturl:'export_addresses'}&amp;id={option:!group}all{/option:!group}{option:group}{$group.id}{/option:group}" class="btn btn-default" title="{$lblExportAddresses|ucfirst}">
          <span class="fa fa-download"></span>
          {option:!group}
          &nbsp;{$lblExportAddresses|ucfirst}
          {/option:!group}
        </a>
        {/option:showMailmotorExportAddresses}
        {option:showMailmotorAddAddress}
        <a href="{$var|geturl:'add_address'}{option:group}&amp;group_id={$group.id}{/option:group}" class="btn btn-default">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAddEmail|ucfirst}
        </a>
        {/option:showMailmotorAddAddress}
      </div>
    </div>
  </div>
</div>
{option:csvURL}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="alert alert-warning" role="alert">
      <p><strong>{$msgImportRecentlyFailed}</strong></p>
      <p>{$msgImportFailedDownloadCSV|sprintf:{$csvURL}}</p>
    </div>
  </div>
</div>
{/option:csvURL}
<div class="row fork-module-content">
  <div class="col-md-12">
    {form:filter}
      {$hidGroupId}
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
                <label for="email">{$lblEmailAddress|ucfirst}</label>
                {$txtEmail} {$txtEmailError}
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div class="btn-toolbar">
            <div class="btn-group pull-right">
              <button id="search" type="submit" class="btn btn-primary" name="search">
                <span class="fa fa-refresh"></span>&nbsp;
                {$lblUpdateFilter|ucfirst}
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:filter}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    <form action="{$var|geturl:'mass_address_action'}" method="get" class="forkForms submitWithLink" id="massAddressAction">
      <input type="hidden" name="offset" value="{$offset}" />
      <input type="hidden" name="order" value="{$order}" />
      <input type="hidden" name="sort" value="{$sort}" />
      <input type="hidden" name="email" value="{$email}" />
      {option:group}
      <input type="hidden" name="group_id" value="{$group.id}" />
      {/option:group}
      {$dataGrid}
    </form>
    {/option:dataGrid}
    {option:!dataGrid}
    {option:oPost}
    <p>{$msgNoResultsForFilter|sprintf:{$email}}</p>
    {/option:oPost}
    {option:!oPost}
    <p>{$msgNoSubscriptions}</p>
    {/option:!oPost}
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
