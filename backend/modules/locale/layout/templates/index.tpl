{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>
		<div class="buttonHolderRight">
			{option:showLocaleAdd}<a href="{$var|geturl:'add'}{$filter}" class="button icon iconAdd"><span>{$lblAdd|ucfirst}</span></a>{/option:showLocaleAdd}
			{option:showLocaleExport}<a href="{$var|geturl:'export'}{$filter}" class="button icon iconExport"><span>{$lblExport|ucfirst}</span></a>{/option:showLocaleExport}
			{option:showLocaleImport}<a href="{$var|geturl:'import'}{$filter}" class="button icon iconImport"><span>{$lblImport|ucfirst}</span></a>{/option:showLocaleImport}
		</div>
</div>

<div class="dataGridHolder">
	{form:filter}
		<div class="dataFilter">
			<table>
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
								<label>{$lblTypes|ucfirst}</label>
								{option:type}
									<ul>
										{iteration:type}<li>{$type.chkType} <label for="{$type.id}">{$type.label|ucfirst}</label></li>{/iteration:type}
									</ul>
								{/option:type}
							</div>
						</td>
						<td>
							<div class="options">
								<label>{$lblLanguages|ucfirst}</label>
								{option:language}
									<ul>
										{iteration:language}<li>{$language.chkLanguage} <label for="{$language.id}">{$language.label|ucfirst}</label></li>{/iteration:language}
									</ul>
								{/option:language}
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
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblLabels|ucfirst}</h3>
		</div>
		{$dgLabels}
	</div>
	{/option:dgLabels}

	{option:dgMessages}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblMessages|ucfirst}</h3>
		</div>
		{$dgMessages}
	</div>
	{/option:dgMessages}

	{option:dgErrors}
	<div class="dataGridHolder">
		<div class="tableHeading">
			<h3>{$lblErrors|ucfirst}</h3>
		</div>
		{$dgErrors}
	</div>
	{/option:dgErrors}

	{option:dgActions}
	<div class="dataGridHolder">
		<div class="tableHeading oneLiner">
			<h3>{$lblActions|ucfirst} </h3>
				<abbr class="help">(?)</abbr>
				<span class="tooltip" style="display: none;">
					{$msgHelpActionValue}
				</span>
		</div>
		{$dgActions}
	</div>
	{/option:dgActions}

	{option:noItems}
		<p>{$msgNoItemsFilter|sprintf:{$addURL}}</p>
	{/option:noItems}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
