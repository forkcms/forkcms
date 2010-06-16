{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				<div class="pageTitle">
					<h2>{$lblBlog|ucfirst}: {$lblCategories}</h2>
					<div class="buttonHolderRight">
						<a href="{$var|geturl:'add_category'}" class="button icon iconAdd"><span>{$lblAddCategory|ucfirst}</span></a>
					</div>
				</div>

				{option:datagrid}
					<div class="datagridHolder">
						{$datagrid}
					</div>
				{/option:datagrid}
				{option:!datagrid}{$msgNoItems}{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}