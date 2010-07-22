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
	<table border="0" cellspacing="0" cellpadding="0" id="installHolder">
		<tr>
			<td>
				<div id="installerBox" >
					<div id="installerBoxTop">
						<h2>Login</h2>
					</div>

					{form:step4}
						<div>
							<div class="horizontal">
								<h3>Login credentials</h3>
								<p>Below you can provide your e-mailaddress and password you wish to use to log in.</p>
								<p>
									<label for="email">E-mail <abbr title="Required">*</abbr></label>
									{$txtEmail} {$txtEmailError}
								</p>
								<p>
									<label for="password">Password <abbr title="Required">*</abbr></label>
									{$txtPassword} {$txtPasswordError}
								</p>
							</div>

							<div>
								<p class="spacing">
									<a href="index.php?step=3">Previous</a>
									<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Finish" />
								</p>
							</div>
						</div>
					{/form:step4}
					<ul id="installerNav">
						<li><a href="http://userguide.fork-cms.be">Userguide</a></li>
						<li><a href="http://docs.fork-cms.be">Developer</a></li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</body>
</html>