{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

{form:edit}
	<div class="box">
		<div class="heading">
			<h3>{$lblEditTag} {$msgEditWithItem|sprintf:{$name}}</h3>
		</div>
		<div class="options">
			<p>
				<label for="name">{$lblName|ucfirst}</label>
				{$txtName} {$txtNameError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblEdit|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/foot.tpl'}