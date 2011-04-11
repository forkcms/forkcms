<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="{$LANGUAGE}" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1" />
	<meta name="robots" content="noindex, nofollow" />

	<title>{$SITE_TITLE} - Fork CMS</title>
	<link rel="shortcut icon" href="/backend/favicon.ico" />

	{iteration:cssFiles}<link rel="stylesheet" type="text/css" media="screen" href="{$cssFiles.path}" />{$CRLF}{$TAB}{/iteration:cssFiles}
	<!--[if IE 7]><link rel="stylesheet" type="text/css" media="screen" href="/backend/core/layout/css/conditionals/ie7.css" /><![endif]-->

	{iteration:javascriptFiles}<script type="text/javascript" src="{$javascriptFiles.path}"></script>{$CRLF}{$TAB}{/iteration:javascriptFiles}
</head>