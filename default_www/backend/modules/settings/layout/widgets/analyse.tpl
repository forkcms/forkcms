<div class="box" id="widgetSettingsAnalyse">
	<div class="heading">
		<h3>{$lblAnalyse|ucfirst}</a></h3>
	</div>
	<div>
		{option:warnings}
		<ul>
			{iteration:warnings}
			<li>{$warnings.message}</li>
			{/iteration:warnings}
		</ul>
		{/option:warnings}
	</div>
	<div class="footer">
	</div>
</div>