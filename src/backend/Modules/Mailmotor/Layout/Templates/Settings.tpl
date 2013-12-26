{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblModuleSettings|ucfirst}: {$lblMailmotor|ucfirst}</h2>
</div>

{option:!clientId}
	<div class="generalMessage infoMessage content">
		<p><strong>{$msgConfigurationError}</strong></p>
		<ul class="pb0">
			{option:!account}<li>{$errNoCMAccount}</li>{/option:!account}
			{option:account}<li>{$errNoCMClientID}</li>{/option:account}
		</ul>
	</div>
{/option:!clientId}

<div class="tabs">
	<ul>
		<li><a href="#tabSettingsGeneral">{$lblGeneral|ucfirst}</a></li>
		<li><a href="#tabSettingsAccount">CampaignMonitor - {$lblAccountSettings|ucfirst}</a></li>
		{option:account}<li><a href="#tabSettingsClient">CampaignMonitor - {$lblClientSettings|ucfirst}</a></li>{/option:account}
	</ul>

	<div id="tabSettingsGeneral">
		{form:settingsGeneral}
		<div id="general">
			<div class="box horizontal">
				<div class="heading">
					<h3>{$lblSender|ucfirst}</h3>
				</div>

				<div class="options">
					<p>
						<label for="fromName">{$lblName|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						{$txtFromName} {$txtFromNameError}
					</p>

					<p>
						<label for="fromEmail">{$lblEmailAddress|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						{$txtFromEmail} {$txtFromEmailError}
					</p>
				</div>
			</div>

			<div class="box horizontal">
				<div class="heading">
					<h3>{$lblReplyTo|ucfirst}</h3>
				</div>

				<div class="options">
					<p>
						<label for="replyToEmail">{$lblEmailAddress|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						{$txtReplyToEmail} {$txtReplyToEmailError}
					</p>
				</div>
			</div>

			<div class="box">
				<div class="heading">
					<h3>{$lblPlainTextVersion|ucfirst}</h3>
				</div>

				<div class="options">
					<ul class="inputList p0">
						<li>{$chkPlainTextEditable} <label for="plainTextEditable">{$msgPlainTextEditable|ucfirst}</label></li>
					</ul>
				</div>
			</div>

			{option:userIsGod}
			<div class="box horizontal">
				<div class="heading">
					<h3>{$lblPrices|ucfirst}</h3>
				</div>

				<div class="options">
					<p>
						<label for="pricePerEmail">{$lblPerSentMailing|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						€ {$txtPricePerEmail} {$txtPricePerEmailError}
						<span class="helpTxt">{$msgHelpPrice}</span>
					</p>
					<p>
						<label for="pricePerEmail">{$lblPerCampaign|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
						€ {$txtPricePerCampaign} {$txtPricePerCampaignError}
						<span class="helpTxt">{$msgHelpPrice}</span>
					</p>
				</div>
			</div>
			{/option:userIsGod}

			<div class="fullwidthOptions">
				<div class="buttonHolderRight">
					<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
				</div>
			</div>
		</div>
		{/form:settingsGeneral}
	</div>

	<div id="tabSettingsAccount">
		{form:settingsAccount}
		<div class="box horizontal" id="accountBox">
			<div class="heading">
				<h3>CampaignMonitor - Account</h3>
			</div>
			<div class="options">
				<p>
					<label for="url">{$lblURL|uppercase}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtUrl} {$txtUrlError}
					<span class="helpTxt">{$msgHelpCMURL}</span>
				</p>
				<p>
					<label for="username">{$lblUsername|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtUsername} {$txtUsernameError}
				</p>
				<p>
					<label for="password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtPassword} {$txtPasswordError}
				</p>
				<div class="buttonHolder">
					{option:!account}<a id="linkAccount" href="#" class="askConfirmation button inputButton"><span>{$msgLinkCMAccount}</span></a>{/option:!account}
					{option:account}
					<a id="unlinkAccount" href="#" class="askConfirmation submitButton button inputButton"><span>{$msgUnlinkCMAccount}</span></a>
					{option:clientId}<a href="{$var|geturl:'index'}" class="mainButton button"><span>{$msgViewMailings}</span></a>{/option:clientId}
					{/option:account}
				</div>
			</div>
		</div>
		{/form:settingsAccount}
	</div>

	{option:account}
	<div id="tabSettingsClient">
		{form:settingsClient}
		<div class="box horizontal">
			<div class="heading">
				<h3>CampaignMonitor - Client</h3>
			</div>
			<div class="options id">
				<p>
					<label for="clientId">{$lblClient|ucfirst}</label>
					{$ddmClientId}
				</p>
				{option:!clientId}<p class="formError"><strong>{$msgNoClientID}</strong></p>{/option:!clientId}
			</div>

			<div class="options generate">
				<p>
					<label for="companyName">{$lblCompanyName|ucfirst}<abbr title="{$lblRequiredField|ucfirst}">*</abbr></label>
					{$txtCompanyName} {$txtCompanyNameError}
				</p>
				<p>
					<label for="countries">{$lblCountry|ucfirst}</label>
					{$ddmCountries} {$ddmCountriesError}
				</p>
				<p>
					<label for="timezones">{$lblTimezone|ucfirst}</label>
					{$ddmTimezones} {$ddmTimezonesError}
				</p>
			</div>

			<div class="fullwidthOptions">
				<div class="buttonHolderRight">
					<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
				</div>
			</div>
		</div>
		{/form:settingsClient}
	</div>
	{/option:account}
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}