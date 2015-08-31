{option:source_graph_data}
  <div class="box" id="widgetAnalyticsTraficSources">
    <div class="heading">
      <h3><a href="{$googleTrafficSourcesURL}">{$lblPageviewsByTrafficSources|ucfirst}</a></h3>
    </div>
    <div class="options">
      <div id="dataChartPieChart" class="hidden">
        <ul class="data">
          {iteration:source_graph_data}
          <li><span class="label">{$source_graph_data.ga_medium}</span><span class="value">{$source_graph_data.ga_pageviews}</span></li>
          {/iteration:source_graph_data}
        </ul>
      </div>
      <div id="chartPieChart">&nbsp;</div>
    </div>
    <div class="footer">
      <div class="buttonHolderRight">
        <a href="{$var|geturl:'Index':'Analytics'}" class="button"><span>{$lblAllStatistics|ucfirst}</span></a>
      </div>
    </div>
  </div>
{/option:source_graph_data}
