{option:updateSettingsSuccess}<div class="alert alert-success" role="alert">{$msgUpdateSettingsIsSuccess}</div>{/option:updateSettingsSuccess}
{option:updateSettingsHasFormError}<div class="alert alert-danger" role="alert">{$errFormError}</div>{/option:updateSettingsHasFormError}

<section id="settingsForm" class="profiles">
	<div class="bd">
		{form:updateSettings}
			<fieldset class="form-horizontal">
				<legend>{$lblYourData|ucfirst}</legend>

				<div class="form-group{option:txtDisplayNameError} has-error{/option:txtDisplayNameError}">
					<label class="control-label col-sm-2" for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
						{$txtDisplayName}{$txtDisplayNameError}
						<small class="helpTxt muted">{$msgHelpDisplayNameChanges|sprintf:{$maxDisplayNameChanges}:{$displayNameChangesLeft}}</small>
					</div>
				</div>
				<div class="form-group{option:txtEmailError} has-error{/option:txtEmailError}">
					<label class="control-label col-sm-2" for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
					<div class="col-sm-6">
						{$txtEmail}{$txtEmailError}
						<a href="{$var|geturlforblock:'profiles':'change_email'}">{$msgChangeEmail}</a>
					</div>
				</div>
				<div class="form-group{option:txtFirstNameError} has-error{/option:txtFirstNameError}">
					<label class="control-label col-sm-2" for="firstName">{$lblFirstName|ucfirst}</label>
					<div class="col-sm-6">
						{$txtFirstName}{$txtFirstNameError}
					</div>
				</div>
				<div class="form-group{option:txtLastNameError} has-error{/option:txtLastNameError}">
					<label class="control-label col-sm-2" for="lastName">{$lblLastName|ucfirst}</label>
					<div class="col-sm-6">
						{$txtLastName}{$txtLastNameError}
					</div>
				</div>
				<div class="form-group{option:ddmGenderError} has-error{/option:ddmGenderError}">
					<label class="control-label col-sm-2" for="gender">{$lblGender|ucfirst}</label>
					<div class="col-sm-2">
						{$ddmGender} {$ddmGenderError}
					</div>
				</div>
				<div class="birthDate form-group{option:ddmYearError} has-error{/option:ddmYearError}">
					<label class="control-label col-sm-2" for="day">{$lblBirthDate|ucfirst}</label>
					<div class="col-sm-6">
					  <div class="row">
						<div class="col-xs-4">
							{$ddmDay}
						</div>
						<div class="col-xs-4">
							{$ddmMonth}
						</div>
						<div class="col-xs-4">
							{$ddmYear}{$ddmYearError}
						</div>
						 
					  </div>
					</div>
				</div>
			</fieldset>

			<fieldset class="form-horizontal">
				<legend>{$lblYourLocationData|ucfirst}</legend>

				<div class="form-group{option:txtCityError} has-error{/option:txtCityError}">
					<label class="control-label col-sm-2" for="city">{$lblCity|ucfirst}</label>
					<div class="col-sm-6">
						{$txtCity}{$txtCityError}
					</div>
				</div>
				<div class="form-group{option:ddmCountryError} has-error{/option:ddmCountryError}">
					<label class="control-label col-sm-2" for="country">{$lblCountry|ucfirst}</label>
					<div class="col-sm-6">
						{$ddmCountry} {$ddmCountryError}
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-6">
						<input class="btn btn-primary" type="submit" value="{$lblSave|ucfirst}" />
					</div>
				</div>
			</fieldset>
		{/form:updateSettings}
	</div>
</section>
