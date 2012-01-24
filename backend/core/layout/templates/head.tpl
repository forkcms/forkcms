<!DOCTYPE html>
<html lang="{$INTERFACE_LANGUAGE}">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<meta name="robots" content="noindex, nofollow" />

	<title>{$SITE_TITLE} - Fork CMS</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />

	{iteration:cssFiles}<link rel="stylesheet" href="{$cssFiles.file}" />{$CRLF}{$TAB}{/iteration:cssFiles}
	<!--[if IE 7]><link rel="stylesheet" href="/backend/core/layout/css/conditionals/ie7.css" /><![endif]-->
	<!--[if IE 8]><link rel="stylesheet" href="/backend/core/layout/css/conditionals/ie8.css" /><![endif]-->
</head>