<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	{* do NOT remove the UTF-8 part *}
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Fork CMS - {$msgResetYourPasswordMailSubject}</title>
	<style type="text/css" media="screen">
		html, body {
			height: 100%;
			width: 100%;
			margin: 0;
			padding: 0;
		}

		body {
			font-size: 12px;
			font-family: "Lucida Grande", Arial, sans-serif;
			line-height: 1.5;
		}

		p {
			padding: 0 0 12px;
			margin: 0;
		}

		h3 {
			font-size: 18px;
			font-weight: 700;
			color: #000;
		}

		.dataGrid {
			margin: 0 0 12px;
			border-collapse: collapse;
			width: 100%;
		}

			.dataGrid td,
			.dataGrid th {
				padding: 6px;
				border: 1px solid #C2C2C2;
				text-align: left;
				font-size: 11px;
			}

			.dataGrid .code {
				font-family: Menlo, Monaco, "Courier New", Courier, monospace;
			}

		/*
			Default link behavior
		*/

		a {
			color: #2244BB;
		}

		#footer {
			color: #666;
			font-size: 11px;
		}

		#footer a {
			color: #333;
		}

	</style>
</head>
<body>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
		<tr>
			<td align="center">
				<table border="0" cellspacing="0" cellpadding="0" width="400">
					<tr>
						<td>

							<h3>{$lblResetYourPassword|ucfirst}</h3>

							<div id="content">
								<p>{$lblDear|ucfirst},</p>
								<p>{$msgResetYourPasswordMailContent}</p>
								<p><a href="{$resetLink}">{$resetLink}</a></p>
							</div>

							<div id="footer">
								<p><strong>Fork CMS</strong></p>
							</div>

						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>
</body>
</html>