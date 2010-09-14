{include:file='{$THEME_PATH}/core/templates/_head.tpl'}
<body id="home" class="{$LANGUAGE}">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			<div id="navigation">
				{$var|getnavigation:'page':0:1}
			</div>
		</div>

		<div id="main">
			{option:block1IsHTML}{$block1}{/option:block1IsHTML}
			{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
		</div>

		{include:file='{$THEME_PATH}/core/templates/_footer.tpl'}

	</div>
</body>
</html>