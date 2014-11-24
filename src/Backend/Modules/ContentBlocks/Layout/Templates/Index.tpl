{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblContentBlocks|ucfirst}</h2>

	{option:showContentBlocksAdd}
	<div class="buttonHolderRight">
		<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
	</div>
	{/option:showContentBlocksAdd}
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
							<label for="title">{$lblTitle|ucfirst}</label>
							{$txtTitle} {$txtTitleError}
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
		{$dataGrid}
	{/option:dataGrid}

	{option:!dataGrid}
		<p>{$msgNoItems}</p>
	{/option:!dataGrid}
</div>

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
