<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="nl" lang="nl">
<head>
	<title>{$siteTitle} - Fork CMS</title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />

	<link rel="shortcut icon" href="{$BACKEND_URL}/favicon.ico" />
	{iteration:cssFiles}<link rel="stylesheet" type="text/css" media="screen" href="{$cssFiles.path}" />{/iteration:cssFiles}

	{iteration:javascriptFiles}<script type="text/javascript" src="{$javascriptFiles.path}"></script>{/iteration:javascriptFiles}

</head>

<body>
<div id="container">
	<div id="header">
		<img src="{$BACKEND_CORE_URL}/layout/images/fork.gif" width="110" height="34" alt="Fork">
		<h1>SITE TITLE {option:oDebugStatus}(Debug mode){/option:oDebugStatus}</h1>
	</div>
	
	<div id="navigation">
		{$var|getnavigation:1:1}
	</div>
	<div id="subnavigation">
		{$var|getnavigation:2:3}
	</div>
	