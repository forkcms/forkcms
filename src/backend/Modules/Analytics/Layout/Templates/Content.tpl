{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblContent|ucfirst}</h2>
</div>

{option:!dataAvailable}
	<div class="generalMessage infoMessage content singleMessage">
		<p><strong>{$msgNoData}</strong></p>
	</div>
{/option:!dataAvailable}

<div class="box">
	{include:{$BACKEND_MODULE_PATH}/layout/templates/period.tpl}

	<div class="options content">
		<div class="analyticsColWrapper">
			<div class="analyticsCol">
				<p><strong>{$pageviews} </strong><a href="{$googleContentURL}">{$lblPageviews|ucfirst}</a></p>
				<p><strong>{$uniquePageviews} </strong><a href="{$googleContentURL}">{$lblUniquePageviews|ucfirst}</a></p>
			</div>
			<div class="analyticsCol">
				<p><strong>{$newVisits}% </strong><a href="{$googleContentURL}">{$lblNewVisitsPercentage|ucfirst}</a> <small>({$newVisitsDifference}%)</small></p>
				<p><strong>{$bounces}% </strong><a href="{$googleContentURL}">{$lblBounceRate|ucfirst}</a> <small>({$bouncesDifference}%)</small></p>
			</div>
		</div>
	</div>

	<div class="options content">
		<div class="analyticsGraphWrapper">
			<div class="box boxLevel2">
				<div class="heading">
					<h3><a href="{$googleContentURL}">{$lblVisits|ucfirst}</a></h3>
					<div class="buttonHolderRight">
						<a class="button icon iconGoto linkButton" href="{$googleContentURL}"><span>{$lblViewReport|ucfirst}</span></a>
					</div>
				</div>
				<div class="options">
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
					<div class="buttonHolderRight">
						<a href="http://highcharts.com/" class="analyticsBacklink">Highcharts</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{* Top pages *}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>
			{option:showAnalyticsAllPages}<a href="{$var|geturl:'all_pages'}">{/option:showAnalyticsAllPages}
				{$lblTopPages|ucfirst}
			{option:showAnalyticsAllPages}</a>{/option:showAnalyticsAllPages}
		</h3>

		<div class="buttonHolderRight">
			<a class="button icon iconGoto linkButton" href="{$googleTopContentURL}"><span>{$lblViewReport|ucfirst}</span></a>
		</div>
	</div>

	{option:dgContent}
		{$dgContent}
	{/option:dgContent}

	{option:!dgContent}
		<table class="dataGrid">
			<tr>
				<td>{$msgNoContent}</td>
			</tr>
		</table>
	{/option:!dgContent}
</div>

{* Top exit pages *}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>
			{option:showAnalyticsExitPages}<a href="{$var|geturl:'exit_pages'}">{/option:showAnalyticsExitPages}
				{$lblTopExitPages|ucfirst}
			{option:showAnalyticsExitPages}</a>{/option:showAnalyticsExitPages}
		</h3>

		<div class="buttonHolderRight">
			<a class="button icon iconGoto linkButton" href="{$googleTopExitPagesURL}"><span>{$lblViewReport|ucfirst}</span></a>
		</div>
	</div>

	{option:dgExitPages}
		{$dgExitPages}
	{/option:dgExitPages}

	{option:!dgExitPages}
		<table class="dataGrid">
			<tr>
				<td>{$msgNoExitPages}</td>
			</tr>
		</table>
	{/option:!dgExitPages}
</div>

{* Top entry pages *}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>
			{option:showAnalyticsLandingPages}<a href="{$var|geturl:'landing_pages'}">{/option:showAnalyticsLandingPages}
				{$lblTopLandingPages|ucfirst}
			{option:showAnalyticsLandingPages}</a>{/option:showAnalyticsLandingPages}
		</h3>

		<div class="buttonHolderRight">
			<a class="button icon iconGoto linkButton" href="{$googleTopLandingPagesURL}"><span>{$lblViewReport|ucfirst}</span></a>
		</div>
	</div>

	{option:dgLandingPages}
		{$dgLandingPages}
	{/option:dgLandingPages}

	{option:!dgLandingPages}
		<table class="dataGrid">
			<tr>
				<td>{$msgNoLandingPages}</td>
			</tr>
		</table>
	{/option:!dgLandingPages}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}