<html>
<head>
{* do NOT remove the UTF-8 part *}
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Fork CMS</title>
<style>
	body { background-color:#ebf3f9 }
	h2 {font-family: Arial, sans-serif; font-size: 22px; font-weight: bold; color: #000000; margin: 12px 0 12px 0; padding: 0; text-align: left;}
	h3 {font-family: Arial, sans-serif; font-size: 14px; font-weight: bold; color: #000000; margin: 12px 0 6px 0; padding: 0; text-align: left;}
	h4 {font-family: Arial, sans-serif; font-size: 12px; font-weight: bold; color: #000000; margin: 0 0 6px 0; padding: 0; text-align: left;}
	p, table, ol, ul, dl {font-family: Arial, sans-serif; font-size: 12px; color: #000000; margin: 0 0 12px 0; padding: 0; text-align: left;}
	a {color: #2244bb; text-decoration: underline;}
	h2 a, h3 a, h4 a {text-decoration: none;}
	small {font-family: Arial, sans-serif; font-size: 11px; font-weight: normal; color: #a9a9a9; display: block; text-align: center;}
	small a {color: #7f7f7f;}
	img {border: 0; display: block;}
</style>
</head>
<body style="margin: 0; padding: 0; background-color: #EBF3F9">
<style>
	body { background-color:#ebf3f9 }
	h2 {font-family: Arial, sans-serif; font-size: 22px; font-weight: bold; color: #000000; margin: 12px 0 12px 0; padding: 0; text-align: left;}
	h3 {font-family: Arial, sans-serif; font-size: 14px; font-weight: bold; color: #000000; margin: 12px 0 6px 0; padding: 0; text-align: left;}
	h4 {font-family: Arial, sans-serif; font-size: 12px; font-weight: bold; color: #000000; margin: 0 0 6px 0; padding: 0; text-align: left;}
	p, table, ol, ul, dl {font-family: Arial, sans-serif; font-size: 12px; color: #000000; margin: 0 0 12px 0; padding: 0; text-align: left;}
	a {color: #2244bb; text-decoration: underline;}
	h2 a, h3 a, h4 a {text-decoration: none;}
	small {font-family: Arial, sans-serif; font-size: 11px; font-weight: normal; color: #a9a9a9; display: block; text-align: center;}
	small a {color: #7f7f7f;}
	img {border: 0; display: block;}
</style>
	<table border="0" cellpadding="0" width="100%" style="margin:0; padding:0; background-color: #EBF3F9">
		<tr>
			<td width="100%" align="center" valign="top">
				<table width="580" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td height="24">
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="580" style="border: 1px solid #BED7E9; background-color:#ffffff;">
								<tr>
									<td colspan="3" height="12">
									</td>
								</tr>
								<tr>
									<td width="24">
									</td>
									<td width="532">
										<h2>Fork CMS: {$msgFaqOwnQuestionSubject|sprintf:'{$name}'}</h2>
										<h3>{$lblSenderInformation|ucfirst}</h3>
										<p><strong>{$lblSentOn|ucfirst}:</strong><br /> {$sentOn|date:{$dateFormatLong}:{$LANGUAGE}}</p>

										<h3>{$lblContent|ucfirst}</h3>
										<p><strong>{$lblName|ucfirst}:</strong><br /> {$name}</p>
										<p><strong>{$lblEmail|ucfirst}:</strong><br /> {$email}</p>
										<p><strong>{$lblQuestion|ucfirst}:</strong><br /> {$message}</p>
									</td>
									<td width="24">
									</td>
								</tr>
								<tr>
									<td colspan="3" height="24">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td height="24">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>