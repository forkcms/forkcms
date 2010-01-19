<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$LANGUAGE}" lang="{$LANGUAGE}">

<head>
	<title>{$pageTitle}</title>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="{$LANGUAGE}" />
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />

	{iteration:cssFiles}<link rel="stylesheet" type="text/css" media="screen" href="{$cssFiles.path}" />{$CRLF}{$TAB}{/iteration:cssFiles}
	<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="css/ie6.css" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="css/ie7.css" /><![endif]-->

	{iteration:javascriptFiles}<script type="text/javascript" src="{$javascriptFiles.path}"></script>{$CRLF}{$TAB}{/iteration:javascriptFiles}
</head>

<body class="{$LANGUAGE} frontend">
	<div id="container">
		<div id="topbar">
			<!-- breadcrumb -->
			{include:file="{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl"}

			<!-- user -->
			{include:file="{$FRONTEND_CORE_PATH}/layout/templates/user.tpl"}
		</div>

		<div id="header">
			<h1><a href="/">{$siteTitle}</a></h1>

			<!-- languages -->
			{include:file="{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl"}
		</div>

		<div id="main">
			<!-- navigation -->
			<div id="navigation">
				{$navigation}
			</div>

			<!-- content -->
			<div id="content">
				<h2>Titel pagina</h2>

				<!-- Block 0 -->
				{include:file="{$block0}"}

				<!-- Block 1 -->
				{include:file="{$block1}"}

				<!-- Block 2 -->
				{include:file="{$block2}"}
			</div>
		</div>

		<!-- footer -->
		{include:file="{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl"}
	</div>
</body>
</html>