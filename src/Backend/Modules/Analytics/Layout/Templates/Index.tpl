{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
  <h2>{$lblAnalytics|ucfirst}</h2>
</div>

<div class="box">
  <div class="heading">
    <h3>{$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}</h3>
  </div>

  <div class="footer oneLiner">
    {form:dates}
      <p>
        <label for="startDate" class="control-label">{$lblStartDate|ucfirst}</label>
        {$txtStartDate}
      </p>
      <p>
        <label for="endDate" class="control-label">{$lblEndDate|ucfirst}</label>
        {$txtEndDate}
      </p>
      <p>
        <input id="update" type="submit" name="update" value="{$lblChangePeriod|ucfirst}" />
      </p>
      {$txtStartDateError}
      {$txtEndDateError}
    {/form:dates}
  </div>
  <div class="options">
    <div class="analyticsColWrapper clearfix">
      <div class="analyticsCol">
        <p><strong>{$page_views}</strong> {$lblPageviews|ucfirst}</p>
        <p><strong>{$visitors}</strong> {$lblVisitors|ucfirst}</p>
      </div>
      <div class="analyticsCol">
        <p><strong>{$pages_per_visit|formatfloat}</strong> {$lblPagesPerVisit|ucfirst}</a></p>
        <p><strong>{$time_on_site|formattime}</strong> {$lblAverageTimeOnSite|ucfirst}</p>
      </div>
      <div class="analyticsCol">
        <p><strong>{$new_sessions_percentage|formatfloat}%</strong> {$lblNewVisitsPercentage|ucfirst}</p>
        <p><strong>{$bounce_rate|formatfloat}%</strong> {$lblBounceRate|ucfirst}</p>
      </div>
    </div>
  </div>

  <div class="options content">
    <div class="analyticsGraphWrapper">
      <div class="analyticsLeftCol">
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
      <div class="analyticsRightCol">
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
  </div>
  <div class="options content">
    <div class="dataGridHolder">
      {$dataGridMostViewedPages}
    </div>
  </div>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
