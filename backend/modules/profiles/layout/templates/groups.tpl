{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGroups|ucfirst}</h2>

	{option:showProfilesAddGroup}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add_group'}" class="button icon iconAdd">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showProfilesAddGroup}
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
									<label for="name">{$lblName|ucfirst}</label>
									{$txtName} {$txtNameError}
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

	{option:dgGroups}
		<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
			<div>
				<input type="hidden" name="offset" value="{$offset}" />
				<input type="hidden" name="order" value="{$order}" />
				<input type="hidden" name="sort" value="{$sort}" />
			</div>
			{$dgGroups}
		</form>
	{/option:dgGroups}

	{option:!dgGroups}
		<p>{$msgNoItems}</p>
	{/option:!dgGroups}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
