<div id="mailmotorIndex">
	{* @todo	this shouldn't be a datagrid, this sucks *}
	{option:dataGrid}
		{$dataGrid}
	{/option:dataGrid}

	{option:!dataGrid}
		<div class="alert">{$msgNoSentMailings}</div>
	{/option:!dataGrid}
</div>