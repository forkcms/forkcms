{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}
<div class="row fork-module-heading">
	<div class="col-md-12">
		<h2>{$lblAnalytics|ucfirst}</h2>
	</div>
</div>
{option:warnings}
<div class="row fork-module-messages">
	<div class="col-md-12">
		<div class="alert alert-warning" role="alert">
			<p><strong>{$msgConfigurationError}</strong></p>
			<ul>
				{iteration:warnings}
				<li>{$warnings.message}</li>
				{/iteration:warnings}
			</ul>
		</div>
	</div>
</div>
{/option:warnings}

{option:!warnings}
<div class="row fork-module-messages">
	<div class="col-md-12">
		{option:!dataAvailable}
		<div class="alert alert-warning">
			<p>{$msgNoData}</p>
		</div>
		{/option:!dataAvailable}
		<div class="panel panel-default">
			{include:{$BACKEND_MODULE_PATH}/Layout/Templates/Period.tpl}
			<div class="panel-body">
				<div class="options content">
					<div class="analyticsColWrapper clearfix">
						<div class="analyticsCol">
							<p><strong>{$pageviews} </strong><a href="{$googlePageviewsURL}">{$lblPageviews|ucfirst}</a></p>
							<p><strong>{$visitors} </strong><a href="{$googleVisitorsURL}">{$lblVisitors|ucfirst}</a></p>
						</div>
						<div class="analyticsCol">
							<p><strong>{$pagesPerVisit} </strong><a href="{$googleAveragePageviewsURL}">{$lblPagesPerVisit|ucfirst}</a> <small>({$pagesPerVisitDifference}%)</small></p>
							<p><strong>{$timeOnSite} </strong><a href="{$googleTimeOnSiteURL}">{$lblAverageTimeOnSite|ucfirst}</a> <small>({$timeOnSiteDifference}%)</small></p>
						</div>
						<div class="analyticsCol">
							<p><strong>{$newVisits}% </strong><a href="{$googleVisitorTypesURL}">{$lblNewVisitsPercentage|ucfirst}</a> <small>({$newVisitsDifference}%)</small></p>
							<p><strong>{$bounces}% </strong><a href="{$googleBouncesURL}">{$lblBounceRate|ucfirst}</a> <small>({$bouncesDifference}%)</small></p>
						</div>
					</div>
				</div>

				<div class="options content">
					<div class="analyticsGraphWrapper">
						<div class="analyticsLeftCol">
							<div class="box boxLevel2">
								<div class="heading">
									<h3><a href="{$googleVisitorsURL}">{$lblRecentVisits|ucfirst}</a></h3>
									<div class="buttonHolderRight">
										<a class="button icon iconGoto linkButton" href="{$googleVisitorsURL}"><span>{$lblViewReport|ucfirst}</span></a>
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

						<div class="analyticsRightCol">
							<div class="box boxLevel2">
								<div class="heading">
									<h3><a href="{$googleTrafficSourcesURL}">{$lblPageviewsByTrafficSources|ucfirst}</a></h3>
									<div class="buttonHolderRight">
										<a class="button icon iconGoto linkButton" href="{$googleTrafficSourcesURL}"><span>{$lblViewReport|ucfirst}</span></a>
									</div>
								</div>
								<div class="options">
									{option:pieGraphData}
										<div id="dataChartPieChart" class="hidden">
											<ul class="data">
												{iteration:pieGraphData}
													<li><span class="label">{$pieGraphData.label}</span><span class="value">{$pieGraphData.value}</span><span class="percentage">{$pieGraphData.percentage}</span></li>
												{/iteration:pieGraphData}
											</ul>
										</div>
										<div id="chartPieChart">&nbsp;</div>
									{/option:pieGraphData}
									<div class="buttonHolderRight">
										<a href="http://highcharts.com/" class="analyticsBacklink">Highcharts</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="box">



		</div>

		<div class="dataGridHolder" id="analyticsDataGridLeftCol">
			<div class="tableHeading">
				<h3><a href="{$googleTopReferrersURL}">{$lblTopReferrers|ucfirst}</a></h3>
				<div class="buttonHolderRight">
					<a class="button icon iconGoto linkButton" href="{$googleTopReferrersURL}"><span>{$lblViewReport|ucfirst}</span></a>
				</div>
			</div>

			{* Top referrers *}
			{option:dgReferrers}
				{$dgReferrers}
			{/option:dgReferrers}
			{option:!dgReferrers}
				<table class="dataGrid">
					<tr>
						<td>{$msgNoReferrers}</td>
					</tr>
				</table>
			{/option:!dgReferrers}
		</div>

		<div class="dataGridHolder" id="analyticsDataGridRightCol">
			<div class="tableHeading">
				<h3><a href="{$googleTopKeywordsURL}">{$lblTopKeywords|ucfirst}</a></h3>
				<div class="buttonHolderRight">
					<a class="button icon iconGoto linkButton" href="{$googleTopKeywordsURL}"><span>{$lblViewReport|ucfirst}</span></a>
				</div>
			</div>

			{* Top keywords *}
			{option:dgKeywords}
				{$dgKeywords}
			{/option:dgKeywords}
			{option:!dgKeywords}
				<table class="dataGrid">
					<tr>
						<td>{$msgNoKeywords}</td>
					</tr>
				</table>
			{/option:!dgKeywords}
		</div>
	</div>
</div>
{/option:!warnings}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
