<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Installer - Fork CMS</title>
	<link rel="shortcut icon" href="../backend/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="layout/css/installer.css" />
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/conditionals/ie7.css" /><![endif]-->

	<script type="text/javascript" src="../frontend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/backend.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
</head>
<body id="installer">

	<div id="installHolder" class="step2">

		<h2>Database configuration</h2>
		{form:step2}
			{option:formError}<div class="formMessage errorMessage"><p>{$formError}</p></div>{/option:formError}
			<div class="horizontal">
				<p>Enter your database details. Make sure this database already exists.</p>
				<p>
					<label for="hostname">Hostname<abbr title="Required field">*</abbr></label>
					{$txtHostname} {$txtHostnameError}
					<span class="helpTxt">If you are working locally, your hostname is probably <strong>localhost</strong>.</span>
				</p>
				<p>
					<label for="database">Database<abbr title="Required field">*</abbr></label>
					{$txtDatabase} {$txtDatabaseError}
				</p>
				<p>
					<label for="username">Username<abbr title="Required field">*</abbr></label>
					{$txtUsername} {$txtUsernameError}
				</p>
				<p>
					<label for="password">Password<abbr title="Required field">*</abbr></label>
					{$txtPassword} {$txtPasswordError}
				</p>
			</div>

			<p class="buttonHolder spacing">
				<a class="button" href="index.php?step=1">Previous</a>
				<input id="installerButton" class="button inputButton mainButton" type="submit" name="installer" value="Next" />
			</p>
		{/form:step2}

	</div>

</body>
</html>