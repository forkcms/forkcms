{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblStatistics|ucfirst} {$lblFor} &ldquo;{$campaign.name}&rdquo;</h2>

	{option:showMailmotorExportStatisticsCampaign}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'export_statistics_campaign'}&amp;id={$campaign.id}" class="button icon iconExport" title="{$lblExportStatistics|ucfirst}">
			<span>{$lblExportStatistics|ucfirst}</span>
		</a>
	</div>
	{/option:showMailmotorExportStatisticsCampaign}
</div>

<div class="box">
	<div class="heading">
		<h3>{$lblStatistics|ucfirst}</h3>
	</div>
	{*
	<div class="horizontal">
		<div class="options">
			<ul>
				<li>{$lblTotalSentMailings|ucfirst}: <strong>{$stats.recipients}</strong></li>
				<li>{$lblOpenedMailings|ucfirst}: <strong>{$stats.unique_opens}</strong> ({$stats.unique_opens_percentage})</li>
				<li>{$lblUnopenedMailings|ucfirst}: <strong>{$stats.unopens}</strong> ({$stats.unopens_percentage})</li>
				<li>{$lblClickRate|ucfirst}: <strong>{$stats.clicks}</strong> ({$stats.clicks_percentage})</li>
				{option:stats.bounces}<li>{$lblBounceRate|ucfirst}: <strong>{$stats.bounces}/{$stats.recipients_total}</strong> ({$stats.bounces_percentage})</li>{/option:stats.bounces}
			</ul>
		</div>
	</div>
	*}
	<div class="options content">
		<div class="mailAnalyticsGraphWrapper">
			<div class="mailAnalyticsLeftCol">
				<div class="box boxLevel2">
					<div class="heading">
						<h3>{$lblOpenedMailings|ucfirst}</h3>
					</div>
					<div class="options">
							<div id="dataChartPieChart" class="hidden">
								<ul class="data">
									{option:stats.unique_opens}<li><span class="label">{$lblOpenedMailings|ucfirst}</span><span class="value">{$stats.unique_opens}</span><span class="percentage">{$pieGraphData.percentage}</span></li>{/option:stats.unique_opens}
									{option:stats.unopens}<li><span class="label">{$lblUnopenedMailings|ucfirst}</span><span class="value">{$stats.unopens}</span><span class="percentage">{$pieGraphData.percentage}</span></li>{/option:stats.unopens}
									{option:stats.bounces}<li><span class="label">{$lblBounces|ucfirst}</span><span class="value">{$stats.bounces}</span><span class="percentage">{$pieGraphData.percentage}</span></li>{/option:stats.bounces}
								</ul>
							</div>
							<div id="chartPieChart">&nbsp;</div>
						<div class="buttonHolderRight">
							<a href="http://highcharts.com/" class="mailAnalyticsBacklink">Highcharts</a>
						</div>
					</div>
				</div>
			</div>
			<div class="mailAnalyticsRightCol">
				<div class="box boxLevel2">
					<div class="heading">
						<h3>{$lblSummary|ucfirst}</h3>
					</div>
					<div class="options mailData">
						<p><strong>{$stats.recipients}</strong> {$lblSentMailings|ucfirst} <small>(100%)</small></p>
						<p><strong>{$stats.unique_opens}</strong> {$lblOpenedMailings|ucfirst} <small>({$stats.unique_opens_percentage})</small></p>
						<p><strong>{$stats.unopens}</strong> {$lblUnopenedMailings|ucfirst} <small>({$stats.unopens_percentage})</small></p>
						<p><strong>{$stats.clicks}</strong> {$lblClicks|ucfirst} <small>({$stats.clicks_percentage})</small></p>
						{option:stats.bounces}<p><strong>{$stats.bounces}/{$stats.recipients_total} </strong>{$lblBounceRate|ucfirst} <small>({$stats.bounces_percentage})</small></p>{/option:stats.bounces}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{option:dataGrid}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$msgCampaignMailings|ucfirst}</h3>
	</div>
	{$dataGrid}
</div>
{/option:dataGrid}

{option:showMailmotorCampaigns}
<div class="buttonHolder">
	<a href="{$var|geturl:'campaigns'}" class="button" title="{$lblCampaigns|ucfirst}">
		<span>{$msgBackToCampaigns|sprintf:{$campaign.name}}</span>
	</a>
</div>
{/option:showMailmotorCampaigns}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}