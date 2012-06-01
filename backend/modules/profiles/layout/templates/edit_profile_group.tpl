{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:editProfileGroup}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfiles|ucfirst}: {$lblEditMembership}</h3>
		</div>
		<div class="content">
			<fieldset>
				<p>
					<label for="group">{$lblGroup|ucfirst}</label>
					{$ddmGroup} {$ddmGroupError}
				</p>
				<p class="p0"><label for="expirationDate">{$lblExpiresOn|ucfirst}:</label></p>
				<div class="oneLiner">
					<p>
						{$txtExpirationDate} {$txtExpirationDateError}
					</p>
					<p>
						<label for="expirationTime">{$lblAt}</label>
						{$txtExpirationTime} {$txtExpirationTimeError}
					</p>
				</div>
			</fieldset>


		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showProfilesDeleteProfileGroup}
		<a href="{$var|geturl:'delete_profile_group'}&amp;id={$profileGroup.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
			<span>{$lblDelete|ucfirst}</span>
		</a>
		<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
			<p>
				{$msgConfirmProfileGroupDelete|sprintf:{$profileGroup.name}}
			</p>
		</div>
		{/option:showProfilesDeleteProfileGroup}

		<div class="buttonHolderRight">
			<input id="saveButton" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:editProfileGroup}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}