{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblGeneralSettings|ucfirst}</h2>
</div>

{form:settingsIndex}
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
			<h3>
				<label for="siteTitle">{$lblWebsiteTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
			</h3>
		</div>
		<div class="options">
			{$txtSiteTitle} {$txtSiteTitleError}
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblScripts|ucfirst}</h3>
		</div>
		<div class="options">
			<div class="textareaHolder">
				<p class="p0"><label for="siteHtmlHeader"><code>&lt;head&gt;</code> script(s)</label></p>
				{$txtSiteHtmlHeader} {$txtSiteHtmlHeaderError}
				<span class="helpTxt">{$msgHelpScriptsHead}</span>
			</div>
		</div>
		<div class="options">
			<div class="textareaHolder">
				<p class="p0"><label for="siteHtmlFooter">End of <code>&lt;body&gt;</code> script(s)</label></p>
				{$txtSiteHtmlFooter} {$txtSiteHtmlFooterError}
				<span class="helpTxt">{$msgHelpScriptsFoot}</span>
			</div>
		</div>
	</div>

	<div class="box">
		<div class="heading">
			<h3>{$lblLanguages|ucfirst}</h3>
		</div>
		<div class="options">
			<p>{$msgHelpLanguages}</p>
			<ul id="activeLanguages" class="inputList pb0">
				{iteration:activeLanguages}
					<li>{$activeLanguages.chkActiveLanguages} <label for="{$activeLanguages.id}">{$activeLanguages.label|ucfirst}{option:activeLanguages.default} ({$lblDefault}){/option:activeLanguages.default}</label></li>
				{/iteration:activeLanguages}
			</ul>
		</div>
		<div class="options">
			<p>{$msgHelpRedirectLanguages}</p>
			<ul id="redirectLanguages" class="inputList pb0">
				{iteration:redirectLanguages}
					<li>{$redirectLanguages.chkRedirectLanguages} <label for="{$redirectLanguages.id}">{$redirectLanguages.label|ucfirst}{option:redirectLanguages.default} ({$lblDefault}){/option:redirectLanguages.default}</label></li>
				{/iteration:redirectLanguages}
			</ul>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblDateAndTime|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="timeFormat">{$lblTimeFormat|ucfirst}</label>
				{$ddmTimeFormat} {$ddmTimeFormatError}
				<span class="helpTxt">{$msgHelpTimeFormat}</span>
			</p>
			<p>
				<label for="dateFormatShort">{$lblShortDateFormat|ucfirst}</label>
				{$ddmDateFormatShort} {$ddmDateFormatShortError}
				<span class="helpTxt">{$msgHelpDateFormatShort}</span>
			</p>
			<p>
				<label for="dateFormatLong">{$lblLongDateFormat|ucfirst}</label>
				{$ddmDateFormatLong} {$ddmDateFormatLongError}
				<span class="helpTxt">{$msgHelpDateFormatLong}</span>
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>{$lblNumbers|ucfirst}</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="numberFormat">{$lblNumberFormat|ucfirst}</label>
				{$ddmNumberFormat} {$ddmNumberFormatError}
				<span class="helpTxt">{$msgHelpNumberFormat}</span>
			</p>
		</div>
	</div>

	<div id="settingsApiKeys" class="box">
		<div class="heading">
			<h3>{$lblAPIKeys|ucfirst}</h3>
		</div>
		<div class="content">
			<p>{$msgHelpAPIKeys}</p>
			<div class="dataGridHolder">
				<table class="dataGrid dynamicStriping">
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
								<td class="title"><label for="akismetKey">Akismet key</label></td>
								<td>{$txtAkismetKey} {$txtAkismetKeyError}</td>
								<td><a href="http://akismet.com/personal">http://akismet.com/personal</a></td>
							</tr>
						{/option:needsAkismet}
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>CKFinder</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="ckfinderLicenseName">{$lblLicenseName|ucfirst}</label>
				{$txtCkfinderLicenseName} {$txtCkfinderLicenseNameError}
			</p>
			<p>
				<label for="ckfinderLicenseKey">{$lblLicenseKey|ucfirst}</label>
				{$txtCkfinderLicenseKey} {$txtCkfinderLicenseKeyError}
			</p>
			<p>
				<label for="ckfinderImageMaxWidth">{$lblMaximumWidth|ucfirst}</label>
				{$txtCkfinderImageMaxWidth} {$txtCkfinderImageMaxWidthError}
				<span class="helpTxt">{$msgHelpCkfinderMaximumWidth}</span>
			</p>
			<p>
				<label for="ckfinderImageMaxHeight">{$lblMaximumHeight|ucfirst}</label>
				{$txtCkfinderImageMaxHeight} {$txtCkfinderImageMaxHeightError}
				<span class="helpTxt">{$msgHelpCkfinderMaximumHeight}</span>
			</p>
		</div>
	</div>

	<div class="box horizontal">
		<div class="heading">
			<h3>Facebook</h3>
		</div>
		<div class="options labelWidthLong">
			<p>
				<label for="addValue-facebookAdminIds">{$lblAdminIds|ucfirst}</label>
				<span style="float: left;">
					{$txtFacebookAdminIds} {$txtFacebookAdminIdsError}
				</span>
				<span class="helpTxt" style="clear: left;">{$msgHelpFacebookAdminIds}</span>
			</p>
			<p>
				<label for="facebookApplicationId">{$lblApplicationId|ucfirst}</label>
				{$txtFacebookApplicationId} {$txtFacebookApplicationIdError}
				<span class="helpTxt">{$msgHelpFacebookApplicationId}</span>
			</p>
			<p>
				<label for="facebookApplicationSecret">{$lblApplicationSecret|ucfirst}</label>
				{$txtFacebookApplicationSecret} {$txtFacebookApplicationSecretError}
				<span class="helpTxt">{$msgHelpFacebookApplicationSecret}</span>
			</p>
		</div>
	</div>

	<div class="fullwidthOptions">
		<div class="buttonHolderRight">
			<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
		</div>
	</div>

{/form:settingsIndex}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}