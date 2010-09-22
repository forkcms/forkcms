<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />

	<title>Installer - Fork CMS</title>
	<link rel="shortcut icon" href="../backend/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="layout/css/installer.css" />
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="../backend/core/layout/css/conditionals/ie7.css" /><![endif]-->

	<script type="text/javascript" src="../frontend/core/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/backend.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
</head>
<body id="installer">

	<div id="installHolder" class="step4">

		<h2>Your login info</h2>

		{form:step4}
			<div class="horizontal">
				<p>Enter the e-mail address and password you'd like to use to log in.</p>
				<p>
					<label for="email">E-mail <abbr title="Required field">*</abbr></label>
					{$txtEmail} {$txtEmailError}
				</p>
				<p>
					<label for="password">Password <abbr title="Required field">*</abbr></label>
					{$txtPassword} {$txtPasswordError}
				</p>
				<table id="passwordStrengthMeter" class="passwordStrength" rel="password" cellspacing="0">
					<tr>
						<td class="strength" id="passwordStrength">
							<p class="strength none">/</p>
							<p class="strength weak" style="background: red;">Weak</p>
							<p class="strength ok" style="background: orange;">OK</p>
							<p class="strength strong" style="background: green;">Strong</p>
						</td>
						<td>
							<p class="helpTxt">Strong passwords consist of a combination of capitals, small letters, digits and special characters.</p>
						</td>
					</tr>
				</table>
				<p>
					<label for="confirm">Confirm <abbr title="Required field">*</abbr></label>
					{$txtConfirm} {$txtConfirmError}
				</p>
			</div>

			<p class="spacing buttonHolder">
				<a href="index.php?step=3" class="button">Previous</a>
				<input id="installerButton" class="inputButton button mainButton" type="submit" name="installer" value="Finish installation" />
			</p>
		{/form:step4}

	</div>

</body>
</html>