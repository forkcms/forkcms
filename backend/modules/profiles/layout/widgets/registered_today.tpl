<div class="box" id="widgetProfiles">
	<div class="heading">
		<h3><a href="{$var|geturl:'index':'profiles'}">{$lblProfiles|ucfirst}</a></h3>
	</div>

	<div class="options content">
		<p>{$lblNumberOfProfiles|ucfirst}: {$number}</p>
	</div>

	<div class="options">
		<div id="tabs" class="tabs">
			<ul>
				<li><a href="#tabRegistrations">{$lblRegistrations|ucfirst}</a></li>
				<li><a href="#tabStatus">{$lblStatus|ucfirst}</a></li>
				<li><a href="#tabOnline">{$lblOnline|ucfirst}</a></li>
			</ul>

			<div id="tabRegistrations">
				{$lblFrom|ucfirst}: <input type="text" id="fromDate" name="from_date" maxlength="10" class="inputText inputDatefieldNormal" /> 
				{$lblTo|ucfirst}: <input type="text" id="toDate" name="to_date" maxlength="10" class="inputText inputDatefieldNormal" />
				<div class="dataGridHolder">
					{option:profiles}
					<table class="dataGrid">
						<tbody>
							{iteration:profiles}
							<tr class="{cycle:'odd':'even'}">
								<td>{$profiles.display_name}</td>
								<td class="name">{$profiles.status}</td>
							</tr>
							{/iteration:profiles}
						</tbody>
					</table>
					{/option:profiles}
				</div>
					{option:barGraphData}
					<div id="dataBarChart" class="hidden">
						<ul class="data">
							{iteration:barGraphData}
								<li><span class="count">{$barGraphData.count}</span><span class="date">{$barGraphData.date}</span></li>
							{/iteration:barGraphData}
						</ul>
					</div>
					<div id="barChart">&nbsp;</div>
				{/option:barGraphData}
			</div>

			<div id="tabStatus" class="options">
				{option:pieGraphData}
					<div id="dataPieChart" class="hidden">
						<ul class="data">
							{iteration:pieGraphData}
								<li><span class="label">{$pieGraphData.label}</span><span class="value">{$pieGraphData.value}</span><span class="percentage">{$pieGraphData.percentage}</span></li>
							{/iteration:pieGraphData}
						</ul>
					</div>
				<div id="pieChart">&nbsp;</div>
				{/option:pieGraphData}
			</div>

			<div id="tabOnline">
				{option:online}
					<div class="dataGridHolder">
						<table class="dataGrid">
							<tbody>
								{iteration:online}
								<tr class="{cycle:'odd':'even'}">
									<td>{$online.display_name}</td>
									<td class="name">{$online.date}</td>
								</tr>
								{/iteration:online}
							</tbody>
						</table>
					</div>
				{/option:online}
				{option:!online}
					<p>{$lblNoOnlineUsers}</p>
				{/option:!online}
			</div>

		</div>
	</div>

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index':'profiles'}" class="button"><span>{$lblAllProfiles|ucfirst}</span></a>
		</div>
	</div>
</div>