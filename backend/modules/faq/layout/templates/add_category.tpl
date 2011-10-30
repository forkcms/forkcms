{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add_category}
	<div class="pageTitle">
		<h2>{$lblFaq|ucfirst}: {$lblAddCategory}</h2>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblFaq|ucfirst}: {$lblAddCategory}</h3>
		</div>
		<div class="options">
			<p>
				<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtName} {$txtNameError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="addCategory" value="{$lblAddCategory|ucfirst}" />
		</div>
	</div>
{/form:add_category}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}