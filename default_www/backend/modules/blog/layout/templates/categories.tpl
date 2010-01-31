{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblBlog|ucfirst} &gt; {$lblCategories|ucfirst}</p>
			</div>

			<div class="inner">
				{option:datagrid}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblCategories|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add_category'}" class="button icon iconAdd"><span><span><span>{$lblAddCategory|ucfirst}</span></span></span></a>
							</div>
						</div>
						{$datagrid}
					</div>
				{/option:datagrid}
				{option:!datagrid}{$msgNoItems}{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}