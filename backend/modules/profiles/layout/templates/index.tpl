{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblProfiles|ucfirst}</h2>
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
									<label for="email">{$lblEmail|ucfirst}</label>
									{$txtEmail} {$txtEmailError}
								</p>
							</div>
						</td>
						<td>
							<div class="options">
								<p>
									<label for="status">{$lblStatus|ucfirst}</label>
									{$ddmStatus} {$ddmStatusError}
								</p>
							</div>
						</td>
						{option:ddmGroup}
							<td>
								<div class="options">
									<p>
										<label for="group">{$lblGroup|ucfirst}</label>
										{$ddmGroup} {$ddmGroupError}
									</p>
								</div>
							</td>
						{/option:ddmGroup}
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

	{option:dgProfiles}
		<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink">
			<div>
				<input type="hidden" name="offset" value="{$offset}" />
				<input type="hidden" name="order" value="{$order}" />
				<input type="hidden" name="sort" value="{$sort}" />
				<input type="hidden" name="email" value="{$email}" />
				<input type="hidden" name="status" value="{$status}" />
				<input type="hidden" name="newGroup" value="" />
			</div>
			{$dgProfiles}
		</form>
	{/option:dgProfiles}

	{option:!dgProfiles}
		<p>{$msgNoItems}</p>
	{/option:!dgProfiles}
</div>

<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassDelete}</p>
</div>
<div id="confirmAddToGroup" title="{$lblAddToGroup|ucfirst}?" style="display: none;">
	<p>{$msgConfirmMassAddToGroup}</p>
	<div id="massAddToGroupListPlaceholder"></div>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
