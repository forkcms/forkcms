<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="{$LANGUAGE}" class="ie6 ie"> <![endif]-->
<!--[if IE 7 ]> <html lang="{$LANGUAGE}" class="ie7 ie"> <![endif]-->
<!--[if IE 8 ]> <html lang="{$LANGUAGE}" class="ie8 ie"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="{$LANGUAGE}"> <!--<![endif]-->
<head>
	<title>{$pageTitle}</title>

	{* Meta *}
	<meta charset="utf-8" />
	<meta name="generator" content="Fork CMS" />
	<meta name="description" content="{$metaDescription}" />
	<meta name="keywords" content="{$metaKeywords}" />
	{option:debug}<meta name="robots" content="noindex, nofollow" />{/option:debug}
	{$metaCustom}

	{* Favicon and Apple touch icon *}
	<link rel="shortcut icon" href="{$THEME_URL}/favicon.ico" />
	<link rel="apple-touch-icon" href="{$THEME_URL}/apple-touch-icon.png" />

	{* 
		google fonts 
		change and remove
	*}
	<link href='http://fonts.googleapis.com/css?family=Pacifico|Oswald|Crimson+Text:400,400italic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Serif:400,400italic' rel='stylesheet' type='text/css'>
	
	{* Stylesheets *}
	{iteration:cssFiles}
	<link rel="stylesheet" href="{$cssFiles.file}" />
	{/iteration:cssFiles}
	
	{* HTML5, respond Javascript *}
	<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>