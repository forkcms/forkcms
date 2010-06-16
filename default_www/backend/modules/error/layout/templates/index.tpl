{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body>
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				<div>
					<h2>{$title}</h2>
					<p>{$message}</p>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}