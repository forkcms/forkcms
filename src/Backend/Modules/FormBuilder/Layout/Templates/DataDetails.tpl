{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblFormBuilder|ucfirst}: {$lblFormData|sprintf:{$name}}</h2>

	{option:showFormBuilderData}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'data'}&amp;id={$formId}&amp;start_date={$filter.start_date}&amp;end_date={$filter.end_date}" class="button icon iconBack"><span>{$lblBackToData|ucfirst}</span></a>
	</div>
	{/option:showFormBuilderData}
</div>

<div class="box">
	<div class="heading">
		<h3>{$lblSenderInformation|ucfirst}</h3>
	</div>
	<div class="options">
		<p><strong>{$lblSentOn|ucfirst}:</strong> {$sentOn|formatdatetime}</p>
	</div>
</div>

<div class="box">
	<div class="heading">
		<h3>{$lblContent|ucfirst}</h3>
	</div>
	<div class="options">
		{option:data}
			{iteration:data}
				<p><strong>{$data.label}:</strong> {$data.value}</p>
			{/iteration:data}
		{/option:data}
	</div>
</div>

{option:showFormBuilderMassDataAction}
<div class="fullwidthOptions">
	<a href="{$var|geturl:'mass_data_action'}&amp;action=delete&amp;form_id={$formId}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
		<span>{$lblDelete|ucfirst}</span>
	</a>
</div>
{/option:showFormBuilderMassDataAction}

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>{$msgConfirmDeleteData}</p>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
