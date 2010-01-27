{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
	<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Gebruikers > Gebruiker toevoegen</p>
			</div>
			<div class="inner">

				{form:edit}
				<div class="box">
					<div class="heading">
						<h3>{$msgHeaderEdit|sprintf:{$nickname}}</h3>
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
						{$btnEdit}
						<a href="{$var|geturl:'delete'}?id={$id}" class="askConfirmation" rel="{$msgConfirmDelete|sprintf:{$nickname}}" title="{$lblDelete}">{$lblDelete}</a>
					</div>
				</div>
				
				{/form:edit}

			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}