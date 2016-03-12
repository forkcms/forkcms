{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblAnalytics|ucfirst}</h2>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    {form:dates}
      <div class="panel panel-default">
        <div class="panel-heading">
          {$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="startDate">{$lblStartDate|ucfirst}</label>
                {option:txtStartDateError}
                <p class="text-danger">{$txtStartDateError}</p>
                {/option:txtStartDateError}
                {$txtStartDate}
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="endDate">{$lblEndDate|ucfirst}</label>
                {option:txtEndDateError}
                <p class="text-danger">{$txtEndDateError}</p>
                {/option:txtEndDateError}
                {$txtEndDate}
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer">
          <div class="btn-toolbar">
            <div class="btn-group pull-right">
              <button id="update" type="submit" name="update" class="btn btn-primary">
                {$lblChangePeriod|ucfirst}
              </button>
            </div>
          </div>
        </div>
      </div>
    {/form:dates}
  </div>
</div>
<div class="row fork-module-content analyticsGraphWrapper">
  <div class="col-md-6 analyticsLeftCol">
    <div class="box boxLevel2">
      <div class="heading">
        <h3>{$lblRecentVisits|ucfirst}</h3>
      </div>
      <div class="options">
        {option:visitors_graph_data}
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
        {/option:visitors_graph_data}
      </div>
    </div>
  </div>
  <div class="col-md-6 analyticsRightCol">
    <div class="box boxLevel2">
      <div class="heading">
        <h3><a href="{$googleTrafficSourcesURL}">{$lblPageviewsByTrafficSources|ucfirst}</a></h3>
      </div>
      <div class="options">
        {option:source_graph_data}
        <div id="dataChartPieChart" class="hidden">
          <ul class="data">
            {iteration:source_graph_data}
            <li><span class="label">{$source_graph_data.ga_medium}</span><span class="value">{$source_graph_data.ga_pageviews}</span></li>
            {/iteration:source_graph_data}
          </ul>
        </div>
        <div id="chartPieChart">&nbsp;</div>
        {/option:source_graph_data}
      </div>
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default jsDataGridHolder">
      <div class="panel-heading">
        <h3 class="panel-title">{$lblMostViewedPages|ucfirst}</h3>
      </div>
      {$dataGridMostViewedPages}
    </div>
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
