{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Gebruikers > Gebruiker toevoegen</p>
			</div>
			<div class="inner">

				{form:add}
				<div class="box">
					<div class="heading">
						<h3>{$msgHeaderAdd}</h3>
					</div>
					<div class="options horizontal">
						<p>
							<label for="username">{$lblUsername|ucfirst}</label>
							{$txtUsername} {$txtUsernameError}
						</p>

						<p>
							<label for="password">{$lblPassword|ucfirst}</label>
							{$txtPassword} {$txtPasswordError}
						</p>

						<p>
							<label for="nickname">{$lblNickname|ucfirst}</label>
							{$txtNickname} {$txtNicknameError}
						</p>

						<p>
							<label for="avatar">{$lblAvatar|ucfirst}</label>
							<!-- <img src="{$avatarImage}" width="64" height="64" alt="avatar" /> -->
							{$fileAvatar} {$fileAvatarError}
						</p>

						<p>
							<label for="email">{$lblEmail|ucfirst}</label>
							{$txtEmail} {$txtEmailError}
						</p>

						<p>
							<label for="name">{$lblName|ucfirst}</label>
							{$txtName} {$txtNameError}
						</p>

						<p>
							<label for="surname">{$lblSurname|ucfirst}</label>
							{$txtSurname} {$txtSurnameError}
						</p>

						<p>
							<label for="username">{$lblInterfaceLanguage|ucfirst}</label>
							{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}
						</p>

					</div>
				</div>
				
				<div class="fullwidthOptions">
					<div class="buttonHolderRight">
						{$btnAdd}
					</div>
				</div>
				
				{/form:add}

			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}


