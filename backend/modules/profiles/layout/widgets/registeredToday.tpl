<div class="box" id="widgetProfiles">
	<div class="heading">
		<h3><a href="{$var|geturl:'index':'profiles'}">{$lblRegistered|ucfirst}</a></h3>
	</div>

	<div class="options content">
		<p>{$lblNumberOfProfiles|ucfirst}: {$number}</p>
	</div>

	<div class="options">
		<div id="tabs" class="tabs">
			<ul>
				<li><a href="#tabRegisteredToday">{$lblRegisteredToday|ucfirst}</a></li>
				<li><a href="#tabRegisteredYesterday">{$lblRegisteredYesterday|ucfirst}</a></li>
				<li><a href="#tabRegisteredAllWeek">{$lblRegisteredAllWeek|ucfirst}</a></li>
			</ul>

			<div class="dataGridHolder" id="tabRegisteredToday">
				{option:today}
				<table class="dataGrid">
					<tbody>
						{iteration:today}
						<tr class="{cycle:'odd':'even'}">
							<td>{$today.display_name}</td>
							<td class="name">{$today.status}</td>
						</tr>
						{/iteration:today}
					</tbody>
				</table>
				{/option:today}
			</div>

			<div class="dataGridHolder" id="tabRegisteredYesterday">
				{option:yesterday}
				<table class="dataGrid">
					<tbody>
						{iteration:yesterday}
						<tr class="{cycle:'odd':'even'}">
							<td>{$yesterday.display_name}</td>
							<td class="name">{$yesterday.status}</td>
						</tr>
						{/iteration:yesterday}
					</tbody>
				</table>
				{/option:yesterday}
			</div>

			<div class="dataGridHolder" id="tabRegisteredAllWeek">
				{option:week}
				<table class="dataGrid">
					<tbody>
						{iteration:week}
						<tr class="{cycle:'odd':'even'}">
							<td>{$week.display_name}</td>
							<td class="name">{$week.status}</td>
						</tr>
						{/iteration:week}
					</tbody>
				</table>
				{/option:week}
			</div>

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
	</div>

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index':'profiles'}" class="button"><span>{$lblAllProfiles|ucfirst}</span></a>
		</div>
	</div>
</div>