{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="box">
		<div class="heading">
			<h3>{$lblTags|ucfirst}: {$msgEditTag|sprintf:{$name}}</h3>
		</div>
		<div class="options horizontal">

			<p>
				<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtName} {$txtNameError}
			</p>

			<div class="fakeP">
				<label>{$lblUsedIn|ucfirst}</label>

				{option:usage}
					<div class="dataGridHolder dataGridInHorizontalForm">
						{$usage}
					</div>
				{/option:usage}
				{option:!usage}<p>{$msgNoUsage}</p>{/option:!usage}
			</div>

		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}