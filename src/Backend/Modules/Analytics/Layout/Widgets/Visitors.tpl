{option:analyticsValidSettings}
<div id="widgetAnalyticsVisitors" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$var|geturl:'index':'analytics'}">
        {$lblRecentVisits|ucfirst} {$lblFrom}
        {$analyticsRecentVisitsStartDate|date:'j-m':{$INTERFACE_LANGUAGE}} {$lblTill}
        {$analyticsRecentVisitsEndDate|date:'j-m':{$INTERFACE_LANGUAGE}}
      </a>
    </h2>
  </div>
  <div class="panel-body">
    {option:analyticsGraphData}
    <div id="dataChartWidget" class="hidden">
      <span id="maxYAxis">{$analyticsMaxYAxis}</span>
      <span id="tickInterval">{$analyticsTickInterval}</span>
      <span id="yAxisTitle">{$lblPageviews|ucfirst} / {$lblVisitors|ucfirst}</span>
      <ul class="series">
        {iteration:analyticsGraphData}
        <li class="serie" id="metric{$analyticsGraphData.i}serie">
          <span class="name">{$analyticsGraphData.label}</span>
          <ul class="data">
            {iteration:analyticsGraphData.data}
            <li>
              <span class="fulldate">{$analyticsGraphData.data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
              <span class="date">{$analyticsGraphData.data.date|date:'D':{$INTERFACE_LANGUAGE}|ucfirst}</span>
              <span class="value">{$analyticsGraphData.data.value}</span>
            </li>
            {/iteration:analyticsGraphData.data}
          </ul>
        </li>
        {/iteration:analyticsGraphData}
      </ul>
    </div>
    <div id="chartWidget">&nbsp;</div>
    <p>
      <a href="http://highcharts.com/" class="analyticsBacklink">Highcharts</a>
    </p>
    {/option:analyticsGraphData}
    {option:!analyticsGraphData}
    <p class="analyticsFallback">
      <a href="{$var|geturl:'index':'analytics'}" class="linkedImage">
        <img src="{$SITE_URL}/src/Backend/Modules/Analytics/Layout/images/analytics_widget_{$INTERFACE_LANGUAGE}.jpg" alt="" />
      </a>
    </p>
    {/option:!analyticsGraphData}
  </div>
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'index':'analytics'}" class="btn"><span>{$lblAllStatistics|ucfirst}</span></a>
      </div>
    </div>
  </div>
</div>
{/option:analyticsValidSettings}
