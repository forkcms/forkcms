{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Blog &gt; {$msgHeaderIndex}</p>
			</div>

			<div class="inner">
				{option:report}
					<div class="report fadeOutAfterMouseMove">{$reportMessage}</div>
					{option:highlight}
						<script type="text/javascript">
							var highlightId = '#{$highlight}';
						</script>
					{/option:highlight}
				{/option:report}

				<div>
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}
						<p>{$msgNoItems}</p>
						
						<div class="buttonHolder">
							<a class="button icon iconAdd" href="{$var|geturl:'add'}" title="{$lblAdd}"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
						</div>
					{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}