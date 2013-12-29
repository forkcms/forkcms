{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

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

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
