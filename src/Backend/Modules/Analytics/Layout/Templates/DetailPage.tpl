{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2><a href="{$pagePath}">{$pagePath}</a></h2>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {option:!dataAvailable}
    <div class="alert alert-warning">
      <p>{$msgNoData}</p>
    </div>
    {/option:!dataAvailable}
    {include:{$BACKEND_MODULE_PATH}/Layout/Templates/Period.tpl}
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-4">
    <p>
      <strong>{$pageviews} </strong>
      <a href="{$googleContentDetailURL}">{$lblPageviews|ucfirst}</a>
    </p>
    <p>
      <strong>{$visits} </strong>
      <a href="{$googleContentDetailURL}">{$lblVisits|ucfirst}</a>
    </p>
  </div>
  <div class="col-md-4">
    <p>
      <strong>{$pagesPerVisit} </strong>
      <a href="{$googleContentDetailURL}">{$lblPagesPerVisit|ucfirst}</a>
      <small>({$pagesPerVisitDifference}%)</small>
    </p>
    <p>
      <strong>{$timeOnPage} </strong>
      <a href="{$googleContentDetailURL}">{$lblAverageTimeOnPage|ucfirst}</a>
      <small>({$timeOnPageDifference}%)</small>
    </p>
  </div>
  <div class="col-md-4">
    <p>
      <strong>{$newVisits}% </strong>
      <a href="{$googleContentDetailURL}">{$lblNewVisitsPercentage|ucfirst}</a>
      <small>({$newVisitsDifference}%)</small>
    </p>
    <p>
      <strong>{$bounces}% </strong><a href="{$googleContentDetailURL}">{$lblBounceRate|ucfirst}</a>
      <small>({$bouncesDifference}%)</small>
    </p>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleContentDetailURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleContentDetailURL}">{$lblPageviews|ucfirst}</a>
        </h3>
      </div>
      <div class="panel-body">
        {option:lineGraphData}
        <div id="dataChartSingleMetricPerDay" class="hidden">
          <span id="maxYAxis">{$maxYAxis}</span>
          <span id="tickInterval">{$tickInterval}</span>
          <span id="yAxisTitle">{$lblPageviews|ucfirst}</span>
          <ul class="series">
            {iteration:lineGraphData}
            <li class="serie" id="metricserie">
              <span class="name">{$lineGraphData.label}</span>
              <ul class="data">
                {iteration:lineGraphData.data}
                <li>
                  <span class="fulldate">{$lineGraphData.data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
                  <span class="date">{$lineGraphData.data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
                  <span class="value">{$lineGraphData.data.value}</span>
                </li>
                {/iteration:lineGraphData.data}
              </ul>
            </li>
            {/iteration:lineGraphData}
          </ul>
        </div>
        <div id="chartSingleMetricPerDay">&nbsp;</div>
        {/option:lineGraphData}
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
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
