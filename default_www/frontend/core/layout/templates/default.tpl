{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>

			<!-- {include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'} -->

			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
		</div>

		<div id="navigation">
			{$var|getnavigation:'page':0:1}
		</div>

		<div id="main">
			<div id="subnavigation">
				{$var|getsubnavigation:'page':{$page['id']}:2}
			</div>

			<div id="content" class="content">
				{option:!hideContentTitle}<h2 class="pageTitle">{$page['title']}</h2>{/option:!hideContentTitle}

				{* Block 1 *}
				{option:block1IsHTML}{$block1}{/option:block1IsHTML}
				{option:!block1IsHTML}{include:file='{$block1}'}{/option:!block1IsHTML}
			</div>
			<div id="sidebar">
				{* Block 2 *}
				{option:block2IsHTML}{$block2}{/option:block2IsHTML}
				{option:!block2IsHTML}{include:file='{$block2}'}{/option:!block2IsHTML}

				{* Block 3 *}
				{option:block3IsHTML}{$block3}{/option:block3IsHTML}
				{option:!block3IsHTML}{include:file='{$block3}'}{/option:!block3IsHTML}
			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>