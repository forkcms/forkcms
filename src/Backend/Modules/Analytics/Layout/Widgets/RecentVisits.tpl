{option:visitors_graph_data}
<div id="widgetAnalyticsRecentVisits" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      {$lblRecentVisits|ucfirst}
    </h2>
  </div>
  <div class="panel-body">
    <div id="dataChartDoubleMetricPerDay" class="hidden">
      <span id="maxYAxis">{$maxYAxis}</span>
      <span id="tickInterval">{$tickInterval}</span>
      <span id="yAxisTitle">{$lblVisits|ucfirst}</span>
      <ul class="series">
        <li class="serie" id="metric1serie">
          <span class="name">Pageviews</span>
          <ul class="data">
            {iteration:visitors_graph_data}
            <li>
              <span class="fulldate">{$visitors_graph_data.ga_date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
              <span class="date">{$visitors_graph_data.ga_date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
              <span class="value">{$visitors_graph_data.ga_pageviews}</span>
            </li>
            {/iteration:visitors_graph_data}
          </ul>
        </li>
        <li class="serie" id="metric2serie">
          <span class="name">Visitors</span>
          <ul class="data">
            {iteration:visitors_graph_data}
            <li>
              <span class="fulldate">{$visitors_graph_data.ga_date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
              <span class="date">{$visitors_graph_data.ga_date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
              <span class="value">{$visitors_graph_data.ga_users}</span>
            </li>
            {/iteration:visitors_graph_data}
          </ul>
        </li>
      </ul>
    </div>
    <div id="chartDoubleMetricPerDay">&nbsp;</div>
  </div>
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'Index':'Analytics'}" class="btn">{$lblAllStatistics|ucfirst}</a>
      </div>
    </div>
  </div>
</div>
{/option:visitors_graph_data}
