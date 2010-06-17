{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

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
				<span>{$lblDelete|ucfirst}</span>
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

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}