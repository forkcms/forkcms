{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblStatistics|ucfirst} {$lblFor} &ldquo;{$mailing.name}&rdquo;</h2>

	{option:showMailmotorExportStatistics}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'export_statistics'}&amp;id={$mailing.id}" class="button icon iconExport" title="{$lblExportStatistics|ucfirst}">
			<span>{$lblExportStatistics|ucfirst}</span>
		</a>
	</div>
	{/option:showMailmotorExportStatistics}
</div>

<div class="box">
	<div class="heading">
		<h3>{$lblStatistics|ucfirst}</h3>
	</div>
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
								{option:stats.unique_opens}<li><span class="label">{$lblOpenedMailings|ucfirst}</span><span class="value">{$stats.unique_opens}</span><span class="percentage">{$stats.unique_opens_percentage}</span></li>{/option:stats.unique_opens}
								{option:stats.unopens}<li><span class="label">{$lblUnopenedMailings|ucfirst}</span><span class="value">{$stats.unopens}</span><span class="percentage">{$stats.unopens_percentage}</span></li>{/option:stats.unopens}
								{option:stats.bounces}<li><span class="label">{$lblBounces|ucfirst}</span><span class="value">{$stats.bounces}</span><span class="percentage">{$stats.bounces_percentage}</span></li>{/option:stats.bounces}
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
						<p><strong>{$stats.clicks_total}</strong> <a href="#clicks">{$lblClicks|ucfirst}</a> <small>({$stats.clicks_percentage})</small></p>
						{option:stats.bounces}<p><strong>{$stats.bounces}/{$stats.recipients_total} </strong><a href="{$var|geturl:'statistics_bounces'}&amp;mailing_id={$mailing.id}">{$lblBounceRate|ucfirst}</a> <small>({$stats.bounces_percentage})</small></p>{/option:stats.bounces}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{option:dataGrid}
<div class="dataGridHolder">
	<div class="tableHeading" id="clicks">
		<h3>{$msgMailingLinks|ucfirst}</h3>
	</div>
	{$dataGrid}
</div>
{/option:dataGrid}

{option:showMailmotorIndex}
<div class="buttonHolder">
	<a href="{$var|geturl:'index'}" class="button" title="{$lblNewsletters|ucfirst}">
		<span>{$msgBackToMailings|sprintf:{$mailing.name}}</span>
	</a>
</div>
{/option:showMailmotorIndex}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}