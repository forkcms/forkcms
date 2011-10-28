{option:analyticsValidSettings}
<div class="box" id="widgetAnalyticsTrafficSources">
	<div class="heading">
		<h3>
			<a href="{$var|geturl:'index':'analytics'}">
				{$lblTrafficSources|ucfirst}
				{$lblFrom}
				<span id="trafficSourcesDate">{$analyticsTrafficSourcesDate}</span>
			</a>
		</h3>
	</div>

	<div class="options">
		<div id="tabs" class="tabs">
			<ul>
				<li><a href="#tabAnalyticsReferrers">{$lblTopReferrers|ucfirst}</a></li>
				<li><a href="#tabAnalyticsKeywords">{$lblTopKeywords|ucfirst}</a></li>
			</ul>

			<div id="tabAnalyticsReferrers">
				{* Top referrers *}
				<div class="dataGridHolder" id="dataGridReferrers">
					{option:dgAnalyticsReferrers}
						{$dgAnalyticsReferrers}
					{/option:dgAnalyticsReferrers}

					{option:!dgAnalyticsReferrers}
						<table border="0" cellspacing="0" cellpadding="0" class="dataGrid">
							<tr>
								<td>{$msgNoReferrers}</td>
							</tr>
						</table>
					{/option:!dgAnalyticsReferrers}
				</div>
			</div>

			<div id="tabAnalyticsKeywords">
				{* Top keywords *}
				<div class="dataGridHolder" id="dataGridKeywords">
					{option:dgAnalyticsKeywords}
						{$dgAnalyticsKeywords}
					{/option:dgAnalyticsKeywords}

					{option:!dgAnalyticsKeywords}
						<table border="0" cellspacing="0" cellpadding="0" class="dataGrid">
							<tr>
								<td>{$msgAnalyticsNoKeywords}</td>
							</tr>
						</table>
					{/option:!dgAnalyticsKeywords}
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index':'analytics'}" class="button"><span>{$lblAllStatistics|ucfirst}</span></a>
			<a href="#refresh" id="refreshTrafficSources" class="submitButton button inputButton mainButton iconLink icon iconRefresh"><span></span></a>
			<div id="settingsUrl" class="hidden">{$settingsUrl}</div>
		</div>
	</div>
</div>
{/option:analyticsValidSettings}