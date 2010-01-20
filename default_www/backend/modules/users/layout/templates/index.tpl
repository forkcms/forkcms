{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Blog > Overzicht</p>
				<div class="oneLiner" id="searchModuleHolder">
					<p><input type="text" class="inputText" id="searchModule"/></p>
					<p><a href="#" class="button" id="toggleFilter"><span><span><span>Search</span></span></span></a></p>
				</div>
			</div>
			<div id="filterBar" style="display: none;">
				<p>Now displaying articles matching "Webdesign"</p>
				<a class="button" href="#">X</a>
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
				
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$msgHeaderIndex|ucfirst}</h3>
					</div>
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}{$msgNoItems}{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}
