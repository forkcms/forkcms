<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http: //www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$SITE_TITLE}</title>
	<style type="text/css">
		#outlook a { padding: 0; }
		body { width: 100% !important; } .ReadMsgBody { width: 100%; } .ExternalClass { width: 100%; }
		body { -webkit-text-size-adjust: none; }
		body { margin: 0; padding: 0; }
		img { border: 0; outline: none; text-decoration: none; }
		table td { border-collapse: collapse; }
		#backgroundTable { height: 100% !important; margin: 0; padding: 0; width: 100% !important; }
		body, #backgroundTable { background-color: #FAFAFA; }
		#templateContainer { border: 1px solid #DDDDDD; }
		h1, .h1 { color: #202020; display: block; font-family: Arial; font-size: 34px; font-weight: bold; line-height: 100%; margin-top: 0; margin-right: 0; margin-bottom: 10px; margin-left: 0; text-align: left; }
		h2, .h2 { color: #202020; display: block; font-family: Arial; font-size: 30px; font-weight: bold; line-height: 100%; margin-top: 0; margin-right: 0; margin-bottom: 10px; margin-left: 0; text-align: left; }
		h3, .h3 { color: #202020; display: block; font-family: Arial; font-size: 26px; font-weight: bold; line-height: 100%; margin-top: 0; margin-right: 0; margin-bottom: 10px; margin-left: 0; text-align: left; }
		h4, .h4 { color: #202020; display: block; font-family: Arial; font-size: 22px; font-weight: bold; line-height: 100%; margin-top: 0; margin-right: 0; margin-bottom: 10px; margin-left: 0; text-align: left; }
		#templateContainer, .bodyContent { background-color: #FFFFFF; }
		.bodyContent div { color: #505050; font-family: Arial; font-size: 12px; line-height: 150%; text-align: left; }
		.bodyContent div a:link, .bodyContent div a:visited, .bodyContent div a .yshortcuts { color: #336699; font-weight: normal; text-decoration: underline; }
		.bodyContent img { display: inline; height: auto; margin-bottom: 10px; max-width: 280px; }
	</style>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
<center>
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="backgroundTable">
		<tr>
			<td align="center" valign="top">
				<br />
				<br />
				<table border="0" cellpadding="0" cellspacing="0" width="320" id="templateContainer">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="320" id="templateBody">
								<tr>
									<td valign="top" class="bodyContent">
										<table border="0" cellpadding="20" cellspacing="0" width="100%">
											<tr>
												<td valign="top">
													<div>
														<h3>{$msgFormBuilderSubject|sprintf:{$name}}</h3>
														<p>
															<strong>{$lblSentOn|ucfirst}:</strong><br />
															{$sentOn|date:{$dateFormatLong}:{$LANGUAGE}}
														</p>
														{iteration:fields}
															<p>
																<strong> {$fields.label}:</strong><br />
																{$fields.value}
															</p>
														{/iteration:fields}
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br />
				<br />
			</td>
		</tr>
	</table>
</center>
</body>
</html>
