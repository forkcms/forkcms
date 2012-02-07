<div class="box" id="widgetBlogComments">
	<div class="heading">
		<h3>{$lblProfiles|ucfirst}: {$lblRegisteredToday|ucfirst}</a></h3>
	</div>
	
	{option:profiles}
	<div class="dataGridHolder">
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
	</div>
	{/option:profiles}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'profiles'}" class="button"><span>{$lblAllProfiles|ucfirst}</span></a>
		</div>
	</div>
</div>