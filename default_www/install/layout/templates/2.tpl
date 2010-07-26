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
	<script type="text/javascript" src="js/install.js"></script>
</head>
<body id="installer">

	<div id="installHolder">

		<h2>Database-configuration</h2>

		{form:step2}
			{option:formError}<div style="color: red;">{$formError}</div>{/option:formError}
			<div class="horizontal">
				<h3>Database details</h3>
				<p>Make sure this database already exists.</p>
				<p>
					<label for="hostname">Hostname<abbr title="Required">*</abbr></label>
					{$txtHostname} {$txtHostnameError}
				</p>
				<p>
					<label for="database">Database<abbr title="Required">*</abbr></label>
					{$txtDatabase} {$txtDatabaseError}
				</p>
				<p>
					<label for="username">Username<abbr title="Required">*</abbr></label>
					{$txtUsername} {$txtUsernameError}
				</p>
				<p>
					<label for="password">Password<abbr title="Required">*</abbr></label>
					{$txtPassword} {$txtPasswordError}
				</p>
			</div>

			<p class="spacing">
				<a href="index.php?step=1">Previous</a>
				<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Next" />
			</p>
		{/form:step2}

	</div>

</body>
</html>