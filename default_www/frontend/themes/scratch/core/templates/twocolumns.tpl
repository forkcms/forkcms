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
			<div id="content">
				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
			</div>
			<div id="sidebar">
				{option:block2IsHTML}{$block1}{/option:block2IsHTML}
				{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}
			</div>
		</div>

		{include:file='{$THEME_PATH}/core/templates/_footer.tpl'}

	</div>
</body>
</html>