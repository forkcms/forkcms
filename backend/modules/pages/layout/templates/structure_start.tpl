<body id="{$bodyID}" class="{$bodyClass}">

	<table id="encloser">
		<tr>
			<td>
				{include:{$BACKEND_CORE_PATH}/layout/templates/header.tpl}
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">

					<table id="mainHolder">
						<tr>
							{include:{$BACKEND_MODULES_PATH}/pages/layout/templates/tree.tpl}
							{include:{$BACKEND_CORE_PATH}/layout/templates/switch.tpl}
							<td id="contentHolder">
								<div class="inner">