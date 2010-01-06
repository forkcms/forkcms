{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
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
				<div class="oneLiner" id="pagesCTA">
					<p>{$msgHelpAdd}</p>
					<div class="buttonHolder">
						<a href="{$var|geturl:"add"}" class="button icon iconAdd">
							<span><span><span>{$lblAdd}</span></span></span></span>
						</a>
					</div>
				</div>
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$lblRecentlyEdited|ucfirst}</h3>
					</div>

					{option:datagrid}{$datagrid}{/option:datagrid}
					{option:!datagrid}{$msgNoItems}{/option:!datagrid}
				</div>
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}