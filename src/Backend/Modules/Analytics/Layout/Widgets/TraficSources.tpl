{option:source_graph_data}
<div id="widgetAnalyticsTraficSources" class="panel panel-primary">
  <div class="panel-heading">
    <h2 class="panel-title">
      <a href="{$googleTrafficSourcesURL}">{$lblPageviewsByTrafficSources|ucfirst}</a>
    </h2>
  </div>
  <div class="panel-body">
    <div id="dataChartPieChart" class="hidden">
      <ul class="data">
        {iteration:source_graph_data}
        <li><span class="label">{$source_graph_data.ga_medium}</span><span class="value">{$source_graph_data.ga_pageviews}</span></li>
        {/iteration:source_graph_data}
      </ul>
    </div>
    <div id="chartPieChart">&nbsp;</div>
  </div>
  <div class="panel-footer">
    <div class="btn-toolbar">
      <div class="btn-group">
        <a href="{$var|geturl:'Index':'Analytics'}" class="btn">{$lblAllStatistics|ucfirst}</a>
      </div>
    </div>
  </div>
</div>
{/option:source_graph_data}
