{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

{form:edit}
	<div class="tabs">
		<ul>
			<li><a href="#tabGeneral">{$lblGeneral|ucfirst}</a></li>
			<li><a href="#tabGroups">{$lblGroups|ucfirst}</a></li>
		</ul>

		<div id="tabGeneral" class="subtleBox">
			<div class="heading">
				<h3>{$lblProfile|ucfirst}</h3>
			</div>
			<div class="options">
				<fieldset>
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						{$txtDisplayName} {$txtDisplayNameError}
					</p>
					<p>
						<label for="password">{$lblPassword|ucfirst}</label>
						{$txtPassword} {$txtPasswordError}
					</p>
				</fieldset>
			</div>

			<div class="heading">
				<h3>{$lblSettings|ucfirst}</h3>
			</div>
			<div class="options">
				<fieldset>
					<p>
						<label for="firstName">{$lblFirstName|ucfirst}</label>
						{$txtFirstName} {$txtFirstNameError}
					</p>
					<p>
						<label for="lastName">{$lblLastName|ucfirst}</label>
						{$txtLastName} {$txtLastNameError}
					</p>
					<p>
						<label for="gender">{$lblGender|ucfirst}</label>
						{$ddmGender} {$ddmGenderError}
					</p>
					<p>
						<label for="day">{$lblBirthDate|ucfirst}</label>
						<span class="tinyInput">{$ddmDay}</span> <span class="smallInput">{$ddmMonth}</span> <span class="tinyInput">{$ddmYear}</span> {$ddmYearError}
					</p>
					<p>
						<label for="city">{$lblCity|ucfirst}</label>
						{$txtCity} {$txtCityError}
					</p>
					<p>
						<label for="country">{$lblCountry|ucfirst}</label>
						{$ddmCountry} {$ddmCountryError}
					</p>
				</fieldset>
			</div>
		</div>

		<div id="tabGroups">
			<div class="dataGridHolder">
				<div class="tableHeading">
					<div class="oneLiner">
						<h3 class="floater">{$lblGroups|ucfirst}</h3>
					</div>

					{option:showProfilesAddProfileGroup}
					<div class="buttonHolderRight">
						<a href="{$var|geturl:'add_profile_group'}&amp;id={$profile.id}" class="button icon iconAdd" title="{$lblAddGroup|ucfirst}">
							<span>{$lblAddGroup|ucfirst}</span>
						</a>
					</div>
					{/option:showProfilesAddProfileGroup}
				</div>

				{option:dgGroups}{$dgGroups}{/option:dgGroups}
				{option:!dgGroups}
					<p>{$msgNoGroups}</p>
				{/option:!dgGroups}
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		{* Depending on the status of the profile, we want to show a delete button or a undelete button*}

		{option:showProfilesDelete}
		{option:deleted}
			<a href="{$var|geturl:'delete'}&amp;id={$profile.id}" data-message-id="confirmUndelete" class="askConfirmation button linkButton icon iconApprove">
				<span>{$lblUndelete|ucfirst}</span>
			</a>
		{/option:deleted}

		{option:!deleted}
			<a href="{$var|geturl:'delete'}&amp;id={$profile.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
		{/option:!deleted}
		{/option:showProfilesDelete}

		{* Depending on the status of the profile, we want to show a block button or a unblock button*}

		{option:showProfilesBlock}
		{option:blocked}
			<a href="{$var|geturl:'block'}&amp;id={$profile.id}" data-message-id="confirmUnblock" class="askConfirmation button linkButton icon iconApprove">
				<span>{$lblUnblock|ucfirst}</span>
			</a>
		{/option:blocked}

		{option:!blocked}
			<a href="{$var|geturl:'block'}&amp;id={$profile.id}" data-message-id="confirmBlock" class="askConfirmation button linkButton icon iconReject">
				<span>{$lblBlock|ucfirst}</span>
			</a>
		{/option:!blocked}
		{/option:showProfilesBlock}

		<div class="buttonHolderRight">
			<input id="saveButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$profile.email}}
		</p>
	</div>

	<div id="confirmUndelete" title="{$lblUndelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmUndelete|sprintf:{$profile.email}}
		</p>
	</div>

	<div id="confirmBlock" title="{$lblBlock|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmBlock|sprintf:{$profile.email}}
		</p>
	</div>
	<div id="confirmUnblock" title="{$lblUnblock|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmUnblock|sprintf:{$profile.email}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}
