{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:editCategory}
	<div class="box">
		<div class="heading">
			<h3>{$lblBlog|ucfirst}: {$msgEditCategory|sprintf:{$name}}</h3>
		</div>
		<div class="options">
			<p>
				<label for="name">{$lblCategory|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtName} {$txtNameError}
			</p>

			<ul class="inputList">
				<li>
					<label for="isDefault">{$msgMakeDefaultCategory|sprintf:{$defaultCategory.name}}</label>
					{$chkIsDefault} {$chkIsDefaultError}
				</li>
			</ul>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:deleteAllowed}
			<a href="{$var|geturl:'delete_category'}&amp;id={$id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
			<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
				<p>
					{$msgConfirmDeleteCategory|sprintf:{$name}}
				</p>
			</div>
		{/option:deleteAllowed}
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editCategory}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
