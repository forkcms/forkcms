<html>
<head>
	<title>Fork CMS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style>
		body { background-color:#f4f4f4 }
		h2 {font-family: Arial, sans-serif; font-size: 22px; font-weight: bold; color: #000000; margin: 12px 0 12px 0; padding: 0; text-align: left;}
		h3 {font-family: Arial, sans-serif; font-size: 14px; font-weight: bold; color: #000000; margin: 12px 0 6px 0; padding: 0; text-align: left;}
		h4 {font-family: Arial, sans-serif; font-size: 12px; font-weight: bold; color: #000000; margin: 0 0 6px 0; padding: 0; text-align: left;}
		p {font-family: Arial, sans-serif; font-size: 12px; color: #000000; margin: 0 0 12px 0; padding: 0; text-align: left;}
		ul, ol, dl, table {font-family: Arial, sans-serif; font-size: 12px; color: #000000; text-align: left;}
		a {color: #1E6AB4; text-decoration: underline;}
		a:hover, a:active {color: #114477;}
		h2 a, h3 a, h4 a {text-decoration: none;}
		small {font-family: Arial, sans-serif; font-size: 11px; font-weight: normal; color: #a9a9a9; display: block;}
		small a {color: #7f7f7f; border-color: #cdcdcd;}
		img {border: 0; display: block;}
	</style>
</head>
<body>
	<style>
		body { background-color:#f4f4f4 }
		h2 {font-family: Arial, sans-serif; font-size: 22px; font-weight: bold; color: #000000; margin: 12px 0 12px 0; padding: 0; text-align: left;}
		h3 {font-family: Arial, sans-serif; font-size: 14px; font-weight: bold; color: #000000; margin: 12px 0 6px 0; padding: 0; text-align: left;}
		h4 {font-family: Arial, sans-serif; font-size: 12px; font-weight: bold; color: #000000; margin: 0 0 6px 0; padding: 0; text-align: left;}
		p {font-family: Arial, sans-serif; font-size: 12px; color: #000000; margin: 0 0 12px 0; padding: 0; text-align: left;}
		ul, ol, dl, table {font-family: Arial, sans-serif; font-size: 12px; color: #000000; text-align: left;}
		a {color: #1E6AB4; text-decoration: underline;}
		a:hover, a:active {color: #114477;}
		h2 a, h3 a, h4 a {text-decoration: none;}
		small {font-family: Arial, sans-serif; font-size: 11px; font-weight: normal; color: #a9a9a9; display: block;}
		small a {color: #7f7f7f; border-color: #cdcdcd;}
		img {border: 0; display: block;}
	</style>

	<h2>Fork CMS</h2>
	<hr />
	<h4>{$msgContactSubject}</h4>
	<p><strong>{$lblName|ucfirst}:</strong> {$author}</p>
	<p><strong>{$lblEmail|ucfirst}:</strong> {$email}</p>
	<p><strong>{$lblMessage|ucfirst}:</strong></p>
	{$message}
</body>
</html>