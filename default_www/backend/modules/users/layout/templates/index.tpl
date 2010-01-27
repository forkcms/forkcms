{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Gebruikers > Overzicht</p>
			</div>
			<div class="inner">
				{option:report}
					<div class="successMessage fadeOutAfterMouseMove">{$reportMessage}</div>
					{option:highlight}
						<script type="text/javascript">
							var highlightId = '#{$highlight}';
						</script>
					{/option:highlight}
				{/option:report}

				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$msgHeaderIndex|ucfirst}</h3>
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
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}
