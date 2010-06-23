{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body class="{$LANGUAGE} frontend">
	<div id="container">

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>
			{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}
		</div>

		<div id="main">
			<div id="navigation">
				{$var|getnavigation}
			</div>

			<div id="content">
				<!-- {include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'} -->

				{option:!hideContentTitle}<h2 class="pageTitle">{$pageDatatitle}</h2>{/option:!hideContentTitle}

				{* Block 0 *}
				{option:blockAIsHTML}{$blockA}{/option:blockAIsHTML}
				{option:!blockAIsHTML}{include:file='{$blockA}'}{/option:!blockAIsHTML}

				{* Block 1 *}
				{option:blockBIsHTML}{$blockB}{/option:blockBIsHTML}
				{option:!blockBIsHTML}{include:file='{$blockB}'}{/option:!blockBIsHTML}

				{* Block 2 *}
				{option:blockCIsHTML}{$blockC}{/option:blockCIsHTML}
				{option:!blockCIsHTML}{include:file='{$blockC}'}{/option:!blockCIsHTML}
			</div>
		</div>

		{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}
	</div>
</body>
</html>