{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">

			<div class="inner">
				
				{form:editCategory}
					<div class="box">
						<div class="heading">
							<h3>{$lblEditCategory|ucfirst}</h3>
						</div>
						<div class="options">
							<label for="name">{$lblCategory|ucfirst}</label>
							{$txtName} {$txtNameError}
						</div>
					</div>

					<div class="fullwidthOptions">
						{option:deleteAllowed}
							<a href="{$var|geturl:'delete_category'}&id={$id}" rel="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
								<span><span><span>{$lblDelete|ucfirst}</span></span></span>
							</a>
							<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
								<p>
									{$msgConfirmDeleteCategory|sprintf:{$name}}
								</p>
							</div>
						{/option:deleteAllowed}
						<div class="buttonHolderRight">
							<input id="edit" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
						</div>
					</div>
				{/form:editCategory}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}