<td id="pagesTree">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td id="treeHolder">
				<div id="treeOptions">
					<div class="buttonHolder">
						<a href="{$var|geturl:'index'}" class="button icon iconBack iconOnly"><span>{$lblBack|ucfirst}</span></a>
						<a href="{$var|geturl:'add'}" class="button icon iconAdd"><span>{$lblAdd|ucfirst}</span></a>
					</div>
				</div>
				<div id="tree">
					{$tree}
				</div>
			</td>
		</tr>
	</table>
</td>