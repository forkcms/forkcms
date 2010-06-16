{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				<div class="pageTitle">
					<h2>{$lblTags|ucfirst}</h2>
				</div>

				{option:datagrid}
					<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="tags">
						<div class="datagridHolder">
							{$datagrid}
						</div>
					</form>
				{/option:datagrid}
				{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}