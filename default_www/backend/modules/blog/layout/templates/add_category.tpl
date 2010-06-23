{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:addCategory}
	<div class="box">
		<div class="heading">
			<h3>{$lblBlog|ucfirst}: {$lblAddCategory}</h3>
		</div>
		<div class="options">
			<label for="name">{$lblName|ucfirst}</label>
			{$txtName} {$txtNameError}
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="addCategory" value="{$lblAddCategory|ucfirst}" />
		</div>
	</div>
{/form:addCategory}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}