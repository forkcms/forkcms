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
	<link rel="apple-touch-icon-precomposed" href="{$THEME_URL}/apple-touch-icon.png" /> {* @todo create a 144x144 png file *}

	{* Favicon *}
	<link rel="icon" href="{$THEME_URL}/favicon.png"> {* @todo create a 96x96 png file *}
	<!--[if IE]><link rel="shortcut icon" href="{$THEME_URL}/favicon.ico"><![endif]--> {* @todo	create a 32x32 ico file and store it in the root of the website.  *}

	{* Windows 8 tile *}
	<meta name="application-name" content="{$siteTitle}"/>
	<meta name="msapplication-TileColor" content="#3380aa"/> {* @todo choose a decent color *}
	<meta name="msapplication-TileImage" content="{$THEME_URL}/metro-tile.png"/> {* @todo create a white monochrome version (144x144) of the logo of the site *}

	{* Facebook *}
	<link rel="image_src" href="{$THEME_URL}/image_src.png" />
	<meta property="og:image" content="{$THEME_URL}/image_src.png" />

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