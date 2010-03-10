{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" width="264">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td id="treeHolder">
						<div id="tree">
							{$tree}
						</div>
					</td>
				</tr>
			</table>
		</td>
		<td id="fullwidthSwitch"><a href="#close">&nbsp;</a></td>
		<td id="contentHolder">
			<div class="inner">
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$lblRecentlyEdited|ucfirst}</h3>
						<div class="buttonHolderRight">
							<a href="{$var|geturl:'add'}" class="button icon iconAdd">
								<span><span><span>{$msgAddPage}</span></span></span>
							</a>
						</div>
					</div>

					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}
					<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
						<tr>
							<td>
								{$msgNoItems}
							</td>
						</tr>
					</table>
					{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>

{option:openedPageId}
<script type="text/javascript">
	var pageID = {$openedPageId};
</script>
{/option:openedPageId}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}