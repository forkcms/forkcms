<body id="{$bodyID}" class="{$bodyClass}">
	{include:{$BACKEND_CORE_PATH}/Layout/Templates/Header.tpl}
	<div id="content" class="container">
		{option:page_title}
		<div class="row">
			<div class="col-md-12">
				<div class="page-header">
					<h1>{$page_title|ucfirst}</h1>
				</div>
			</div>
		</div>
		{/option:page_title}
		<div class="row">
			<div class="col-md-3">
				{include:{$BACKEND_CORE_PATH}/Layout/Templates/Subnavigation.tpl}
				{include:{$BACKEND_CORE_PATH}/Layout/Templates/Switch.tpl}
			</div>
			<div class="col-md-9">
