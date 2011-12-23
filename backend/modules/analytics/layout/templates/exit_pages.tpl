{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblExitPages|ucfirst}</h2>
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
				<p><strong>{$exits} </strong><a href="{$googleTopExitPagesURL}">{$lblExits|ucfirst}</a></p>
				<p><strong>{$pageviews} </strong><a href="{$googleTopExitPagesURL}">{$lblPageviews|ucfirst}</a></p>
			</div>
			<div class="analyticsCol">
				<p><strong>{$exitsPercentage}% </strong><a href="{$googleTopExitPagesURL}">{$lblExitRate|ucfirst}</a></p>
			</div>
		</div>
	</div>

	<div class="options content">
		<div class="analyticsGraphWrapper">
			<div class="box boxLevel2">
				<div class="heading">
					<h3><a href="{$googleTopExitPagesURL}">{$lblExits|ucfirst}</a></h3>
					<div class="buttonHolderRight">
						<a class="button icon iconGoto linkButton" href="{$googleTopExitPagesURL}"><span>{$lblViewReport|ucfirst}</span></a>
					</div>
				</div>
				<div class="options">
					{option:graphData}
						<div id="dataChartSingleMetricPerDay" class="hidden">
							<span id="maxYAxis">{$maxYAxis}</span>
							<span id="tickInterval">{$tickInterval}</span>
							<span id="yAxisTitle">{$lblExits|ucfirst}</span>
							<ul class="series">
								{iteration:graphData}
									<li class="serie" id="metricserie">
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
						<div id="chartSingleMetricPerDay">&nbsp;</div>
					{/option:graphData}
					<div class="buttonHolderRight">
						<a href="http://highcharts.com/" class="analyticsBacklink">Highcharts</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{* Exit pages *}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblExitPages|ucfirst}</h3>
		<div class="buttonHolderRight">
			<a class="button icon iconGoto linkButton" href="{$googleTopExitPagesURL}"><span>{$lblViewReport|ucfirst}</span></a>
		</div>
	</div>

	{option:dgPages}
		{$dgPages}
	{/option:dgPages}

	{option:!dgPages}
		<table class="dataGrid">
			<tr>
				<td>{$msgNoPages}</td>
			</tr>
		</table>
	{/option:!dgPages}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}