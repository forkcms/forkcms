{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblBounces|ucfirst} {$lblFor} &ldquo;{$mailing.name}&rdquo;</h2>
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorStatistics}
        <a href="{$var|geturl:'statistics'}&amp;id={$mailing.id}" class="btn btn-default" title="{$lblStatistics|ucfirst}">
          <span class="fa fa-bar-chart-o"></span>
          {$msgBackToStatistics|sprintf:{$mailing.name}}
        </a>
        {/option:showMailmotorStatistics}
        {option:showMailmotorDeleteBounces}
        <a href="{$var|geturl:'delete_bounces'}&amp;mailing_id={$mailing.id}" class="btn btn-danger" title="{$msgDeleteBounces|ucfirst}">
          <span class="fa fa-trash-o"></span>
          {$msgDeleteBounces|ucfirst}
        </a>
        {/option:showMailmotorDeleteBounces}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:dataGrid}
    <form action="{$var|geturl:'mass_bounces_action'}" method="get" class="forkForms submitWithLink" id="bounces">
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
