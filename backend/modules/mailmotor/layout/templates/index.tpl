{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<form action="{$var|geturl:'mass_mailing_action'}" method="get" class="forkForms submitWithLink" id="mailings">
	{option:dgUnsentMailings}
	<div class="pageTitle">
		<h2>{$lblUnsentMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>

		{option:showMailmotorAdd}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}" class="button icon iconMailAdd" title="{$lblAddNewMailing|ucfirst}">
				<span>{$lblAddNewMailing|ucfirst}</span>
			</a>
		</div>
		{/option:showMailmotorAdd}
	</div>
	<div class="dataGridHolder">
		{$dgUnsentMailings}
	</div>
	{/option:dgUnsentMailings}

	{option:!dgUnsentMailings}
	<div class="pageTitle">
		<h2>{$lblNewsletters|ucfirst}</h2>

		{option:showMailmotorAdd}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}" class="button icon iconMailAdd" title="{$lblAddNewMailing|ucfirst}">
				<span>{$lblAddNewMailing|ucfirst}</span>
			</a>
		</div>
		{/option:showMailmotorAdd}
	</div>
	<p>{$msgNoUnsentMailings}</p>
	{/option:!dgUnsentMailings}

	{option:dgQueuedMailings}
	<div class="pageTitle">
		<h2>{$lblQueuedMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>
	</div>
	<div class="dataGridHolder">
		{$dgQueuedMailings}
	</div>
	{/option:dgQueuedMailings}

	{option:dgSentMailings}
	<div class="pageTitle">
		<h2>{$lblSentMailings|ucfirst}{option:name} {$lblIn} {$lblCampaign} &ldquo;{$name}&rdquo;{/option:name}</h2>
	</div>
	<div class="dataGridHolder">
		{$dgSentMailings}
	</div>
	{/option:dgSentMailings}
</form>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}