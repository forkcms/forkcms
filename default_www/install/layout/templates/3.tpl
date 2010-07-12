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

					{form:step3}
						<div>
							<div class="horizontal">
								<h3>Modules</h3>
								<p><span class="helpTxt">Enable the modules that will be used.</span></p>
								<ul>
									{iteration:modules}
										<li>{$modules.chkModules} <label for="{$modules.id}">{$modules.label}</label></li>
									{/iteration:modules}
								</ul>
							</div>

							<div class="horizontal">
								<h3>Fork API</h3>
								<p>
									<label for="apiEmail">Email<abbr title="Required">*</abbr></label>
									{$txtApiEmail} {$txtApiEmailError}
								</p>
							</div>

							<div class="horizontal">
								<h3>Password</h3>
								<p>
									<label for="password">Password<abbr title="Required">*</abbr></label>
									{$txtPassword} {$txtPasswordError}
								</p>
							</div>

							<div class="horizontal">
								<h3>STMP settings</h3>
								<p>These settings are used to send mail.</p>
								<p>
									<label for="smtpServer">Server<abbr title="Required">*</abbr></label>
									{$txtSmtpServer} {$txtSmtpServerError}
								</p>
								<p>
									<label for="smtpPort">Port<abbr title="Required">*</abbr></label>
									{$txtSmtpPort} {$txtSmtpPortError}
								</p>
								<p>
									<label for="smtpUsername">Username<abbr title="Required">*</abbr></label>
									{$txtSmtpUsername} {$txtSmtpUsernameError}
								</p>
								<p>
									<label for="smtpPassword">Password<abbr title="Required">*</abbr></label>
									{$txtSmtpPassword} {$txtSmtpPasswordError}
								</p>
							</div>

							<div>
								<p class="spacing">
									<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
								</p>
							</div>
						</div>
					{/form:step3}
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