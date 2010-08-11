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

	<div id="installHolder">

		<h2>Installation complete</h2>
		<p>Fork CMS is installed! You can now <a href="../private/">log in</a> using these credentials.</p>
		<p>For future reference:</p>
		<table border="0" cellspacing="0" cellpadding="0" class="infoGrid">
			<tr>
				<th>CMS</th>
				<td><a href="../private/">http://{$url}/private/</a></td>
			</tr>
			<tr>
				<th>Email</th>
				<td>{$email}</td>
			</tr>
			<tr>
				<th>Password</th>
				<td>
					<span id="plainPassword" class="hidden">{$password}</span>
					<input type="button" id="showPassword" name="showPassword" value="show password" />
				</td>
			</tr>
		</table>
	</div>

</body>
</html>