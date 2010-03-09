{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblTags|ucfirst} &gt; {$lblOverview|ucfirst}</p>
			</div>

			<div class="inner">
				{option:datagrid}
					<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="tags">
						<div class="datagridHolder">
							<div class="tableHeading">
								<h3>Tags</h3>
							</div>
							{$datagrid}
						</div>
					</form>
				{/option:datagrid}
				{option:!datagrid}{$msgNoItems}{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}