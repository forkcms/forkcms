<div class="box" id="widgetEventsComments">
	<div class="heading">
		<h3><a href="{$var|geturl:'comments':'events'}">{$lblLatestComments|ucfirst}</a></h3>
	</div>

	{option:eventsNumCommentsToModerate}
	<div class="moderate">
		<div class="oneLiner">
			<p>{$msgCommentsToModerate|sprintf:{$eventsNumCommentsToModerate}}</p>
			<div class="buttonHolder">
				<a href="{$var|geturl:'comments':'events'}#tabModeration" class="button"><span>{$lblModerate|ucfirst}</span></a>
			</div>
		</div>
	</div>
	{/option:eventsNumCommentsToModerate}

	{option:eventsComments}
	<div class="datagridHolder">
		<table cellspacing="0" class="datagrid">
			<tbody>
				{iteration:eventsComments}
				<tr class="{cycle:'odd':'even'}">
					<td><a href="{$eventsComments.full_url}">{$eventsComments.title}</a></td>
					<td class="name">{$eventsComments.author}</td>
				</tr>
				{/iteration:eventsComments}
			</tbody>
		</table>
	</div>
	{/option:eventsComments}

	{option:!eventsComments}
	<div class="options content">
		<p>{$msgNoPublishedComments}</p>
	</div>
	{/option:!eventsComments}

	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'comments':'events'}" class="button"><span>{$lblAllComments|ucfirst}</span></a>
		</div>
	</div>
</div>