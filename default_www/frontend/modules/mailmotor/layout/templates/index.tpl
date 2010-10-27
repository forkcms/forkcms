<div id="mailmotorIndex" class="mod">
	<div class="inner">
		<div class="bd content">
			{option:datagrid}
				{$datagrid}
			{/option:datagrid}

			{option:!datagrid}
				<p>{$msgNoSentMailings}</p>
			{/option:!datagrid}
		</div>
	</div>
</div>