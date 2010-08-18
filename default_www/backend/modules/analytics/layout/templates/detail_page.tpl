{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2><a href="{$pagePath}">{$pagePath}</a></h2>
</div>

<div class="box">
	{include:file='{$BACKEND_MODULE_PATH}/layout/templates/period.tpl'}

	<div class="options content">
		<div class="analyticsColWrapper">
			<div class="analyticsCol">
				<p><strong>{$pageviews} </strong><a href="{$googleContentDetailURL}">{$lblPageviews|ucfirst}</a></p>
				<p><strong>{$visits} </strong><a href="{$googleContentDetailURL}">{$lblVisits|ucfirst}</a></p>
			</div>
			<div class="analyticsCol">
				<p><strong>{$pagesPerVisit} </strong><a href="{$googleContentDetailURL}">{$lblAnalyticsPagesPerVisit|ucfirst}</a> <small>({$pagesPerVisitDifference}%)</small></p>
				<p><strong>{$timeOnPage} </strong><a href="{$googleContentDetailURL}">{$lblAverageTimeOnPage|ucfirst}</a> <small>({$timeOnPageDifference}%)</small></p>
			</div>
			<div class="analyticsCol">
				<p><strong>{$newVisits}% </strong><a href="{$googleContentDetailURL}">{$lblNewVisitsPercentage|ucfirst}</a> <small>({$newVisitsDifference}%)</small></p>
				<p><strong>{$bounces}% </strong><a href="{$googleContentDetailURL}">{$lblBounceRate|ucfirst}</a> <small>({$bouncesDifference}%)</small></p>
			</div>
		</div>
	</div>

	<div class="options content">
		<div class="analyticsGraphWrapper">
			<div class="box boxLevel2">
				<div class="heading">
					<h3><a href="{$googleContentDetailURL}">{$lblPageviews|ucfirst}</a></h3>
				</div>
				<div class="options">
					{option:lineGraphData}
						<div id="dataChartSingleMetricPerDay" class="hidden">
							<span id="maxYAxis">{$maxYAxis}</span>
							<span id="tickInterval">{$tickInterval}</span>
							<span id="yAxisTitle">{$lblPageviews|ucfirst}</span>
							<ul class="series">
								{iteration:lineGraphData}
									<li class="serie" id="metricserie">
										<span class="name">{$lineGraphData.label}</span>
										<ul class="data">
											{iteration:lineGraphData.data}
												<li>
													<span class="fulldate">{$data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
													<span class="date">{$data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
													<span class="value">{$data.value}</span>
												</li>
											{/iteration:lineGraphData.data}
										</ul>
									</li>
								{/iteration:lineGraphData}
							</ul>
						</div>
						<div id="chartSingleMetricPerDay">&nbsp;</div>
					{/option:lineGraphData}
					<div class="buttonHolderRight">
						<a href="http://highcharts.com/" class="analyticsBacklink">Highcharts</a>
						<a class="button icon iconGoto linkButton" href="{$googleContentDetailURL}"><span>{$lblViewReport|ucfirst}</span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}