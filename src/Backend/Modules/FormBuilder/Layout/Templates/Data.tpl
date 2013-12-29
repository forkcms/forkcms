{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblFormBuilder|ucfirst}: {$lblFormData|sprintf:{$name}}</h2>

	<div class="buttonHolderRight">
		{option:showFormBuilderIndex}<a href="{$var|geturl:'index'}" class="button icon iconBack"><span>{$lblOverview|ucfirst}</span></a>{/option:showFormBuilderIndex}
		{option:showFormBuilderExportData}<a href="{$var|geturl:'export_data'}&id={$id}&amp;start_date={$start_date}&amp;end_date={$end_date}" class="button icon iconExport"><span>{$lblExport|ucfirst}</span></a>{/option:showFormBuilderExportData}
	</div>
</div>

<div class="dataGridHolder">
	{form:filter}
		<div class="dataFilter">

			<input type="hidden" name="id" value="{$id}" />

			<table>
				<tbody>
					<tr>
						<td>
							<div class="options">
								<p>
									<label for="startDate">{$lblStartDate|ucfirst}</label>
									{$txtStartDate} {$txtStartDateError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<p>
									<label for="endDate">{$lblEndDate|ucfirst}</label>
									{$txtEndDate} {$txtEndDateError}
								</p>
							</div>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="2">
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

	{option:dataGrid}
		<form action="{$var|geturl:'mass_data_action'}" method="get" class="forkForms">
			<div class="dataGridHolder">
				<input type="hidden" name="form_id" value="{$id}" />
				{$dataGrid}
			</div>
		</form>
	{/option:dataGrid}
	{option:!dataGrid}<p>{$msgNoData}</p>{/option:!dataGrid}
</div>

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassDelete}</p>
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
