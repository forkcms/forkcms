<div id="user">
	{option:user.loggedIn}
		{* @later	implement when profiles is done *}
	{/option:user.loggedIn}
	{option:!user.loggedIn}
		<form action="index_ingelogd.html" method="post">
			<fieldset>
				<p>
					<label for="username">Gebruikersnaam:<abbr title="{$lblRequired|ucfirst}">*</abbr></label>
					<input type="text" id="onsite_username" name="onsite_username" value="" maxlength="255" class="inputText" />
					<label for="password">Wachtwoord:<abbr title="{$lblRequired|ucfirst}">*</abbr></label>
					<input type="password" id="onsite_password" name="onsite_password" value="" maxlength="255" class="inputPassword" />
					<input type="submit" class="inputSubmit" name="submit" value="Aanmelden" />
				</p>
			</fieldset>
		</form>
	{/option:!user.loggedIn}
</div>