{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblCampaigns|ucfirst}</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorAddCampaign}
        <a href="{$var|geturl:'add_campaign'}" class="btn btn-default" title="{$lblAddCampaign|ucfirst}">
          <span class="fa fa-plus"></span>&nbsp;
          {$lblAddCampaign|ucfirst}
        </a>
        {/option:showMailmotorAddCampaign}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    <form action="{$var|geturl:'mass_campaign_action'}" method="get" class="forkForms submitWithLink" id="campaigns">
      {$dataGrid}
    </form>
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoItems}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
