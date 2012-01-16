{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}: {$msgEditUser|sprintf:{$record.settings.nickname}}</h2>
</div>

{form:edit}
	<table class="settingsUserInfo">
		<tr>
			<td>
				<div class="avatar av48">
					{option:record.settings.avatar}
						<img src="{$FRONTEND_FILES_URL}/backend_users/avatars/64x64/{$record.settings.avatar}" width="48" height="48" alt="" />
					{/option:record.settings.avatar}
				</div>
			</td>
			<td>
				<table class="infoGrid">
					<tr>
						<th>{$lblName|ucfirst}:</th>
						<td><strong>{$record.settings.name} {$record.settings.surname}</strong></td>
					</tr>
					<tr>
						<th>{$lblNickname|ucfirst}:</th>
						<td>{$record.settings.nickname}</td>
					</tr>
					<tr>
						<th>{$lblEmail|ucfirst}:</th>
						<td>{$record.email}</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabProfile">{$lblProfile|ucfirst}</a></li>
			{option:allowPasswordEdit}<li><a href="#tabPassword">{$lblPassword|ucfirst}</a></li>{/option:allowPasswordEdit}
			<li><a href="#tabSettings">{$lblSettings|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabProfile">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblPersonalInformation|ucfirst}</h3>
				</div>
				<div class="options horizontal labelWidthLong">
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<label for="name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtName} {$txtNameError}
					</p>
					<p>
						<label for="surname">{$lblSurname|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtSurname} {$txtSurnameError}
					</p>
					<p>
						<label for="nickname">{$lblNickname|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtNickname} {$txtNicknameError}
						<span class="helpTxt">{$msgHelpNickname}</span>
					</p>
					<p>
						<label for="avatar">{$lblAvatar|ucfirst}</label>
						{$fileAvatar} {$fileAvatarError}
						<span class="helpTxt">{$msgHelpAvatar}</span>
					</p>
				</div>
			</div>
		</div>

		<div id="tabSettings">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblInterfacePreferences|ucfirst}</h3>
				</div>
				<div class="options horizontal labelWidthLong">
					<p>
						<label for="interfaceLanguage">{$lblLanguage|ucfirst}</label>
						{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
					</p>
					<p>
						<label for="dateFormat">{$lblDateFormat|ucfirst}</label>
						{$ddmDateFormat} {$ddmDateFormatError}
					</p>
					<p>
						<label for="timeFormat">{$lblTimeFormat|ucfirst}</label>
						{$ddmTimeFormat} {$ddmTimeFormatError}
					</p>
					<p>
						<label for="numberFormat">{$lblNumberFormat|ucfirst}</label>
						{$ddmNumberFormat} {$ddmNumberFormatError}
					</p>
				</div>
			</div>
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblCSV|ucfirst}</h3>
				</div>
				<div class="options horizontal labelWidthLong">
					<p>
						<label for="csvSplitCharacter">{$lblSplitCharacter|ucfirst}</label>
						{$ddmCsvSplitCharacter} {$ddmCsvSplitCharacterError}
					</p>
					<p>
						<label for="csvLineEnding">{$lblLineEnding|ucfirst}</label>
						{$ddmCsvLineEnding} {$ddmCsvLineEndingError}
					</p>
				</div>
			</div>
		</div>

		{option:allowPasswordEdit}
		<div id="tabPassword">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblChangePassword|ucfirst}</h3>
				</div>
				<div class="options horizontal labelWidthLong">
					<p>
						<label for="newPassword">{$lblPassword|ucfirst}</label>
						{$txtNewPassword} {$txtNewPasswordError}
					</p>
					<table id="passwordStrengthMeter" class="passwordStrength" data-id="newPassword">
						<tr>
							<td class="strength" id="passwordStrength">
								<p class="strength none">/</p>
								<p class="strength weak">{$lblWeak|ucfirst}</p>
								<p class="strength ok">{$lblOK|ucfirst}</p>
								<p class="strength strong">{$lblStrong|ucfirst}</p>
							</td>
							<td>
								<p class="helpTxt">{$msgHelpStrongPassword}</p>
							</td>
						</tr>
					</table>
					<p>
						<label for="confirmPassword">{$lblConfirmPassword|ucfirst}</label>
						{$txtConfirmPassword} {$txtConfirmPasswordError}
					</p>
				</div>
			</div>
		</div>
		{/option:allowPasswordEdit}

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblAccountManagement|ucfirst}</h3>
				</div>

				<div class="options">
					<ul class="inputList">
						<li>{$chkActive} <label for="active">{$msgHelpActive}</label> {$chkActiveError}</li>
						<li>{$chkApiAccess} <label for="apiAccess">{$msgHelpAPIAccess}</label> {$chkApiAccessError}</li>
					</ul>
					<p>{$lblGroups|ucfirst}</p>
					<ul id="groupList" class="inputList">
						{iteration:groups}
							<li>{$groups.chkGroups} <label for="{$groups.id}">{$groups.label}</label></li>
						{/iteration:groups}
					</ul>
					{$chkGroupsError}
				</div>
			</div>
		</div>
	</div>

	<div class="fullwidthOptions">
		{option:showUsersDelete}
			<a href="{$var|geturl:'delete'}&amp;id={$record.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
				<span>{$lblDelete|ucfirst}</span>
			</a>
		{/option:showUsersDelete}

		<div class="buttonHolderRight">
			<input id="editButton" class="inputButton button mainButton" type="submit" name="edit" value="{$lblSave|ucfirst}" />
		</div>
	</div>

	<div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
		<p>
			{$msgConfirmDelete|sprintf:{$record.settings.nickname}}
		</p>
	</div>
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}