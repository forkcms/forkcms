{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
<table border="0" cellspacing="0" cellpadding="0" id="pagesHolder">
	<tr>
		<td id="pagesTree" style="width: 264px;">
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
				<div class="pageTitle">
					<h2>{$lblRecentlyEdited|ucfirst}</h2>
					<div class="buttonHolderRight">
						<a href="{$var|geturl:'add'}" class="button icon iconAdd">
							<span>{$msgAddPage}</span>
						</a>
					</div>
				</div>

				<div class="datagridHolder {option:!datagrid}datagridHolderNoDatagrid{/option:!datagrid}">
					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}<p>{$msgNoItems}</p>{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>

{option:openedPageId}
	<script type="text/javascript">
		//<![CDATA[
		var pageID = {$openedPageId};
		//]]>
	</script>
{/option:openedPageId}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}