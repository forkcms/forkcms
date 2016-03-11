{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-6">
    <h2>{$lblStatistics|ucfirst} {$lblFor} &ldquo;{$mailing.name}&rdquo;</h2>
  </div>
  <div class="col-md-6">
    <div class="btn-toolbar pull-right">
      <div class="btn-group" role="group">
        {option:showMailmotorIndex}
        <a href="{$var|geturl:'index'}" class="btn btn-default" title="{$lblNewsletters|ucfirst}">
          <span class="fa fa-list"></span>
          {$msgBackToMailings|sprintf:{$mailing.name}}
        </a>
        {/option:showMailmotorIndex}
        {option:showMailmotorExportStatistics}
        <a href="{$var|geturl:'export_statistics'}&amp;id={$mailing.id}" class="btn btn-default" title="{$lblExportStatistics|ucfirst}">
          <span class="fa fa-upload"></span>
          {$lblExportStatistics|ucfirst}
        </a>
        {/option:showMailmotorExportStatistics}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblOpenedMailings|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <div id="dataChartPieChart" class="hidden">
          <ul class="data">
            {option:stats.unique_opens}<li><span class="label">{$lblOpenedMailings|ucfirst}</span><span class="value">{$stats.unique_opens}</span><span class="percentage">{$stats.unique_opens_percentage}</span></li>{/option:stats.unique_opens}
            {option:stats.unopens}<li><span class="label">{$lblUnopenedMailings|ucfirst}</span><span class="value">{$stats.unopens}</span><span class="percentage">{$stats.unopens_percentage}</span></li>{/option:stats.unopens}
            {option:stats.bounces}<li><span class="label">{$lblBounces|ucfirst}</span><span class="value">{$stats.bounces}</span><span class="percentage">{$stats.bounces_percentage}</span></li>{/option:stats.bounces}
          </ul>
        </div>
        <div id="chartPieChart">&nbsp;</div>
      </div>
      <div class="panel-footer">
        <div class="btn-toolbar">
          <div class="btn-group pull-right" role="group">
            <a href="http://highcharts.com/" class="btn analyticsBacklink">{$lblHighcharts}</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          {$lblSummary|ucfirst}
        </h3>
      </div>
      <div class="panel-body">
        <p><strong>{$stats.recipients}</strong> {$lblSentMailings|ucfirst} <small>(100%)</small></p>
        <p><strong>{$stats.unique_opens}</strong> {$lblOpenedMailings|ucfirst} <small>({$stats.unique_opens_percentage})</small></p>
        <p><strong>{$stats.unopens}</strong> {$lblUnopenedMailings|ucfirst} <small>({$stats.unopens_percentage})</small></p>
        <p><strong>{$stats.clicks_total}</strong> <a href="#clicks">{$lblClicks|ucfirst}</a> <small>({$stats.clicks_percentage})</small></p>
        {option:stats.bounces}<p><strong>{$stats.bounces}/{$stats.recipients_total} </strong><a href="{$var|geturl:'statistics_bounces'}&amp;mailing_id={$mailing.id}">{$lblBounceRate|ucfirst}</a> <small>({$stats.bounces_percentage})</small></p>{/option:stats.bounces}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <h3>{$msgMailingLinks|ucfirst}</h3>
    {option:dataGrid}
    {$dataGrid}
    {/option:dataGrid}
    {option:!dataGrid}
    <p>{$msgNoItems}</p>
    {/option:!dataGrid}
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
