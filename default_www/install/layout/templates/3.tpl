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

					{form:step3}
						<div>
							<div class="horizontal">
								<h3>Modules</h3>
								<p><span class="helpTxt">Enable the modules that will be used.</span></p>
								<ul>
									{iteration:modules}
									<li><label for="{$modules.id}">{$modules.chkModules} {$modules.label}</li>
									{/iteration:modules}
								</ul>
							</div>

							<div class="horizontal">
								<h3>Fork API</h3>
								<p>The Fork API provides your website with extra services.</p>
								<p>
									<label for="apiEmail">Email<abbr title="Required">*</abbr></label>
									{$txtApiEmail} {$txtApiEmailError}
								</p>
							</div>

							<div>
								<p class="spacing">
									<input id="installer" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
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