<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$LANG}" lang="{$LANG}">
<head>
	<title>{$pageTitle}</title>
	
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-15" />
	<meta http-equiv="content-language" content="{$LANG}" />
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{$metaCustom}

	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$SITE_URL_CORE}/layout/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="{$SITE_URL_CORE}/layout/css/screen.css" />
	<link rel="stylesheet" type="text/css" media="print" href="{$SITE_URL_CORE}/layout/css/print.css" />
	{iteration:iCssFile}<link rel="stylesheet" type="text/css" media="screen" href="{$file}" />{$BR}{/iteration:iCssFile}

	<!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="{$SITE_URL_CORE}/layout/css/ie6.css" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="{$SITE_URL_CORE}/layout/css/ie7.css" /><![endif]-->

	<script type="text/javascript" src="/modules/core/js/jquery/jquery-1.2.6.min.js"></script>
	{iteration:iJavascriptFile}<script type="text/javascript" src="{$file}"></script>{$BR}{/iteration:iJavascriptFile}
	
</head>
<body class="{$LANG} onsite">
	<div id="container">
		<div id="topbar" class="clearfix">
			<div id="breadcrumb">
				<span>{$lblYouAreHere|ucfirst}:</span> {$breadcrumb}
			</div>
			<div id="user">
				{#core|user}
			</div>
		</div>
		<div id="header" class="clearfix">
			<h1><a href="/" title="{$SITE_TITLE}">{$SITE_TITLE}</a></h1>
			{option:oLanguageInclude}{include:file="{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl"}{/option:oLanguageInclude}
		</div>
		<div id="main" class="clearfix">
			<div id="navigation">
				{$navigation}
				{include:file="{$FRONTEND_CORE_PATH}/layout/templates/search.tpl"}
			</div>
			<div id="content">
				{option:oContentTitle}<h2>{$contentTitle|ucfirst}</h2>{/option:oContentTitle}
				{option:oContent}{$content}{/option:oContent}
				{option:oExtra}{$extra}{/option:oExtra}
			</div>
		</div>
		<div id="footer">
			{include:file="{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl"}
		</div>
	</div>
</body>
</html>