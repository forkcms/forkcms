{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}

	<h2>{$msgHeaderEdit|sprintf:{$nickname}}</h2>
	
	{form:edit}
		<fieldset>
		<dl>
			<dt><label for="username">{$lblUsername|ucfirst}</label></dt>
			<dd>{$txtUsername} {$txtUsernameError}</dd>
			<dt><label for="password">{$lblPassword|ucfirst}</label></dt>
			<dd>{$txtPassword} {$txtPasswordError}</dd>

			<dt><label for="nickname">{$lblNickname|ucfirst}</label></dt>
			<dd>{$txtNickname} {$txtNicknameError}</dd>
			
			<dt><label for="avatar">{$lblAvatar|ucfirst}</label></dt>
			<dd><img src="{$avatarImage}" width="64" height="64" alt="avatar" /></dd>
			<dd>{$fileAvatar} {$fileAvatarError}</dd>
			
			<dt><label for="email">{$lblEmail|ucfirst}</label></dt>
			<dd>{$txtEmail} {$txtEmailError}</dd>
			<dt><label for="name">{$lblName|ucfirst}</label></dt>
			<dd>{$txtName} {$txtNameError}</dd>
			<dt><label for="surname">{$lblSurname|ucfirst}</label></dt>
			<dd>{$txtSurname} {$txtSurnameError}</dd>

			<dt><label for="username">{$lblInterfaceLanguage|ucfirst}</label></dt>
			<dd>{$ddmInterfaceLanguage} {$ddmInterfaceLanguageError}</dd>
			
			<dd><input type="submit" value="{$lblUpdate|ucfirst}" /></dd>
		</dl>
		</fieldset>
	{/form:edit}
	<p>
		<!-- @todo why can't the second parameter be optional? -->
		<a href="{$var|geturl:delete:users}?id={$id}" class="askConfirmation" rel="{$msgConfirmDelete|sprintf:{$nickname}}" title="{$lblDelete}">{$lblDelete}</a>
	</p>

{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}