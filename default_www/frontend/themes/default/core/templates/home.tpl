{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}
<body class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
		</div>

		<div id="main">
			<div id="sidebar">
				<div id="navigation">
					{$var|getnavigation}
				</div>
				
				{* Block 1 *}
				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}

			</div>

			<div id="content">
				{* Block 0 *}
				{option:block0IsHTML}{$block0}{/option:block0IsHTML}
				{option:!block0IsHTML}{include:file='{$block0}'}{/option:!block0IsHTML}

				<div id="homeCTA">
					{* Block 2 *}
					{option:block2IsHTML}{$block2}{/option:block2IsHTML}
					{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
				</div>
			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>