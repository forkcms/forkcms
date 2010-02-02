{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblSettings|ucfirst} &gt; {$lblGeneral|ucfirst}</p>
			</div>

			{option:report}
			<div id="report">
				<div class="singleMessage successMessage">
					<p>{$reportMessage}</p>
				</div>
			</div>
			{/option:report}

			<div class="inner">
				{form:settings}
					{option:warnings}
						<div class="generalMessage infoMessage">
							<p><strong>{$msgConfigurationError}</strong></p>
							<ul class="comboList">
								{iteration:warnings}
									<li>{$warnings.message}</li>
								{/iteration:warnings}
							</ul>
						</div>
					{/option:warnings}

					<div class="box">
						<div class="heading">
							<h3>{$lblWebsiteTitle|ucfirst}</h3>
						</div>
						<div class="options">
							{$txtSiteTitle} {$txtSiteTitleError}
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblEmailWebmaster|ucfirst}</h3>
						</div>
						<div class="options">
							<p class="p0">
								{$txtEmail} {$txtEmailError}
								<span class="helpTxt">{$msgHelpEmailWebmaster}</span>
							</p>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblLanguages|ucfirst}</h3>
						</div>
						<div class="options">
							<p>{$msgLanguagesText}:</p>
							<ul class="inputList">
								{iteration:languages}
									<li>{$languages.chkLanguages} <label for="language_{$languages.value}">{$languages.label}{option:languages.default} ({$lblDefault}){/option:languages.default}</label></li>
								{/iteration:languages}
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblScripts|ucfirst}</h3>
						</div>
						<div class="options last">
							<p>{$msgScriptsText}</p>
							{$txtSiteWideHtml} {$txtSiteWideHtmlError}
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblDomains|ucfirst}</h3>
						</div>
						<div class="content">
							<p>{$msgDomainsText}:</p>
							{$txtSiteDomains} {$txtSiteDomainsError}
						</div>
					</div>

					<div id="settingsApiKeys" class="box">
						<div class="heading">
							<h3>{$lblAPIKeys|ucfirst}</h3>
						</div>
						<div class="content">
							<p>{$msgApiKeysText}:</p>
							<div class="datagridHolder">
								<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
									<tr>
										<th class="title" width="20%"><span>{$lblName|ucfirst}</span></th>
										<th width="40%"><span>{$lblAPIKey|ucfirst}</span></th>
										<th width="60%"><span>{$lblAPIURL|ucfirst}</span></th>
									</tr>
									<tr>
										<td class="title">Fork public key</td>
										<td>{$txtForkApiPublicKey} {$txtForkApiPublicKeyError}</td>
										<td><a href="http://www.fork-cms.be/info/api">http://www.fork-cms.be/info/api</a></td>
									</tr>
									<tr>
										<td class="title">Fork private key</td>
										<td>{$txtForkApiPrivateKey} {$txtForkApiPrivateKeyError}</td>
										<td>&nbsp;</td>
									</tr>
									{option:needsGoogleMaps}
										<tr>
											<td class="title">Google key</td>
											<td>{$txtGoogleMapsKey} {$txtGoogleMapsKeyError}</td>
											<td><a href="http://code.google.com/apis/maps/signup.html">http://code.google.com/apis/maps/signup.html</a></td>
										</tr>
									{/option:needsGoogleMaps}
									{option:needsAkismet}
										<tr>
											<td class="title">Akismet key</td>
											<td>{$txtAkismetKey} {$txtAkismetKeyError}</td>
											<td><a href="http://akismet.com/personal">http://akismet.com/personal</a></td>
										</tr>
									{/option:needsAkismet}
								</table>
							</div>
						</div>
					</div>

					<div class="fullwidthOptions">
						<div class="buttonHolderRight">
							{$btnSave}
						</div>
					</div>
				{/form:settings}
			</div>
		</td>
	</tr>
</table>
{include:file="{$BACKEND_CORE_PATH}/layout/templates/footer.tpl"}