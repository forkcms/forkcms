{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:addGroup}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfiles|ucfirst}: {$lblAddGroup}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtName} {$txtNameError}
				</p>
			</fieldset>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAddGroup|ucfirst}" />
		</div>
	</div>
{/form:addGroup}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}