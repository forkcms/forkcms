{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
  <div class="col-md-12">
    <h2>{$lblContent|ucfirst}</h2>
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
  <div class="col-md-6">
    <p>
      <strong>{$pageviews} </strong>
      <a href="{$googleContentURL}">{$lblPageviews|ucfirst}</a>
    </p>
    <p>
      <strong>{$uniquePageviews} </strong>
      <a href="{$googleContentURL}">{$lblUniquePageviews|ucfirst}</a>
    </p>
  </div>
  <div class="col-md-6">
    <p>
      <strong>{$newVisits}% </strong>
      <a href="{$googleContentURL}">{$lblNewVisitsPercentage|ucfirst}</a>
      <small>({$newVisitsDifference}%)</small>
    </p>
    <p>
      <strong>{$bounces}% </strong>
      <a href="{$googleContentURL}">{$lblBounceRate|ucfirst}</a>
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
            <a href="{$googleContentURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          <a href="{$googleContentURL}">{$lblVisits|ucfirst}</a>
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
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTopContentURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          {option:showAnalyticsAllPages}
          <a href="{$var|geturl:'all_pages'}">
          {/option:showAnalyticsAllPages}
            {$lblTopPages|ucfirst}
          {option:showAnalyticsAllPages}
          </a>
          {/option:showAnalyticsAllPages}
        </h3>
      </div>
      {option:dgContent}
      {$dgContent}
      {/option:dgContent}
      {option:!dgContent}
      <div class="panel-body">
        <p>{$msgNoContent}</p>
      </div>
      {/option:!dgContent}
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTopExitPagesURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          {option:showAnalyticsExitPages}
          <a href="{$var|geturl:'exit_pages'}">
          {/option:showAnalyticsExitPages}
            {$lblTopExitPages|ucfirst}
          {option:showAnalyticsExitPages}
          </a>
          {/option:showAnalyticsExitPages}
        </h3>
      </div>
      {option:dgExitPages}
      {$dgExitPages}
      {/option:dgExitPages}
      {option:!dgExitPages}
      <div class="panel-body">
        <p>{$msgNoExitPages}</p>
      </div>
      {/option:!dgExitPages}
    </div>
  </div>
</div>
<div class="row fork-module-content">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
        <div class="btn-toolbar pull-right">
          <div class="btn-group" role="group">
            <a href="{$googleTopExitPagesURL}" class="btn btn-default btn-xs" target="_blank" title="{$lblViewReport|ucfirst}">
              <span class="glyphicon glyphicon-stats"></span>
            </a>
          </div>
        </div>
        <h3 class="panel-title">
          {option:showAnalyticsLandingPages}
          <a href="{$var|geturl:'landing_pages'}">
          {/option:showAnalyticsLandingPages}
            {$lblTopLandingPages|ucfirst}
          {option:showAnalyticsLandingPages}
          </a>
          {/option:showAnalyticsLandingPages}
        </h3>
      </div>
      {option:dgLandingPages}
      {$dgLandingPages}
      {/option:dgLandingPages}
      {option:!dgLandingPages}
      <div class="panel-body">
        <p>{$msgNoLandingPages}</p>
      </div>
      {/option:!dgLandingPages}
    </div>
  </div>
</div>
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
