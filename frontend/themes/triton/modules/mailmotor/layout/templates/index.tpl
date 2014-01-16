<section id="mailmotorIndex" class="mod">
	<div class="inner">
		<div class="bd">
			{option:dataGrid}
				{$dataGrid}
			{/option:dataGrid}

			{option:!dataGrid}
				<p>{$msgNoSentMailings}</p>
			{/option:!dataGrid}
		</div>
	</div>
</section>