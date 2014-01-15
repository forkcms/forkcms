<body id="{$bodyID}" class="{$bodyClass}">

	<table id="encloser">
		<tr>
			<td>
				{include:{$BACKEND_CORE_PATH}/Layout/Templates/Header.tpl}
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">

					<table id="mainHolder">
						<tr>
							{include:{$BACKEND_MODULES_PATH}/Pages/Layout/Templates/Tree.tpl}
							{include:{$BACKEND_CORE_PATH}/Layout/Templates/Switch.tpl}
							<td id="contentHolder">
								<div class="inner">
