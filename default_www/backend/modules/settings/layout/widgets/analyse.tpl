{option:warnings}
	<div class="box" id="widgetSettingsAnalyse">
		<div class="heading">
			<h3>{$lblAnalysis|ucfirst}</h3>
		</div>
		<div class="options content">
			<ul>
				{iteration:warnings}
					<li>{$warnings.message}</li>
				{/iteration:warnings}
			</ul>
		</div>
	</div>
{/option:warnings}