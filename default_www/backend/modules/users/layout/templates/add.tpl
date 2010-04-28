{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
	<td id="contentHolder">

			<div class="inner">
				
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
									<h3>{$lblCredentials|ucfirst}</h3>
								</div>
								<div class="options labelWidthLong horizontal">
									<p>
										<label for="username">{$lblUsername|ucfirst}</label>
										{$txtUsername} {$txtUsernameError}
									</p>
									<p>
										<label for="password">{$lblPassword|ucfirst}</label>
										{$txtPassword} {$txtPasswordError}

										<table id="passwordStrengthMeter" class="passwordStrength" rel="password" cellspacing="0">
											<td class="strength" id="passwordStrength">
												<p class="strength none">{$lblNone|ucfirst}</p>
												<p class="strength weak" style="background: red;">{$lblWeak|ucfirst}</p>
												<p class="strength ok" style="background: orange;">{$lblOK|ucfirst}</p>
												<p class="strength strong" style="background: green;">{$lblStrong|ucfirst}</p>
											</td>
											<td>
												<p class="helpTxt">{$msgHelpStrongPassword}</p>
											</td>
										</table>
									</p>
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
										<label for="interface_language">{$lblInterfaceLanguage|ucfirst}</label>
										{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
									</p>
									<p>
										<label for="date_format">{$lblDateFormat|ucfirst}</label>
										{$ddmDateFormat} {$ddmDateFormatError}
									</p>
									<p>
										<label for="time_format">{$lblTimeFormat|ucfirst}</label>
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
											<label for="active">{$msgEnableUser}</label>
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
							<input id="add" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
						</div>
					</div>
				{/form:add}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}