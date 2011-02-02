{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}&amp;language={$language}&amp;application={$application}&amp;module={$module}&amp;type={$type}&amp;name={$name}&amp;value={$value}" class="button icon iconAdd"><span>{$lblAdd|ucfirst}</span></a>
	</div>
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
									<label for="language">{$lblLanguage|ucfirst}</label>
									{$ddmLanguage} {$ddmLanguageError}
								</p>
								<p>
									<label for="application">{$lblApplication|ucfirst}</label>
									{$ddmApplication} {$ddmApplicationError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<p>
									<label for="module">{$lblModule|ucfirst}</label>
									{$ddmModule} {$ddmModuleError}
								</p>
								<p>
									<label for="type">{$lblType|ucfirst}</label>
									{$ddmType} {$ddmTypeError}
								</p>
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

	{option:datagrid}
		<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
			<div>
				<input type="hidden" name="offset" value="{$offset}" />
				<input type="hidden" name="order" value="{$order}" />
				<input type="hidden" name="sort" value="{$sort}" />
				<input type="hidden" name="language" value="{$language}" />
				<input type="hidden" name="application" value="{$application}" />
				<input type="hidden" name="module" value="{$module}" />
				<input type="hidden" name="type" value="{$type}" />
				<input type="hidden" name="name" value="{$name}" />
				<input type="hidden" name="value" value="{$value}" />
			</div>
			{$datagrid}
		</form>
	{/option:datagrid}
</div>

{option:!datagrid}
	<div class="tableHeading">
		<h3>{$lblTranslations|ucfirst}</h3>
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}&amp;language={$language}&amp;application={$application}&amp;module={$module}&amp;type={$type}&amp;name={$name}&amp;value={$value}" class="button icon iconAdd"><span>{$lblAdd|ucfirst}</span></a>
		</div>
	</div>
	{option:filter}<p>{$msgNoItemsFilter|sprintf:{$addUrl}}</p>{/option:filter}
	{option:!filter}<p>{$msgNoItems|sprintf:{$addUrl}}</p>{/option:!filter}
{/option:!datagrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
