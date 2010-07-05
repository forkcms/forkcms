{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$lblGeneralSettings|ucfirst}</h2>
</div>

{form:generalSettings}
	{option:warnings}
		<div class="generalMessage infoMessage content">
			<p><strong>{$msgConfigurationError}</strong></p>
			<ul class="pb0">
				{iteration:warnings}
					<li>{$warnings.message}</li>
				{/iteration:warnings}
			</ul>
		</div>
	{/option:warnings}

	<div class="box">
		<div class="heading">
			<h3>{$lblWebsiteTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></h3>
		</div>
		<div class="options">
			{$txtSiteTitle} {$txtSiteTitleError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblEmail|ucfirst}</h3>
		</div>
		<div class="options">
			<h4>{$lblFrom|ucfirst}</h4>
			<p>{$msgHelpEmailFrom}</p>
			<p>
				<label for="mailer_from_name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerFromName} {$txtMailerFromNameError}
			</p>
			<p>
				<label for="mailer_from_email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerFromEmail} {$txtMailerFromEmailError}
			</p>
		</div>
		<div class="options">
			<h4>{$lblTo|ucfirst}</h4>
			<p>{$msgHelpEmailTo}</p>
			<p>
				<label for="mailer_to_name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerToName} {$txtMailerToNameError}
			</p>
			<p>
				<label for="mailer_to_email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerToEmail} {$txtMailerToEmailError}
			</p>
		</div>
		<div class="options">
			<h4>{$lblReplyTo|ucfirst}</h4>
			<p>
				<label for="mailer_reply_to_name">{$lblName|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerReplyToName} {$txtMailerReplyToNameError}
			</p>
			<p>
				<label for="mailer_reply_to_email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtMailerReplyToEmail} {$txtMailerReplyToEmailError}

			</p>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblSMTP|ucfirst}</h3>
		</div>
		<div class="options">
			<p>
				<label for="smtp_server">{$lblServer|ucfirst} &amp; {$lblPort}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtSmtpServer}:{$txtSmtpPort} {$txtSmtpServerError} {$txtSmtpPortError}
				<span class="helpTxt">{$msgHelpSMTPServer}</span>
			</p>
			<p>
				<label for="smtp_username">{$lblUsername|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtSmtpUsername} {$txtSmtpUsernameError}
			</p>
			<p>
				<label for="smtp_password">{$lblPassword|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
				{$txtSmtpPassword} {$txtSmtpPasswordError}
			</p>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblLanguages|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpLanguages}:</p>

			<ul class="inputList">
				{iteration:activeLanguages}
					<li>{$activeLanguages.chkActiveLanguages} <label for="{$activeLanguages.id}">{$activeLanguages.label|ucfirst}{option:activeLanguages.default} ({$lblDefault}){/option:activeLanguages.default}</label></li>
				{/iteration:activeLanguages}
			</ul>

			<p>{$msgHelpRedirectLanguages}:</p>
			<ul class="inputList">
				{iteration:redirectLanguages}
					<li>{$redirectLanguages.chkRedirectLanguages} <label for="{$redirectLanguages.id}">{$redirectLanguages.label|ucfirst}{option:redirectLanguages.default} ({$lblDefault}){/option:redirectLanguages.default}</label></li>
				{/iteration:redirectLanguages}
			</ul>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblScripts|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpScripts}</p>
			{$txtSiteWideHtml} {$txtSiteWideHtmlError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblThemes|ucfirst}</h3>
		</div>
		<div class="content">
			<p>{$msgHelpThemes}</p>
			{$ddmTheme} {$ddmThemeError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblDomains|ucfirst}</h3>
		</div>
		<div class="content">
			<p>{$msgHelpDomains}</p>
			{$txtSiteDomains} {$txtSiteDomainsError}
		</div>
	</div>

	<div id="settingsApiKeys" class="box">
		<div class="heading">
			<h3>{$lblAPIKeys|ucfirst}</h3>
		</div>
		<div class="content">
			<p>{$msgHelpAPIKeys}</p>
			<div class="datagridHolder">
				<table border="0" cellspacing="0" cellpadding="0" class="datagrid dynamicStriping">
					<thead>
						<tr>
							<th class="title" style="width: 20%;"><span>{$lblName|ucfirst}</span></th>
							<th style="width: 40%;"><span>{$lblAPIKey|ucfirst}</span></th>
							<th style="width: 60%;"><span>{$lblAPIURL|ucfirst}</span></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="title"><label for="forkApiPublicKey">Fork public key</label></td>
							<td>{$txtForkApiPublicKey} {$txtForkApiPublicKeyError}</td>
							<td><a href="http://www.fork-cms.be/info/api">http://www.fork-cms.be/info/api</a></td>
						</tr>
						<tr>
							<td class="title"><label for="forkApiPrivateKey">Fork private key</label></td>
							<td>{$txtForkApiPrivateKey} {$txtForkApiPrivateKeyError}</td>
							<td>&nbsp;</td>
						</tr>
						{option:needsGoogleMaps}
							<tr>
								<td class="title"><label for="googleMapsKey">Google maps key<abbr title="{$lblRequiredField}">*</abbr></label></td>
								<td>{$txtGoogleMapsKey} {$txtGoogleMapsKeyError}</td>
								<td><a href="http://code.google.com/apis/maps/signup.html">http://code.google.com/apis/maps/signup.html</a></td>
							</tr>
						{/option:needsGoogleMaps}
						{option:needsAkismet}
							<tr>
								<td class="title"><label for="akismetKey">Akismet key<abbr title="{$lblRequiredField}">*</abbr></label></td>
								<td>{$txtAkismetKey} {$txtAkismetKeyError}</td>
								<td><a href="http://akismet.com/personal">http://akismet.com/personal</a></td>
							</tr>
						{/option:needsAkismet}
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>
{/form:generalSettings}

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}