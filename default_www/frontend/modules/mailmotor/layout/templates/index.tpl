<section id="mailmotorIndex" class="mod">
	<div class="inner">
		<div class="bd">
			{option:datagrid}
				{$datagrid}
			{/option:datagrid}

			{option:!datagrid}
				<p>{$msgNoSentMailings}</p>
			{/option:!datagrid}
		</div>
	</div>
</section>