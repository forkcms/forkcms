{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div class="inner">
				<h3>{$lblGeneralSettings|ucfirst}</h3>

				{form:generalsettings}
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
								{iteration:activeLanguages}
									<li>{$activeLanguages.chkActiveLanguages} <label for="{$activeLanguages.id}">{$activeLanguages.label|ucfirst}{option:activeLanguages.default} ({$lblDefault}){/option:activeLanguages.default}</label></li>
								{/iteration:activeLanguages}
							</ul>

							<p>{$msgRedirectLanguagesText}:</p>
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
								<table border="0" cellspacing="0" cellpadding="0" class="datagrid dynamicStriping">
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
											<td class="title">Google maps key</td>
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
							<input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
						</div>
					</div>
				{/form:generalsettings}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}