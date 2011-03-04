<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="nl" class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="nl" class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="nl" class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="nl" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="nl"> <!--<![endif]-->
<head>
	<title>{$pageTitle}</title>

	{* Meta *}
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="content-language" content="{$LANGUAGE}" />
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{option:debug}<meta name="robots" content="noindex, nofollow" />{/option:debug}
	{$metaCustom}

	{* Favicon and Apple touch icon *}
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">

	{* Stylesheets *}
	{iteration:cssFiles}
		<link rel="stylesheet" media="{$cssFiles.media}" href="{$cssFiles.file}" />
	{/iteration:cssFiles}

	{* HTML5 Javascript *}
	<!--[if lt IE 9]> <script src="{$FRONTEND_CORE_URL}/js/html5.js"></script> <![endif]-->

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>