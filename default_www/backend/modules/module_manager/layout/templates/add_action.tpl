{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:add}
	<div class="box horizontal labelWidthLong">
		<div class="heading">
			<h3>{$lblModuleManager|ucfirst}: {$lblAddAction}</h3>
		</div>
		<div class="options">

			<p>
				<label for="action">{$lblAction|ucfirst}</label>
				{$txtAction} {$txtActionError}
			</p>
			<p>
				<label for="groupId">{$lblGroup|ucfirst}</label>
				{$ddmGroupId} {$ddmGroupIdError}
			</p>
			<p>
				<label for="modulesList">{$lblModule|ucfirst}</label>
				{$ddmModulesList} {$ddmModulesListError}
			</p>
			<p>
				<label for="levels">{$lblLevel|ucfirst}</label>
				{$ddmLevels} {$ddmLevelsError}
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
