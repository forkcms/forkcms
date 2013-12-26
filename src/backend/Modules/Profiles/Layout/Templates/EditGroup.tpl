{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:editGroup}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfiles|ucfirst}: {$lblEditGroup}</h3>
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
		{option:showProfilesDeleteGroup}
		<a href="{$var|geturl:'delete_group'}&amp;id={$group.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmDeleteGroup|sprintf:{$group.name}}
			</p>
		</div>
		{/option:showProfilesDeleteGroup}

		<div class="buttonHolderRight">
			<input id="saveButton" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editGroup}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}