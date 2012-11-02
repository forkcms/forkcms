<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$LANGUAGE}" class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="{$LANGUAGE}" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="{$LANGUAGE}" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="{$LANGUAGE}" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{$LANGUAGE}"> <!--<![endif]-->
<head>
	{* Meta *}
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="generator" content="Fork CMS" />
	{$meta}
	{$metaCustom}

	<title>{$pageTitle}</title>

	{* Stylesheets *}
	{iteration:cssFiles}
		<link rel="stylesheet" href="{$cssFiles.file}" />
	{/iteration:cssFiles}

	{* Favicon and Apple touch icon *}
	<link rel="shortcut icon" href="{$THEME_URL}/favicon.ico" />
	<link rel="apple-touch-icon" href="{$THEME_URL}/apple-touch-icon.png" />

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	{* General Javascript *}
	{iteration:jsFiles}
		<script src="{$jsFiles.file}"></script>
	{/iteration:jsFiles}

	{* Theme specific Javascript *}
	<script src="{$THEME_URL}/core/js/bootstrap.js"></script>
	<script src="{$THEME_URL}/core/js/theme.js"></script>

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>