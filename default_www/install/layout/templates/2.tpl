<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Installer - Fork CMS</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/install/layout/css/installer.css" />
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/conditionals/ie7.css" /><![endif]-->
	<script type="text/javascript" src="../frontend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
</head>
<body id="installer">
	<table border="0" cellspacing="0" cellpadding="0" id="installHolder">
		<tr>
			<td>
				<div id="installerBox" >
					<div id="installerBoxTop">
						<h2>System-configuration</h2>
					</div>

					{option:error}{$error}{/option:error}

					{form:step2}
						<div>
							<div class="horizontal">
								<h3>Filesystem configuration</h3>
								<p>
									<label for="pathLibrary">Library path<abbr title="Required">*</abbr></label>
									{$txtPathLibrary} {$txtPathLibraryError}
									<span class="helpTxt">Correct the path to the library folder if needed.</span>
								</p>
							</div>

							<div class="horizontal">
								<h3>Debug configuration</h3>
								<p>
									<label for="debugEmail">Debug email address<abbr title="Required">*</abbr></label>
									{$txtDebugEmail} {$txtDebugEmailError}
									<span class="helpTxt">When an error occures an email will be send to this address.</span>
								</p>
							</div>

							<div class="horizontal">
								<h3>Database configuration</h3>
								<p>Make sure the database already exists.</p>
								{option:databaseError}<p><span class="formError">{$databaseError}</span></p>{/option:databaseError}
								<p>
									<label for="databaseHostname">Hostname<abbr title="Required">*</abbr></label>
									{$txtDatabaseHostname} {$txtDatabaseHostnameError}
									<span class="helpTxt">If you are not sure, use the default setting or check with your hosting provider.</span>
								</p>
								<p>
									<label for="databaseName">Database name<abbr title="Required">*</abbr></label>
									{$txtDatabaseName} {$txtDatabaseNameError}
								</p>
								<p>
									<label for="databaseUsername">Username<abbr title="Required">*</abbr></label>
									{$txtDatabaseUsername} {$txtDatabaseUsernameError}
								</p>
								<p>
									<label for="databasePassword">Password<abbr title="Required">*</abbr></label>
									{$txtDatabasePassword} {$txtDatabasePasswordError}
								</p>
							</div>

							<div class="horizontal">
								<h3>Site configuration</h3>
								<p>These values are just default. They can be alltered in the settings-menu.</p>
								<p>
									<label for="siteDomain">Default domain<abbr title="Required">*</abbr></label>
									{$txtSiteDomain} {$txtSiteDomainError}
								</p>
								<p>
									<label for="siteTitle">Default title<abbr title="Required">*</abbr></label>
									{$txtSiteTitle} {$txtSiteTitleError}
								</p>
							</div>

							<div class="horizontal">
								<h3>Languages configuration</h3>
								<p>Will your site be available in multiple languages or just one? Changing this setting in a later stage, will have an influence on your URLs</p>

								<div>
									{iteration:languageType}
										{$languageType.rbtLanguageType} <label for="{$languageType.id}">{$languageType.label}</label><br />
										{option:languageType.multiple}
											<p id="multipleLanguages" class="hidden" style="margin-left: 25px;">
												{iteration:multipleLanguages}
													{$multipleLanguages.chkMultipleLanguages} <label for="{$multipleLanguages.id}">{$multipleLanguages.label}</label><br />
												{/iteration:multipleLanguages}
											</p>
										{/option:languageType.multiple}
										{option:languageType.single}
											<p id="singleLanguages" class="hidden" style="margin-left: 25px;">
												{iteration:singleLanguages}
													{$singleLanguages.rbtSingleLanguages} <label for="{$singleLanguages.id}">{$singleLanguages.label}</label><br />
												{/iteration:singleLanguages}
											</p>
										{/option:languageType.single}
									{/iteration:languageType}
								</div>

								<p style="margin-top: 20px;">
									What is the default language, we should use for the website?<br />
									{option:rbtDefaultLanguageError}<span class="formError">{$rbtDefaultLanguageError}</span>{/option:rbtDefaultLanguageError}
									{iteration:defaultLanguage}
										{$defaultLanguage.rbtDefaultLanguage} <label for="{$defaultLanguage.id}">{$defaultLanguage.label}</label><br />
									{/iteration:defaultLanguage}
								</p>
							</div>

							<div>
								<p class="spacing">
									<a href="index.php?step=1">Previous</a>
									<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
								</p>
							</div>
						</div>
					{/form:step2}
					<ul id="installerNav">
						<li><a href="http://userguide.fork-cms.be">Gebruikersgids</a></li>
						<li><a href="http://docs.fork-cms.be">Developer</a></li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</body>
</html>