{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblIndex|ucfirst}</p>
			</div>

			<div class="inner">
				{option:datagrid}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblLocale|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
							</div>
						</div>

						<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
							<div class="datagridHolder">
								{$datagrid}
							</div>
						</form>
					</div>
				{/option:datagrid}
				{option:!datagrid}{$msgNoItems}{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}