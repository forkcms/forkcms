{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblEmailAddresses|ucfirst}{option:group} {$lblFor} {$lblGroup} &ldquo;{$group.name}&rdquo;{/option:group}</h2>
	<div class="buttonHolderRight">
		{option:showMailmotorAddAddress}<a href="{$var|geturl:'add_address'}{option:group}&amp;group_id={$group.id}{/option:group}" class="button icon iconMailAdd"><span>{$lblAddEmail|ucfirst}</span></a>{/option:showMailmotorAddAddress}
		{option:showMailmotorImportAddresses}<a href="{$var|geturl:'import_addresses'}{option:group}&amp;group_id={$group.id}{/option:group}" class="button icon iconFolderAdd"><span>{$lblImportAddresses|ucfirst}</span></a>{/option:showMailmotorImportAddresses}
		{option:showMailmotorExportAddresses}<a href="{$var|geturl:'export_addresses'}&amp;id={option:!group}all{/option:!group}{option:group}{$group.id}{/option:group}" class="button icon iconExport"><span>{$lblExportAddresses|ucfirst}</span></a>{/option:showMailmotorExportAddresses}
	</div>
</div>

{option:csvURL}
<div class="generalMessage infoMessage content">
	<p><strong>{$msgImportRecentlyFailed}</strong></p>
	<p>{$msgImportFailedDownloadCSV|sprintf:{$csvURL}}</p>
</div>
{/option:csvURL}

<div class="dataGridHolder">
	{form:filter}
		{$hidGroupId}
		<div class="dataFilter">
			<table>
				<tbody>
					<tr>
						<td>
							<div class="options">
								<p>
									<label for="email">{$lblEmailAddress|ucfirst}</label>
									{$txtEmail} {$txtEmailError}
								</p>
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

	{option:dataGrid}
	<form action="{$var|geturl:'mass_address_action'}" method="get" class="forkForms submitWithLink" id="massAddressAction">
		<fieldset>
			<input type="hidden" name="offset" value="{$offset}" />
			<input type="hidden" name="order" value="{$order}" />
			<input type="hidden" name="sort" value="{$sort}" />
			<input type="hidden" name="email" value="{$email}" />
			{option:group}<input type="hidden" name="group_id" value="{$group.id}" />{/option:group}
			{$dataGrid}
		</fieldset>
	</form>
	{/option:dataGrid}
</div>

{option:!dataGrid}
	{option:oPost}<p>{$msgNoResultsForFilter|sprintf:{$email}}</p>{/option:oPost}
	{option:!oPost}<p>{$msgNoSubscriptions}</p>{/option:!oPost}
{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}