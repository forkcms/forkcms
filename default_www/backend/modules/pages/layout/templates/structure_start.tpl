<body id="{$bodyID}" class="{$bodyClass}">

	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">

					<table border="0" cellspacing="0" cellpadding="0" id="mainHolder">
						<tr>
							{include:file='{$BACKEND_MODULES_PATH}/pages/layout/templates/tree.tpl'}
							{include:file='{$BACKEND_CORE_PATH}/layout/templates/switch.tpl'}
							<td id="contentHolder">
								<div class="inner">