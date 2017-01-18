{option:visitors_graph_data}
  <div class="box" id="widgetAnalyticsRecentVisits">
    <div class="heading">
      <h3>{$lblRecentVisits|ucfirst}</h3>
    </div>
    <div class="options">
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
    <div class="footer">
      <div class="buttonHolderRight">
        <a href="{$var|geturl:'Index':'Analytics'}" class="button"><span>{$lblAllStatistics|ucfirst}</span></a>
      </div>
    </div>
  </div>
{/option:visitors_graph_data}
