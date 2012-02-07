<div class="box" id="widgetProfiles">
	<div class="heading">
		<h3><a href="{$var|geturl:'index':'profiles'}">{$lblProfiles|ucfirst}: {$lblRegisteredToday|ucfirst}</a></h3>
	</div>
	
	{option:today}
	<div class="dataGridHolder">
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
	</div>
	{/option:today}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'index':'profiles'}" class="button"><span>{$lblAllProfiles|ucfirst}</span></a>
		</div>
	</div>
</div>