{include:{$BACKEND_CORE_PATH}/Layout/Templates/Head.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureStartModule.tpl}

<div class="pageTitle">
	<h2>{$lblProfiles|ucfirst}: {$lblAdd}</h2>
</div>

{form:add}
    {option:notifyProfile}
    <div class="generalMessage infoMessage content">
		<p>{$lblNewProfileWillBeNotified|ucfirst}</p>
	</div>
    {/option:notifyProfile}
		
	<div class="box">
		<div class="heading">
			<h3>{$lblProfile|ucfirst}</h3>
		</div>
		<div class="options">
			<fieldset>
				<p>
					<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<label for="displayName">{$lblDisplayName|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtDisplayName} {$txtDisplayNameError}
				</p>
				<p>
					<label for="password">{$lblPassword|ucfirst}{option:!notifyProfile}<abbr title="{$lblRequiredField|ucfirst}">*</abbr>{/option:!notifyProfile}</label>
					{$txtPassword} {$txtPasswordError}
					{option:notifyProfile}<span class="helpTxt">{$msgAutoGeneratedPasswordIfEmpty}</span>{/option:notifyProfile}
				</p>
			</fieldset>
		</div>
    </div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSettings|ucfirst}</h3>
		</div>
		<div class="options">
			<fieldset>
				<p>
					<label for="firstName">{$lblFirstName|ucfirst}</label>
					{$txtFirstName} {$txtFirstNameError}
				</p>
				<p>
					<label for="lastName">{$lblLastName|ucfirst}</label>
					{$txtLastName} {$txtLastNameError}
				</p>
				<p>
					<label for="gender">{$lblGender|ucfirst}</label>
					{$ddmGender} {$ddmGenderError}
				</p>
				<p>
					<label for="day">{$lblBirthDate|ucfirst}</label>
					<span class="tinyInput">{$ddmDay}</span> <span class="smallInput">{$ddmMonth}</span> <span class="tinyInput">{$ddmYear}</span> {$ddmYearError}
				</p>
				<p>
					<label for="city">{$lblCity|ucfirst}</label>
					{$txtCity} {$txtCityError}
				</p>
				<p>
					<label for="country">{$lblCountry|ucfirst}</label>
					{$ddmCountry} {$ddmCountryError}
				</p>
			</fieldset>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblAdd|ucfirst}" />
		</div>
	</div>

{/form:add}

{include:{$BACKEND_CORE_PATH}/Layout/Templates/StructureEndModule.tpl}
{include:{$BACKEND_CORE_PATH}/Layout/Templates/Footer.tpl}
