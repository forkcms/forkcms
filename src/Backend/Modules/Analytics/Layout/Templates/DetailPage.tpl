{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2><a href="{$pagePath}">{$pagePath}</a></h2>
</div>

{option:!dataAvailable}
	<div class="generalMessage infoMessage content singleMessage">
		<p><strong>{$msgNoData}</strong></p>
	</div>
{/option:!dataAvailable}

<div class="box">
	{include:{$BACKEND_MODULE_PATH}/Layout/Templates/Period.tpl}

	<div class="options content">
		<div class="analyticsColWrapper">
			<div class="analyticsCol">
				<p><strong>{$pageviews} </strong><a href="{$googleContentDetailURL}">{$lblPageviews|ucfirst}</a></p>
				<p><strong>{$visits} </strong><a href="{$googleContentDetailURL}">{$lblVisits|ucfirst}</a></p>
			</div>
			<div class="analyticsCol">
				<p><strong>{$pagesPerVisit} </strong><a href="{$googleContentDetailURL}">{$lblPagesPerVisit|ucfirst}</a> <small>({$pagesPerVisitDifference}%)</small></p>
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
													<span class="fulldate">{$lineGraphData.data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
													<span class="date">{$lineGraphData.data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
													<span class="value">{$lineGraphData.data.value}</span>
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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
