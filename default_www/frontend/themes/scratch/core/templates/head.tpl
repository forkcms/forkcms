<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$LANGUAGE}">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<title>{$pageTitle}</title>
	<meta http-equiv="content-language" content="{$LANGUAGE}" />

	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="image_src" href="{$FRONTEND_CORE_URL}/layout/images/image_src.jpg" />

	{option:debug}<meta name="robots" content="noindex, nofollow" />{/option:debug}
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{$metaCustom}

	{* Stylesheets *}
	{iteration:cssFiles}
		<link rel="stylesheet" type="text/css" href="{$cssFiles.file}" />
	{/iteration:cssFiles}
	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="{$THEME_URL}/core/css/ie6.css" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="{$THEME_URL}/core/css/ie7.css" /><![endif]-->
	<link rel="stylesheet" type="text/css" media="print" href="{$THEME_URL}/core/css/print.css" />

	{* Javascript *}
	{iteration:javascriptFiles}
		<script type="text/javascript" src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>