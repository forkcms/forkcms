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
						<h2>Finished</h2>
					</div>

					<form action="#" method="post" id="step4" class="forkForms">
						<div>
							<div class="horizontal">
								<p>Nice, everything is installed!</p>
							</div>

							{iteration:items}
							<div>
								<h3>{$items.name}</h3>
								{iteration:items.messages}
								<div class="{$messages.type}">
									{$messages.message}
								</div>
								{/iteration:items.messages}
							</div>
							{/iteration:items}
						</div>
					</form>
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