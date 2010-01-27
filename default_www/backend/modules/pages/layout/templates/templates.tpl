{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Pages &gt; Settings &gt; Templates</p>
			</div>

			<div class="inner">
				<div class="datagridHolder">
					<div class="tableHeading">
						<div class="buttonHolderRight">
							<a href="{$var|geturl:'add_template'}" class="button icon iconAdd" title="{$lblAddTemplate}">
								<span><span><span>{$lblAddTemplate}</span></span></span>
							</a>
						</div>
					</div>
					{option:datagrid}{$datagrid}{/option:datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}