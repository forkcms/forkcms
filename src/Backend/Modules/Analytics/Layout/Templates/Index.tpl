{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAnalytics|ucfirst}</h2>
  </div>
</div>
{option:warnings}
<div class="row fork-module-messages">
  <div class="col-md-12">
    <div class="alert alert-warning" role="alert">
      <p><strong>{$msgConfigurationError}</strong></p>
      <ul>
        {iteration:warnings}
        <li>{$warnings.message}</li>
        {/iteration:warnings}
      </ul>
    </div>
  </div>
</div>
{/option:warnings}
{option:!warnings}
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
      <a href="{$googlePageviewsURL}">{$lblPageviews|ucfirst}</a>
    </p>
    <p>
      <strong>{$visitors} </strong>
      <a href="{$googleVisitorsURL}">{$lblVisitors|ucfirst}</a>
    </p>
  </div>
  <div class="col-md-4">
    <p>
      <strong>{$pagesPerVisit} </strong>
      <a href="{$googleAveragePageviewsURL}">{$lblPagesPerVisit|ucfirst}</a>
      <small>({$pagesPerVisitDifference}%)</small>
    </p>
    <p>
      <strong>{$timeOnSite} </strong>
      <a href="{$googleTimeOnSiteURL}">{$lblAverageTimeOnSite|ucfirst}</a>
      <small>({$timeOnSiteDifference}%)</small>
    </p>
  </div>
  <div class="col-md-4">
    <p>
      <strong>{$newVisits}% </strong>
      <a href="{$googleVisitorTypesURL}">{$lblNewVisitsPercentage|ucfirst}</a>
      <small>({$newVisitsDifference}%)</small>
    </p>
    <p>
      <strong>{$bounces}% </strong>
      <a href="{$googleBouncesURL}">{$lblBounceRate|ucfirst}</a>
      <small>({$bouncesDifference}%)</small>
    </p>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleVisitorsURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleVisitorsURL}">{$lblRecentVisits|ucfirst}</a>
        </h3>
      </div>
      <div class="panel-body">
        {option:graphData}
        <div id="dataChartDoubleMetricPerDay" class="hidden">
          <span id="maxYAxis">{$maxYAxis}</span>
          <span id="tickInterval">{$tickInterval}</span>
          <span id="yAxisTitle">{$lblVisits|ucfirst}</span>
          <ul class="series">
            {iteration:graphData}
            <li class="serie" id="metric{$graphData.i}serie">
              <span class="name">{$graphData.label}</span>
              <ul class="data">
                {iteration:graphData.data}
                <li>
                  <span class="fulldate">{$graphData.data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
                  <span class="date">{$graphData.data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
                  <span class="value">{$graphData.data.value}</span>
                </li>
                {/iteration:graphData.data}
              </ul>
            </li>
            {/iteration:graphData}
          </ul>
        </div>
        <div id="chartDoubleMetricPerDay">&nbsp;</div>
        {/option:graphData}
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
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTrafficSourcesURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleTrafficSourcesURL}">{$lblPageviewsByTrafficSources|ucfirst}</a>
        </h3>
      </div>
      <div class="panel-body">
        {option:pieGraphData}
        <div id="dataChartPieChart" class="hidden">
          <ul class="data">
            {iteration:pieGraphData}
              <li>
                <span class="label">{$pieGraphData.label}</span>
                <span class="value">{$pieGraphData.value}</span>
                <span class="percentage">{$pieGraphData.percentage}</span>
              </li>
            {/iteration:pieGraphData}
          </ul>
        </div>
        <div id="chartPieChart">&nbsp;</div>
        {/option:pieGraphData}
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
<div class="row fork-module-content">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTopReferrersURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleTopReferrersURL}">{$lblTopReferrers|ucfirst}</a>
        </h3>
      </div>
      {option:dgReferrers}
      {$dgReferrers}
      {/option:dgReferrers}
      {option:!dgReferrers}
      <div class="panel-body">
        <p>{$msgNoReferrers}</p>
      </div>
      {/option:!dgReferrers}
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTopKeywordsURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleTopKeywordsURL}">{$lblTopKeywords|ucfirst}</a>
        </h3>
      </div>
      {option:dgKeywords}
      {$dgKeywords}
      {/option:dgKeywords}
      {option:!dgKeywords}
      <div class="panel-body">
        <p>{$msgNoKeywords}</p>
      </div>
      {/option:!dgKeywords}
    </div>
  </div>
</div>
{/option:!warnings}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
