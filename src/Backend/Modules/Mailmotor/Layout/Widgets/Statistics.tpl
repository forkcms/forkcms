<div class="box" id="widgetMailmotorClassic">
	<div class="heading">
		<h3>
			<a href="{$var|geturl:'index':'mailmotor'}">
				{$lblMailmotor|ucfirst}
			</a>
		</h3>
	</div>

	<div class="options">
		<div id="tabs" class="tabs">
			<ul>
				<li><a href="#tabMailmotorSubscriptions">{$lblSubscriptions|ucfirst}</a></li>
				<li><a href="#tabMailmotorUnsubscriptions">{$lblUnsubscriptions|ucfirst}</a></li>
				<li><a href="#tabMailmotorStatistics">{$lblStatistics|ucfirst}</a></li>
			</ul>

			<div id="tabMailmotorSubscriptions">
				{* All the subscriptions *}
				<div id="dataGridSubscriptions">
					{option:dgMailmotorSubscriptions}
					<div class="dataGridHolder">
						{$dgMailmotorSubscriptions}
					</div>
					{/option:dgMailmotorSubscriptions}
					{option:!dgMailmotorSubscriptions}
					<p>
						{$msgNoSubscriptions|ucfirst}
					</p>
					{/option:!dgMailmotorSubscriptions}
				</div>
			</div>

			<div id="tabMailmotorUnsubscriptions">
				{* All the unsubscriptions *}
				<div id="dataGridUnsubscriptions">
					{option:dgMailmotorUnsubscriptions}
					<div class="dataGridHolder" >
						{$dgMailmotorUnsubscriptions}
					</div>
					{/option:dgMailmotorUnsubscriptions}
					{option:!dgMailmotorUnsubscriptions}
					<p>
						{$msgNoUnsubscriptions|ucfirst}
					</p>
					{/option:!dgMailmotorUnsubscriptions}
				</div>
			</div>

			<div id="tabMailmotorStatistics">
				{* The stats *}
				<div id="dataGridStatistics">
					{option:dgMailmotorStatistics}
					<div class="dataGridHolder">
						{$dgMailmotorStatistics}
					</div>
					{/option:dgMailmotorStatistics}

					{option:!dgMailmotorStatistics}
					<p>
						{$msgNoSentMailings|ucfirst}
					</p>
					{/option:!dgMailmotorStatistics}
				</div>
			</div>
		</div>
	</div>
	<div class="footer">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'addresses':'mailmotor'}" class="button"><span>{$msgAllAddresses|ucfirst}</span></a>
		</div>
	</div>
</div>