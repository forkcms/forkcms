<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$LANGUAGE}" lang="{$LANGUAGE}">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<title>{$pageTitle}</title>
	<meta http-equiv="content-language" content="{$LANGUAGE}" />

	<link rel="shortcut icon" href="/favicon.ico" />

	{option:debug}<meta name="robots" content="noindex, nofollow" />{/option:debug}
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{$metaCustom}

	{* Stylesheets *}
	{iteration:cssFiles}
		{option:!cssFiles.condition}<link rel="stylesheet" type="text/css" media="{$cssFiles.media}" href="{$cssFiles.file}" />{/option:!cssFiles.condition}
		{option:cssFiles.condition}<!--[if {$cssFiles.condition}]><link rel="stylesheet" type="text/css" media="{$cssFiles.media}" href="{$cssFiles.file}" /><![endif]-->{/option:cssFiles.condition}
	{/iteration:cssFiles}

	{* Javascript *}
	{iteration:javascriptFiles}
		<script type="text/javascript" src="{$javascriptFiles.file}"></script>
	{/iteration:javascriptFiles}

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>