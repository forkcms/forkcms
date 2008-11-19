<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$LANGUAGE}" lang="{$LANGUAGE}">
<head>
	<title>{$pageTitle}</title>
	
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-15" />
	<meta http-equiv="content-language" content="{$LANGUAGE}" />
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{$metaCustom}

	<link rel="shortcut icon" href="/favicon.ico" />
	<!-- start added css-files -->
	{iteration:iCssFiles}
		{option:oHasNoCondition}<link rel="stylesheet" type="text/css" media="{$media}" href="{$file}" />{/option:oHasNoCondition}
		{option:oHasCondition}<!--[if {$condition}]><link rel="stylesheet" type="text/css" media="{$media}" href="{$file}" /><![endif]-->{/option:oHasCondition}
	{/iteration:iCssFiles}
	<!-- end added css-files -->

	<!-- start added js-files -->
	{iteration:iJsFiles}<script type="text/javascript" src="{$file}"></script>{/iteration:iJsFiles}
	<!-- end added js-files -->
</head>
<body class="{$LANGUAGE} onsite">
	<div id="container">
		<div id="topbar" class="clearfix">
			{include:file="{$FRONTEND_CORE_PATH}/layout/templates/breadcrumb.tpl"}
			{option:oExtranetInclude}{include:file="{$FRONTEND_CORE_PATH}/layout/templates/extranet.tpl"}{/option:oExtranetInclude}
		</div>
		<div id="header" class="clearfix">
			<h1><a href="/" title="{$SITE_TITLE}">{$SITE_TITLE}</a></h1>
			{option:oLanguageInclude}{include:file="{$FRONTEND_CORE_PATH}/layout/templates/languages.tpl"}{/option:oLanguageInclude}
		</div>
		<div id="main" class="clearfix">
			<div id="navigation">
				{$nav|getnavigation:0:1:null:1}
			</div>
			<div id="content">
				<!-- start content -->
				{option:oHasBodyTitle}<h2>{$bodyTitle|ucfirst}</h2>{/option:oHasBodyTitle}
				{option:oHasBodyContent}{$bodyContent}{/option:oHasBodyContent}
				<!-- end content -->

				<!-- start extra -->
				{option:oHasExtra}{include:file="{$extraTemplatePath}"}{/option:oHasExtra}
				<!-- end extra -->
			</div>
		</div>
		<!-- start footer -->
		{include:file="{$FRONTEND_CORE_PATH}/layout/templates/footer.tpl"}
		<!-- end footer -->
	</div>
</body>
</html>