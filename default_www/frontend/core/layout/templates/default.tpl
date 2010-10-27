{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE}">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>

			<div id="language">
				{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
			</div>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'}

			{option:!hideContentTitle}
				<div class="pageTitle">
					<h2>{$page['title']}</h2>
				</div>
			{/option:!hideContentTitle}

			{* Block 1 (default: Editor) *}
			{option:block1IsHTML}
				{option:block1}
					<div class="mod">
						<div class="inner">
							<div class="bd">
								{$block1}
							</div>
						</div>
					</div>
				{/option:block1}
			{/option:block1IsHTML}
			{option:!block1IsHTML}
				{include:file='{$block1}'}
			{/option:!block1IsHTML}
		</div>

		<div id="footer">
			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
		</div>
	</div>
</body>
</html>