{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblUsers|ucfirst} &gt; {$lblIndex|ucfirst}</p>
			</div>

			{option:report}
			<div id="report">
				<div class="singleMessage successMessage">
					<p>{$reportMessage}</p>
				</div>
			</div>
			{/option:report}

			<div class="inner">
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$lblUsers|ucfirst}</h3>
						<div class="buttonHolderRight">
							<a class="button icon iconAdd" href="{$var|geturl:'add'}"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
						</div>
					</div>
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}{$msgNoItems}{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}
