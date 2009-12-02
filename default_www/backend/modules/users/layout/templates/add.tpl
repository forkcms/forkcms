{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$msgHeaderAdd}</h2>
	
	{form:add}
		<fieldset>
			<label for="username">{$lblUsername|ucfirst}</label>
			<p>{$txtUsername} {$txtUsernameError}</p>
			<label for="password">{$lblPassword|ucfirst}</label>
			<p>{$txtPassword} {$txtPasswordError}</p>

			<label for="nickname">{$lblNickname|ucfirst}</label>
			<p>{$txtNickname} {$txtNicknameError}</p>
			
			<label for="avatar">{$lblAvatar|ucfirst}</label>
			<p><img src="{$avatarImage}" width="64" height="64" alt="avatar" /></p>
			<p>{$fileAvatar} {$fileAvatarError}</p>
			
			<label for="email">{$lblEmail|ucfirst}</label>
			<p>{$txtEmail} {$txtEmailError}</p>
			<label for="name">{$lblName|ucfirst}</label>
			<p>{$txtName} {$txtNameError}</p>
			<label for="surname">{$lblSurname|ucfirst}</label>
			<p>{$txtSurname} {$txtSurnameError}</p>

			<label for="username">{$lblInterfaceLanguage|ucfirst}</label>
			<p>{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}</p>
			
			<p>{$btnAdd}</p>
		</fieldset>
	{/form:add}

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}