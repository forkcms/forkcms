{option:updateSettingsSuccess}<div class="alert alert-success" role="alert">{$msgUpdateSettingsIsSuccess}</div>{/option:updateSettingsSuccess}
{option:updateSettingsHasFormError}<div class="alert alert-error" role="alert">{$errFormError}</div>{/option:updateSettingsHasFormError}

<section id="settingsForm" class="profiles">
	<div class="bd">
		{form:updateSettings}
			<fieldset class="form-horizontal">
				<legend>{$lblYourData|ucfirst}</legend>

				<div class="control-group{option:txtDisplayNameError} error{/option:txtDisplayNameError}">
					<label class="control-label" for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtDisplayName}{$txtDisplayNameError}
						<small class="helpTxt muted">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
					</div>
				</div>
				<div class="control-group{option:txtEmailError} error{/option:txtEmailError}">
					<label class="control-label" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="controls">
						{$txtEmail}{$txtEmailError}
						<a href="{$var|geturlforblock:'profiles':'change_email'}">{$msgChangeEmail}</a>
					</div>
				</div>
				<div class="control-group{option:txtFirstNameError} error{/option:txtFirstNameError}">
					<label class="control-label" for="firstName">{$lblFirstName|ucfirst}</label>
					<div class="controls">
						{$txtFirstName}{$txtFirstNameError}
					</div>
				</div>
				<div class="control-group{option:txtLastNameError} error{/option:txtLastNameError}">
					<label class="control-label" for="lastName">{$lblLastName|ucfirst}</label>
					<div class="controls">
						{$txtLastName}{$txtLastNameError}
					</div>
				</div>
				<div class="control-group{option:ddmGenderError} error{/option:ddmGenderError}">
					<label class="control-label" for="gender">{$lblGender|ucfirst}</label>
					<div class="controls">
						{$ddmGender} {$ddmGenderError}
					</div>
				</div>
				<div class="birthDate control-group{option:ddmYearError} error{/option:ddmYearError}">
					<label class="control-label" for="day">{$lblBirthDate|ucfirst}</label>
					<div class="controls row-fluid">
						<div class="span1">
							{$ddmDay}
						</div>
						<div class="span1">
							{$ddmMonth}
						</div>
						<div class="span1">
							{$ddmYear}
						</div>
						 {$ddmYearError}
					</div>
				</div>
			</fieldset>

			<fieldset class="form-horizontal">
				<legend>{$lblYourLocationData|ucfirst}</legend>

				<div class="control-group{option:txtCityError} error{/option:txtCityError}">
					<label class="control-label" for="city">{$lblCity|ucfirst}</label>
					<div class="controls">
						{$txtCity}{$txtCityError}
					</div>
				</div>
				<div class="control-group{option:ddmCountryError} error{/option:ddmCountryError}">
					<label class="control-label" for="country">{$lblCountry|ucfirst}</label>
					<div class="controls">
						{$ddmCountry} {$ddmCountryError}
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updateSettings}
	</div>
</section>