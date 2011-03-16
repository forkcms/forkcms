{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblTranslations|ucfirst}</h2>
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
							</div>
						</td>
						<td>
							<div class="options">
								<p>
									<label for="name">{$lblName|ucfirst}</label>
									{$txtName} {$txtNameError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<label for="languages">{$lblLanguages|ucfirst}</label>
								{option:languages}
									<ul class="inputList">
										{iteration:languages}<li>{$languages.chkLanguages} <label for="{$languages.value}">{$languages.label|ucfirst}</label></li>{/iteration:languages}
									</ul>
								{/option:languages}
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
			<h3>{$lblActions|ucfirst}</h3>
		</div>
		{$dgActions}
	</div>
	{/option:dgActions}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
