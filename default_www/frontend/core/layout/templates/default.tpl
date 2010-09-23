{include:file='{$FRONTEND_CORE_PATH}/layout/templates/head.tpl'}

<body>

	<h1><a href="/">{$siteTitle}</a></h1>

	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl'}

	{$var|getnavigation:'page':0:1}

	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl'}

	{* Block 1 *}
	{option:block1IsHTML}{$block1}{/option:block1IsHTML}
	{option:!block1IsHTML}
		{include:file='{$block1}'}
	{/option:!block1IsHTML}

	{include:file='{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl'}

</body>
</html>