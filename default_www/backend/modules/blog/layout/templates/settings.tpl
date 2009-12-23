{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="content">
			<div id="statusBar">
				<p class="breadcrumb">Blog > {$msgHeaderIndex}</p>
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

				<h2></h2>
				<a href="{$var|geturl:'add'}" title="{$lblAdd}">{$lblAdd}</a>
				
				<div>
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}{$msgNoItems}{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}