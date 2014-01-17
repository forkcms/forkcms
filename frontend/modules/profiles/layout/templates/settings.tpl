{* Success *}
{option:updateSettingsSuccess}
	<div class="alert-box success"><p>{$msgUpdateSettingsIsSuccess}</p></div>
{/option:updateSettingsSuccess}

{* Error *}
{option:updateSettingsHasFormError}
	<div class="alert-box error"><p>{$errFormError}</p></div>
{/option:updateSettingsHasFormError}

<section>
	{form:updateSettings}
		<fieldset>
			<legend>{$lblYourData|ucfirst}</legend>
			<p{option:txtDisplayNameError} class="error-area"{/option:txtDisplayNameError}>
				<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtDisplayName}{$txtDisplayNameError}
				<small class="helpTxt">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
			</p>
			<p{option:txtEmailError} class="error-area"{/option:txtEmailError}>
				<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtEmail}{$txtEmailError}
			</p>
			<p>
				<a href="{$var|geturlforblock:'profiles':'change_email'}">{$msgChangeEmail}</a>
			</p>
			<p{option:txtFirstNameError} class="error-area"{/option:txtFirstNameError}>
				<label for="firstName">{$lblFirstName|ucfirst}</label>
				{$txtFirstName}{$txtFirstNameError}
			</p>
			<p{option:txtLastNameError} class="error-area"{/option:txtLastNameError}>
				<label for="lastName">{$lblLastName|ucfirst}</label>
				{$txtLastName}{$txtLastNameError}
			</p>
			<p{option:ddmGenderError} class="error-area"{/option:ddmGenderError}>
				<label for="gender">{$lblGender|ucfirst}</label>
				{$ddmGender} {$ddmGenderError}
			</p>
			<p{option:ddmYearError} class="error-area"{/option:ddmYearError}>
				<label for="day">{$lblBirthDate|ucfirst}</label>
				{$ddmDay} {$ddmMonth} {$ddmYear} {$ddmYearError}
			</p>
		</fieldset>
		<fieldset>
			<legend>{$lblYourLocationData|ucfirst}</legend>
			<p{option:txtCityError} class="error-area"{/option:txtCityError}>
				<label for="city">{$lblCity|ucfirst}</label>
				{$txtCity}{$txtCityError}
			</p>
			<p{option:ddmCountryError} class="error-area"{/option:ddmCountryError}>
				<label for="country">{$lblCountry|ucfirst}</label>
				{$ddmCountry} {$ddmCountryError}
			</p>
		</fieldset>
		<fieldset>
			<legend>{$lblYourAvatar|ucfirst}</legend>
			{option:avatar}
				<img src="{$FRONTEND_FILES_URL}/profiles/avatars/240x240/{$avatar}" />
			{/option:avatar}
			<p{option:fileAvatarError} class="error-area"{/option:fileAvatarError}>
				<label for="avatar">{$lblAvatar|ucfirst}</label>
				{$fileAvatar}{$fileAvatarError}
			</p>
		</fieldset>
		<p>
			<input type="submit" value="{$lblSave|ucfirst}" />
		</p>
	{/form:updateSettings}
</section>