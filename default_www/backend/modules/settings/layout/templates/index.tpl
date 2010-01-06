{include:file="{$BACKEND_CORE_PATH}/layout/templates/header.tpl"}
{include:file="{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl"}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">Settings > Website</p>
			</div>

			<div class="inner">
				{option:formSucces}
				<div class="generalMessage succesMessage">
					<p>{$msgSaved}</p>
				</div>
				{/option:formSucces}
				{form:settings}
					{option:showWarning}
					<div class="generalMessage infoMessage">
						<p><strong>Some site settings have not been configured yet:</strong></p>
						<ul class="comboList">
							<li>Blog API-key not set <a href="#" class="button"><span><span><span>Configure</span></span></span></a></li>
							<li>Blog RSS title not set <a href="#" class="button"><span><span><span>Configure</span></span></span></a></li>
							<li>Blog RSS description title not set <a href="#" class="button"><span><span><span>Configure</span></span></span></a></li>
							<li>Events RSS title not set <a href="#" class="button"><span><span><span>Configure</span></span></span></a></li>
							<li>News RSS description title not set <a href="#" class="button"><span><span><span>Configure</span></span></span></a></li>
						</ul>
					</div>
					{/option:showWarning}

					<div class="box">
						<div class="heading">
							<h3>{$lblWebsiteTitle|ucfirst}</h3>
						</div>
						<div class="options">
							{$txtCoreWebsiteTitle} {$txtCoreWebsiteTitleError}
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblEmailWebmaster|ucfirst}</h3>
						</div>
						<div class="options">
							<p class="p0">
								{$txtCoreEmail} {$txtCoreEmailError}
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
								<li><input type="checkbox" class="inputCheckbox" id="language1" name="languages"><label for="#" for="language1">Nederlands</label></li>
							</ul>
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblScripts|ucfirst}</h3>
						</div>
						<div class="options last">
							<p>{$msgScriptsText}</p>
							{$txtCoreSiteWideHtml} {$txtCoreSiteWideHtmlError}
						</div>
					</div>

					<div class="box">
						<div class="heading">
							<h3>{$lblDomains|ucfirst}</h3>
						</div>
						<div class="content">
							<p>{$msgDomainsText}:</p>
							{$txtCoreSiteDomains} {$txtCoreSiteDomainsError}
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
										<th class="title" width="20%">{$lblName|ucfirst}</th>
										<th width="40%">{$lblAPIKey|ucfirst}</th>
										<th width="60%">{$lblAPIURL|ucfirst}</th>
									</tr>
									<tr>
										<td class="title">Fork public key</td>
										<td>{$txtCoreForkApiPublicKey} {$txtCoreForkApiPublicKeyError}</td>
										<td><a href="http://www.fork-cms.be/info/api">http://www.fork-cms.be/info/api</a></td>
									</tr>
									<tr>
										<td class="title">Fork private key</td>
										<td>{$txtCoreForkApiPrivateKey} {$txtCoreForkApiPrivateKeyError}</td>
										<td>&nbsp;</td>
									</tr>
									{option:needsGoogleMaps}
									<tr>
										<td class="title">Google key</td>
										<td>{$txtCoreGoogleMapsKey} {$txtCoreGoogleMapsKeyError}</td>
										<td><a href="http://code.google.com/apis/maps/signup.html">http://code.google.com/apis/maps/signup.html</a></td>
									</tr>
									{/option:needsGoogleMaps}
									{option:needsAkismet}
									<tr>
										<td class="title">Akismet key</td>
										<td>{$txtCoreAkismetKey} {$txtCoreAkismetKeyError}</td>
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