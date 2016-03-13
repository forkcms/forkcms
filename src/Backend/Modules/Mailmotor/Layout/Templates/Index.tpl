{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<form action="{$var|geturl:'mass_mailing_action'}" method="get" class="forkForms submitWithLink" id="mailings">
  <div class="row fork-module-heading">
    <div class="col-md-6">
      {option:dgUnsentMailings}
      <h2>{$lblUnsentMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>
      {/option:dgUnsentMailings}
      {option:!dgUnsentMailings}
      <h2>{$lblNewsletters|ucfirst}</h2>
      {/option:!dgUnsentMailings}
    </div>
    <div class="col-md-6">
      <div class="btn-toolbar pull-right">
        <div class="btn-group" role="group">
          {option:showMailmotorAdd}
          <a href="{$var|geturl:'add'}" class="btn btn-default" title="{$lblAddNewMailing|ucfirst}">
            <span class="fa fa-fa fa-send-o-o"></span>&nbsp;
            {$lblAddNewMailing|ucfirst}
          </a>
          {/option:showMailmotorAdd}
        </div>
      </div>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      {option:dgUnsentMailings}
      <div class="dataGridHolder">
        {$dgUnsentMailings}
      </div>
      {/option:dgUnsentMailings}
      {option:!dgUnsentMailings}
      <p>{$msgNoUnsentMailings}</p>
      {/option:!dgUnsentMailings}
    </div>
  </div>
  {option:dgQueuedMailings}
  <div class="row fork-module-heading">
    <div class="col-md-12">
      <h2>{$lblQueuedMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      {$dgQueuedMailings}
    </div>
  </div>
  {/option:dgQueuedMailings}
  {option:dgSentMailings}
  <div class="row fork-module-heading">
    <div class="col-md-12">
      <h2>{$lblSentMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>
    </div>
  </div>
  <div class="row fork-module-content">
    <div class="col-md-12">
      {$dgSentMailings}
    </div>
  </div>
  {/option:dgSentMailings}
</form>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
