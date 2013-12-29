{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

{form:import}
	<div class="box">
		<div class="heading">
			<h3>{$msgImportGroupsTitle}</h3>
		</div>
		<div class="options">
			<p>{$msgImportGroups}:</p>
			<ul class="inputList">
				{iteration:groups}
					<li><strong>{$groups.name}</strong> ({$groups.subscribers_amount} {$lblEmailAddresses})</li>
				{/iteration:groups}
			</ul>
		</div>
	</div>

	{option:showMailmotorImportGroups}
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'import_groups'}" class="submitButton button inputButton button mainButton"><span>{$msgImportGroupsTitle}</span></a>
		</div>
	</div>
	{/option:showMailmotorImportGroups}
{/form:import}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
