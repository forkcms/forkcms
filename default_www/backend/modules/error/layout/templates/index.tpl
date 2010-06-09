{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body>
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table border="0" cellspacing="0" cellpadding="0" id="loginHolder">
		<tr>
			<td>
				<div id="loginNav">
					<ul>
						<li><span>Fork</span> <strong>CMS</strong></li>
					</ul>
				</div>
				<div id="loginBox">
					<div id="loginBoxTop">
						<h2>{$title}</h2>
						<p>{$message}</p>
					</div>
				</div>
			</td>
		</tr>
	</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}