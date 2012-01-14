{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabProfile">{$lblProfile|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabProfile">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblLoginDetails|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<div class="oneLiner" style="margin-bottom: 6px;">
						<p>
							<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							{$txtPassword}
						</p>
						{$txtPasswordError}
					</div>
					<table id="passwordStrengthMeter" class="passwordStrength" data-id="password">
						<tr>
							<td class="strength" id="passwordStrength">
								<p class="strength none">{$lblNone|ucfirst}</p>
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
						<label for="confirmPassword">{$lblConfirmPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtConfirmPassword} {$txtConfirmPasswordError}
					</p>
				</div>
			</div>

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblPersonalInformation|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
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

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblInterfacePreferences|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
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

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblAccountManagement|ucfirst}</h3>
				</div>
				<div class="options last">
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
		<div class="buttonHolderRight">
			<input id="addButton" class="button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>
{/form:add}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}