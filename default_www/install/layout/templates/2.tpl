<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1">

	<title>Installer - Fork CMS</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/install/layout/css/installer.css" />
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/conditionals/ie7.css" /><![endif]-->
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
									<label for="spoonPath">Default domain<abbr title="Required">*</abbr></label>
									{$txtSpoonPath} {$txtSpoonPathError}
									<span class="helpTxt">Correct the path to the folder containing Spoon if needed.</span>
								</p>
							</div>

							<div class="horizontal">
								<h3>Debug configuration</h3>
								<p>
									<label for="debugEmail">Debug email-address</label>
									{$txtDebugEmail} {$txtDebugEmailError}
									<span class="helpTxt">When an error occurs a mail will be send to this emailaddress. This will be usefull when the site is on the production server.</span>
								</p>
							</div>

							<div class="horizontal">
								<h3>Database configuration</h3>
								<p>The database will store all the data for this site. <strong>Make sure the database already exists on the server and you remember the credentials.</strong>. You should import the <em>default.sql</em>-file in that database.</p>

								<p>
									<label for="databaseHost">Host<abbr title="Required">*</abbr></label>
									{$txtDatabaseHost} {$txtDatabaseHostError}
									<span class="helpTxt">If you're not sure what you should enter here, leave the default setting or check with your hosting provider.</span>
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
								<p>Will your site be available in multiple languages or just one? If you want to alter this setting later on: all URL's will change, so choose wisely.</p>
								{$rbtMultilanguageError}

								<ul class="inputList">
									{iteration:multilanguage}
									<li><label for="{$multilanguage.id}">{$multilanguage.rbtMultilanguage} {$multilanguage.label}</label></li>
									{/iteration:multilanguage}
								</ul>
							</div>

							<div>
								<p class="spacing">
									<input id="installer" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
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