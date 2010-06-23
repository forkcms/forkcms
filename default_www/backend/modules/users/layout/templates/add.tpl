{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblUsers|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
	<div id="tabs" class="tabs">
		<ul>
			<li><a href="#tabSettings">{$lblSettings|ucfirst}</a></li>
			<li><a href="#tabPermissions">{$lblPermissions|ucfirst}</a></li>
		</ul>

		<div id="tabSettings">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblLoginCredentials|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="username">{$lblUsername|ucfirst}</label>
						{$txtUsername} {$txtUsernameError}
					</p>
					<p>
						<label for="password">{$lblPassword|ucfirst}</label>
						{$txtPassword} {$txtPasswordError}
					</p>
					<table id="passwordStrengthMeter" class="passwordStrength" rel="password" cellspacing="0">
						<tr>
							<td class="strength" id="passwordStrength">
								<p class="strength none">{$lblNone|ucfirst}</p>
								<p class="strength weak" style="background: red;">{$lblWeak|ucfirst}</p>
								<p class="strength ok" style="background: orange;">{$lblOK|ucfirst}</p>
								<p class="strength strong" style="background: green;">{$lblStrong|ucfirst}</p>
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

			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblPersonalInformation|ucfirst}</h3>
				</div>
				<div class="options labelWidthLong horizontal">
					<p>
						<label for="name">{$lblName|ucfirst}</label>
						{$txtName} {$txtNameError}
					</p>
					<p>
						<label for="surname">{$lblSurname|ucfirst}</label>
						{$txtSurname} {$txtSurnameError}
					</p>
					<p>
						<label for="email">{$lblEmail|ucfirst}</label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<label for="nickname">{$lblNickname|ucfirst}</label>
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
						<label for="interfaceLanguage">{$lblInterfaceLanguage|ucfirst}</label>
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
				</div>
			</div>
		</div>

		<div id="tabPermissions">
			<div class="subtleBox">
				<div class="heading">
					<h3>{$lblAccountManagement|ucfirst}</h3>
				</div>
				<div class="options last horizontal">
					<ul class="inputList">
						<li>
							{$chkActive}
							<label for="active">{$msgHelpActive}</label>
							 {$chkActiveError}
						</li>
					</ul>
					<p>
						<label for="group">{$lblGroup|ucfirst}</label>
						{$ddmGroup} {$ddmGroupError}
					</p>
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

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}