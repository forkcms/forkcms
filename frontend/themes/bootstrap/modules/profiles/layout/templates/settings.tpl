{* Success *}
{option:updateSettingsSuccess}
	<div class="message success"><p>{$msgUpdateSettingsIsSuccess}</p></div>
{/option:updateSettingsSuccess}

{* Error *}
{option:updateSettingsHasFormError}
	<div class="message error"><p>{$errFormError}</p></div>
{/option:updateSettingsHasFormError}

<section id="settingsForm" class="mod">
	<div class="inner">
		<div class="bd">
			{form:updateSettings}
				<fieldset>
					<legend>{$lblYourData|ucfirst}</legend>

					<p{option:txtDisplayNameError} class="errorArea"{/option:txtDisplayNameError}>
						<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtDisplayName}{$txtDisplayNameError}
						<small class="helpTxt">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
					</p>
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail}{$txtEmailError}
					</p>
					<p>
						<a href="{$var|geturlforblock:'profiles':'change_email'}">{$msgChangeEmail}</a>
					</p>
					<p{option:txtFirstNameError} class="errorArea"{/option:txtFirstNameError}>
						<label for="firstName">{$lblFirstName|ucfirst}</label>
						{$txtFirstName}{$txtFirstNameError}
					</p>
					<p{option:txtLastNameError} class="errorArea"{/option:txtLastNameError}>
						<label for="lastName">{$lblLastName|ucfirst}</label>
						{$txtLastName}{$txtLastNameError}
					</p>
					<p{option:ddmGenderError} class="errorArea"{/option:ddmGenderError}>
						<label for="gender">{$lblGender|ucfirst}</label>
						{$ddmGender} {$ddmGenderError}
					</p>
					<p{option:ddmYearError} class="errorArea"{/option:ddmYearError}>
						<label for="day">{$lblBirthDate|ucfirst}</label>
						{$ddmDay} {$ddmMonth} {$ddmYear} {$ddmYearError}
					</p>
				</fieldset>
				<fieldset>
					<legend>{$lblYourLocationData|ucfirst}</legend>

					<p{option:txtCityError} class="errorArea"{/option:txtCityError}>
						<label for="city">{$lblCity|ucfirst}</label>
						{$txtCity}{$txtCityError}
					</p>
					<p{option:ddmCountryError} class="errorArea"{/option:ddmCountryError}>
						<label for="country">{$lblCountry|ucfirst}</label>
						{$ddmCountry} {$ddmCountryError}
					</p>
				</fieldset>
				<p>
					<input class="inputSubmit" type="submit" value="{$lblSave|ucfirst}" />
				</p>
			{/form:updateSettings}
		</div>
	</div>
</section>