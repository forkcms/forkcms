{* Success *}
{option:updateSettingsSuccess}
	<div class="alert alert-success"><p>{$msgUpdateSettingsIsSuccess}</p></div>
{/option:updateSettingsSuccess}

{* Error *}
{option:updateSettingsHasFormError}
	<div class="alert alert-danger"><p>{$errFormError}</p></div>
{/option:updateSettingsHasFormError}

<section id="settingsForm">
	{form:updateSettings}
		<fieldset>
			<legend>{$lblYourData|ucfirst}</legend>

			<p{option:txtDisplayNameError} class="alert alert-danger"{/option:txtDisplayNameError}>
				<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtDisplayName}{$txtDisplayNameError}
				<small class="helpTxt">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
			</p>
			<p{option:txtEmailError} class="alert alert-danger"{/option:txtEmailError}>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail}{$txtEmailError}
			</p>
			<p>
				<a href="{$var|geturlforblock:'profiles':'change_email'}">{$msgChangeEmail}</a>
			</p>
			<p{option:txtFirstNameError} class="alert alert-danger"{/option:txtFirstNameError}>
				<label for="firstName">{$lblFirstName|ucfirst}</label>
				{$txtFirstName}{$txtFirstNameError}
			</p>
			<p{option:txtLastNameError} class="alert alert-danger"{/option:txtLastNameError}>
				<label for="lastName">{$lblLastName|ucfirst}</label>
				{$txtLastName}{$txtLastNameError}
			</p>
			<p{option:ddmGenderError} class="alert alert-danger"{/option:ddmGenderError}>
				<label for="gender">{$lblGender|ucfirst}</label>
				{$ddmGender} {$ddmGenderError}
			</p>
			<p{option:ddmYearError} class="alert alert-danger"{/option:ddmYearError}>
				<label for="day">{$lblBirthDate|ucfirst}</label>
				{$ddmDay} {$ddmMonth} {$ddmYear} {$ddmYearError}
			</p>
		</fieldset>
		<fieldset>
			<legend>{$lblYourLocationData|ucfirst}</legend>

			<p{option:txtCityError} class="alert alert-danger"{/option:txtCityError}>
				<label for="city">{$lblCity|ucfirst}</label>
				{$txtCity}{$txtCityError}
			</p>
			<p{option:ddmCountryError} class="alert alert-danger"{/option:ddmCountryError}>
				<label for="country">{$lblCountry|ucfirst}</label>
				{$ddmCountry} {$ddmCountryError}
			</p>
		</fieldset>
		<fieldset>
			<legend>{$lblYourAvatar|ucfirst}</legend>
			{option:avatar}
				<img src="{$FRONTEND_FILES_URL}/profiles/avatars/240x240/{$avatar}" />
			{/option:avatar}

			<p{option:fileAvatarError} class="alert alert-danger"{/option:fileAvatarError}>
				<label for="avatar">{$lblAvatar|ucfirst}</label>
				{$fileAvatar}{$fileAvatarError}
			</p>
		</fieldset>
		<p>
			<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
		</p>
	{/form:updateSettings}
</section>