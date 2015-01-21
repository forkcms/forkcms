{option:analyticsValidSettings}
<div id="widgetAnalyticsTrafficSources" class="panel panel-primary">
	<div class="panel-heading">
		<h2 class="panel-title">
			<a href="{$var|geturl:'index':'analytics'}">
				{$lblTrafficSources|ucfirst}
				{$lblFrom}
				<span id="trafficSourcesDate">{$analyticsTrafficSourcesDate}</span>
			</a>
		</h2>
	</div>
	<div class="panel-body">
		<div class="fork-tabs" role="tabpanel">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#tabAnalyticsReferrers">{$lblTopReferrers|ucfirst}</a>
				</li>
				<li role="presentation">
					<a href="#tabAnalyticsKeywords">{$lblTopKeywords|ucfirst}</a>
				</li>
			</ul>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="tabAnalyticsReferrers">
					{option:dgAnalyticsReferrers}
					{$dgAnalyticsReferrers}
					{/option:dgAnalyticsReferrers}
					{option:!dgAnalyticsReferrers}
					<p>{$msgNoReferrers}</p>
					{/option:!dgAnalyticsReferrers}
				</div>
			</div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane" id="tabAnalyticsKeywords">
					{option:dgAnalyticsKeywords}
					{$dgAnalyticsKeywords}
					{/option:dgAnalyticsKeywords}
					{option:!dgAnalyticsKeywords}
					<p>{$msgNoKeywords}</p>
					{/option:!dgAnalyticsKeywords}
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="{$var|geturl:'index':'analytics'}" class="btn"><span>{$lblAllStatistics|ucfirst}</span></a>
				<a href="#refresh" id="refreshTrafficSources" class="submitButton button inputButton mainButton iconLink icon iconRefresh"><span></span></a>
				{option:settingsUrl}
				<div id="settingsUrl" class="hidden">{$settingsUrl}</div>
				{/option:settingsUrl}
			</div>
		</div>
	</div>
</div>
{/option:analyticsValidSettings}
