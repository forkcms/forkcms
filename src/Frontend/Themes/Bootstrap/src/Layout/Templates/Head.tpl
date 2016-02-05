<!DOCTYPE html>
<!--[if lte IE 8 ]> <html lang="{$LANGUAGE}" class="ie8 ie"> <![endif]-->
<!--[if IE 9 ]> <html lang="{$LANGUAGE}" class="ie9 ie"> <![endif]-->
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

	{* humans.txt, see http://humanstxt.org *}
	<link rel="author" href="/humans.txt" />

	{* Apple touch icon *}
	<link rel="apple-touch-icon" href="{$THEME_URL}/apple-touch-icon.png" /> {* @todo create a 180x180 png file *}

	{* Favicon *}
	<link rel="icon" href="{$THEME_URL}/favicon.png"> {* @todo create a 96x96 png file *}
	<!--[if IE]><link rel="shortcut icon" href="{$THEME_URL}/favicon.ico"><![endif]--> {* @todo	create a 32x32 ico file and store it in the root of the website.  *}

	{* Windows 8 tile *}
	<meta name="application-name" content="{$siteTitle}"/>
	<meta name="msapplication-TileColor" content="#3380aa"/> {* @todo choose a decent color *}
	<meta name="msapplication-TileImage" content="{$THEME_URL}/metro-tile.png"/> {* @todo create a white monochrome version (144x144) of the logo of the site *}

	{* Facebook *}
	<meta property="og:image" content="{$SITE_URL}{$THEME_URL}/image_src.png" /> {* @todo create a 200x200 png file *}

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js"></script>
	<![endif]-->

	{option:SPOON_DEBUG}
		<script src="http://localhost:35729/livereload.js?snipver=1"></script>
	{/option:SPOON_DEBUG}

	{* Site wide HTML *}
	{$siteHTMLHeader}
</head>
