{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>
	{option:isGod}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}&{$filter}" class="button icon iconAdd"><span>{$lblAdd|ucfirst}</span></a>
			<a href="{$var|geturl:'export'}&amp;language={$language}&amp;application={$application}&amp;module={$module}&amp;type={$type}&amp;name={$name}&amp;value={$value}" class="button icon iconExport"><span>{$lblExport|ucfirst}</span></a>
			<a href="{$var|geturl:'import'}" class="button icon iconImport"><span>{$lblImport|ucfirst}</span></a>
		</div>
	{/option:isGod}
</div>

<div class="datagridHolder">
	{form:filter}
		<div class="dataFilter">
			<table cellspacing="0" cellpadding="0" border="0">
				<tbody>
					<tr>
						<td>
							<div class="options">
								<p>
									<label for="application">{$lblApplication|ucfirst}</label>
									{$ddmApplication} {$ddmApplicationError}
								</p>
								<p>
									<label for="module">{$lblModule|ucfirst}</label>
									{$ddmModule} {$ddmModuleError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<label for="translationTypes">{$lblTypes|ucfirst}</label>
								{option:translationTypes}
									<ul class="inputList">
										{iteration:translationTypes}<li>{$translationTypes.chkTranslationTypes} <label for="{$translationTypes.id}">{$translationTypes.label|ucfirst}</label></li>{/iteration:translationTypes}
									</ul>
								{/option:translationTypes}
							</div>
						</td>
						<td>
							<div class="options">
								<label for="languages">{$lblLanguages|ucfirst}</label>
								{option:languages}
									<ul class="inputList">
										{iteration:languages}<li>{$languages.chkLanguages} <label for="{$languages.id}">{$languages.label|ucfirst}</label></li>{/iteration:languages}
									</ul>
								{/option:languages}
							</div>
						</td>
						<td>
							<div class="options">
								<div class="oneLiner">
									<p>
										<label for="name">{$lblReferenceCode|ucfirst}</label>
									</p>
									<p>
										<abbr class="help">(?)</abbr>
										<span class="tooltip" style="display: none;">
											{$msgHelpName}
										</span>
									</p>
								</div>
								{$txtName} {$txtNameError}

								<div class="oneLiner">
									<p>
										<label for="value">{$lblValue|ucfirst}</label>
									</p>
									<p>
										<abbr class="help">(?)</abbr>
										<span class="tooltip" style="display: none;">
											{$msgHelpValue}
										</span>
									</p>
								</div>
								{$txtValue} {$txtValueError}

							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="99">
							<div class="options">
								<div class="buttonHolder">
									<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblUpdateFilter|ucfirst}" />
								</div>
							</div>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	{/form:filter}


	{option:dgLabels}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblLabels|ucfirst}</h3>
		</div>
		{$dgLabels}
	</div>
	{/option:dgLabels}

	{option:dgMessages}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblMessages|ucfirst}</h3>
		</div>
		{$dgMessages}
	</div>
	{/option:dgMessages}

	{option:dgErrors}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblErrors|ucfirst}</h3>
		</div>
		{$dgErrors}
	</div>
	{/option:dgErrors}

	{option:dgActions}
	<div class="datagridHolder">
		<div class="tableHeading">
			<h3>{$lblActions|ucfirst} </h3>
				<abbr class="help">(?)</abbr>
				<span class="tooltip" style="display: none;">
					{$msgHelpActionValue}
				</span>
		</div>
		{$dgActions}
	</div>
	{/option:dgActions}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
