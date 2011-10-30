{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:addProfileGroup}
	<div class="box">
		<div class="heading">
			<h3>{$lblProfiles|ucfirst}: {$lblAddMembership}</h3>
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
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:addProfileGroup}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}