{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">

				<div class="pageTitle">
					<h2>{$lblTemplates|ucfirst}</h2>
					<div class="buttonHolderRight">
						<a href="{$var|geturl:'add_template'}" class="button icon iconAdd" title="{$lblAddTemplate|ucfirst}">
							<span><span><span>{$lblAddTemplate|ucfirst}</span></span></span>
						</a>
					</div>
				</div>

				<div class="datagridHolder">
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}{$msgNoItems}{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}
